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
	 * Fragment getter/setter.
	 *
	 * @return GlueDB_Fragment
	 */
	public function fragment($fragment = null) {
		if (func_num_args() === 0)
			return $this->fragment;
		else {
			$this->fragment = $fragment;
			$this->invalidate();
		}	
	}
	
	/**
	 * Alias getter/setter.
	 *
	 * @param string
	 *
	 * @return string
	 */
	public function alias($alias = null) {
		if (func_num_args() === 0)
			return $this->alias;
		else {
			$this->alias = $alias;
			$this->invalidate();
		}		
	}
}