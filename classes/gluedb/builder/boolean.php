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
	 * Adds parts to the expression, surrounding them with parenthesis, and connecting
	 * them to the expression with given connector. Connector is ignored if expression
	 * is empty.
	 *
	 * @param array $parts
	 * @param string $connector
	 */
	protected function add($parts, $connector = null) {
		// Add connector :
		if (isset($connector) && ! $this->isempty())
			$this->parts[] = ' ' . $connector . ' ';

		// Add parts :
		$this->parts[] = '(';
		$this->parts = array_merge($this->parts, $parts);
		$this->parts[] = ')';
	}

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
	 * Inserts a nested expression into the current expression. The parameter is initialized
	 * with a builder that can be used at a later time to define the content of the nested
	 * expression.
	 *
	 * @return GlueDB_Builder_Boolean
	 */
	public function orexpr(GlueDB_Builder_Boolean &$builder) {
		// Init builder :
		$builder = new GlueDB_Builder_Boolean($this->query);

		// Add builder :
		$this->add(array($builder), 'OR');

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
	public function andexpr(GlueDB_Builder_Boolean &$builder) {
		// Init builder :
		$builder = new GlueDB_Builder_Boolean($this->query);

		// Add builder :
		$this->add(array($builder), 'AND');

		return $this;
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