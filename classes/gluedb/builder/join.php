<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Join expression builder class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Builder_Join extends GlueDB_Builder {
	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function inner($table, &$helper) {
		return $this;
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function left($table, &$helper) {
		return $this;
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function right($table, &$helper) {
		return $this;
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function inner_expr(&$joinbuilder, &$onbuilder) {
		return $this;
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function left_expr(&$joinbuilder, &$onbuilder) {
		return $this;
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function right_expr(&$joinbuilder, &$onbuilder) {
		return $this;
	}
}