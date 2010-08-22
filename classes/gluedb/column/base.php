<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base column class.
 * 
 * TODO : describe this
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Column_Base {
	/**
	 * @var GlueDB_Table_Base Virtual table object this column belongs to.
	 */
	protected $table;

	/**
	 * @var string Name of this column, as you would refer to it in queries.
	 */
	protected $name;

	/**
	 * Constructor.
	 * 
	 * @param GlueDB_Table_Base $table
	 * @param string $name
	 */
	public function __construct(GlueDB_Table_Base $table, $name) {
		$this->table	= $table;
		$this->name		= $name;
	}
	
	/**
	 * Returns the virtual table object this column belongs to.  
	 * 
	 * @return array
	 */
	public function table() {
		return $this->table;
	}
	
	/**
	 * Returns the database that owns the virtual table object this column belongs to.
	 * 
	 * @return array
	 */
	public function db() {
		return $this->table->db();
	}	
	
	/**
	 * Returns the PHP type of the values returned by this column, after they have been formatted.  
	 * 
	 * @return array
	 */
	abstract public function type();

	/**
	 * Returns the underlying database table and column this virtual column is stored into.  
	 * 
	 * @return array
	 */
	abstract public function dblocation();
	
	/**
	 * Returns the underlying column database type.
	 * 
	 * @return string
	 */
	abstract public function dbtype();
	
	/**
	 * Returns whether or not the underlying column can accept null values. 
	 * 
	 * @return boolean
	 */
	abstract public function dbnullable();
	
	/**
	 * Returns the maximum length that underlying column accepts. 
	 * 
	 * @return integer
	 */
	abstract public function dbmaxlength();
	
	/**
	 * Returns the total number of significant digits of the underlying column. 
	 * 
	 * @return integer
	 */
	abstract public function dbprecision();
	
	/**
	 * Returns the number of significant digits in the decimal part af the underlying column. 
	 * 
	 * @return integer
	 */
	abstract public function dbscale();
	
	/**
	 * Returns the default value of the underlying column (raw from the database, not type casted !). 
	 * 
	 * @return string
	 */
	abstract public function dbdefault();
	
	/**
	 * Whether or not the underlying column is auto-incrementing. 
	 * 
	 * @return string
	 */
	abstract public function dbauto();
}













