<?php
/**
 * This class compiles and minifies CSS, LESS and JS.
 *
 * @package Beans\Framework\API\Complier
 *
 * @since   1.5.0
 */

/**
 * Compiles and minifies CSS, LESS and JS.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Complier
 */
final class _Beans_Compiler {

	/**
	 * Compiler's runtime configuration parameters.
	 *
	 * @var array
	 */
	protected $compiler;

	/**
	 * Cache dir.
	 *
	 * @var string
	 */
	protected $dir;

	/**
	 * Cache url.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Set during in fragments loop.
	 *
	 * @var string
	 */
	protected $current_fragment;

	/**
	 * Create a new Compiler.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Moved config initializer & compile tasks out of constructor.
	 *
	 * @param array $config Runtime configuration parameters for the Compiler.
	 */
	public function __construct( array $config ) {
		// Modify the WP Filesystem method.
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

		$this->compiler = array_merge( $defaults, $config );
		$this->dir      = beans_get_compiler_dir( is_admin() ) . $this->compiler['id'];
		$this->url      = beans_get_compiler_url( is_admin() ) . $this->compiler['id'];

		$this->set_fragments();
		$this->set_filname();

		if ( ! $this->cache_file_exist() ) {
			$this->filesystem();
			$this->maybe_make_dir();
			$this->cache_file();
		}

		$this->enqueue_file();

		// Keep it safe and reset WP Filesystem method.
		remove_filter( 'filesystem_method', array( $this, 'filesystem_method' ) );
	}

	/**
	 * Callback to set WP Filesystem method.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function filesystem_method() {
		return 'direct';
	}

	/**
	 * Initialise WP Filsystem.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function filesystem() {

		// Initialize the WordPress Filesystem.
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
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_make_dir() {

		if ( ! @is_dir( $this->dir ) ) {  // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			wp_mkdir_p( $this->dir );
		}

		return is_writable( $this->dir );
	}

	/**
	 * Set class fragments.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_fragments() {
		global $_beans_compiler_added_fragments;

		$added_fragments = beans_get( $this->compiler['id'], $_beans_compiler_added_fragments[ $this->compiler['format'] ] );

		if ( $added_fragments ) {
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
		if ( ! _beans_is_compiler_dev_mode() || ! @is_dir( $this->dir ) ) { // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			$this->compiler['filename'] = $hash . '.' . $this->get_extension();
			return;
		}

		$fragments_filemtime = array();

		// Check for internal file changes.
		foreach ( $this->compiler['fragments'] as $id => $fragment ) {

			// Ignore if the fragment is a function.
			if ( $this->is_function( $fragment ) ) {
				continue;
			}

			// Only check file time for internal files.
			if ( false !== strpos( $fragment, $_SERVER['HTTP_HOST'] ) || 1 === preg_match( '#^\/[^\/]#', $fragment ) ) {
				$fragments_filemtime[ $id ] = @filemtime( beans_url_to_path( $fragment ) ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			}
		}

		if ( ! empty( $fragments_filemtime ) ) {

			// Set filemtime hash.
			$_hash = substr( md5( @serialize( $fragments_filemtime ) ), 0, 7 );  // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.

			$items = @scandir( $this->dir ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
			unset( $items[0], $items[1] );

			// Clean up other modified files.
			foreach ( $items as $item ) {

				// Remove if it contains initial hash, is the same format and doesn't contain the filemtime hash.
				if ( false !== stripos( $item, $hash ) && false !== stripos( $item, $this->get_extension() ) && false === stripos( $item, $_hash ) ) {
					@unlink( $this->dir . '/' . $item ); // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
				}
			}

			// Set the new hash which will trigger to new compiling.
			$hash = $hash . '-' . $_hash;
		}

		$this->compiler['filename'] = $hash . '.' . $this->get_extension();
	}

	/**
	 * Checks if the file exists on the filesystem, meaning it's been cached.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function cache_file_exist() {
		$filename = $this->get_filename();

		if ( empty( $filename ) ) {
			return false;
		}

		return file_exists( $filename );
	}

	/**
	 * Get the absolute path of the cached and compiled file.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_filename() {
		if ( isset( $this->config['filename'] ) ) {
			return $this->dir . '/' . $this->config['filename'];
		}

		return '';
	}

	/**
	 * Create cached file.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function cache_file() {
		$content  = $this->combine_fragments();
		$filename = $this->get_filename();

		if ( empty( $filename ) ) {
			return false;
		}

		// Safe to access filesystem since we made sure it was set.
		return $GLOBALS['wp_filesystem']->put_contents( $filename, $content, FS_CHMOD_FILE );
	}

	/**
	 * Enqueue cached file.
	 *
	 * @since 1.0.0
	 *
	 * @return void|bool
	 */
	public function enqueue_file() {

		// Enqueue css.
		if ( 'style' == $this->compiler['type'] ) {
			return wp_enqueue_style( $this->compiler['id'], $this->get_url(), $this->compiler['depedencies'], $this->compiler['version'] );
		}

		// Enqueue js file.
		if ( 'script' == $this->compiler['type'] ) {
			return wp_enqueue_script( $this->compiler['id'], $this->get_url(), $this->compiler['depedencies'], $this->compiler['version'], $this->compiler['in_footer'] );
		}

		return false;
	}

