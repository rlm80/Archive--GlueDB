<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base database class.
 *
 * A database object is a PDO instance connected to a specific database. This
 * class extends PDO and adds to it a unified interface for database introspection
 * and a query compiler to generate RDBMS specific SQL queries.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Database extends PDO {
	/**
	 * @var string Name of the default database.
	 */
	const DEFAULTDB = 'Primary';

	/**
	 * @var array Database instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string Identifier of the current database.
	 */
	protected $name;

	/**
	 * @var string The user name for the DSN string.
	 */
	protected $username;

	/**
	 * @var string The password for the DSN string.
	 */
	protected $password;

	/**
	 * @var string A key=>value array of driver-specific connection options.
	 */
	protected $options = array();

	/**
	 * @var string Connection charset.
	 */
	protected $charset = 'utf8';

	/**
	 * @var boolean Locks constructor access from anywhere but self::create.
	 * 				This ensures correct singleton behaviour even though constructor must
	 * 				remain public because parent constructor is. The other solution was
	 * 				to wrap the PDO instance into this class instead of extending PDO,
	 * 				but this is not good because I wish to expose all PDO features.
	 */
	private static $constuctor_locked = TRUE;

	/**
	 * Constructor.
	 *
	 * @param string $name Identifier of this database.
	 */
	public function __construct($name) {
		// Check lock :
		if (self::$constuctor_locked)
			throw Kohana_Exception('Cannot instanciate databases directly. Call GlueDB::db($name) instead.');

		// Set identifier :
		$this->name = $name;

		// Set PDO options :
		$this->options[PDO::ATTR_ERRMODE]			= PDO::ERRMODE_EXCEPTION;
		$this->options[PDO::ATTR_STATEMENT_CLASS]	= array('GlueDB_Statement', array($this));

		// Call parent constructor to establish connection :
		parent::__construct($this->dsn(), $this->username, $this->password, $this->options);

		// Unset connection parameters for security, to make sure no forgotten debug message
		// displays them unintentionaly to a user :
		$this->username = null;
		$this->password = null;

		// Set connection charset :
		$this->set_charset();
	}

	/**
	 * Returns the DSN pointing to the current database.
	 *
	 * @returns string
	 */
	abstract protected function dsn();

	/**
	 * Getter for database name.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Issues the right query to set current connection charset. This is probably
	 * RDBMS specific so it's factored out of the constructor into a function
	 * that can be redefined if necessary.
	 */
	protected function set_charset() {
		$this->exec('SET NAMES ' . $this->quote($this->charset));
	}

	/**
	 * Compiles given fragment into an SQL string.
	 *
	 * @param GlueDB_Fragment $fragment
	 *
	 * @return string
	 */
	public function compile(GlueDB_Fragment $fragment) {
		if ($fragment instanceof GlueDB_Fragment_Operand_Bool)
			return $this->compile_operand_bool($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Operand_Join)
			return $this->compile_operand_join($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Aliased)
			return $this->compile_aliased($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Builder)
			return $this->compile_builder($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Ordered)
			return $this->compile_ordered($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Column)
			return $this->compile_column($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Table)
			return $this->compile_table($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Template)
			return $this->compile_template($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Value)
			return $this->compile_value($fragment);
		elseif ($fragment instanceof GlueDB_Fragment_Query_Select)
			return $this->compile_query_select($fragment);
	}

	/**
	 * Compiles GlueDB_Fragment_Operand_Bool fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Operand_Bool $fragment
	 *
	 * @return string
	 */
	protected function compile_operand_bool(GlueDB_Fragment_Operand_Bool $fragment) {
		// Get data from fragment :
		$operator	= $fragment->operator();
		$operand	= $fragment->operand();

		// Initialize SQL with operator :
		$sql = '';
		if (isset($operator)) {
			switch ($operator) {
				case GlueDB_Fragment_Operand_Bool::_AND :	$sql = 'AND ';		break;
				case GlueDB_Fragment_Operand_Bool::_OR :	$sql = 'OR ';		break;
				case GlueDB_Fragment_Operand_Bool::ANDNOT :	$sql = 'AND NOT ';	break;
				case GlueDB_Fragment_Operand_Bool::ORNOT :	$sql = 'OR NOT ';	break;
			}
		}

		// Operand :
		$sql .= '(' . $operand->sql($this) . ')';

		return $sql;
	}

	/**
	 * Compiles GlueDB_Fragment_Operand_Join fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Operand_Join $fragment
	 *
	 * @return string
	 */
	protected function compile_operand_join(GlueDB_Fragment_Operand_Join $fragment) {
		// Get data from fragment :
		$operator	= $fragment->operator();
		$operand	= $fragment->operand();
		$on			= $fragment->on();

		// Initialize SQL with operator :
		$sql = '';
		if (isset($operator)) {
			switch ($operator) {
				case GlueDB_Fragment_Operand_Join::INNER_JOIN :			$sql .= 'INNER JOIN ';			break;
				case GlueDB_Fragment_Operand_Join::RIGHT_OUTER_JOIN :	$sql .= 'RIGHT OUTER JOIN ';	break;
				case GlueDB_Fragment_Operand_Join::LEFT_OUTER_JOIN :	$sql .= 'LEFT OUTER JOIN ';		break;
			}
		}

		// Add operand SQL :
		$sqlop = $operand->sql($this);
		if ( ! $operand instanceof GlueDB_Fragment_Aliased_Table)
			$sqlop	= '(' . $sqlop . ')';
		$sql .= $sqlop;

		// Add on SQL :
		if (isset($operator)) {
			$sqlon = $on->sql($this);
			$sql .= ' ON ' . $sqlon;
		}

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles GlueDB_Fragment_Aliased fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Aliased $fragment
	 *
	 * @return string
	 */
	protected function compile_aliased(GlueDB_Fragment_Aliased $fragment) {
		// Get data from fragment :
		$toalias	= $fragment->fragment();
		$alias		= $fragment->alias();

		// Generate fragment SQL :
		$sql = $toalias->sql($this);
		if ( ! ($toalias instanceof GlueDB_Fragment_Column || $toalias instanceof GlueDB_Fragment_Table))
			$sql	= '(' . $sql . ')';

		// Add alias :
		$sql .= ' AS ' . $this->quote_identifier($alias);

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles GlueDB_Fragment_Builder fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Builder $fragment
	 *
	 * @return string
	 */
	protected function compile_builder(GlueDB_Fragment_Builder $fragment) {
		// Get data from fragment :
		$children = $fragment->children();

		// Guess connector from fragment type :
		if ($fragment instanceof GlueDB_Fragment_Builder_List)
			$connector = ', ';
		else
			$connector = ' ';

		// Generate fragment SQL :
		$sqls = array();
		foreach ($children as $child)
			$sqls[] = $child->sql($this);
		$sql = implode($connector, $sqls);

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles GlueDB_Fragment_Ordered fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Ordered $fragment
	 *
	 * @return string
	 */
	protected function compile_ordered(GlueDB_Fragment_Ordered $fragment) {
		// Get data from fragment :
		$toorder	= $fragment->fragment();
		$order		= $fragment->order();

		// Generate fragment SQL :
		$sql = $toorder->sql($this);
		if ( ! $toorder instanceof GlueDB_Fragment_Column)
			$sql	= '(' . $sql . ')';

		// Add ordering :
		if (isset($order)) {
			switch ($order) {
				case GlueDB_Fragment_Ordered::ASC :		$sql .= ' ASC';		break;
				case GlueDB_Fragment_Ordered::DESC :	$sql .= ' DESC';	break;
			}
		}

		// Return SQL :
		return $sql;
	}

	/**
	 * Compiles GlueDB_Fragment_Column fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Column $fragment
	 *
	 * @return string
	 */
	protected function compile_column(GlueDB_Fragment_Column $fragment) {
		$tablesql	= $this->quote_identifier($fragment->table_alias()->alias());
		$columnsql	= $this->quote_identifier($fragment->column()->dbcolumn());
		return $tablesql . '.' . $columnsql;
	}

	/**
	 * Compiles GlueDB_Fragment_Column fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Table $fragment
	 *
	 * @return string
	 */
	protected function compile_table(GlueDB_Fragment_Table $fragment) {
		return $this->quote_identifier($fragment->table()->dbtable());
	}

	/**
	 * Compiles GlueDB_Fragment_Template fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Template $fragment
	 *
	 * @return string
	 */
	protected function compile_template(GlueDB_Fragment_Template $fragment) {
		// Get data from fragment :
		$template		= $fragment->template();
		$replacements	= $fragment->replacements();

		// Break appart template :
		$parts = explode('?', $template);
		if (count($parts) !== count($replacements) + 1)
			throw new Kohana_Exception("Number of placeholders different from number of replacements for " . $template);

		// Make replacements :
		$max = count($replacements);
		$sql = $parts[0];
		for($i = 0; $i < $max; $i++) {
			$sql .= $replacements[$i]->sql($this);
			$sql .= $parts[$i + 1];
		}

		return $sql;
	}

	/**
	 * Compiles GlueDB_Fragment_Value fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Value $fragment
	 *
	 * @return string
	 */
	protected function compile_value(GlueDB_Fragment_Value $fragment) {
		// Get data from fragment :
		$value = $fragment->value();

		// Generate SQL :
		return $this->quote_value($value);
	}

	/**
	 * Compiles GlueDB_Fragment_Query_Select fragments into an SQL string.
	 *
	 * @param GlueDB_Fragment_Query_Select $fragment
	 *
	 * @return string
	 */
	protected function compile_query_select(GlueDB_Fragment_Query_Select $fragment) {
		// Get data from fragment :
		$selectsql	= $fragment->select()->sql($this);
		$fromsql	= $fragment->from()->sql($this);
		$wheresql	= $fragment->where()->sql($this);
		$groupbysql	= $fragment->groupby()->sql($this);
		$havingsql	= $fragment->having()->sql($this);
		$orderbysql	= $fragment->orderby()->sql($this);

		// Mandatory :
		$sql = 'SELECT ' . (empty($selectsql) ? '*' : $selectsql) . ' FROM ' . $fromsql;

		// Optional :
		if ( ! empty($wheresql))		$sql .= ' WHERE '		. $wheresql;
		if ( ! empty($groupbysql))	$sql .= ' GROUP BY '	. $groupbysql;
		if ( ! empty($havingsql))	$sql .= ' HAVING '		. $havingsql;
		if ( ! empty($orderbysql))	$sql .= ' ORDER BY '	. $orderbysql;

		return $sql;
	}

	/**
	 * Quotes an identifier according to current database conventions.
	 *
	 * @param string $identifier
	 *
	 * @return string
	 */
	protected function quote_identifier($identifier) {
		return '"' . $identifier . '"';
	}

	/**
	 * Quotes a value for inclusion into an SQL query.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	protected function quote_value($value) {
		if (is_string($value))
			return $this->quote_string($value);
		elseif (is_array($value))
			return $this->quote_array($value);
		elseif (is_bool($value))
			return $this->quote_bool($value);
		elseif (is_integer($value))
			return $this->quote_integer($value);
		elseif (is_float($value))
			return $this->quote_float($value);
		elseif (is_null($value))
			return $this->quote_null($value);
		else
			throw new Kohana_Exception("Cannot quote objects.");
	}

	/**
	 * Quotes a string for inclusion into an SQL query.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function quote_string($value) {
		return $this->quote($value);
	}

	/**
	 * Quotes an array for inclusion into an SQL query.
	 *
	 * @param array $value
	 *
	 * @return string
	 */
	protected function quote_array(array $value) {
		$arr = array();
		foreach ($value as $val)
			$arr[] = $this->quote_value($val);
		return '(' . implode(',', $arr) . ')';
	}

	/**
	 * Quotes an integer for inclusion into an SQL query.
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	protected function quote_integer($value) {
		return (string) $value;
	}

	/**
	 * Quotes an boolean for inclusion into an SQL query.
	 *
	 * @param boolean $value
	 *
	 * @return string
	 */
	protected function quote_bool($value) {
		return $value ? 'TRUE' : 'FALSE';
	}

	/**
	 * Quotes a float for inclusion into an SQL query.
	 *
	 * @param float $value
	 *
	 * @return string
	 */
	protected function quote_float($value) {
		return (string) $value;
	}

	/**
	 * Returns SQL representation of null.
	 *
	 * @param null $value
	 *
	 * @return string
	 */
	protected function quote_null($value) {
		return 'NULL';
	}

	/**
	 * Returns the appropriate formatter for given column.
	 *
	 * @param GlueDB_Column $column
	 *
	 * @return GlueDB_Formatter
	 */
	abstract public function get_formatter(GlueDB_Column $column);

	/**
	 * Returns structured information about the columns and primary key of a real database table.
	 * Columns are returned alphabetically ordered.
	 *
	 * Structure :
	 * array(
	 * 		'columns' => array(
	 * 			0 => array (
	 * 				'column'	=> < Column name >
	 *				'type'		=> < Native database type >
	 *				'nullable'	=> < Whether or not the column is nullable >
	 *				'maxlength'	=> < Maximum length of a text column >
	 *				'precision' => < Precision of the column >
	 *				'scale' 	=> < Scale of the column >
	 *				'default'	=> < Default value of the column (stored as is from the database, not type casted) >
	 *				'auto'		=> <Whether or not the column auto-incrementing >
	 *			)
	 *			1 => ...
	 *			...
	 * 		)
	 * 		'pk' => array(
	 * 			0 => < columns 0>
	 * 			1 => < columns 1>
	 * 			...
	 * 		)
	 * )
	 *
	 * Be aware that this function is totally ignorant of any virtual table you may have
	 * defined explicitely ! It's mostly useful internally to query the real underlying
	 * database schema. Users should use the introspection API instead.
	 *
	 * @return array
	 */
	public abstract function table_info($name);

	/**
	 * Returns all tables present in current database as an array of table names.
	 *
	 * Be aware that this function is totally ignorant of any virtual table
	 * you may have defined explicitely !
	 *
	 * @return array Array of table names, numerically indexed, alphabetically ordered.
	 */
	abstract public function real_tables();

	/**
	 * Lazy loads a database object, stores it in cache, and returns it.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Database
	 */
	static public function get($name) {
		$name = strtolower($name);
		if( ! isset(self::$instances[$name]))
			self::$instances[$name] = self::create($name);
		return self::$instances[$name];
	}

	/**
	 * Returns a new database instance. Throws class not found exception if
	 * no class is defined for given database.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Database
	 */
	static protected function create($name) {
		// Class name :
		$class = 'GlueDB_Database_'.ucfirst($name);

		// Unlock constructor, create instance and relock constructor :
		self::$constuctor_locked = false;
		$instance = new $class($name);
		self::$constuctor_locked = true;

		return $instance;
	}
}