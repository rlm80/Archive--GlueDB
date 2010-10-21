<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_List_Orderby extends GlueDB_Fragment_Builder_List {
	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query = null) {
		$this->set_forward($query);
	}

	/**
	 * Adds fragment of appropriate type.
	 *
	 * @param array $params
	 */
	protected function add($params) {
		// Split params :
		$first = array_shift($params);

		// Add fragment :
		if (is_string($first))
			$this->push(new GlueDB_Fragment_Ordered(
				new GlueDB_Fragment_Template($first, $params)
			));
		else
			$this->push(new GlueDB_Fragment_Ordered($first));
	}

	/**
	 * Sets order ASC for the last element of the list.
	 *
	 * @return GlueDB_Fragment_Builder_List_Orderby
	 */
	public function asc() {
		if ($last = $this->last())
			$last->order(GlueDB_Fragment_Ordered::ASC);
		else
			throw new Kohana_Exception("No column to set an order to.");

		return $this;
	}

	/**
	 * Sets order DESC for the last element of the list.
	 *
	 * @return GlueDB_Fragment_Builder_List_Orderby
	 */
	public function desc() {
		if ($last = $this->last())
			$last->order(GlueDB_Fragment_Ordered::DESC);
		else
			throw new Kohana_Exception("No column to set an order to.");

		return $this;
	}
}