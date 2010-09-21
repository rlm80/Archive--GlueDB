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
	 * If there is no parameters, returns the value. Otherwise changes the value and
	 * invalidates the fragment.
	 *
	 * @param mixed $value
	 * 
	 * @return GlueDB_Fragment_Value
	 */
	public function value($value = null) {
		if (func_num_args() === 0)
			return $this->value;
		else {
			$this->value = $value;
			$this->invalidate();
			return $this;
		}
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string. In this case, simply
	 * returns the quoted value according to current database conventions.
	 *
	 * @param string $dbname	 
	 *
	 * @return string
	 */
	protected function compile($dbname) {
		return gluedb::db($dbname)->quote_value($this->value);
	}
}