	/**
	 * Get cached file url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_url() {
		$url = trailingslashit( $this->url ) . beans_get( 'filename', $this->compiler );

		if ( is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		}

		return $url;
	}

	/**
	 * Get the file extension from the configured "type".
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_extension() {

		if ( 'style' == $this->compiler['type'] ) {
			return 'css';
		}

		if ( 'script' == $this->compiler['type'] ) {
			return 'js';
		}
	}

	/**
	 * Combine fragments content.
	 *
	 * @since 1.0.0
	 *
	 * @return string
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
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function get_internal_content() {
		$fragment = $this->current_fragment;

		if ( ! file_exists( $fragment ) ) {

			// Replace url with path.
			$fragment = beans_url_to_path( $fragment );

			// Stop here if it isn't a valid file.
			if ( ! file_exists( $fragment ) || 0 === @filesize( $fragment ) ) { // @codingStandardsIgnoreLine - Generic.PHP.NoSilencedErrors.Discouraged  This is a valid use case.
				return false;
			}
		}

		// Safe to access filesystem since we made sure it was set.
		return $GLOBALS['wp_filesystem']->get_contents( $fragment );
	}

	/**
	 * Get external file content.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function get_remote_content() {
		$fragment = $this->current_fragment;

		// Replace double slashes by http. Mostly used for font referencing urls.
		if ( 1 === preg_match( '#^\/\/#', $fragment ) ) {
			$fragment = preg_replace( '#^\/\/#', 'http://', $fragment );
		} elseif ( 1 === preg_match( '#^\/#', $fragment ) ) { // Add domain if it is local but could not be fetched as a file.
			$fragment = site_url( $fragment );
		}

		$request = wp_remote_get( $fragment );

		if ( is_wp_error( $request ) ) {
			return '';
		}

		// If failed to get content, try with ssl url, otherwise go to next fragment.
		if ( ! isset( $request['body'] ) || 200 !== $request['response']['code'] ) {

			$fragment = preg_replace( '#^http#', 'https', $fragment );
			$request  = wp_remote_get( $fragment );

			if ( is_wp_error( $request ) ) {
				return '';
			}

			if ( ! isset( $request['body'] ) || 200 !== $request['response']['code'] ) {
				return false;
			}
		}

		return wp_remote_retrieve_body( $request );
	}

	/**
	 * Get function content.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool
	 */
	public function get_function_content() {

		if ( ! is_callable( $this->current_fragment ) ) {
			return false;
		}

		return call_user_func( $this->current_fragment );
	}

	/**
	 * Wrap content in query.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	public function add_content_media_query( $content ) {

		// Ignore if the fragment is a function.
		if ( $this->is_function( $this->current_fragment ) ) {
			return $content;
		}

		$parse_url = parse_url( $this->current_fragment );
		$query     = beans_get( 'query', $parse_url );

		// Bail out if there are no query args or no media query.
		if ( empty( $query ) || false === stripos( $query, 'beans_compiler_media_query' ) ) {
			return $content;
		}

		// Wrap the content in the query.
		return sprintf(
			"@media %s {\n%s\n}\n",
			beans_get( 'beans_compiler_media_query', wp_parse_args( $query ) ),
			$content
		);
	}

	/**
	 * Formal CSS, LESS and JS content.
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	public function format_content( $content ) {

		if ( 'style' == $this->compiler['type'] ) {

			if ( 'less' == $this->compiler['format'] ) {

				if ( ! class_exists( 'Beans_Lessc' ) ) {
					require_once( BEANS_API_PATH . 'compiler/vendors/lessc.php' );
				}

				$less    = new Beans_Lessc();
				$content = $less->compile( $content );
			}

			if ( ! _beans_is_compiler_dev_mode() ) {
				return $this->strip_whitespace( $content );
			}

			return $content;
		}

		if ( 'script' == $this->compiler['type'] && ! _beans_is_compiler_dev_mode() && $this->compiler['minify_js'] ) {

			if ( ! class_exists( 'JSMin' ) ) {
				require_once( BEANS_API_PATH . 'compiler/vendors/js-minifier.php' );
			}

			$js_min = new JSMin( $content );
			return $js_min->min();

		}

		return $content;
	}

	/**
	 * Replace CSS url shortcuts with a valid url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Given content to process.
	 *
	 * @return string
	 */
	public function replace_css_url( $content ) {
		return preg_replace_callback(
			'#url\s*\(\s*[\'"]*?([^\'"\)]+)[\'"]*\s*\)#i',
			array( $this, 'replace_css_url_callback' ),
			$content
		);
	}

	/**
	 * Convert any CSS url relative paths to absolute URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param array $matches Matches to process, where 0 is the CSS' url() and 1 is the URI.
	 *
	 * @return string
	 */
	public function replace_css_url_callback( $matches ) {

		// Stop here if it isn't a internal file or not a valid format.
		if ( true == preg_match( '#^(http|https|\/\/|data)#', $matches[1] ) ) {
			return $matches[0];
		}

		$base = $this->current_fragment;

		// Separate the placeholders and path.
		$explode_path = explode( '../', $matches[1] );

		// Replace the base part according to the path "../".
		foreach ( $explode_path as $value ) {
			$base = dirname( $base );
		}

		// Rebuild path.
		$replace      = preg_replace( '#^\/#', '', $explode_path );
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

		$search  = array_keys( $replace );
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
