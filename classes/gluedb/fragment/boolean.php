<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Boolean extends GlueDB_Fragment {
	/**
	 * @var array Components of current expression.
	 */
	protected $parts = array();	
	
	/**
	 * Initializes the expression with the given arguments surrounded with parenthesis.
	 *
	 * @return GlueDB_Builder_Boolean
	 */
	public function init() {
		// Reset content :
		$this->parts = array();

		// Add arguments :
		$args = func_get_args();
		$this->add($args);

		return $this;
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
	 * Initializes the expression with a nested expression.
	 *
	 * @param GlueDB_Builder_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Builder_Boolean
	 */
	public function initx(&$builder) {
		// Reset content :
		$this->parts = array();

		// Init builder :
		$builder = new GlueDB_Builder_Boolean($this);

		// Add builder :
		$this->add(array($builder));

		return $this;
	}

	/**
	 * Inserts a nested expression into the current expression.
	 *
	 * @param GlueDB_Builder_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Builder_Boolean
	 */
	public function orx(&$builder) {
		// Init builder :
		$builder = new GlueDB_Builder_Boolean($this);

		// Add builder :
		$this->add(array($builder), 'OR');

		return $this;
	}

	/**
	 * Inserts a nested expression into the current expression.
	 *
	 * @param GlueDB_Builder_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Builder_Boolean
	 */
	public function andx(&$builder) {
		// Init builder :
		$builder = new GlueDB_Builder_Boolean($this);

		// Add builder :
		$this->add(array($builder), 'AND');

		return $this;
	}

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

		// Invalidate :
		$this->invalidate();
	}
	
	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string.
	 * 
	 * @param string $dbname
	 * 
	 * @return string
	 */
	protected function compile($dbname) {
		$sql = '';
		foreach ($this->parts as $part) {
			if ($part instanceof GlueDB_Fragment) {
				// Children fragments :
				$sql .= $part->sql($dbname);
			}
			elseif (is_object($part)) {
				// Another object is an error :
				$sql .= ''; // TODO
			}			
			elseif (is_array($part)) {
				// Array elements are quoted and imploded :
				$sql .= ''; // TODO
			}
			else {
				// Basic data types (no, strings are NOT quoted) :
				$sql .= $part;
			}			
		}
		return $sql;
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
	}
}