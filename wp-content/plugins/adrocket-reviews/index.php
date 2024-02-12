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
        <div>
            <div>
                <p><strong>Carica un'immagine per questa recensione.</strong></p>
            </div>
            <div>
                <label for="review-image">Seleziona:</label>
                <input type="file" name="review-image" id="review-image">
                <button type="button" id="upload-btn">Upload</button>
            </div>
        </div>
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

add_action( 'wp_ajax_upload_review_image', 'handle_ajax_review_image_upload' );

function adrocket_reviews_admin_scripts() {
	global $pagenow;

	if ( $pagenow == 'comment.php' && isset( $_GET['action'] ) && $_GET['action'] == 'editcomment' && isset( $_GET['c'] ) ) {
		$comment_id = $_GET['c'];
		$handle     = 'adrocket-reviews-js';
		wp_enqueue_script( $handle, plugin_dir_url( __FILE__ ) . 'js/reviews.js', array( 'jquery' ), microtime(), true );

		// Prepara i dati da passare allo script
		$script_data = array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'commentID' => $comment_id
		);

		wp_localize_script( $handle, 'adrocketReviewsData', $script_data );
	}
}

add_action( 'admin_enqueue_scripts', 'adrocket_reviews_admin_scripts' );

function custom_review_image_display_field( $comment ) {
	if ( 'review' === get_comment_type( $comment->comment_ID ) ) {
		// Recupera l'URL dell'immagine meta della recensione
		$image_url = get_comment_meta( $comment->comment_ID, 'review_image_url', true );

		// Mostra l'immagine se presente
		if ( ! empty( $image_url ) ) {
			echo '<p>Current user: '.current_user_can('administrator').'</p>';
			echo '<div>';
			echo '<div><p><strong>Immagine Caricata:</strong></p></div>';
			echo "<div><img id='review-image-display' src='" . esc_url( $image_url ) . "' alt='Review Image' style='max-width: 180px; height: auto;' /></div>";
			echo '</div>';
		} else {
			echo '<div><p><strong>Nessuna immagine caricata per questa recensione!</strong></p></div>';
		}
	}
}

add_action( 'add_meta_boxes_comment', 'custom_review_image_display_field' );

function handle_ajax_review_image_upload() {

	// Verifica se l'utente corrente è un amministratore
	if (!current_user_can('administrator')) {
		wp_send_json_error('Non autorizzato');
		return;
	}

	// Verifica il nonce per la sicurezza
	if (!isset($_POST['custom_review_image_nonce']) || !wp_verify_nonce($_POST['custom_review_image_nonce'], 'custom_review_image_upload')) {
		wp_send_json_error('Verifica di sicurezza fallita');
		return;
	}

	$comment_id = $_POST['comment_id'];
	handle_review_image_upload($comment_id); // La tua funzione di caricamento esistente

	// Recupera l'URL dell'immagine caricata
	$image_url = get_comment_meta($comment_id, 'review_image_url', true);

	if (!empty($image_url)) {
		wp_send_json_success(array('image_url' => $image_url));
	} else {
		wp_send_json_error('Errore nel caricamento dell\'immagine');
	}
}

add_action('wp_ajax_upload_review_image', 'handle_ajax_review_image_upload');

add_action('add_meta_boxes_comment', 'adrocket_add_clone_button_on_edit');

