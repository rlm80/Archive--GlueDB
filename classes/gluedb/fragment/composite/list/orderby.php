<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_List_Orderby extends GlueDB_Fragment_Composite_List {
	/**
	 * Adds fragment of appropriate type.
	 *
	 * @param array $params
	 */
	protected function add($params) {
		// Split params :
		$first = array_shift($params);

		// Add fragment :
		if ($first instanceof GlueDB_Fragment_Column)
			$this->push(new GlueDB_Fragment_Ordered_Column($first));
		else
			$this->push(new GlueDB_Fragment_Ordered_Computed($first, $params));
	}

	/**
	 * Sets order ASC for the last element of the list.
	 *
	 * @return GlueDB_Fragment_Composite_List_Orderby
	 */
	public function asc() {
		if ($last = $this->last())
			$last->set_asc(true);
		else
			throw new Kohana_Exception("No column to set an order to.");

		return $this;
	}

	/**
	 * Sets order DESC for the last element of the list.
	 *
	 * @return GlueDB_Fragment_Composite_List_Orderby
	 */
	public function desc() {
		if ($last = $this->last())
			$last->set_asc(false);
		else
			throw new Kohana_Exception("No column to set an order to.");

		return $this;
	}
}