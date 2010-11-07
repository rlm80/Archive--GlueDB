<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an assignment in an update query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Assignment extends GlueDB_Fragment {
	/**
	 * @var GlueDB_Fragment_Column Left side of the assignment.
	 */
	protected $column;

	/**
	 * @var GlueDB_Fragment Right side of the assignment.
	 */
	protected $to;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Column $set
	 * @param mixed $to
	 */
	public function __construct(GlueDB_Fragment_Column $column, $to = null) {
		$this->column($column);
		$this->to($to);
	}

	/**
	 * Left side of the assignment getter/setter.
	 *
	 * @param GlueDB_Fragment_Column $column
	 *
	 * @return mixed
	 */
	public function column(GlueDB_Fragment_Column $column = null) {
		if (func_num_args() === 0)
			return $this->column;
		else
			return $this->set_property('column', $column);
	}

	/**
	 * Right side of the assignment getter/setter.
	 *
	 * @param mixed $to
	 *
	 * @return mixed
	 */
	public function to($to = null) {
		if (func_num_args() === 0)
			return $this->to;
		else {
			// Turn parameter into a fragment if it isn't already :
			if ( ! $to instanceof GlueDB_Fragment)
				$to = new GlueDB_Fragment_Value($to);

			// Replace to by new fragment :
			return $this->set_property('to', $to);
		}
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
		return $db->compile_assignment($this, $style);
	}	
}