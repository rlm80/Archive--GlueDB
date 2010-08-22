<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Statement class.
 * 
 * PDOStatement extension that adds automatic type casting.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Statement extends PDOStatement {
    /**
     * @var GlueDB_Database PDO instance that spawned this statement. Automatically
     * 						passed by PDO to the constructor of this class when a new
     * 						statement is created.
     */
    protected $db;
    
    /**
     * @var array Array of formatters that will be used to type cast column values.
     */
    protected $formatters;
    
    /**
     * Constructor.
     * 
     * @param GlueDB_Database $db
     */
    protected function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Binds a formatter to a column of the result set.
     * 
     * @param mixed $column The column (name or integer index).
     * @param mixed $formatter A GlueDB_Formatter instance, or a PHP type as a string.
     */
    public function bindFormatter($column, $formatter) {
    	// TODO think about this "name or integer index", and don't forget bindColumn
    }
}