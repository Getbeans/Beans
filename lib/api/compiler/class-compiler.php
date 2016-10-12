<?php
/**
 * Compiles and minifies CSS, LESS and JS.
 *
 * @ignore
 *
 * @package API\Compiler
 */
final class _Beans_Compiler {

	/**
	 * Compiler arguments.
	 *
	 * @type array
	 */
	var $compiler;

	/**
	 * Cache dir.
	 *
	 * @type string
	 */
	var $dir;

	/**
	 * Cache url.
	 *
	 * @type string
	 */
	var $url;

	/**
	 * Set during in fragments loop.
	 *
	 * @type string
	 */
	var $current_fragment;

	/**
	 * Constructor.
	 *
	 * @param array $args Setting arguments.
	 */
	public function __construct( $args ) {

		// Modify the WP Filsystem method.
		add_filter( 'filesystem_method', array( $this, 'filesystem_method' ) );

		$defaults = array(
			'id'          => false,
			'type'        => false,
			'format'      => false,
			'fragments'   => array(),
			'depedencies' => false,
			'in_footer'   => false,
			'minify_js'   => false,
			'version'     => false,
		);

		$this->compiler = array_merge( $defaults, $args );
		$this->dir = beans_get_compiler_dir( is_admin() ) . $this->compiler['id'];
		$this->url = beans_get_compiler_url( is_admin() ) . $this->compiler['id'];

		$this->set_fragments();
		$this->set_filname();

		if ( ! $this->cache_file_exist() ) {

			$this->filesystem();
			$this->maybe_make_dir();
			$this->cache_file();

		}

		$this->enqueue_file();

		// Keep it safe and reset WP Filsystem method.
		remove_filter( 'filesystem_method', array( $this, 'filesystem_method' ) );

	}

	/**
	 * Set WP Filsystem method.
	 */
	public function filesystem_method() {

		return 'direct';

	}

	/**
	 * Initialise WP Filsystem.
	 */
	public function filesystem() {

		// Initialize the WordPress Filsystem.
		if ( ! isset( $GLOBALS['wp_filesystem'] ) || empty( $GLOBALS['wp_filesystem'] ) ) {

			require_once( ABSPATH . '/wp-admin/includes/file.php' );

			if ( ! WP_Filesystem() ) {
				return $this->kill();
			}
		}

		return true;

	}

