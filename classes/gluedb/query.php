<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base class for query objects.
 *
 * A query is always bound to a specific database. That way, when for example with join a table,
 * we know in which database that table is stored and we can retrieve the table object to get
 * useful information about it, for example columns PHP types.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

abstract class GlueDB_Query {
	/**
	 * @var GlueDB_Database The database object this query is meant to be used against.
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Database $db
	 */
	public function __construct(GlueDB_Database $db) {
		$this->db = $db;
	}

	/**
	 * Compiles query against current database.
	 *
	 * @return string
	 */
	public function compile() {
		return $this->db->compile($this);
	}

	/*
	 * Compiles this query into an SQL string and asks PDO to prepare it for execution. Returns
	 * a PDOStatement object that can be executed multiple times. If you need to execute a statement
	 * more than once, or if you need query parameters, this is the method of choice for security
	 * and performance.
	 *
	 * @see PDO::prepare()
	 */
	public function prepare($driver_options = null) {
		return $this->db->prepare($this->compile(), $driver_options);
	}

	/*
	 * @see PDO::exec() and PDO::query()
	 */
	abstract public function execute();
}