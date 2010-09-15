<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that holds a value to be quoted.
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
	 * Changes the value and invalidates the fragment.
	 *
	 * @param mixed $value
	 */
	public function set_value($value) {
		$this->value = $value;
		$this->invalidate();
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string. In this case, simply
	 * returns the quoted value according to current database conventions.
	 *
	 * @return string
	 */
	protected function compile() {
		return $this->root()->db()->quote($this->value);
	}
}