	/**
	 * Make directory.
	 */
	public function maybe_make_dir() {

		if ( ! @is_dir( $this->dir ) ) {
			wp_mkdir_p( $this->dir );
		}

		if ( ! is_writable( $this->dir ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Set class fragments.
	 */
	public function set_fragments() {

		global $_beans_compiler_added_fragments;

		if ( $added_fragments = beans_get( $this->compiler['id'], $_beans_compiler_added_fragments[ $this->compiler['format'] ] ) ) {
			$this->compiler['fragments'] = array_merge( $this->compiler['fragments'], $added_fragments );
		}

		/**
		 * Filter the compiler fragment files.
		 *
		 * The dynamic portion of the hook name, $this->compiler['id'], refers to the compiler id used as a reference.
		 *
		 * @since 1.0.0
		 *
		 * @param array $fragments An array of fragment files.
		 */
		$this->compiler['fragments'] = apply_filters( 'beans_compiler_fragments_' . $this->compiler['id'], $this->compiler['fragments'] );

	}

	/**
	 * Set class filname.
	 */
	public function set_filname() {

		$hash = substr( md5( @serialize( $this->compiler ) ), 0, 7 );

		// Stop here and return filename if not in dev mode or if not using filesystem.
		if ( ! _beans_is_compiler_dev_mode() || ! @is_dir( $this->dir ) ) {
			return $this->compiler['filename'] = $hash . '.' . $this->get_extension();
		}

		$fragments_filemtime = array();

		// Check for internal file changes.
		foreach ( $this->compiler['fragments'] as $id => $fragment ) {

			// Ignore if the fragment is a function.
			if ( $this->is_function( $fragment ) ) {
				continue;
			}

			// Only check file time for internal files.
			if ( false !== strpos( $fragment, $_SERVER['HTTP_HOST'] ) || true == preg_match( '#^\/[^\/]#', $fragment ) ) {
				$fragments_filemtime[ $id ] = @filemtime( beans_url_to_path( $fragment ) );
			}
		}

		if ( ! empty( $fragments_filemtime ) ) {

			// Set filemtime hash.
			$_hash = substr( md5( @serialize( $fragments_filemtime ) ), 0, 7 );

			$items = @scandir( $this->dir );
			unset( $items[0], $items[1] );

			// Clean up other modified files.
			foreach ( $items as $item ) {

				// Remove if it contains initial hash, is the same format and doesn't contain the filemtime hash.
				if ( false !== stripos( $item, $hash ) && false !== stripos( $item, $this->get_extension() ) && false === stripos( $item, $_hash ) ) {
					@unlink( $this->dir . '/' . $item );
				}
			}

			// Set the new hash which will trigger to new compiling.
			$hash = $hash . '-' . $_hash;

		}

		$this->compiler['filename'] = $hash . '.' . $this->get_extension();

	}

	/**
	 * Check if cached file exists.
	 */
	public function cache_file_exist() {

		if ( ( $filname = beans_get( 'filename', $this->compiler ) ) && file_exists( $this->dir . '/' . $filname ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Create cached file.
	 */
	public function cache_file() {

		$content = $this->combine_fragments();
		$filename = $this->dir . '/' . $this->compiler['filename'];

		// Safe to access filesystem since we made sure it was set.
		if ( ! $GLOBALS['wp_filesystem']->put_contents( $filename, $content, FS_CHMOD_FILE ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Enqueue cached file.
	 */
	public function enqueue_file() {

		// Enqueue css.
		if ( 'style' == $this->compiler['type'] ) {
			return wp_enqueue_style( $this->compiler['id'], $this->get_url(), $this->compiler['depedencies'], $this->compiler['version'] );
		} elseif ( 'script' == $this->compiler['type'] ) { // Enqueue js file.
			return wp_enqueue_script( $this->compiler['id'], $this->get_url(), $this->compiler['depedencies'], $this->compiler['version'], $this->compiler['in_footer'] );
		}

		return false;

	}

	/**
	 * Get cached file url.
	 */
	public function get_url() {

		$url = trailingslashit( $this->url ) . beans_get( 'filename', $this->compiler );

		if ( is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}

		return $url;

	}

	/**
	 * Get file extension.
	 */
	public function get_extension() {

		if ( 'style' == $this->compiler['type'] ) {
			return 'css';
		} elseif ( 'script' == $this->compiler['type'] ) {
			return 'js';
		}

	}

	/**
	 * Combine fragments content.
	 */
	public function combine_fragments() {

		$content = '';

		// Loop through fragments.
		foreach ( $this->compiler['fragments'] as $fragment ) {

			// Stop here if the fragment is empty.
			if ( empty( $fragment ) ) {
				continue;
			}

			// Set the current fragment used by other functions.
			$this->current_fragment = $fragment;

			// Treat function.
			if ( $this->is_function( $fragment ) ) {

				$get_content = $this->get_function_content();

			} else { // Treat file.

				$get_content = $this->get_internal_content();

				// Try remote content if the internal content returned false.
				if ( ! $get_content ) {
					$get_content = $this->get_remote_content();
				}
			}

			// Stop here if no content or content is an html page.
			if ( ! $get_content || preg_match( '#^\s*\<#', $get_content ) ) {
				continue;
			}

			// Add the content.
			if ( 'style' == $this->compiler['type'] ) {

				$get_content = $this->replace_css_url( $get_content );
				$get_content = $this->add_content_media_query( $get_content );

			}

			$content .= ( $content ? "\n\n" : '' ) . $get_content;

		}

		return $this->format_content( $content );

	}

	/**
	 * Get internal file content.
	 */
	public function get_internal_content() {

		$fragment = $this->current_fragment;

		if ( ! file_exists( $fragment ) ) {

			// Replace url with path.
			$fragment = beans_url_to_path( $fragment );

			// Stop here if it isn't a valid file.
			if ( ! file_exists( $fragment ) || 0 === @filesize( $fragment ) ) {
				return false;
			}
		}

		// Safe to access filesystem since we made sure it was set.
		return $GLOBALS['wp_filesystem']->get_contents( $fragment );

	}

	/**
	 * Get external file content.
	 */
	public function get_remote_content() {

		$fragment = $this->current_fragment;

		// Replace double slaches by http. Mostly used for font referencing urls.
		if ( true == preg_match( '#^\/\/#', $fragment ) ) {
			$fragment = preg_replace( '#^\/\/#', 'http://', $fragment );
		} elseif ( true == preg_match( '#^\/#', $fragment ) ) { // Add domain if it is local but could not be fetched as a file.
			$fragment = site_url( $fragment );
		}

		$request = wp_remote_get( $fragment );

		// If failed to get content, try with ssl url, otherwise go to next fragment.
		if ( ! is_wp_error( $request ) && ( ! isset( $request['body'] ) || 200 != $request['response']['code'] ) ) {

			$fragment = preg_replace( '#^http#', 'https', $fragment );
			$request = wp_remote_get( $fragment );

			if ( ! is_wp_error( $request ) && ( ! isset( $request['body'] ) || 200 != $request['response']['code'] ) ) {
				return false;
			}
		}

		return wp_remote_retrieve_body( $request );

	}

	/**
	 * Get function content.
	 */
	public function get_function_content() {

		return call_user_func( $this->current_fragment );

	}

	/**
	 * Wrap content in query.
	 */
	public function add_content_media_query( $content ) {

		// Ignore if the fragment is a function.
		if ( $this->is_function( $this->current_fragment ) ) {
			return $content;
		}

		$parse_url = parse_url( $this->current_fragment );

		// Return content if it no media query is set.
		if ( ! ( $query = beans_get( 'query', $parse_url ) ) || false === stripos( $query, 'beans_compiler_media_query' ) ) {
			return $content;
		}

		// Wrap the content in the query.
		$new_content = '@media ' . beans_get( 'beans_compiler_media_query', wp_parse_args( $query ) ) . ' {' . "\n";

			$new_content .= $content . "\n";

		$new_content .= '}' . "\n";

		return $new_content;

	}

	/**
	 * Formal CSS, LESS and JS content.
	 */
	public function format_content( $content ) {

		if ( 'style' == $this->compiler['type'] ) {

			if ( 'less' == $this->compiler['format'] ) {

				if ( ! class_exists( 'Beans_Lessc' ) ) {
					require_once( BEANS_API_PATH . 'compiler/vendors/lessc.php' );
				}

				$less = new Beans_Lessc();

				$content = $less->compile( $content );

			}

			if ( ! _beans_is_compiler_dev_mode() ) {
				$content = $this->strip_whitespace( $content );
			}
		}

		if ( 'script' == $this->compiler['type'] && ! _beans_is_compiler_dev_mode() && $this->compiler['minify_js'] ) {

			if ( ! class_exists( 'JSMin' ) ) {
				require_once( BEANS_API_PATH . 'compiler/vendors/js-minifier.php' );
			}

			$js_min = new JSMin( $content );

			$content = $js_min->min();

		}

		return $content;

	}

	/**
	 * Replace CSS url shortcuts with a valid url.
	 */
	public function replace_css_url( $content ) {

		// Replace css path to urls.
		return preg_replace_callback( '#url\s*\(\s*[\'"]*?([^\'"\)]+)[\'"]*\s*\)#i', array( $this, 'css_path_to_url' ) , $content );

	}

	/**
	 * replace_css_url() callback.
	 */
	public function css_path_to_url( $matches, $base_is_path = false ) {

		$base = $this->current_fragment;

		// Stop here if it isn't a internal file or not a valid format.
		if ( true == preg_match( '#^(http|https|\/\/|data)#', $matches[1] ) ) {
			return $matches[0];
		}

		$explode_path = explode( '../', $matches[1] );

		// Replace the base part according to the path "../".
		foreach ( $explode_path as $value ) {
			$base = dirname( $base );
		}

		// Rebuild path.
		$replace = preg_replace( '#^\/#', '', $explode_path );
		$rebuilt_path = end( $replace );

		// Make sure it is a valid base.
		if ( '.' === $base ) {
			$base = '';
		}

		// Rebuild url and make sure it is a valid one using the beans_path_to_url function.
		$url = beans_path_to_url( trailingslashit( $base ) . $rebuilt_path );

		// Return the rebuilt path converted to url.
		return 'url("' . $url . '")';

	}

	/**
	 * Minify CSS.
	 */
	public function strip_whitespace( $content ) {

		$replace = array(
			'#/\*.*?\*/#s' => '', // Strip comments.
			'#\s\s+#'      => ' ', // Strip excess whitespace.
		);

		$search = array_keys( $replace );
		$content = preg_replace( $search, $replace, $content );

		$replace = array(
			': '  => ':',
			'; '  => ';',
			' {'  => '{',
			' }'  => '}',
			', '  => ',',
			'{ '  => '{',
			';}'  => '}', // Strip optional semicolons.
			',\n' => ',', // Don't wrap multiple selectors.
			'\n}' => '}', // Don't wrap closing braces.
			'} '  => "}\n", // Put each rule on it's own line.
			'\n'  => '', // Take out all line breaks
		);

		$search = array_keys( $replace );

		return trim( str_replace( $search, $replace, $content ) );

	}

	/**
	 * Is the fragement a function.
	 */
	public function is_function( $fragment ) {

		if ( is_array( $fragment ) || is_callable( $fragment ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Kill it :(
	 */
	public function kill() {

		// Send report if set.
		if ( beans_get( 'beans_send_compiler_report' ) ) {
			$this->report();
		}

		$html = beans_output( 'beans_compiler_error_title_text', sprintf(
			'<h2>%s</h2>',
			__( 'Not cool, Beans cannot work its magic :(', 'tm-beans' )
		) );

		$html .= beans_output( 'beans_compiler_error_message_text', sprintf(
			'<p>%s</p>',
			__( 'Your current install or file permission prevents Beans from working its magic. Please get in touch with Beans support, we will gladly get you started within 24 - 48 hours (working days).', 'tm-beans' )
		) );

		$html .= beans_output( 'beans_compiler_error_contact_text', sprintf(
			'<a class="button" href="http://www.getbeans.io/contact/?compiler_report=1" target="_blanc">%s</a>',
			__( 'Contact Beans Support', 'tm-beans' )
		) );

		$html .= beans_output( 'beans_compiler_error_report_text', sprintf(
			'<p style="margin-top: 12px; font-size: 12px;"><a href="' . add_query_arg( 'beans_send_compiler_report', true ) . '">%1$s</a>. %2$s</p>',
			__( 'Send us an automatic report', 'tm-beans' ),
			__( 'We respect your time and understand you might not be able to contact us.', 'tm-beans' )
		) );

		wp_die( $html );

	}

	/**
	 * Send report.
	 */
	public function report() {

		// Send report.
		$send = wp_mail(
			'hello@getbeans.io',
			'Compiler error',
			'Compiler error reported by ' . home_url(),
			array(
				'MIME-Version: 1.0' . "\r\n",
				'Content-type: text/html; charset=utf-8' . "\r\n",
				"X-Mailer: PHP \r\n",
				'From: ' . wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . ' < ' . get_option( 'admin_email' ) . '>' . "\r\n",
				'Reply-To: ' . get_option( 'admin_email' ) . "\r\n",
			)
		);

		// Die and display message.
		wp_die( beans_output( 'beans_compiler_report_error_text', sprintf(
			'<p>%s<p>',
			__( 'Thanks for your contribution by reporting this issue. We hope to hear from you again.', 'tm-beans' )
		) ) );

	}
}
