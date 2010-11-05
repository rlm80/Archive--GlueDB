<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a delete query.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB_Fragment_Query_Delete extends GlueDB_Fragment_Query {
	/**
	 * @var GlueDB_Fragment_Aliased_Table Table to delete rows from.
	 */
	protected $from;

	/**
	 * @var GlueDB_Fragment_Builder_Bool_Where Where clause.
	 */
	protected $where;

	/**
	 * Constructor.
	 *
	 * @param string $table_name Name of the main table you're deleting from.
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 */
	public function __construct($table_name = null, &$alias = null) { // TODO think...why is this constructor different from the one of select query ?
		// Init children fragments :
		$this->where	= new GlueDB_Fragment_Builder_Bool_Where();
		$this->from		= new GlueDB_Fragment_Aliased_Table($table_name);

		// Set up dependecies :
		$this->where->register_user($this);
		$this->from->register_user($this);

		// Initialize alias parameter :
		$alias = $this->from;
	}

	/**
	 * From table getter/setter.
	 *
	 * @param mixed $table_name Table name.
	 *
	 * @return mixed
	 */
	public function from($table_name = null) {
		if (func_num_args() > 0) {
			$this->from->aliased(new GlueDB_Fragment_Table($table_name));
			return $this;
		}
		else
			return $this->from;
	}

	/**
	 * Returns the where clause, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_Bool_Where
	 */
	public function where() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->where, 'init'), $args);
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