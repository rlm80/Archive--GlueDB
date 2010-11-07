<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a get query.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB_Fragment_Query_Select extends GlueDB_Fragment_Query {
	/**
	 * @var GlueDB_Fragment_Builder_Get Select list.
	 */
	protected $get;

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
		$this->get		= new GlueDB_Fragment_Builder_Get();
		$this->from		= new GlueDB_Fragment_Builder_Join_From();
		$this->where	= new GlueDB_Fragment_Builder_Bool_Where();
		$this->groupby	= new GlueDB_Fragment_Builder_Groupby();
		$this->having	= new GlueDB_Fragment_Builder_Bool_Having();
		$this->orderby	= new GlueDB_Fragment_Builder_Orderby();

		// Set up dependecies :
		$this->get->register_user($this);
		$this->from->register_user($this);
		$this->where->register_user($this);
		$this->groupby->register_user($this);
		$this->having->register_user($this);
		$this->orderby->register_user($this);
	}

	/**
	 * Returns the select list, initializing it with given parameters if any.
	 *
	 * I.e. "$query->get(...)" is the same as "$query->get()->and(...)".
	 *
	 * @return GlueDB_Fragment_Builder_Get
	 */
	public function get() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->get->reset();
			return call_user_func_array(array($this->get, 'and'), $args);
		}
		else
			return $this->get;
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
		if (func_num_args() > 0) {
			$this->from->reset();
			return $this->from->init($operand, $alias);
		}
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
			$this->where->reset();
			return call_user_func_array(array($this->where, 'init'), $args);
		}
		else
			return $this->where;
	}

	/**
	 * Returns the group by clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->groupby(...)" is the same as "$query->groupby()->and(...)".
	 *
	 * @return GlueDB_Fragment_Builder_List_Groupby
	 */
	public function groupby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->groupby->reset();
			return call_user_func_array(array($this->groupby, 'and'), $args);
		}
		else
			return $this->groupby;
	}

	/**
	 * Returns the group by clause, initializing it with given parameters if any.
	 *
	 * I.e. "$query->having(...)" is the same as "$query->having()->init(...)".
	 *
	 * @return GlueDB_Fragment_Builder_Bool_Having
	 */
	public function having() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->having->reset();
			return call_user_func_array(array($this->having, 'init'), $args);
		}
		else
			return $this->having;
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
			$this->orderby->reset();
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

	/**
	 * Returns database inferred from tables used in the query.
	 *
	 * @return GlueDB_Database
	 */
	protected function find_db() {
		$op = $this->from();
		while ($op instanceof GlueDB_Fragment_Builder_Join)
			$op = $op->first()->operand();
		return $op->aliased()->table()->db();
	}

	/*
	 * Executes current query and returns a result set.
	 *
	 * @see PDO::query()
	 */
	public function execute($arg1 = null, $arg2 = null, $arg3 = null) {
		return $this->db->query($this->compile(), $arg1, $arg2, $arg3);
	}

	/**
	 * Forwards call to given database.
	 *
	 * @param GlueDB_Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db, $style) {
		// Forwards call to database :
		return $db->compile_query_select($this, $style);
	}
}