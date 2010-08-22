<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Column class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Column extends GlueDB_Column_Base {
	/**
	 * @var GlueDB_Formatter Formatter object to format values coming from and going to this column.
	 */
	protected $formatter;	

	/**
	 * @var string Underlying database table this virtual column is stored into.
	 */
	protected $dbtable;	
	
	/**
	 * @var string Underlying database column this virtual column is stored into.
	 */
	protected $dbcolumn;

	/**
	 * @var string Native database type of this column.
	 */
	protected $dbtype;

	/**
	 * @var string	Whether or not the underlying database column is nullable.
	 */
	protected $dbnullable;

	/**
	 * @var string	Maximum length of the underlying database column (for text).
	 */
	protected $dbmaxlength;

	/**
	 * @var string	Precision of the underlying database column (total number of significant digits).
	 */
	protected $dbprecision;

	/**
	 * @var string	Scale of the underlying database column (number of significant digits in the decimal part).
	 */
	protected $dbscale;

	/**
	 * @var string	Default value of the underlying database column (stored as is from the database, not type casted).
	 */
	protected $dbdefault;

	/**
	 * @var string	Whether or not the underlying database column auto-incrementing.
	 */
	protected $dbauto;

	public function __construct(GlueDB_Table $table, $name, $formatter, $dbtable, $dbcolumn, $dbtype, $dbnullable, $dbmaxlength, $dbprecision, $dbscale, $dbdefault, $dbauto) {
		// Call parent constructor :
		parent::__construct($table, $name);
		
		// Init properties :
		$this->formatter	= $formatter;
		$this->dbtable		= $dbtable;
		$this->dbcolumn		= $dbcolumn;
		$this->dbtype		= $dbtype;
		$this->dbnullable	= $dbnullable;
		$this->dbmaxlength	= $dbmaxlength;
		$this->dbprecision	= $dbprecision;
		$this->dbscale		= $dbscale;
		$this->dbdefault	= $dbdefault;
		$this->dbauto		= $dbauto;
	}
	
	/**
	 * Returns the PHP type of the values returned by this column, after they have been formatted.  
	 * 
	 * @return array
	 */
	public function type() {
		return $this->formatter->type();
	}	
	
	/**
	 * Returns the underlying database table and column this virtual column is stored into.  
	 * 
	 * @return array
	 */
	public function dblocation() {
		return array(0 => array('table' => $this->dbtable, 'column' => $this->dbcolumn));
	}
	
	/**
	 * Returns the underlying column database type.
	 * 
	 * @return string
	 */
	public function dbtype() {
		
	}
	
	/**
	 * Returns whether or not the underlying column can accept null values. 
	 * 
	 * @return boolean
	 */
	public function dbnullable() {
		
	}
	
	/**
	 * Returns the maximum length that underlying column accepts. 
	 * 
	 * @return integer
	 */
	public function dbmaxlength() {
		
	}
	
	/**
	 * Returns the total number of significant digits of the underlying column. 
	 * 
	 * @return integer
	 */
	public function dbprecision() {
		
	}
	
	/**
	 * Returns the number of significant digits in the decimal part af the underlying column. 
	 * 
	 * @return integer
	 */
	public function dbscale() {
		
	}
	
	/**
	 * Returns the default value of the underlying column (raw from the database, not type casted !). 
	 * 
	 * @return string
	 */
	public function dbdefault() {
		
	}
	
	/**
	 * Whether or not the underlying column is auto-incrementing. 
	 * 
	 * @return string
	 */
	public function dbauto() {
		
	}	
}













