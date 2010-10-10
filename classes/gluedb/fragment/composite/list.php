<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base fragment that provides a fluent interface to build a list of items.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment_Composite_List extends GlueDB_Fragment_Composite {
	/**
	 * @var Query that owns this list.
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
	 * Initializes the list with a first item.
	 *
	 * @return GlueDB_Fragment_Composite_List
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
	 * @return GlueDB_Fragment_Composite_List
	 */
	public function then() {
		// Get params :
		$params	= func_get_args();

		// Add fragment :
		$this->add($params);

		return $this;
	}

	/**
	 * Adds fragment of appropriate type.
	 *
	 * @param array $params
	 */
	abstract protected function add($params);

	/*
	 * Forwards unknown calls to query.
	 *
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call($name, $args) {
		return call_user_func_array(array($this->query, $name), $args);
	}
}