<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a select query.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB_Fragment_Query_Select extends GlueDB_Fragment_Query {
	/**
	 * @var GlueDB_Fragment_Builder_Select Select list.
	 */
	protected $select;

	/**
	 * @var GlueDB_Fragment_Builder_Join_From From clause.
	 */
	protected $from;

	/**
	 * @var GlueDB_Fragment_Builder_Bool_Where Where clause.
	 */
	protected $where;

	/**
	 * @var GlueDB_Fragment_Builder_Groupby Group by list.
	 */
	protected $groupby;

	/**
	 * @var GlueDB_Fragment_Builder_Bool_Having Having clause.
	 */
	protected $having;

	/**
	 * @var GlueDB_Fragment_Builder_Orderby Order by list.
	 */
	protected $orderby;

	/**
	 * @var Integer Limit.
	 */
	protected $limit;

	/**
	 * @var Integer Offset.
	 */
	protected $offset;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Init children fragments :
		$this->select	= new GlueDB_Fragment_Builder_Select();
		$this->from		= new GlueDB_Fragment_Builder_Join_From($this);
		$this->where	= new GlueDB_Fragment_Builder_Bool_Where($this);
		$this->groupby	= new GlueDB_Fragment_Builder_Groupby();
		$this->having	= new GlueDB_Fragment_Builder_Bool_Having($this);
		$this->orderby	= new GlueDB_Fragment_Builder_Orderby();

		// Set up dependencies :
		$this->select->register_user($this);
		$this->groupby->register_user($this);
		$this->orderby->register_user($this);
	}

	/**
	 * Returns the select list, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_List_Select
	 */
	public function select() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->select, 'and'), $args);
		}
		return $this->select;
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
			return $this->from->init($operand, $alias);
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

	/**
	 * Returns the group by clause, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_List_Groupby
	 */
	public function groupby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->groupby, 'and'), $args);
		}
		else
			return $this->groupby;
	}

	/**
	 * Returns the group by clause, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_Bool_Having
	 */
	public function having() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this->having, 'init'), $args);
		}
		else
			return $this->having;
	}

	/**
	 * Returns the order by clause, initializing it with given parameters if any.
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

	/**
	 * Limit getter/setter.
	 *
	 * @param integer $limit
	 *
	 * @return integer
	 */
	public function limit($limit = null) {
		if (func_num_args() === 0)
			return $this->limit;
		else {
			$this->limit = $limit;
			$this->invalidate();
			return $this;
		}
	}

	/**
	 * Offset getter/setter.
	 *
	 * @param integer $offset
	 *
	 * @return integer
	 */
	public function offset($offset = null) {
		if (func_num_args() === 0)
			return $this->offset;
		else {
			$this->offset = $offset;
			$this->invalidate();
			return $this;
		}
	}

	protected function find_db() {
		// TODO
	}

	/*
	 * Executes current query and returns a result set.
	 *
	 * @see PDO::query()
	 */
	public function execute($arg1 = null, $arg2 = null, $arg3 = null) {
		return $this->db->query($this->compile(), $arg1, $arg2, $arg3);
	}
}