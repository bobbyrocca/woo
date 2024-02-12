<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.3.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments">
		<h2 class="woocommerce-Reviews-title">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s Review', '%1$s Reviews', $count, 'woocommerce' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'woocommerce' );
			}
			?>
		</h2>

		<?php if ( have_comments() ) : ?>
            <div class="review-summary-custom">
                <?php my_custom_reviews_summary();?>
            </div>
			<ol class="commentlist">
				<?php my_custom_reviews(); ?>
			</ol>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
							'next_text' => is_rtl() ? '&larr;' : '&rarr;',
							'type'      => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php else : ?>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
				$commenter    = wp_get_current_commenter();
				$comment_form = array(
					/* translators: %s is product title */
					'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
					/* translators: %s is product title */
					'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
					'title_reply_before'  => '<span id="reply-title" class="comment-reply-title">',
					'title_reply_after'   => '</span>',
					'comment_notes_after' => '',
					'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
					'logged_in_as'        => '',
					'comment_field'       => '',
				);

				$name_email_required = (bool) get_option( 'require_name_email', 1 );
				$fields              = array(
					'author' => array(
						'label'    => __( 'Name', 'woocommerce' ),
						'type'     => 'text',
						'value'    => $commenter['comment_author'],
						'required' => $name_email_required,
					),
					'email'  => array(
						'label'    => __( 'Email', 'woocommerce' ),
						'type'     => 'email',
						'value'    => $commenter['comment_author_email'],
						'required' => $name_email_required,
					),
				);

				$comment_form['fields'] = array();

				foreach ( $fields as $key => $field ) {
					$field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
					$field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

					if ( $field['required'] ) {
						$field_html .= '&nbsp;<span class="required">*</span>';
					}

					$field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

					$comment_form['fields'][ $key ] = $field_html;
				}

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					/* translators: %s opening and closing link tags respectively */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
				}

				if ( wc_review_ratings_enabled() ) {
					$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
					</select></div>';
				}

				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	<?php else : ?>
		<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>
	<?php endif; ?>

	<div class="clear"></div>
</div>

<?php
function my_custom_reviews() {
	global $product;

	$args = array(
		'post_id' => $product->get_id(),
		'status'  => 'approve',
		'type'    => 'review',
	);

	$reviews = get_comments($args);

	if (count($reviews) > 0) {
		echo '<ol class="commentlist">';

		foreach ($reviews as $review) {
			$rating = intval(get_comment_meta($review->comment_ID, 'rating', true));
			$image_url = get_comment_meta($review->comment_ID, 'review_image_url', true);

			echo '<li class="review">';
			if ($image_url) {
				echo '<img src="' . esc_url($image_url) . '" alt="Review Image" style="max-width: 100px; height: auto;">';
			}
			// Nome del recensore e rating
			echo '<p class="review-author">' . esc_html($review->comment_author) . '</p>';
			if ($rating) {
				echo '<div class="star-rating" title="' . sprintf(esc_attr__('Rated %d out of 5', 'woocommerce'), $rating) . '">';
				echo '<span style="width:' . (($rating / 5) * 100) . '%"><strong class="rating">' . $rating . '</strong> out of 5</span>';
				echo '</div>';
			}
			echo '<p class="review-text">' . esc_html($review->comment_content) . '</p>';
			echo '</li>';
		}

		echo '</ol>';
	} else {
		echo '<p class="woocommerce-noreviews">There are no reviews yet.</p>';
	}
}

function my_custom_reviews_summary() {
	global $product;

	if ( ! $product ) {
		return; // Esci se l'oggetto prodotto non è disponibile
	}

	$average       = $product->get_average_rating();
	$review_count  = $product->get_review_count();
	$rating_counts = $product->get_rating_counts(); // Ottieni il conteggio per ciascuna valutazione stellare.

	// Stampa il sommario delle recensioni.
	echo '<div class="product-reviews-summary">';
	echo '<div class="summary-content">';
	echo '<div class="expanded-summary-avg summary-text">';

	// Stampa le stelle.
	echo '<div class="star-rating" title="' . sprintf(esc_attr__('Rated %s out of 5', 'woocommerce'), $average) . '">';
	echo '<span style="width:' . (($average / 5) * 100) . '%">';
	echo '<strong class="rating">' . esc_html($average) . '</strong> out of 5';
	echo '</span>';
	echo '</div>'; // Chiusura .star-rating.

	echo '<span>' . esc_html($average) . '</span>';
	echo '</div>'; // Chiusura .expanded-summary-avg.
	echo '<div class="summary-text expanded-summary-count">';
	echo '<span>' . sprintf(_n('%s Review', '%s Reviews', $review_count, 'woocommerce'), number_format_i18n($review_count)) . '</span>';
	echo '</div>'; // Chiusura .summary-text.
	echo '</div>'; // Chiusura .summary-content.

	// Distribuzione delle recensioni.
	echo '<div class="reviews-dist">';
	echo '<div class="progress-section">';
	echo '<table>';
	foreach ( $rating_counts as $rating => $count ) {
		$width = ($count > 0) ? ($count / $review_count) * 100 : 0;
		echo '<tr>';
		echo '<td>';
		// Stampa le stelle per ciascuna riga
		echo '<div class="star-rating" title="' . sprintf(esc_attr__('Rated %d out of 5', 'woocommerce'), $rating) . '">';
		echo '<span style="width:' . (($rating / 5) * 100) . '%"><strong class="rating">' . $rating . '</strong> out of 5</span>';
		echo '</div>';
		echo '</td>';
		echo '<td style="padding:10px;"><div class="loox-progress"><div style="width:' . $width . '%" class="loox-progress-value"></div></div></td>';
		echo '<td class="reviews-num">(' . $count . ')</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	echo '</div>'; // Chiusura .progress-section.
	echo '</div>'; // Chiusura .reviews-dist.

	echo '</div>'; // Chiusura .summary.
}


?>