<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Update query data structure.
 *
 * @package GlueDB
 * @author R�gis Lemaigre
 * @license MIT
 */

class GlueDB_Fragment_Query_Update extends GlueDB_Fragment_Query {
	/**
	 * @var GlueDB_Fragment_Builder_Setlist Set list.
	 */
	protected $set;

	/**
	 * @var GlueDB_Fragment_Builder_Join_From From clause.
	 */
	protected $from;

	/**
	 * @var GlueDB_Fragment_Builder_Bool_Where Where clause.
	 */
	protected $where;

	/**
	 * @var GlueDB_Fragment_Builder_Orderby Order by list.
	 */
	protected $orderby;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Init children fragments :
		$this->set		= new GlueDB_Fragment_Builder_Setlist();
		$this->from		= new GlueDB_Fragment_Builder_Join_From();
		$this->where	= new GlueDB_Fragment_Builder_Bool_Where();
		$this->orderby	= new GlueDB_Fragment_Builder_List_Orderby();

		// Set up dependecies :
		$this->set->register_user($this);
		$this->from->register_user($this);
		$this->where->register_user($this);
		$this->orderby->register_user($this);
	}

	/**
	 * Returns the set list, initializing it with given parameters if any.
	 *
	 * I.e. "$query->set(...)" is the same as "$query->set()->and(...)".
	 *
	 * @param GlueDB_Fragment_Column $column
	 * @param mixed $to
	 *
	 * @return GlueDB_Fragment_Builder_List_Set
	 */
	public function set($column = null, $to = null) {
		if (func_num_args() > 0)
			return $this->set->and($column, $to);
		return $this->set;
	}

	/**
	 * Returns the from clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->from(...)" is the same as "$query->from()->init(...)".
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Builder_Join
	 */
	public function from($operand = null, &$alias = null) {
		if (func_num_args() > 0)
			return $this->from->init($operand, $alias);
		return $this->from;
	}

	/**
	 * Returns the where clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->where(...)" is the same as "$query->where()->init(...)".
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

	/**
	 * Returns the order by clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->orderby(...)" is the same as "$query->orderby()->and(...)".
	 *
	 * @return GlueDB_Fragment_Builder_List_Orderby
	 */
	public function orderby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->orderby, 'and'), $args);
		}
		else
			return $this->orderby;
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