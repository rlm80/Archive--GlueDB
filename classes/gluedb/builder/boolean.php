<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Boolean expression builder class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Builder_Boolean extends GlueDB_Builder {
	/**
	 * Use ->or() instead of this. Adds arguments to the expression, surrounding them
	 * with parenthesis, and connecting them to the expression with 'OR' if the expression
	 * isn't empty.
	 * 
	 * @return GlueDB_Builder_Boolean
	 */
	public function _or() {
		$args = func_get_args();
		$this->add($args, 'OR');
		return $this;
	}

	/**
	 * Use ->and() instead of this. Adds arguments to the expression, surrounding them
	 * with parenthesis, and connecting them to the expression with 'AND' if the expression
	 * isn't empty.
	 * 
	 * @return GlueDB_Builder_Boolean
	 */
	public function _and() {
		$args = func_get_args();
		$this->add($args, 'AND');
		return $this;		
	}
	
	/**
	 * Starts a new boolean expression and initializes it with given arguments. The new
	 * expression will be surrounded by with parenthesis, and connected to the expression
	 * with 'OR' if the expression isn't empty. Returns the builder object for the
	 * new expression.
	 * 
	 * @return GlueDB_Builder_Boolean
	 */	
	public function or_nested() {
		// Init new builder :
		$args = func_get_args();
		$builder = new GlueDB_Builder_Boolean($this);
		$builder->add($args);
		
		// Add builder :
		$this->add($builder, 'OR');
		
		return $builder;		
	}	

	/**
	 * Starts a new boolean expression and initializes it with given arguments. The new
	 * expression will be surrounded by with parenthesis, and connected to the expression
	 * with 'OR' if the expression isn't empty. Returns the builder object for the
	 * new expression.
	 * 
	 * @return GlueDB_Builder_Boolean
	 */	
	public function and_nested() {
		// Init new builder :
		$args = func_get_args();
		$builder = new GlueDB_Builder_Boolean($this);
		$builder->add($args);
		
		// Add builder :
		$this->add($builder, 'AND');
		
		return $builder;		
	}		
	
	/* 
	 * Redefined to setup aliases for _or() and _and(). Required because
	 * keywords aren't valid function names in PHP.
	 */
	public function __call($name, $args) {
		if ($name === 'or')
			return call_user_func_array(array($this, '_or'), $args);
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
		else
			return parent::__call($name, $args);
	}
}