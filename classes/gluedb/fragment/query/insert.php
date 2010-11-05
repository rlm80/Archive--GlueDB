<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an insert query.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB_Fragment_Query_Insert extends GlueDB_Fragment_Query {
	/**
	 * @var GlueDB_Fragment_Aliased_Table Table to insert rows into.
	 */
	protected $into;

	/**
	 * @var GlueDB_Fragment_Rowlist Row list.
	 */
	protected $values;

	/**
	 * Constructor.
	 *
	 * @param string $table_name Name of the main table you're updating (= first table in the update clause).
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 */
	public function __construct($table_name = null, &$alias = null) { // TODO think...why is this constructor different into the one of select query ?
		// Init children fragments :
		$this->into		= new GlueDB_Fragment_Aliased_Table($table_name);
		$this->values	= new GlueDB_Fragment_Rowlist();

		// Set up dependecies :
		$this->into->register_user($this);
		$this->values->register_user($this);

		// Initialize alias parameter :
		$alias = $this->into;
	}

	/**
	 * Into table getter/setter.
	 *
	 * @param mixed $table_name Table name.
	 *
	 * @return mixed
	 */
	public function into($table_name = null) {
		if (func_num_args() > 0) {
			$this->into->aliased(new GlueDB_Fragment_Table($table_name));
			return $this;
		}
		else
			return $this->into;
	}

	/**
	 * Returns the values fragment, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Rowlist
	 */
	public function values() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->values, 'init'), $args);
		}
		else
			return $this->where;
	}

	protected function find_db() {
		// TODO
	}

	/*
	 * Executes current query and returns the number of affected rows.
	 *
	 * @see PDO::exec()
	 */
	public function execute() {
		return $this->db->exec($this->compile());
	}
}