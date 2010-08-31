<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * On expression builder class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Builder_Boolean_On extends GlueDB_Builder_Boolean {
	/**
	 * Marks end of on clause : forwards call to parent join expression builder.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function inner($table, &$helper) {
		return $this->parent->inner($table, $helper);
	}

	/**
	 * Marks end of on clause : forwards call to parent join expression builder.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function left($table, &$helper) {
		return $this->parent->left($table, $helper);
	}

	/**
	 * Marks end of on clause : forwards call to parent join expression builder.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function right($table, &$helper) {
		return $this->parent->right($table, $helper);
	}

	/**
	 * Marks end of on clause : forwards call to parent join expression builder.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function inner_expr(&$joinbuilder, &$onbuilder) {
		return $this->parent->inner_expr($joinbuilder, &$onbuilder);
	}

	/**
	 * Marks end of on clause : forwards call to parent join expression builder.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function left_expr(&$joinbuilder, &$onbuilder) {
		return $this->parent->left_expr($joinbuilder, &$onbuilder);
	}

	/**
	 * Marks end of on clause : forwards call to parent join expression builder.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function right_expr(&$joinbuilder, &$onbuilder) {
		return $this->parent->right_expr($joinbuilder, &$onbuilder);
	}
}