<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Select extends GlueDB_Fragment_Builder {
	/**
	 * Adds an element at the end of the select list. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return GlueDB_Fragment_Aliased
	 */
	public function _and() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Compute default alias :
		if ($first instanceof GlueDB_Fragment_Column)
			$alias = $this->compute_alias_column($first->column()->name());
		else
			$alias = $this->compute_alias_computed();

		// Build fragment :
		if ($first instanceof GlueDB_Fragment)
			$fragment = new GlueDB_Fragment_Aliased($first, $alias);
		else
			$fragment = new GlueDB_Fragment_Aliased(
				new GlueDB_Fragment_Template($first, $params),
				$alias
			);

		// Push fragment :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}

	/**
	 * Returns unique alias for computed column.
	 *
	 * @return string
	 */
	protected function compute_alias_computed() {
		// Count number of computed columns so far :
		$i = 0;
		foreach ($this->children as $child)
			if ($child instanceof GlueDB_Fragment_Aliased_Computed)
				$i++;

		// Compute alias :
		if ($i === 0)
			return 'computed';
		else
			return 'computed' . $i;
	}

	/**
	 * Returns unique alias for column.
	 *
	 * @return string
	 */
	protected function compute_alias_column($column_name) {
		// Count number of columns with such a name so far :
		$i = 0;
		foreach ($this->children as $child)
			if ($child instanceof GlueDB_Fragment_Aliased_Column)
				if ($child->column()->column()->name() === $column_name)
					$i++;

		// Compute alias :
		if ($i === 0)
			return $column_name;
		else
			return $column_name . $i;
	}
}