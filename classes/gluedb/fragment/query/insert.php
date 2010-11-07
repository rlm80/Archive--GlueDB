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
	 * @var GlueDB_Fragment_Builder_Rowlist Row list.
	 */
	protected $values;
	
	/**
	 * @var GlueDB_Fragment_Builder_Columns Columns list.
	 */
	protected $columns;

	/**
	 * Constructor.
	 *
	 * @param string $table_name Name of the table you're inserting rows into.
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 */
	public function __construct($table_name = null, &$alias = null) { // TODO think...why is this constructor different into the one of select query ?
		// Init children fragments :
		$this->into		= new GlueDB_Fragment_Aliased_Table($table_name);
		$this->values	= new GlueDB_Fragment_Builder_Rowlist();
		$this->columns	= new GlueDB_Fragment_Builder_Columns();

		// Set up dependecies :
		$this->into->register_user($this);
		$this->values->register_user($this);
		$this->columns->register_user($this);

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
	 * You may pass an array of values or an unlimited number of parameters.
	 *
	 * @return GlueDB_Fragment_Builder_Rowlist
	 */
	public function values() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			$this->values->reset();			
			return call_user_func_array(array($this->values, 'and'), $args);
		}
		else
			return $this->values;
	}
	
	/**
	 * Returns the columns fragment, initializing it with given parameters if any.
	 * You may pass an array of columns or an unlimited number of parameters.
	 *
	 * @return GlueDB_Fragment_Builder_Columns
	 */
	public function columns() {
		if (func_num_args() > 0) {
			// Get columns :
			$args = func_get_args();
			if (is_array($args[0]))
				$columns = $args[0];
			else
				$columns = $args;
			
			// Add columns :
			$this->columns->reset();
			foreach($columns as $column)
				$this->columns->and($column);			
		}
		return $this->columns;
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