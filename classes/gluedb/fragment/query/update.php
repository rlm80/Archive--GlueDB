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
	 * @var GlueDB_Fragment_Builder_List_Set Set list.
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
	 * @var GlueDB_Fragment_Builder_List_Orderby Order by list.
	 */
	protected $orderby;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set		= new GlueDB_Fragment_Builder_Setlist($this);
		$this->from		= new GlueDB_Fragment_Builder_Join_From($this);
		$this->where	= new GlueDB_Fragment_Builder_Bool_Where($this);
		$this->orderby	= new GlueDB_Fragment_Builder_List_Orderby($this);
	}

	/**
	 * Returns the set list, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_List_Set
	 */
	public function set() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->set, 'init'), $args);
		}
		return $this->set;
	}

	/**
	 * Returns the from clause, initializing it with given parameters if any.
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Builder_Join
	 */
	public function from($operand = null, &$alias = null) {
		if (func_num_args() > 0)
			$this->from->init($operand, $alias);
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

	/**
	 * Returns the order by clause, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_List_Orderby
	 */
	public function orderby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->orderby, 'init'), $args);
		}
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