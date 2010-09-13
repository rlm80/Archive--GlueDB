<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that is made of an SQL template with '?' placeholders and an array of replacement
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
	 * @var array Replacements to be made in SQL template.
	 */
	protected $values;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $parent
	 * @param string $template
	 * @param array $values
	 */
	public function __construct(GlueDB_Fragment $parent, $template, array $values = array()) {
		$this->parent = $parent;
		$this->template = $template;
		$this->values = $values;
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string. In this case, returns the template with placeholders
	 * replaced by their quoted values.
	 *
	 * @return string
	 */
	protected function compile() {
		// Get query database :
		$db = $this->query()->db();

		// Quote values :
		$quoted = array();
		foreach($this->values as $value)
			$quoted[] = $this->query()->db()->quote($value);

		// Break appart template :
		$parts = explode('?', $this->template);
		if (count($parts) !== count($quoted) + 1)
			throw new Kohana_Exception("Number of placeholders different from number of replacement values for " . $this->template);

		// Make replacements :
		$max = count($quoted);
		$sql = $parts[0];
		for($i = 0; $i < $max; $i++) {
			$sql .= $quoted[$i];
			$sql .= $parts[$i + 1];
		}

		return $sql;
	}
}