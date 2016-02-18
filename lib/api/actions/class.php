<?php
/**
 * Anonymous Action.
 *
 * @ignore
 *
 * @package API\Actions
 */
final class _Beans_Anonymous_Actions {

	/**
	 * Callback.
	 *
	 * @type string
	 */
	public $callback;

	/**
	 * Constructor.
	 *
	 * @param string   $id       A unique string used as a reference.
	 * @param string   $hook     The name of the action to which the $callback is hooked.
	 * @param string   $callback Content to add to the anonymous function.
	 * @param int      $priority Optional. Used to specify the order in which the functions
	 *                           associated with a particular action are executed. Default 10.
	 *                           Lower numbers correspond with earlier execution,
	 *                           and functions with the same priority are executed
	 *                           in the order in which they were added to the action.
	 * @param int      $args     Optional. The number of arguments the function accepts. Default 1.
	 *
	 * @return bool              Will always return true.
	 */
	public function __construct( $hook, $callback, $priority, $args ) {

		$this->callback = $callback;

		add_action( $hook, array( $this, 'callback' ), $priority, $args );

	}


	/**
	 * Get action content and set it as the callback.
	 */
	public function callback() {

		echo call_user_func_array( $this->callback[0], $this->callback[1] );

	}

}