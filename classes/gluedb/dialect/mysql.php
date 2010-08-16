<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * MySQL dialect class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Dialect_MySQL extends GlueDB_Dialect_ANSI {
	/**
	 * Escapes a string according to MySQL conventions.
	 *
	 * @param string $string
	 *
	 * @return
	 */
	public function quote($string) {
		return mysql_real_escape_string($string);
	}

	/**
	 * Quotes an identifier according to MySQL conventions.
	 * Mysql uses back-ticks for this instead of the ANSI double quote standard character.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		return '`' . $identifier . '`';
	}

	/**
	 * Returns the appropriate PHP type to represent given MySQL type.
	 *
	 * @param string $dbtype
	 *
	 * @return string
	 */
	public function phptype($dbtype) {
		// Extract first word from type (MySQL may return things like "float unsigned" sometimes) :
		if (preg_match('/^\S+/', $dbtype, $matches))
			$dbtype = $matches[0];

		// Convert type to upper case :
		$dbtype = strtoupper($dbtype);

		// Create appropriate formatter :
		switch ($dbtype) {
			// Integer types :
			case 'TINYINT'; case 'SMALLINT'; case 'MEDIUMINT'; case 'INT'; case 'BIGINT';
				$phptype = 'integer';
				break;

			// Real types :
			case 'FLOAT'; case 'DOUBLE'; case 'DECIMAL';
				$phptype = 'float';
				break;

			// Boolean types :
			case 'BIT';
				$phptype = 'boolean';
				break;

			// String types :
			case 'CHAR'; case 'VARCHAR'; case 'TINYTEXT'; case 'TEXT';
			case 'MEDIUMTEXT'; case 'LONGTEXT'; case 'ENUM'; case 'SET';
				$phptype = 'string';
				break;

			// Binary types :
			case 'BINARY'; case 'VARBINARY'; case 'TINYBLOB'; case 'BLOB';
			case 'MEDIUMBLOB'; case 'LONGBLOB';
				$phptype = 'string'; // TODO Is this the right thing to do ?
				break;

			// Date and time types :
			case 'DATE'; case 'DATETIME'; case 'TIME'; case 'TIMESTAMP'; case 'YEAR';
				$phptype = 'string'; // TODO Is this the right thing to do ?
				break;

			// Default :
			default;
				$phptype = null;
		}

		return $phptype;
	}
}