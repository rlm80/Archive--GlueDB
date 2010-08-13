<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base helper class.
 *
 * A helper is an object that helps building a query with the query builder.
 * You usually get such objects by passing parameters by reference to some
 * query builder methods.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Helper {
	/**
	 * @var GlueDB_Query The query that is the context of this helper.
	 */
	protected $query;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Query $query
	 */
	public function __construct(GlueDB_Query $query) {
		$this->query = $query;
	}
}