function adrocket_add_clone_button_on_edit() {
	global $pagenow;

	// Verifica se sei nella pagina di modifica del commento
	if ('comment.php' != $pagenow || !isset($_GET['action']) || 'editcomment' != $_GET['action'] || !isset($_GET['c'])) {
		return;
	}

	$comment_id = intval($_GET['c']);
	$comment = get_comment($comment_id);

	// Verifica che il commento sia una recensione
	if ('review' === $comment->comment_type) {

		// Costruisci l'URL per l'azione di cancellazione dell'immagine
		$delete_image_url = wp_nonce_url(
			admin_url("edit-comments.php?action=adrocket_delete_review_image&c={$comment_id}"),
			'adrocket_delete_review_image_' . $comment_id
		);

		// Aggiungi il pulsante Delete Image
		echo "<div style='margin: 10px 0;'><a href='{$delete_image_url}' class='button'>Delete Image</a></div>";

		// Costruisci l'URL per l'azione di clonazione
		$clone_url = wp_nonce_url(
			admin_url("edit-comments.php?action=adrocket_clone_review&c={$comment_id}"),
			'adrocket_clone_review_' . $comment_id
		);

		// Stampa il link o il bottone
		echo "<div style='margin: 10px 0;'><a href='{$clone_url}' class='button'>Clone Review</a></div>";
	}
}

add_action('admin_init', 'adrocket_handle_clone_review');

function adrocket_handle_clone_review() {
	if (isset($_GET['action']) && $_GET['action'] == 'adrocket_clone_review' && !empty($_GET['c'])) {
		$comment_id = intval($_GET['c']);

		// Verifica il nonce per la sicurezza
		check_admin_referer('adrocket_clone_review_' . $comment_id);

		// Clona la recensione
		$cloned_comment_id = adrocket_clone_comment($comment_id);

		if ($cloned_comment_id) {
			// Reindirizza, per esempio, alla lista delle recensioni
			wp_redirect(admin_url('comment.php?action=editcomment&c=' . $cloned_comment_id));
			exit;
		} else {
			// Gestisci l'errore di clonazione
			wp_die('Si è verificato un errore nella clonazione della recensione.');
		}
	}
}

function adrocket_clone_comment($comment_id) {
	$comment = get_comment($comment_id);

	if (!$comment) {
		return false;
	}

	// Crea un nuovo array di dati di commento basato sul commento originale
	$cloned_comment_data = array(
		'comment_post_ID'      => $comment->comment_post_ID,
		'comment_author'       => $comment->comment_author,
		'comment_author_email' => $comment->comment_author_email,
		'comment_author_url'   => $comment->comment_author_url,
		'comment_content'      => $comment->comment_content,
		'comment_type'         => $comment->comment_type,
		'comment_parent'       => $comment->comment_parent,
		'user_id'              => $comment->user_id,
	);

	// Inserisci il nuovo commento (recensione clonata)
	$new_comment_id = wp_insert_comment($cloned_comment_data);

	// Clona il rating (e altri metadati se necessario)
	$rating = get_comment_meta($comment_id, 'rating', true);
	if (!empty($rating)) {
		update_comment_meta($new_comment_id, 'rating', $rating);
	}

	// Clona eventuali metadati associati
	$meta_keys = ['your_meta_keys']; // Sostituisci con i metadati reali se necessario
	foreach ($meta_keys as $key) {
		$meta_value = get_comment_meta($comment_id, $key, true);
		if (!empty($meta_value)) {
			update_comment_meta($new_comment_id, $key, $meta_value);
		}
	}

	return $new_comment_id;
}

add_action('admin_init', 'adrocket_handle_delete_review_image');

function adrocket_handle_delete_review_image() {
	if (isset($_GET['action']) && $_GET['action'] == 'adrocket_delete_review_image' && !empty($_GET['c'])) {
		$comment_id = intval($_GET['c']);

		// Verifica il nonce per la sicurezza
		check_admin_referer('adrocket_delete_review_image_' . $comment_id);

		// Rimuovi l'immagine e i metadati associati
		$image_url = get_comment_meta($comment_id, 'review_image_url', true);
		if ($image_url) {
			// Elimina il file dell'immagine se necessario
			// ...

			// Rimuovi il metadato
			delete_comment_meta($comment_id, 'review_image_url');
		}

		// Reindirizza di nuovo alla pagina di modifica
		wp_redirect(admin_url('comment.php?action=editcomment&c=' . $comment_id));
		exit;
	}
}