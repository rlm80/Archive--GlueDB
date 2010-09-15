<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that is made of an SQL template with placeholders and an array of replacement fragments.
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

		// Turn replacements that aren't fragments into value fragments (SQL = quoted value) :
		foreach($replacements as $replacement) {
			if ($replacement instanceof GlueDB_Fragment)
				$this->replacements[] = $replacement;
			else
				$this->replacements[] = new GlueDB_Fragment_Value($replacement);
		}
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string. In this case, returns the template with placeholders
	 * replaced by their SQL representations.
	 *
	 * @return string
	 */
	protected function compile() {
		// Break appart template :
		$parts = explode('?', $this->template);
		if (count($parts) !== count($this->replacements) + 1)
			throw new Kohana_Exception("Number of placeholders different from number of replacements for " . $this->template);

		// Make replacements :
		$max = count($this->replacements);
		$sql = $parts[0];
		for($i = 0; $i < $max; $i++) {
			$sql .= $this->replacements[$i]->sql();
			$sql .= $parts[$i + 1];
		}

		return $sql;
	}
}