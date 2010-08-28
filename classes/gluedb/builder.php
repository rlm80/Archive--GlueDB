<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base builder class.
 * 
 * A builder is an object that provides a fluent interface to build an expression
 * represented by the internal state of the builder.
 * 
 * The elements that make up the expression can be atomic elements or expressions
 * themselves. Functions that allow the user to add atomic elements return the
 * current builder, but functions that mark the start of a new expression will
 * return a new builder with the current builder as its parent. 
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Builder {
	/**
	 * @var GlueDB_Builder Parent builder that gave birth to this builder.
	 */
	protected $parent;
	
	/**
	 * Constructor.
	 * 
	 * @param GlueDB_Builder $parent
	 */
	public function __construct(GlueDB_Builder $parent = null) {
		$this->parent = $parent;
	}
	
	/**
	 * Marks the end of the current expression and returns the parent builder so we
	 * can keep building on the parent expression using fluent methods.
	 * 
	 * @return GlueDB_Builder
	 */
	public function end() {
		return $this->parent;
	}
	
	/**
	 * A call to an unkown function ends the current expression and the call
	 * is forwarded to the parent builder. 
	 * 
	 * @param string $name
	 * @param array $arguments
	 * 
	 * @return mixed|NULL
	 */
	public function __call($name, $arguments) {
		// Is there a parent ?
		if (isset($this->parent))
			// Forwards call to parent builder if any :
			return call_user_func_array(array($this->end(), $name), $arguments);
		else {
			// No parent => trigger error :
			$trace = debug_backtrace();
	        trigger_error(
				'Undefined method via __call(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
        	return null;
		}
	}
}