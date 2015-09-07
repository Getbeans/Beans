<?php
/**
 * Images editor.
 *
 * @ignore
 *
 * @package API\Image
 */
class _Beans_Image_Editor {

	/**
	 * Source.
	 *
	 * @type string
	 */
	private $src;

	/**
	 * Arguments.
	 *
	 * @type array
	 */
	private $args = array();


	/**
	 * Returned format.
	 *
	 * @type bool
	 */
	private $output = false;


	/**
	 * Path.
	 *
	 * @type string
	 */
	private $path;


	/**
	 * Rebuilt path.
	 *
	 * @type string
	 */
	private $rebuilt_path;


	/**
	 * Constructor.
	 */
	public function __construct( $src, array $args, $output = 'STRING' ) {

		$this->src = $src;
		$this->args = $args;
		$this->output = $output;

	}


	/**
	 * Initialize the editing.
	 */
	public function init() {

		$this->setup();

		// Try to create image if it doesn't exist.
		if ( !file_exists( $this->rebuilt_path ) )
			// Return orginial image source if it can't be edited.
			if ( !$this->edit() )
				return $this->src;

		$src = beans_path_to_url( $this->rebuilt_path );

		// Simply return the source if dimensions are not requested
		if ( $this->output == 'STRING' )
			return $src;

		// Get the new image dimensions
		list( $width, $height ) = @getimagesize( $this->rebuilt_path );

		$array = array(
			'src' => $src,
			'width' => $width,
			'height' => $height
		);

		if ( $this->output == 'ARRAY_N' )
			return array_values( $array );
		elseif ( $this->output == 'OBJECT' )
			return (object) $array;

		return $array;

	}


	/**
	 * Setup image data.
	 */
	private function setup() {

		$wp_upload_dir = wp_upload_dir();
		$upload_dir = beans_get_images_dir();
		$path = beans_url_to_path( $this->src );
		$info = pathinfo( $path );
		$query = substr( md5( @serialize( $this->args ) ), 0, 7 );
		$extension = $info['extension'];
		$filename = str_replace( '.' . $extension, '', $info['basename'] );

		$this->path = $path;
		$this->rebuilt_path = "{$upload_dir}{$filename}-{$query}.{$extension}";

	}


	/**
	 * Edit image.
	 */
	private function edit() {

		// Prepare editor.
		$editor = wp_get_image_editor( $this->path );

		// Stop here if there was an error.
		if ( is_wp_error( $editor ) )
			return false;

		// Fire image edit.
		foreach ( $this->args as $function => $arguments ) {

			// Make sure it is callable
			if ( is_callable( array( $editor, $function ) ) )
				call_user_func_array( array( $editor, $function ), (array) $arguments );

		}

		// Save new image.
		$editor->save( $this->rebuilt_path );

		// Stop here if there was an error.
		if ( is_wp_error( $editor ) )
			return false;

		return true;

	}

}