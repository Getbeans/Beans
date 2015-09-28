<?php
/**
 * Echo post fragments.
 *
 * @package Fragments\Post
 */

beans_add_smart_action( 'beans_post_header', 'beans_post_title' );

/**
 * Echo post title.
 *
 * @since 1.0.0
 */
function beans_post_title() {

	$title = beans_output( 'beans_post_title_text', get_the_title() );
	$title_tag = 'h1';

	if ( empty( $title ) )
		return;

	if ( !is_singular() ) {

		$title_link = beans_open_markup( 'beans_post_title_link', 'a', array(
			'href' => get_permalink(),
			'title' => the_title_attribute( 'echo=0' ),
			'rel' => 'bookmark'
		) );

			$title_link .= $title;

		$title_link .= beans_close_markup( 'beans_post_title_link', 'a' );

		$title = $title_link;

		$title_tag = 'h2';

	}

	echo beans_open_markup( 'beans_post_title', $title_tag, array(
		'class' => 'uk-article-title',
		'itemprop' => 'headline'
	) );

		echo $title;

	echo beans_close_markup( 'beans_post_title', $title_tag );

}


beans_add_smart_action( 'beans_before_loop', 'beans_post_search_title' );

/**
 * Echo search post title.
 *
 * @since 1.0.0
 */
function beans_post_search_title() {

	if ( !is_search() )
		return;

	echo beans_open_markup( 'beans_search_title', 'h1', array( 'class' => 'uk-article-title') );

		echo beans_output( 'beans_search_title_text', __( 'Search results for: ', 'tm-beans' ) ) . get_search_query();

	echo beans_close_markup( 'beans_search_title', 'h1' );

}


beans_add_smart_action( 'beans_post_header', 'beans_post_meta', 15 );

/**
 * Echo post meta.
 *
 * @since 1.0.0
 */
