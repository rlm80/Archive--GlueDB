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
	 * @var GlueDB_Fragment Fragment.
	 */
	protected $fragment;

	/**
	 * @var string Alias.
	 */
	protected $alias;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $fragment
	 * @param string $alias
	 */
	public function __construct(GlueDB_Fragment $fragment, $alias = null) {
		$this->alias	= $alias;
		$this->fragment	= $fragment;
		$this->fragment->register_user($this);
	}

	/**
	 * Returns fragment.
	 *
	 * @return GlueDB_Fragment
	 */
	public function fragment() {
		return $this->fragment;
	}

	/**
	 * Alias setter.
	 *
	 * @param string $alias
	 */
	public function set_alias($alias) {
		$this->alias = $alias;
		$this->invalidate();
	}

	/**
	 * Returns alias.
	 *
	 * @return string
	 */
	public function alias() {
		return $this->alias;
	}
}