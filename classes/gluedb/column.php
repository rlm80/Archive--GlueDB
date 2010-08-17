<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Column class.
 *
 * Non computed columns map to a real column in a real database table and you can insert data into them.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Column extends GlueDB_Column_Base {
	/**
	 * @var string	Real database table in which this virtual column is stored.
	 */
	protected $dbtable;

	/**
	 * @var string	Real database column in which this virtual column is stored.
	 */
	protected $dbcolumn;

	/**
	 * @var string	Native database type of this column.
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
	 * @var GlueDB_Formatter Formatter object to format values coming from and going to this column.
	 */
	protected $formatter;

	public function __construct(GlueDB_Table_Base $table, $name, $dbtable, $dbcolumn, $dbtype, $dbnullable, $dbmaxlength, $dbprecision, $dbscale, $dbdefault, $dbauto) {
		// Compute PHP type :
		$type = $table->db()->get_phptype($dbtype);

		// Parent constructor :
		parent::__construct($table, $name, $type);

		// Init properties :
		$this->dbtable		= $dbtable;
		$this->dbcolumn		= $dbcolumn;
		$this->dbtype		= $dbtype;
		$this->dbnullable	= $dbnullable;
		$this->dbmaxlength	= $dbmaxlength;
		$this->dbprecision	= $dbprecision;
		$this->dbscale		= $dbscale;
		$this->dbauto		= $dbauto;

		// Init formatter :
		switch ($this->type) {
			case 'integer'; case 'int';
				$this->formatter = new GlueDB_Formatter_Integer;
				break;
			case 'float';
				$this->formatter = new GlueDB_Formatter_Float;
				break;
			case 'boolean';
				$this->formatter = new GlueDB_Formatter_Boolean;
				break;
			default;
				$this->formatter = new GlueDB_Formatter_String;
		}
	}
}













