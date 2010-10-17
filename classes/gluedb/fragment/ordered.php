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
	 * @var GlueDB_Fragment Fragment that needs to have an order set.
	 */
	protected $fragment;
	
	/**
	 * @var integer Ordering.
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
	 * Returns fragment.
	 *
	 * @return GlueDB_Fragment
	 */
	public function fragment() {
		return $this->fragment;
	}	
	
	/**
	 * Order setter.
	 *
	 * @param integer $order
	 */
	public function set_order($order) {
		$this->order = $order;
		$this->invalidate();
	}

	/**
	 * Returns order.
	 *
	 * @return integer
	 */
	public function order() {
		return $this->order;
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string.
	 *
	 * @param GlueDB_Database $db
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db) {
		return $db->compile_ordered($this);
	}
}