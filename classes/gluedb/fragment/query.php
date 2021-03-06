<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base fragement class for queries.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

abstract class GlueDB_Fragment_Query extends GlueDB_Fragment {
	/**
	 * @var GlueDB_Database The database object this query is meant to be used against.
	 */
	protected $db;

	/**
	 * @var integer Number of rows affected by the last call to execute().
	 */
	protected $row_count;

	/**
	 * Number of rows affected by the last call to execute().
	 *
	 * @return integer
	 */
	public function rowCount() {
		return $this->row_count;
	}

	/**
	 * Returns database object, determined from the tables this query manipulates.
	 *
	 *  @return GlueDB_Database
	 */
	public function db() {
		if ( ! isset($this->db))
			$this->db = $this->find_db();
		return $this->db;
	}

	/**
	 * Determines database from the tables this query manipulates.
	 *
	 * @return GlueDB_Database
	 */
	abstract protected function find_db();

	/**
	 * Return current object. Example usage :
	 *
	 * $sql = gluedb::select('mytable')->where('1=1')->sql(); // Doesn't work ! Returns only the SQL of the last builder accessed : the where clause.
	 * $sql = gluedb::select('mytable')->where('1=1')->query()->sql(); // Works. Returns the SQL of the whole query.
	 *
	 * @return GlueDB_Fragment_Query
	 */
	public function query() {
		return $this;
	}

	/**
	 * Compiles this query into an SQL string and asks PDO to prepare it for execution. Returns
	 * a PDOStatement object that can be executed multiple times. If you need to execute a statement
	 * more than once, or if you need query parameters, this is the method of choice for security
	 * and performance.
	 *
	 * @return GlueDB_Statement
	 */
	public function prepare(array $driver_options = array()) {
		$db = $this->db();
		$sql = $this->sql($db);
		return $db->prepare($sql, $driver_options);
	}

	/**
	 * Executes current query.
	 *
	 * @return GlueDB_Fragment_Query
	 */
	public function execute() {
		$db = $this->db();
		$sql = $this->sql($db);
		$this->row_count = (integer) $db->exec($sql);
		return $this;
	}
}