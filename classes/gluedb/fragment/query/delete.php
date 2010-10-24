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
	 */
	public function __construct() {
		$this->where = new GlueDB_Fragment_Builder_Bool_Where($this);
	}

	/**
	 * Returns the from clause, initializing it with given parameters if any.
	 *
	 * @param mixed $table_name Table name.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Query_Delete
	 */
	public function from($table_name = null, &$alias = null) {
		if (func_num_args() > 0) {
			$this->from = new GlueDB_Fragment_Aliased_Table($table_name);
			$this->from->register_user($this);
			$alias = $this->from;
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
			call_user_func_array(array($this->where, 'init'), $args);
		}
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