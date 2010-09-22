<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Column class.
 *
 * TODO : describe this
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Column {
	/**
	 * @var GlueDB_Table_Base Virtual table object this column belongs to.
	 */
	protected $table;

	/**
	 * @var string Name of this column, as you would refer to it in queries.
	 */
	protected $name;

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

	/**
	 * Constructor.
	 */
	public function __construct(GlueDB_Table $table, $name, $dbtable, $dbcolumn, $dbtype, $dbnullable, $dbmaxlength, $dbprecision, $dbscale, $dbdefault, $dbauto) {
		$this->table		= $table;
		$this->name			= $name;
		$this->dbtable		= $dbtable;
		$this->dbcolumn		= $dbcolumn;
		$this->dbtype		= $dbtype;
		$this->dbnullable	= $dbnullable;
		$this->dbmaxlength	= $dbmaxlength;
		$this->dbprecision	= $dbprecision;
		$this->dbscale		= $dbscale;
		$this->dbdefault	= $dbdefault;
		$this->dbauto		= $dbauto;
		$this->formatter	= $table->get_column_formatter($this);
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













