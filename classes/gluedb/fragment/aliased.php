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
	protected $alias;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $aliased
	 * @param string $alias
	 */
	public function __construct(GlueDB_Fragment $aliased, $alias = null) {
		$this->alias	= $alias;
		$this->aliased	= $aliased;
		$this->aliased->register_user($this);
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
			$this->aliased = $aliased;
			$this->invalidate();
			return $this;
		}
	}

	/**
	 * Sets alias. Call as() instead of this.
	 *
	 * @return GlueDB_Fragment_Aliased
	 */
	public function _as($alias) {
		return $this->alias($alias);
	}

	/**
	 * Alias getter/setter.
	 *
	 * @param string
	 *
	 * @return mixed
	 */
	public function alias($alias = null) {
		if (func_num_args() === 0)
			return $this->alias;
		else {
			$this->alias = $alias;
			$this->invalidate();
			return $this;
		}
	}

	/*
	 * Redefined to setup aliases for _as(). Required because keywords aren't valid function
	 * names in PHP. Also forwards unknown calls to query.
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call($name, $args) {
		if ($name === 'as')
			return call_user_func_array(array($this, '_as'), $args);
		else
			return parent::__call($name, $args);
	}
}