<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that is made of an SQL template with placeholders and an array of replacement fragments.
 *
 * TODO : renommer en GlueDB_Fragment_Expression ?
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Template extends GlueDB_Fragment {
	/**
	 * @var string SQL template with placeholders for values that need to be quoted.
	 */
	protected $template;

	/**
	 * @var array Replacements to be made in SQL template.
	 */
	protected $replacements;

	/**
	 * Constructor.
	 *
	 * @param string $template
	 * @param array $replacements
	 */
	public function __construct($template, array $replacements = array()) {
		// Init template :
		$this->template = $template;

		// Init replacements :
		foreach($replacements as $replacement) {
			// Turn replacements that aren't fragments into value fragments (SQL = quoted value) :
			if ( ! $replacement instanceof GlueDB_Fragment)
				$replacement = new GlueDB_Fragment_Value($replacement);

			// Set parent :
			$replacement->register_user($this);

			// Add replacement :
			$this->replacements[] = $replacement;
		}
	}

	/**
	 * Template setter/getter.
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public function template($template = null) {
		if (func_num_args() === 0)
			return $this->template;
		else {
			$this->template = $template;
			$this->invalidate();
		}
	}

	/**
	 * Replacements setter/getter.
	 *
	 * @param array $replacements
	 *
	 * @return array
	 */
	public function replacements($replacements = null) {
		if (func_num_args() === 0)
			return $this->replacements;
		else {
			$this->replacements = $replacements;
			$this->invalidate();
		}
	}
}