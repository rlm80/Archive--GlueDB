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
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used later on to
	 *									  refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function init($table, &$helper = null) {
		// Create fragment :
		if (is_string($table))
			$fragment = $helper = gluedb::table($table)->helper();
		else 
			$fragment = $table;

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
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function inner($table, &$helper = null) {
		$this->join($table, 'INNER JOIN', $helper);
		return $this;
	}

	/**
	 * Adds an left outer join to current expression.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function left($table, &$helper = null) {
		$this->join($table, 'LEFT OUTER JOIN', $helper);
		return $this;
	}

	/**
	 * Adds an right outer join to current expression.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function right($table, &$helper = null) {
		$this->join($table, 'RIGHT OUTER JOIN', $helper);
		return $this;
	}

	/**
	 * Adds a join to current expression with given connector.
	 *
	 * @param mixed $table Table name or join fragment.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 * @param string $connector
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	protected function join($table, $connector, &$helper = null) {
		// Update boolean target :
		$this->boolean_target = new GlueDB_Fragment_Composite_Boolean($this);
		$this->boolean_target->init("1=1");
		
		// Create fragment :
		if (is_string($table)) {
			$fragment = $helper = gluedb::table($table)->helper();
			$template = ' ' . $connector . ' ? ON ( ? ) ';
		}
		else {
			$fragment = $table;
			$template = ' ' . $connector . ' ( ? ) ON ( ? ) ';
		}
		$tpl = gluedb::template($template, $fragment, $this->boolean_target);

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