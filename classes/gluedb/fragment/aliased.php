<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents anything that compiles into "... AS ...".
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Aliased extends GlueDB_Fragment {
	/**
	 * @var GlueDB_Fragment Fragment that needs to have an alias.
	 */
	protected $aliased;

	/**
	 * @var string Alias.
	 */
	protected $as;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $aliased
	 * @param string $as
	 */
	public function __construct(GlueDB_Fragment $aliased, $as = null) {
		$this->as($as);
		$this->aliased($aliased);
	}

	/**
	 * Fragment getter/setter.
	 *
	 * @param GlueDB_Fragment $aliased
	 *
	 * @return mixed
	 */
	public function aliased(GlueDB_Fragment $aliased = null) {
		if (func_num_args() === 0)
			return $this->aliased;
		else {
			if (isset($this->aliased)) $this->aliased->unregister_user($this);
			$this->aliased = $aliased;
			$this->aliased->register_user($this);
			$this->invalidate();
			return $this;
		}
	}

	/**
	 * Alias getter/setter.
	 *
	 * @param string $as
	 *
	 * @return mixed
	 */
	public function _as($as = null) {
		if (func_num_args() === 0)
			return $this->as;
		else {
			$this->as = $as;
			$this->invalidate();
			return $this;
		}
	}
}