function beans_post_meta() {

	/**
	 * Filter whether {@see beans_post_meta()} should be short-circuit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_post_meta', 'post' != get_post_type() ) )
		return;

	echo beans_open_markup( 'beans_post_meta', 'ul', array( 'class' => 'uk-article-meta uk-subnav uk-subnav-line' ) );

		$meta_items = apply_filters( 'beans_post_meta_items', array(
			'date' => 10,
			'author' => 20,
			'comments' => 30
		) );

		asort( $meta_items );

		foreach ( $meta_items as $meta => $priority ) {

			if ( !$content = beans_render_function( 'do_action', "beans_post_meta_$meta" ) )
				continue;

			echo beans_open_markup( "beans_post_meta_item[_{$meta}]", 'li' );

				echo beans_output( "beans_post_meta_item_{$meta}_text", $content ) ;

			echo beans_close_markup( "beans_post_meta_item[_{$meta}]", 'li' );

		}

	echo beans_close_markup( 'beans_post_meta', 'ul' );

}


beans_add_smart_action( 'beans_post_body', 'beans_post_image', 5 );

/**
 * Echo post image.
 *
 * @since 1.0.0
 */
function beans_post_image() {

	if ( !has_post_thumbnail() )
		return false;

	global $post;

	/**
	 * Filter the arguments used by {@see beans_edit_image()} to edit the post image.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array $edit_args Arguments used by {@see beans_edit_image()}. Set to false to use WordPress
	 *                              large size.
	 */
	$edit_args = apply_filters( 'beans_edit_post_image_args', array(
		'resize' => array( 800, false )
	) );

	if ( empty( $edit_args ) )
		$image = beans_get_post_attachment( $post->ID, 'large' );
	else
		$image = beans_edit_post_attachment( $post->ID, $edit_args );

	/**
	 * Filter the arguments used by {@see beans_edit_image()} to edit the post small image.
	 *
	 * The small image is only used for screens equal or smaller than the image width set, which is 480px by default.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|array $edit_args Arguments used by {@see beans_edit_image()}. Set to false to use WordPress
	 *                              small size.
	 */
	$edit_small_args = apply_filters( 'beans_edit_post_image_small_args', array(
		'resize' => array( 480, false )
	) );

	if ( empty( $edit_small_args ) )
		$image_small = beans_get_post_attachment( $post->ID, 'small' );
	else
		$image_small = beans_edit_post_attachment( $post->ID, $edit_small_args );

	echo beans_open_markup( 'beans_post_image', 'div', array( 'class' => 'tm-article-image' ) );

		if ( !is_singular() )
			echo beans_open_markup( 'beans_post_image_link', 'a', array(
				'href' => get_permalink(),
				'title' => the_title_attribute( 'echo=0' )
			) );

			echo beans_open_markup( 'beans_post_image_item_wrap', 'picture' );

				echo beans_selfclose_markup( 'beans_post_image_small_item', 'source', array(
					'media' => '(max-width: ' . $image_small->width . 'px)',
					'srcset' => $image_small->src,
				), $image_small );

				echo beans_selfclose_markup( 'beans_post_image_item', 'img', array(
					'width' => $image->width,
					'height' => $image->height,
					'src' => $image->src,
					'alt' => esc_attr( $image->alt ),
					'itemprop' => 'image'
				), $image );

			echo beans_close_markup( 'beans_post_image_item_wrap', 'picture' );

		if ( !is_singular() )
			echo beans_close_markup( 'beans_post_image_link', 'a' );

	echo beans_close_markup( 'beans_post_image', 'div' );

}


beans_add_smart_action( 'beans_post_body', 'beans_post_content' );

/**
 * Echo post content.
 *
 * @since 1.0.0
 */
function beans_post_content() {

	global $post;

	echo beans_open_markup( 'beans_post_content', 'div', array(
		'class' => 'tm-article-content',
		'itemprop' => 'text'
	) );

		the_content();

		if ( is_singular() && 'open' === get_option( 'default_ping_status' ) && post_type_supports( $post->post_type, 'trackbacks' ) ) :

			echo '<!--';
			trackback_rdf();
			echo '-->' . "\n";

		endif;

	echo beans_close_markup( 'beans_post_content', 'div' );

}


// Filter.
beans_add_smart_action( 'the_content_more_link', 'beans_post_more_link' );

/**
 * Modify post "more link".
 *
 * @since 1.0.0
 *
 * @return string The modified "more link".
 */
function beans_post_more_link() {

	global $post;

	$output = beans_open_markup( 'beans_post_more_link', 'a', array(
		'href' => get_permalink() . "#more-{$post->ID}",
		'class' => 'more-link',
	) );

		$output .= beans_output( 'beans_post_more_link_text', __( 'Continue reading', 'tm-beans' ) );

		$output .= beans_open_markup( 'beans_next_icon[_more_link]', 'i', array(
					'class' => 'uk-icon-angle-double-right uk-margin-small-left'
				) );
		$output .= beans_close_markup( 'beans_previous_icon[_more_link]', 'i' );

	$output .= beans_close_markup( 'beans_post_more_link', 'a' );

	return $output;

}


beans_add_smart_action( 'beans_post_body', 'beans_post_content_navigation', 20 );

/**
 * Echo post content navigation.
 *
 * @since 1.0.0
 */
function beans_post_content_navigation() {

	echo wp_link_pages( array(
		'before' => beans_open_markup( 'beans_post_content_navigation', 'p', array( 'class' => 'uk-text-bold' ) ) . beans_output( 'beans_post_content_navigation_text', __( 'Pages:', 'tm-beans' ) ),
		'after' => beans_close_markup( 'beans_post_content_navigation', 'p' ),
		'echo' => false
	) );

}


beans_add_smart_action( 'beans_post_body', 'beans_post_meta_categories', 25 );

/**
 * Echo post meta categories.
 *
 * @since 1.0.0
 */
function beans_post_meta_categories() {

	if ( !$categories = beans_render_function( 'do_shortcode', '[beans_post_meta_categories]' ) )
		return;

	echo beans_open_markup( 'beans_post_meta_categories', 'span', array( 'class' => 'uk-text-small uk-text-muted uk-clearfix' ) );

		echo $categories;

	echo beans_close_markup( 'beans_post_meta_categories', 'span' );

}


beans_add_smart_action( 'beans_post_body', 'beans_post_meta_tags', 30 );

/**
 * Echo post meta tags.
 *
 * @since 1.0.0
 */
function beans_post_meta_tags() {

	if ( !$tags = beans_render_function( 'do_shortcode', '[beans_post_meta_tags]' ) )
		return;

	echo beans_open_markup( 'beans_post_meta_tags', 'span', array( 'class' => 'uk-text-small uk-text-muted uk-clearfix' ) );

		echo $tags;

	echo beans_close_markup( 'beans_post_meta_tags', 'span' );

}


// Filter.
beans_add_smart_action( 'previous_post_link', 'beans_previous_post_link', 10, 4 );

/**
 * Modify post "previous link".
 *
 * @since 1.0.0
 *
 * @return string The modified "previous link".
 */
function beans_previous_post_link( $output, $format, $link, $post ) {

	// Using $link won't apply wp filters, so rather strip tags the $output.
	$text = strip_tags( $output );

	$output = beans_open_markup( 'beans_previous_link[_post_navigation]', 'a', array(
		'href' => get_permalink( $post ),
		'ref' => 'previous',
		'title' => $post->post_title
	) );

		$output .= beans_open_markup( 'beans_previous_icon[_post_navigation]', 'i', array(
			'class' => 'uk-icon-angle-double-left uk-margin-small-right'
		) );

		$output .= beans_close_markup( 'beans_previous_icon[_post_navigation]', 'i' );

		$output .= beans_output( 'beans_previous_text[_post_navigation]', $text );

	$output .= beans_close_markup( 'beans_previous_link[_post_navigation]', 'a' );

	return $output;

}


// Filter.
beans_add_smart_action( 'next_post_link', 'beans_next_post_link', 10, 4 );

/**
 * Modify post "next link".
 *
 * @since 1.0.0
 *
 * @return string The modified "next link".
 */
function beans_next_post_link( $output, $format, $link, $post ) {

	// Using $link won't apply wp filters, so rather strip tags the $output.
	$text = strip_tags( $output );

	$output = beans_open_markup( 'beans_next_link[_post_navigation]', 'a', array(
		'href' => get_permalink( $post ),
		'rel' => 'next',
		'title' => $post->post_title
	) );

		$output .= beans_output( 'beans_next_text[_post_navigation]', $text );

		$output .= beans_open_markup( 'beans_next_icon[_post_navigation]', 'i', array(
			'class' => 'uk-icon-angle-double-right uk-margin-small-left'
		) );

		$output .= beans_close_markup( 'beans_previous_icon[_post_navigation]', 'i' );

	$output .= beans_close_markup( 'beans_next_link[_post_navigation]', 'a' );

	return $output;

}


beans_add_smart_action( 'beans_post_after_markup', 'beans_post_navigation' );

/**
 * Echo post navigation.
 *
 * @since 1.0.0
 */
function beans_post_navigation() {

	/**
	 * Filter whether {@see beans_post_navigation()} should be short-circuit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_post_navigation', !is_singular( 'post' ) ) )
		return;

	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( !$next && !$previous )
		return;

	echo beans_open_markup( 'beans_post_navigation', 'ul', array(
		'class' => 'uk-pagination',
		'role' => 'navigation'
	) );

		if ( $previous ) :

			// Previous.
			echo beans_open_markup( 'beans_post_navigation_item[_previous]', 'li', array( 'class' => 'uk-pagination-previous' ) );

				echo get_previous_post_link( '%link', __( 'Previous', 'tm-beans' ) );

			echo beans_close_markup( 'beans_post_navigation_item[_previous]', 'li' );

		endif;

		if ( $next ) :

			// Next.
			echo beans_open_markup( 'beans_post_navigation_item[_next]', 'li', array( 'class' => 'uk-pagination-next' ) );

				echo get_next_post_link( '%link', __( 'Next', 'tm-beans' ) );

			echo beans_close_markup( 'beans_post_navigation_item[_next]', 'li' );

		endif;

	echo beans_close_markup( 'beans_post_navigation', 'ul' );

}


beans_add_smart_action( 'beans_after_posts_loop', 'beans_posts_pagination' );

/**
 * Echo posts pagination.
 *
 * @since 1.0.0
 */
function beans_posts_pagination() {

	/**
	 * Filter whether {@see beans_posts_pagination()} should be short-circuit or not.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_post_pagination', is_singular() ) )
		return;

	global $wp_query;

	if ( $wp_query->max_num_pages <= 1 )
		return;

	$current = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$count = intval( $wp_query->max_num_pages );

	echo beans_open_markup( 'beans_posts_pagination', 'ul', array(
		'class' => 'uk-pagination uk-grid-margin',
		'role' => 'navigation'
	) );

		// Previous.
		if ( get_previous_posts_link() ) {

			echo beans_open_markup( 'beans_posts_pagination_item[_previous]', 'li' );

				echo beans_open_markup( 'beans_previous_link[_posts_pagination]', 'a', array(
					'href' => esc_url( previous_posts( false ) )
				), $current );

					echo beans_open_markup( 'beans_previous_icon[_posts_pagination]', 'i', array(
						'class' => 'uk-icon-angle-double-left uk-margin-small-right'
					) );

					echo beans_close_markup( 'beans_previous_icon[_posts_pagination]', 'i' );

					echo beans_output( 'beans_previous_text[_posts_pagination]', __( 'Previous', 'tm-beans' ) );

				echo beans_close_markup( 'beans_previous_link[_posts_pagination]', 'a' );

			echo beans_close_markup( 'beans_posts_pagination_item[_previous]', 'li' );



		}

		// Links.
		foreach ( range( 1, $wp_query->max_num_pages ) as $link ) {

			// Skip if next is set.
			if ( isset( $next ) && $link != $next )
				continue;
			else
				$next = $link + 1;

			$is_separator = array(
				$link != 1, // Not first.
				$current == 1 && $link == 3 ? false : true, // Force first 3 items.
				$count > 3, // More.
				$link != $count, // Not last.
				$link != ( $current - 1 ), // Not previous.
				$link != $current, // Not current.
				$link != ( $current + 1 ), // Not next.
			);

			// Separator.
			if ( !in_array( false, $is_separator ) ) {

				echo beans_open_markup( 'beans_posts_pagination_item[_separator]', 'li' );

					echo beans_output( 'beans_posts_pagination_item_separator_text', '...' );

				echo beans_close_markup( 'beans_posts_pagination_item[_separator]', 'li' );

				// Jump.
				if ( $link < $current )
					$next = $current - 1;
				elseif ( $link > $current )
					$next = $count;

				continue;

			}


			// Integer.
			if ( $link == $current ) {

				echo beans_open_markup( 'beans_posts_pagination_item[_active]', 'li', array( 'class' => 'uk-active') );

					echo '<span>' . $link . '</span>';

				echo beans_close_markup( 'beans_posts_pagination_item[_active]', 'li' );

			} else {

				echo beans_open_markup( 'beans_posts_pagination_item', 'li' );

					echo beans_open_markup( 'beans_posts_pagination_item_link', 'a', array(
						'href' => esc_url( get_pagenum_link( $link ) )
					), $link );

						echo beans_output( 'beans_posts_pagination_item_link_text', $link );

					echo beans_close_markup( 'beans_posts_pagination_item_link', 'a' );

				echo beans_close_markup( 'beans_posts_pagination_item', 'li' );

			}

		}


		// Next.
		if ( get_next_posts_link() ) {

			echo beans_open_markup( 'beans_posts_pagination_item[_next]', 'li' );

				echo beans_open_markup( 'beans_next_link[_posts_pagination]', 'a', array(
					'href' => esc_url( next_posts( $count, false ) )
				), $current );

					echo beans_output( 'beans_next_text[_posts_pagination]', __( 'Next', 'tm-beans' ) );

					echo beans_open_markup( 'beans_next_icon[_posts_pagination]', 'i', array(
						'class' => 'uk-icon-angle-double-right uk-margin-small-left'
					) );

					echo beans_close_markup( 'beans_next_icon[_posts_pagination]', 'i' );

				echo beans_close_markup( 'beans_next_link[_posts_pagination]', 'a' );

			echo beans_close_markup( 'beans_posts_pagination_item[_next]', 'li' );

		}

	echo beans_close_markup( 'beans_posts_pagination', 'ul' );

}


beans_add_smart_action( 'beans_no_post', 'beans_no_post' );

/**
 * Echo no post content.
 *
 * @since 1.0.0
 */
function beans_no_post() {

	echo beans_open_markup( 'beans_post', 'article', array( 'class' => 'tm-no-article uk-article' . ( current_theme_supports( 'beans-default-styling' ) ? ' uk-panel-box' : null ) ) );

		echo beans_open_markup( 'beans_post_header', 'header' );

			echo beans_open_markup( 'beans_post_title', 'h1', array( 'class' => 'uk-article-title' ) );

				echo beans_output( 'beans_no_post_article_title_text', __( 'Whoops, no result found!', 'tm-beans' ) );

			echo beans_close_markup( 'beans_post_title', 'h1' );

		echo beans_close_markup( 'beans_post_header', 'header' );

		echo beans_open_markup( 'beans_post_body', 'div' );

			echo beans_open_markup( 'beans_post_content', 'div', array( 'class' => 'tm-article-content' ) );

				echo beans_open_markup( 'beans_no_post_article_content', 'p', array( 'class' => 'uk-alert uk-alert-warning' ) );

					echo beans_output( 'beans_no_post_article_content_text', __( 'It looks like nothing was found at this location. Maybe try a search?', 'tm-beans' ) );

				echo beans_close_markup( 'beans_no_post_article_content', 'p' );

					echo beans_output( 'beans_no_post_search_form', get_search_form( false ) );

			echo beans_close_markup( 'beans_post_content', 'div' );

		echo beans_close_markup( 'beans_post_body', 'div' );

	echo beans_close_markup( 'beans_post', 'article' );

}


// Filter.
beans_add_smart_action( 'the_password_form', 'beans_post_password_form' );

/**
 * Modify password protected form.
 *
 * @since 1.0.0
 *
 * @return string The form.
 */
function beans_post_password_form() {

	global $post;

	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );

	// Notice.
	$output = beans_open_markup( 'beans_password_form_notice', 'p', array( 'class' => 'uk-alert uk-alert-warning' ) );

		$output .= beans_output( 'beans_password_form_notice_text', __( 'This post is protected. To view it, enter the password below!', 'tm-beans' ) );

	$output .= beans_close_markup( 'beans_password_form_notice', 'p' );

	// Form.
	$output .= beans_open_markup( 'beans_password_form', 'form', array(
		'class' => 'uk-form uk-margin-bottom',
		'method' => 'post',
		'action' => esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) )
	) );

		$output .= beans_selfclose_markup( 'beans_password_form_input', 'input', array(
			'class' => 'uk-margin-small-top uk-margin-small-right',
			'type' => 'password',
			'placeholder' => esc_attr( apply_filters( 'beans_password_form_input_placeholder', __( 'Password', 'tm-beans' ) ) ),
			'name' => 'post_password'
		) );

		$output .= beans_selfclose_markup( 'beans_password_form_submit', 'input', array(
			'class' => 'uk-button uk-margin-small-top',
			'type' => 'submit',
			'name' => 'submit',
			'value' => esc_attr( apply_filters( 'beans_password_form_submit_text', __( 'Submit', 'tm-beans' ) ) )
		) );

	$output .= beans_close_markup( 'beans_password_form', 'form' );

	return $output;

}