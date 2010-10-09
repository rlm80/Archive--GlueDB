<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Select extends GlueDB_Fragment_Composite {
	/**
	 * @var Query that owns this order by clause.
	 */
	protected $query;
	
	/**
	 * @var string Connector redefined.
	 */
	protected $connector = ', ';

	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query = null) {
		$this->query = $query;
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
			if ($child instanceof GlueDB_Fragment_Alias_Computed)
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
			if ($child instanceof GlueDB_Fragment_Alias_Column)
				if ($child->column()->column()->name() === $column_name)
					$i++;
		
		// Compute alias :
		if ($i === 0) 
			return $column_name;
		else
			return $column_name . $i;
	}	
	
	/**
	 * Initializes the list with a first item.
	 *
	 * @return GlueDB_Fragment_Composite_Select
	 */
	public function init() {
		// Get params :
		$params	= func_get_args();

		// Remove children :
		$this->reset();

		// Add fragment :
		$this->add($params);

		return $this;
	}

	/**
	 * Adds an item at the end of the list.
	 *
	 * @return GlueDB_Fragment_Composite_Select
	 */
	public function _and() {
		// Get params :
		$params	= func_get_args();

		// Add fragment :
		$this->add($params);

		return $this;
	}
	
	/**
	 * Adds fragment of appropriate type.
	 * 
	 * @param $first
	 * @param $next
	 */
	protected function add($params) {
		// Split params :
		$first = array_shift($params);
		
		// Add fragment :
		if ($first instanceof GlueDB_Fragment_Column) {
			// Compute default alias :
			$alias = $this->compute_alias_column($first->column()->name());
			
			// Push fragment :			
			$this->push(new GlueDB_Fragment_Alias_Column($first, $alias));
		}
		else {
			// Compute default alias :
			$alias = $this->compute_alias_computed();
			
			// Push fragment :
			$this->push(new GlueDB_Fragment_Alias_Computed($first, $params, $alias));
		}
	}	

	/**
	 * Sets alias of the last element of the list.
	 *
	 * @return GlueDB_Fragment_Composite_Select
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
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
		else
			return call_user_func_array(array($this->query, $name), $args);
	}
}