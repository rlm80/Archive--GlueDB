<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_List_Select extends GlueDB_Fragment_Builder_List {
	/**
	 * @var GlueDB_Fragment_Query Query that owns this list.
	 */
	protected $query;

	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query = null) {
		$this->query = $query;
	}

	/**
	 * Adds fragment of appropriate type.
	 *
	 * @param array $params
	 */
	protected function add($params) {
		// Split params :
		$first = array_shift($params);

		// Compute default alias :
		if ($first instanceof GlueDB_Fragment_Column)
			$alias = $this->compute_alias_column($first->column()->name());
		else
			$alias = $this->compute_alias_computed();

		// Push fragment :
		if ($first instanceof GlueDB_Fragment)	
			$this->push(new GlueDB_Fragment_Aliased(
				$first,
				$alias
			));
		else
			$this->push(new GlueDB_Fragment_Aliased(
				new GlueDB_Fragment_Template($first, $params),
				$alias
			));
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

	/**
	 * Sets alias of the last element of the list.
	 *
	 * @return GlueDB_Fragment_Builder_Select
	 */
	public function _as($alias) {
		if ($last = $this->last())
			$last->set_alias($alias);
		else
			throw new Kohana_Exception("No column to set an alias to.");

		return $this;
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