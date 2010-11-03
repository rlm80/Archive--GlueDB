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
	protected $ordered;

	/**
	 * @var integer Order.
	 */
	protected $order;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $ordered
	 * @param integer $ordered
	 */
	public function __construct(GlueDB_Fragment $ordered, $order = null) {
		$this->ordered($ordered);
		$this->order($order);
	}

	/**
	 * Sets order to ASC.
	 *
	 * @return GlueDB_Fragment_Ordered
	 */
	public function asc() {
		return $this->order(GlueDB_Fragment_Ordered::ASC);
	}

	/**
	 * Sets order to DESC.
	 *
	 * @return GlueDB_Fragment_Ordered
	 */
	public function desc() {
		return $this->order(GlueDB_Fragment_Ordered::DESC);
	}

	/**
	 * Order getter/setter.
	 *
	 * @param integer $order
	 *
	 * @return mixed
	 */
	public function order($order = null) {
		if (func_num_args() === 0)
			return $this->order;
		else
			return $this->set_property('order', $order);
	}

	/**
	 * Fragment getter/setter.
	 *
	 * @param GlueDB_Fragment $ordered
	 *
	 * @return mixed
	 */
	public function ordered(GlueDB_Fragment $ordered = null) {
		if (func_num_args() === 0)
			return $this->ordered;
		else
			return $this->set_property('ordered', $ordered);
	}
}