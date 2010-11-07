<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that holds a value that must be quoted.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Value extends GlueDB_Fragment {
	/**
	 * @var mixed Value.
	 */
	protected $value;

	/**
	 * Constructor.
	 *
	 * @param mixed $value
	 */
	public function __construct($value) {
		$this->value = $value;
	}

	/**
	 * Value setter/getter.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function value($value = null) {
		if (func_num_args() === 0)
			return $this->value;
		else
			return $this->set_property('value', $value);
	}
	
	/**
	 * Forwards call to given database.
	 *
	 * @param GlueDB_Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db, $style) {
		// Forwards call to database :
		return $db->compile_value($this, $style);
	}
}