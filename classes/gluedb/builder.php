<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base builder class.
 *
 * A builder is an object that provides a fluent interface to build an expression. An expression is
 * represented internally as an array of components. Components can be atomic or builders themselves.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Builder {
	/**
	 * @var GlueDB_Builder Expression builder that gave birth to this one (called "context" instead of "parent" to
	 * 					   avoid confusion with parent in class hierarchy).
	 */
	protected $context;

	/**
	 * @var array Components of current expression.
	 */
	protected $parts = array();

	/**
	 * @var string Compiled SQL expression.
	 */
	protected $sql;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Builder $context
	 */
	public function __construct(GlueDB_Builder $context) {
		$this->context = $context;
	}

	/**
	 * Whether or not current expression is empty (i.e. no components).
	 *
	 * @return boolean
	 */
	public function isempty() {
		return count($this->parts) === 0;
	}

	/**
	 * Returns compiled SQL string.
	 *
	 * @return string
	 */
	public function sql() {
		if ( ! isset($this->sql))
			$this->sql = $this->compile();
		return $this->sql;
	}

	/**
	 * Returns compiled SQL string.
	 *
	 * @return string
	 */
	protected function compile() {
		$sql = '';
		foreach ($this->parts as $part) {
			if (is_string($part))
				$sql .= $part;
			elseif ($part instanceof GlueDB_Builder)
				$sql .= $part->sql();
			else
				$sql .= $this->dialect()->compile($part);
		}
		return $sql;
	}

	/**
	 * Returns SQL dialect this expression must be compiled into.
	 *
	 * @return GlueDB_Dialect
	 */
	protected function dialect() {
		return $this->context->dialect();
	}

	/**
	 * Signals that a change has been made to the components of this expression that
	 * invalidates its cached compiled form.
	 */
	protected function invalidate() {
		// Reset SQL cache :
		$this->sql = null;

		// Forward call to context, because if a child is invalid, the context necessarily is, too.
		$this->context->invalidate();
	}
}