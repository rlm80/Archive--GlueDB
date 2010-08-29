<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base builder class.
 * 
 * A builder is an object that provides a fluent interface to build an expression.
 * 
 * An expression is a sequence of components. Components can be atomic or they can be
 * nested expressions. Functions that allow the user to add atomic components return the
 * current builder. Functions that mark the start of a nested expression return a
 * child builder that may be of a different class from its parent, with its own set
 * of methods and rules. When the child builder has finished its work, it returns the
 * parent builder.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Builder {
	/**
	 * @var array Components of current expression.
	 */
	protected $parts;
		
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
		$this->parts = array();
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
	 * Removes all expression components added so far.
	 * 
	 * @return GlueDB_Builder
	 */
	public function reset() {
		$this->parts = array();
		return $this;
	}
	
	/**
	 * Whether or not current expression is empty.
	 * 
	 * @return boolean
	 */
	public function isempty() {
		return count($this->parts) === 0;
	}	
	
	/**
	 * Returns expression components.
	 * 
	 * @return array
	 */
	public function parts() {
		return $this->parts;
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