<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that is made of an SQL template with placeholders and an array of replacement
 * values that need to be quoted.
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
	 * @var array Replacements to be made in SQL template. The keys are placeholders and
	 * 			  the values are what they must be replaced by after beeing quoted.
	 */
	protected $values;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $parent
	 * @param string $template
	 * @param array $values
	 *
	 */
	public function __construct(GlueDB_Fragment $parent, $template, array $values = array()) {
		$this->parent = $parent;
		$this->template = $template;
		$this->values = $values;
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string. In this case, simply returns the template with
	 * placeholders replaced by their quoted values.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile() {
		// Get query database :
		$db = $this->query()->db();

		// Quote values :
		$quoted = array();
		foreach($this->values as $ph => $value)
			$quoted[$ph] = $this->query()->db()->quote($value);

		// Return template with replaced placeholders : TODO changes this to '?'
		return strtr($this->template, $quoted);
	}
}