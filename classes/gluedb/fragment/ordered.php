<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents anything that compiles into "... ASC" or "... DESC".
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Ordered extends GlueDB_Fragment {
	// Order constants :
	const ASC	= 0;
	const DESC	= 1;

	/**
	 * @var GlueDB_Fragment Fragment that needs to have an order.
	 */
	protected $fragment;

	/**
	 * @var integer Order.
	 */
	protected $order;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $fragment
	 */
	public function __construct(GlueDB_Fragment $fragment, $order = null) {
		$this->order	= $order;
		$this->fragment	= $fragment;
		$this->fragment->register_user($this);
	}

	/**
	 * Fragment getter/setter.
	 *
	 * @param GlueDB_Fragment $fragment
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
	 * Order getter/setter.
	 *
	 * @param integer $order
	 *
	 * @return integer
	 */
	public function order($order = null) {
		if (func_num_args() === 0)
			return $this->order;
		else {
			$this->order = $order;
			$this->invalidate();
		}
	}
}