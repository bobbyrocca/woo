<?php
/**
 * Plugin Name: Adrocket Reviews Shortcode
 * Description: A shortcode to display reviews on a page.
 * Version: 1.0
 * Author: Halexo Limited
 */
// Prevenire accesso diretto al file.
defined( 'ABSPATH' ) || exit;

function adrocket_reviews_enqueue_css() {
	wp_enqueue_style( 'adrocket-reviews-css', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . microtime() );
}

add_action( 'wp_enqueue_scripts', 'adrocket_reviews_enqueue_css' );

// Aggiungi il campo di caricamento dell'immagine nell'editor delle recensioni
function custom_review_image_upload_field( $comment ) {
	if ( 'review' === get_comment_type( $comment->comment_ID ) ) {
		wp_nonce_field( 'custom_review_image_upload', 'custom_review_image_nonce' );
		?>
        <p>
            <label for="review-image">Carica Immagine:</label>
            <input type="file" name="review-image" id="review-image">
            <button type="button" id="upload-btn">Upload</button>
        </p>
		<?php
	}
}

add_action( 'add_meta_boxes_comment', 'custom_review_image_upload_field' );

// Funzione per gestire il caricamento dell'immagine
function handle_review_image_upload( $comment_id ) {
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	$uploadedfile = $_FILES['review-image'];

	// Imposta le opzioni per l'upload
	$upload_overrides = array( 'test_form' => false );

	// Esegue l'upload e verifica se ci sono errori
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

	if ( $movefile && ! isset( $movefile['error'] ) ) {
		// L'immagine è stata caricata con successo
		$image_url = $movefile['url'];

		// Salva l'URL dell'immagine nel database
		update_comment_meta( $comment_id, 'review_image_url', $image_url );
	} else {
		// Gestisce gli errori di caricamento
		// Puoi scegliere di registrare questo errore o mostrare un messaggio all'utente
		error_log( 'Errore nel caricamento dell\'immagine: ' . $movefile['error'] );
	}
}

// Aggiungi l'azione per salvare l'immagine quando viene modificata una recensione
add_action( 'edit_comment', 'save_custom_review_image' );

// Funzione per salvare l'immagine personalizzata
function save_custom_review_image( $comment_id ) {
	if ( ! empty( $_FILES['review-image']['name'] ) ) {
		handle_review_image_upload( $comment_id );
	}
}

add_action('wp_ajax_upload_review_image', 'handle_ajax_review_image_upload');

function handle_ajax_review_image_upload() {
	// Assicurati di avere tutti i controlli di sicurezza qui, come la verifica dei nonce

	$comment_id = $_POST['comment_id'];
	handle_review_image_upload($comment_id); // La tua funzione di caricamento esistente

	wp_send_json_success('Immagine caricata con successo');
}


function adrocket_reviews_admin_scripts() {
	global $pagenow;

	if ($pagenow == 'comment.php' && isset($_GET['action']) && $_GET['action'] == 'editcomment') {
		$handle = 'adrocket-reviews-js';
		wp_enqueue_script($handle, plugin_dir_url(__FILE__) . 'js/reviews.js', array('jquery'), microtime(), true);

		// Prepara i dati da passare allo script
		$script_data = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			// Passa altri dati necessari allo script qui, se necessario
		);

		wp_localize_script($handle, 'adrocketReviewsData', $script_data);
	}
}

add_action('admin_enqueue_scripts', 'adrocket_reviews_admin_scripts');
