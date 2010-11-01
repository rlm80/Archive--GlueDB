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
	 * @var GlueDB_Fragment_Column Column fragment.
	 */
	protected $assignee;

	/**
	 * @var GlueDB_Fragment Right side of the equal.
	 */
	protected $assigned;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Column $assignee
	 * @param mixed $assigned
	 */
	public function __construct(GlueDB_Fragment_Column $assignee, $assigned) {
		$this->assignee($assignee);
		$this->assigned($assigned);
	}

	/**
	 * Assignee getter/setter.
	 *
	 * @param GlueDB_Fragment_Column $assignee
	 *
	 * @return mixed
	 */
	public function assignee(GlueDB_Fragment_Column $assignee = null) {
		if (func_num_args() === 0)
			return $this->assignee;
		else {
			if (isset($this->assignee)) $this->assignee->unregister_user($this);
			$this->assignee = $assignee;
			$this->assignee->register_user($this);
			$this->invalidate();
			return $this; // TODO this should return assignee + generalize this to all getter / setters ?
		}
	}

	/**
	 * Assigned getter/setter.
	 *
	 * @param mixed $assigned
	 *
	 * @return mixed
	 */
	public function assigned($assigned = null) {
		if (func_num_args() === 0)
			return $this->assigned;
		else {
			// Turn parameter into a fragment if it isn't already :
			if ( ! $assigned instanceof GlueDB_Fragment)
				$assigned = new GlueDB_Fragment_Value($assigned);

			// Replace assigned by new fragment :
			if (isset($this->assigned)) $this->assigned->unregister_user($this);
			$this->assigned = $assigned;
			$this->assigned->register_user($this);
			$this->invalidate();
			return $this;
		}
	}
}