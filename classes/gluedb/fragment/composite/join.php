<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a join expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Join extends GlueDB_Fragment_Composite {
	/**
	 * @var GlueDB_Fragment_Composite_Boolean Boolean fragment that is the current target for on(), or() and and() calls.
	 */
	protected $boolean_target;

	/**
	 * Initializes the expression with the given table or join fragment.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Alias $alias Initialized with a table alias that may be used later on to refer to the table columns.
	 * @param string $forced_alias Set this to force your own alias for the table.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function init($table, &$alias = null, $forced_alias = null) {
		// Create fragment :
		if (is_string($table)) {
			$alias = new GlueDB_Alias($table, $forced_alias);	
			$fragment = gluedb::template(' ' . $alias->table_sql() . ' ');
		}
		else
			$fragment = gluedb::template(' ( ? ) ', $table);

		// Remove children :
		$this->reset();

		// Add operand :
		$this->push($fragment);

		return $this;
	}

	/**
	 * Adds an inner join to current expression.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Alias $alias Initialized with a table alias that may be used later on to refer to the table columns.
	 * @param string $forced_alias Set this to force your own alias for the table.
	 * 
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function inner($table, &$alias = null, $forced_alias = null) {
		$this->join($table, 'INNER JOIN', $alias, $forced_alias);
		return $this;
	}

	/**
	 * Adds an left outer join to current expression.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Alias $alias Initialized with a table alias that may be used later on to refer to the table columns.
	 * @param string $forced_alias Set this to force your own alias for the table.
	 * 
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function left($table, &$alias = null, $forced_alias = null) {
		$this->join($table, 'LEFT OUTER JOIN', $alias, $forced_alias);
		return $this;
	}

	/**
	 * Adds an right outer join to current expression.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Alias $alias Initialized with a table alias that may be used later on to refer to the table columns.
	 * @param string $forced_alias Set this to force your own alias for the table.
	 * 
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function right($table, &$alias = null, $forced_alias = null) {
		$this->join($table, 'RIGHT OUTER JOIN', $alias, $forced_alias);
		return $this;
	}

	/**
	 * Adds a join to current expression with given connector.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param string $connector 'OR' or 'AND'.
	 * @param GlueDB_Alias $alias Initialized with a table alias that may be used later on to refer to the table columns.
	 * @param string $forced_alias Set this to force your own alias for the table.
	 * 
	 * @return GlueDB_Fragment_Composite_Join
	 */
	protected function join($table, $connector, &$alias, $forced_alias) {
		// Update boolean target :
		$this->boolean_target = new GlueDB_Fragment_Composite_Boolean();
		
		// Create fragment :
		if (is_string($table)) {
			$alias = new GlueDB_Alias($table, $forced_alias);	
			$template = ' ' . $connector . ' ' . $alias->table_sql() . ' ON ( ? ) ';
			$tpl = gluedb::template($template, $this->boolean_target);
		}
		else {
			$template = ' ' . $connector . ' ( ? ) ON ( ? ) ';
			$tpl = gluedb::template($template, $table, $this->boolean_target);
		}

		// Push fragment :
		$this->push($tpl) ;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function on() {
		$args = func_get_args();
		call_user_func_array(array($this->boolean_target, 'init'), $args);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function _or() {
		$args = func_get_args();
		call_user_func_array(array($this->boolean_target, '_or'), $args);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function _and() {
		$args = func_get_args();
		call_user_func_array(array($this->boolean_target, '_and'), $args);
		return $this;
	}

	/*
	 * Setup aliases for _or() and _and(). Required because keywords aren't valid function names in PHP.
	 */
	public function __call($name, $args) {
		if ($name === 'or')
			return call_user_func_array(array($this, '_or'), $args);
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
	}
}