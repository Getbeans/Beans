<?php
/**
 * Add anonymous fitler.
 *
 * @ignore
 *
 * @package API\Filters
 */
final class _Beans_Anonymous_Filters {

	/**
	 * Callback.
	 *
	 * @type string
	 */
	public $callback;

	/**
	 * Constructor.
	 *
	 * @param string   $id       The filter ID.
	 * @param string   $callback Content to add to the anonymous function.
	 * @param int      $priority Optional. Used to specify the order in which the functions
	 *                           associated with a particular action are executed. Default 10.
	 *                           Lower numbers correspond with earlier execution,
	 *                           and functions with the same priority are executed
	 *                           in the order in which they were added to the action.
	 * @param int      $args     Optional. The number of arguments the function accepts. Default 1.
	 *
	 * @return bool Will always return true.
	 */
	public function __construct( $id, $callback, $priority, $args ) {

		$this->callback = $callback;

		add_filter( $id, array( $this, 'callback' ), $priority, $args );

	}


	/**
	 * Get filter content and set it as the callback.
	 */
	public function callback() {

		return $this->callback;

	}

}