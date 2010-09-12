<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base class for query objects.
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
	abstract public function find_db();

	/*
	 * Compiles this query into an SQL string and asks PDO to prepare it for execution. Returns
	 * a PDOStatement object that can be executed multiple times. If you need to execute a statement
	 * more than once, or if you need query parameters, this is the method of choice for security
	 * and performance.
	 *
	 * @see PDO::prepare()
	 */
	public function prepare($driver_options = null) {
		return $this->db()->prepare($this->sql(), $driver_options);
	}

	/**
	 * Returns the query to which the current fragment belongs.
	 *
	 * @return GlueDB_Query
	 */
	protected function query() {
		return $this;
	}

	/*
	 * @see PDO::exec() and PDO::query()
	 */
	abstract public function execute();
}