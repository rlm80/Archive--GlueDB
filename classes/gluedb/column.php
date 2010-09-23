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

class GlueDB_Column {
	/**
	 * @var GlueDB_Table Virtual table this column belongs to.
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
	 * @var string Underlying database column.
	 */
	protected $dbcolumn;

	/**
	 * @var string Native database type.
	 */
	protected $dbtype;

	/**
	 * @var string Whether or not the underlying database column is nullable.
	 */
	protected $dbnullable;

	/**
	 * @var string Maximum length of the underlying database column (for text).
	 */
	protected $dbmaxlength;

	/**
	 * @var string Precision of the underlying database column (total number of significant digits).
	 */
	protected $dbprecision;

	/**
	 * @var string Scale of the underlying database column (number of significant digits in the decimal part).
	 */
	protected $dbscale;

	/**
	 * @var string Default value of the underlying database column (stored as is from the database, not type casted).
	 */
	protected $dbdefault;

	/**
	 * @var boolean Whether or not the underlying database column auto-incrementing.
	 */
	protected $dbauto;

	/**
	 * Constructor.
	 */
	public function __construct(GlueDB_Table $table, $dbcolumn, $dbtype, $dbnullable, $dbmaxlength, $dbprecision, $dbscale, $dbdefault, $dbauto) {
		// Init properties :
		$this->table		= $table;
		$this->dbcolumn		= $dbcolumn;
		$this->dbtype		= $dbtype;
		$this->dbnullable	= $dbnullable;
		$this->dbmaxlength	= $dbmaxlength;
		$this->dbprecision	= $dbprecision;
		$this->dbscale		= $dbscale;
		$this->dbdefault	= $dbdefault;
		$this->dbauto		= $dbauto;
		
		// Get from table object (because there the method can be redefined) :
		$this->formatter	= $table->get_column_formatter($this);
		$this->name			= $table->get_column_alias($this);
	}

	/**
	 * Returns the virtual table of this column.
	 *
	 * @return GlueDB_Table
	 */
	public function table() {
		return $this->table;
	}

	/**
	 * Returns formatter.
	 *
	 * @return GlueDB_Formatter
	 */
	public function formatter() {
		return $this->formatter;
	}

	/**
	 * Returns the underlying database column for this virtual column.
	 *
	 * @return string
	 */
	public function dbcolumn() {
		return $this->dbcolumn;
	}

	/**
	 * Returns the underlying column database type.
	 *
	 * @return string
	 */
	public function dbtype() {
		return $this->dbtype;
	}

	/**
	 * Returns whether or not the underlying column can accept null values.
	 *
	 * @return boolean
	 */
	public function dbnullable() {
		return $this->dbnullable;
	}

	/**
	 * Returns the maximum length that underlying column accepts.
	 *
	 * @return integer
	 */
	public function dbmaxlength() {
		return $this->dbmaxlength;
	}

	/**
	 * Returns the total number of significant digits of the underlying column.
	 *
	 * @return integer
	 */
	public function dbprecision() {
		return $this->dbprecision;
	}

	/**
	 * Returns the number of significant digits in the decimal part af the underlying column.
	 *
	 * @return integer
	 */
	public function dbscale() {
		return $this->dbscale;
	}

	/**
	 * Returns the default value of the underlying column (raw from the database, not type casted !).
	 *
	 * @return string
	 */
	public function dbdefault() {
		return $this->dbdefault;
	}

	/**
	 * Whether or not the underlying column is auto-incrementing.
	 *
	 * @return boolean
	 */
	public function dbauto() {
		return $this->dbauto;
	}
}













