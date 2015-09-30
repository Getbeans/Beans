<?php
/**
 * Compiles and minifies CSS, LESS and JS.
 *
 * @ignore
 *
 * @package API\Compiler
 */
class _Beans_Compiler {

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
	var $cache_dir;

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

		$defaults = array(
			'id' => false,
			'type' => false,
			'format' => false,
			'fragments' => array(),
			'depedencies' => false,
			'in_footer' => false,
			'minify_js' => false,
			'version' => false
		);

		$this->compiler = array_merge( $defaults, $args );
		$this->cache_dir = beans_get_compiler_dir( is_admin() ) . $this->compiler['id'];

		if ( !$this->maybe_make_dir() )
			return false;

		$this->set_fragments();
		$this->set_filname();

		if ( !$this->cache_file_exist() )
			$this->cache_file();

		$this->enqueue_file();

	}


	/**
	 * Make directory.
	 */
	public function maybe_make_dir() {

		if ( !is_dir( $this->cache_dir ) )
			wp_mkdir_p( $this->cache_dir );

		if ( !is_writable( $this->cache_dir ) )
			return false;

		return true;

	}


	/**
	 * Set class fragments.
	 */
	public function set_fragments() {

		global $_beans_compiler_added_fragments;

		if ( $added_fragments = beans_get( $this->compiler['id'], $_beans_compiler_added_fragments[$this->compiler['format']] ) )
			$this->compiler['fragments'] = array_merge( $this->compiler['fragments'], $added_fragments );

		$this->compiler['fragments'] = apply_filters( 'beans_compiler_fragments_' . $this->compiler['id'], $this->compiler['fragments'] );

	}


	/**
	 * Set class filname.
	 */
	public function set_filname() {

		$hash = substr( md5( @serialize( $this->compiler ) ), 0, 7 );

		if ( !_beans_is_compiler_dev_mode() )
			return $this->compiler['filename'] = $hash . '.' . $this->get_extension();

		$fragments_filemtime = array();

		// Check for internal file changes.
		foreach ( $this->compiler['fragments'] as $id => $fragment ) {

			// Ignore if the fragment is a function.
			if ( $this->is_function( $fragment ) )
				continue;

			// Only check file time for internal files.
			if ( strpos( $fragment, $_SERVER['HTTP_HOST'] ) !== false || preg_match( '#^\/[^\/]#', $fragment ) == true )
				$fragments_filemtime[$id] = @filemtime( beans_url_to_path( $fragment ) );

		}

		if ( !empty( $fragments_filemtime ) ) {

			// Set filemtime hash.
			$_hash = substr( md5( @serialize( $fragments_filemtime ) ), 0, 7 );

			$items = scandir( $this->cache_dir );
			unset( $items[0], $items[1] );

			// Clean up other modified files.
			foreach ( $items as $item )
				// Remove if it contains initial hash, is the same format and doesn't contain the filemtime hash.
				if ( stripos( $item, $hash ) !== false && stripos( $item, $this->get_extension() ) !== false && stripos( $item, $_hash ) === false )
					@unlink( $this->cache_dir . '/' . $item );

			// Set the new hash which will trigger to new compiling.
			$hash = $hash . '-' . $_hash;

		}

		$this->compiler['filename'] = $hash . '.' . $this->get_extension();

	}


	/**
	 * Check if cached file exists.
	 */
	public function cache_file_exist() {

		if ( ( $filname = beans_get( 'filename', $this->compiler ) ) && file_exists( $this->cache_dir . '/' . $filname ) )
			return true;

		return false;

	}


	/**
	 * Create cached file.
	 */
	public function cache_file() {

		$content = $this->combine_fragments();

		// Create new file.
		$file_handle = @fopen( $this->cache_dir . '/' . $this->compiler['filename'], 'w' );

		if ( !@fwrite( $file_handle, $content ) )
			return false;

		@chmod( $this->cache_dir . '/' . $this->compiler['filename'], 0755 );

		@fclose( $file_handle );

		return true;

	}


	/**
	 * Enqueue cached file.
	 */
	public function enqueue_file() {

		// Enqueue css.
		if ( $this->compiler['type'] == 'style' )
			return wp_enqueue_style( $this->compiler['id'], $this->get_url(), $this->compiler['depedencies'], $this->compiler['version'] );

		// Enqueue js file.
		elseif ( $this->compiler['type'] == 'script' )
			return wp_enqueue_script( $this->compiler['id'], $this->get_url(), $this->compiler['depedencies'], $this->compiler['version'], $this->compiler['in_footer'] );

		return false;

	}


	/**
	 * Get cached file url.
	 */
	public function get_url() {

		$url = beans_path_to_url( trailingslashit( $this->cache_dir ) . beans_get( 'filename', $this->compiler ) );

		if ( is_ssl() )
			$url = str_replace('http://', 'https://', $url );

		return $url;

	}


	/**
	 * Get file extension.
	 */
	public function get_extension() {

		if ( $this->compiler['type'] == 'style' )
			return 'css';

		elseif ( $this->compiler['type'] == 'script' )
			return 'js';

	}


	/**
	 * Combine fragments content.
	 */
	public function combine_fragments() {

		$content = '';

		// Looo through fragments.
		foreach ( $this->compiler['fragments'] as $fragment ) {

			// Set the current fragment used by other functions.
			$this->current_fragment = $fragment;

			// Treat function.
			if ( $this->is_function( $fragment ) ) {

				$get_content = $this->get_function_content();


			}
			// Treat file.
			else {

				$get_content = $this->get_internal_content();

				// Try remote content if the internal content returned false.
				if ( !$get_content )
					$get_content = $this->get_remote_content();

			}

			// Stop here if no content.
			if ( !$get_content )
				continue;

			// Add the content.
			if ( $this->compiler['type'] == 'style' ) {

				// Add content wrapped in the media query if set.
				$content .= $this->add_content_media_query( $get_content );

			} else {

				// Prevent js conflicts.
				$content .= "\n\n" . ";" . $get_content;

			}

		}

		return $this->format_content( $content );

	}


	/**
	 * Get internal file content.
	 */
	public function get_internal_content() {

		// Replace url with path.
		$fragment = beans_url_to_path( $this->current_fragment );

		// Stop here if it isn't a valid file.
		if ( !file_exists( $fragment ) || filesize( $fragment ) === 0 )
			return false;

		// Handle content.
		$temp_handler = fopen( $fragment, 'r' );

			$content = $this->replace_css_url( fread( $temp_handler, filesize( $fragment ) ) );

		fclose( $temp_handler );

		return $content;

	}


	/**
	 * Get external file content.
	 */
	public function get_remote_content() {

		$fragment = $this->current_fragment;

		// Replace double slaches by http. Mostly used for font referencing urls.
		if ( preg_match( '#^\/\/#', $fragment ) == true )
			$fragment = preg_replace( '#^\/\/#', 'http://', $fragment );

		$request = wp_remote_get( $fragment );

		// If failed to get content, try with ssl url, otherwise go to next fragment.
		if ( !is_wp_error( $request ) && ( !isset( $request['body'] ) || $request['response']['code'] != 200 ) ) {

			$fragment = preg_replace( '#^http#', 'https', $fragment );
			$request = wp_remote_get( $fragment );

			if ( !is_wp_error( $request ) && ( !isset( $request['body'] ) || $request['response']['code'] != 200 ) )
				return false;

		}

		return $this->replace_css_url( wp_remote_retrieve_body( $request ) );

	}


	/**
	 * Get function content.
	 */
	public function get_function_content() {

		return $this->replace_css_url( call_user_func( $this->current_fragment ) );

	}


	/**
	 * Wrap content in query.
	 */
	public function add_content_media_query( $content ) {

		// Ignore if the fragment is a function.
		if ( $this->is_function( $this->current_fragment ) )
			return $content;

		$parse_url = parse_url( $this->current_fragment );

		// Return content if it no media query is set.
		if ( !( $query = beans_get( 'query', $parse_url ) ) || stripos( $query, 'beans_compiler_media_query' ) === false )
			return $content;

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

		if ( $this->compiler['type'] == 'style' ) {

			if ( $this->compiler['format'] == 'less' ) {

				if ( !class_exists( 'lessc' ) )
					require_once( BEANS_API_COMPONENTS_PATH . 'compiler/vendors/lessc.php' );

				$less = new lessc();

				$content = $less->compile( $content );

			}

			if ( _beans_is_compiler_dev_mode() === false )
				$content = $this->strip_whitespace( $content );

		}

		if ( $this->compiler['type'] == 'script' && _beans_is_compiler_dev_mode() === false && $this->compiler['minify_js'] ) {

			if ( !class_exists( 'JSMin' ) )
				require_once( BEANS_API_COMPONENTS_PATH . 'compiler/vendors/js-minifier.php' );

			$js_min = new JSMin( $content );

			$content = $js_min->min();

		}

		return $content;

	}


	/**
	 * Replace CSS url shortcuts with a valid url.
	 */
	public function replace_css_url( $content ) {

		if ( $this->compiler['type'] != 'style' )
			return $content;

		// Replace css path to urls.
		return preg_replace_callback( '#url\s*\(\s*[\'"]*?([^\'"\)]+)[\'"]*\s*\)#i', array( $this, 'css_path_to_url' ) , $content );

	}


	/**
	 * replace_css_url() callback.
	 */
	public function css_path_to_url( $matches, $base_is_path = false ) {

		$base = $this->current_fragment;

		// Stop here if it isn't a internal file or not a valid format.
		if ( preg_match( '#^(http|https|\/\/|data)#', $matches[1] ) == true )
			return $matches[0];

		$explode_path = explode( '../', $matches[1] );

		// Replace the base part according to the path "../".
		foreach ( $explode_path as $value )
			$base = dirname( $base );

		// Rebuild path.
		$replace = preg_replace( '#^\/#', '', $explode_path );
		$rebuilt_path = end( $replace );

		// Make sure it is a valid base.
		if ( $base === '.' )
			$base = '';

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
			"#/\*.*?\*/#s" => '',  // Strip comments.
			"#\s\s+#"      => ' ', // Strip excess whitespace.
		);

		$search = array_keys( $replace );
		$content = preg_replace( $search, $replace, $content );

		$replace = array(
			": "  => ":",
			"; "  => ";",
			" {"  => "{",
			" }"  => "}",
			", "  => ",",
			"{ "  => "{",
			";}"  => "}", // Strip optional semicolons.
			",\n" => ",", // Don't wrap multiple selectors.
			"\n}" => "}", // Don't wrap closing braces.
			"} "  => "}\n", // Put each rule on it's own line.
			"\n" => "" // Take out all line breaks
		);

		$search = array_keys( $replace );

		return trim( str_replace( $search, $replace, $content ) );

	}


	/**
	 * Is the fragement a function.
	 */
	public function is_function( $fragment ) {

		if ( is_array( $fragment ) || is_callable( $fragment ) )
			return true;

		return false;

	}

}