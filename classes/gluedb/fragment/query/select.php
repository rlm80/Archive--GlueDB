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
	 * @var GlueDB_Fragment_Builder_List_Select Select list.
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
	 * @var GlueDB_Fragment_Builder_List_Groupby Group by list.
	 */
	protected $groupby;
	
	/**
	 * @var GlueDB_Fragment_Builder_Bool_Having Having clause.
	 */
	protected $having;
	
	/**
	 * @var GlueDB_Fragment_Builder_List_Orderby Order by list.
	 */
	protected $orderby;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->select	= new GlueDB_Fragment_Builder_List_Select($this);
		$this->from		= new GlueDB_Fragment_Builder_Join_From($this);
		$this->where	= new GlueDB_Fragment_Builder_Bool_Where($this);
		$this->groupby	= new GlueDB_Fragment_Builder_List_Groupby($this);
		$this->having	= new GlueDB_Fragment_Builder_Bool_Having($this);
		$this->orderby	= new GlueDB_Fragment_Builder_List_Orderby($this);
	}
	
	/**
	 * Returns the select list, initializing it with given parameters if any.
	 * 
	 * @return GlueDB_Fragment_Builder_List_Select
	 */
	public function select() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->select, 'init'), $args);
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
	 * Returns the group by clause, initializing it with given parameters if any.
	 * 
	 * @return GlueDB_Fragment_Builder_List_Groupby
	 */
	public function groupby() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->groupby, 'init'), $args);
		}
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
			call_user_func_array(array($this->having, 'init'), $args);
		}
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
			call_user_func_array(array($this->orderby, 'init'), $args);
		}
		return $this->orderby;
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string.
	 *
	 * @param GlueDB_Database $db
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db) {
		if ( ! $this->select->is_empty())	$selectsql	= $this->select->sql($db);	else $selectsql		= null; // TODO this should go in sql() ?
		if ( ! $this->from->is_empty())		$fromsql	= $this->from->sql($db);	else $fromsql		= null;
		if ( ! $this->where->is_empty())	$wheresql	= $this->where->sql($db);	else $wheresql		= null; 
		if ( ! $this->groupby->is_empty())	$groupbysql	= $this->groupby->sql($db);	else $groupbysql	= null;
		if ( ! $this->having->is_empty())	$havingsql	= $this->having->sql($db);	else $havingsql		= null;
		if ( ! $this->orderby->is_empty())	$orderbysql	= $this->orderby->sql($db);	else $orderbysql	= null;
		return $db->compile_query_select($selectsql, $fromsql, $wheresql, $groupbysql, $havingsql, $orderbysql);
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