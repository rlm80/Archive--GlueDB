<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base builder class.
 *
 * A builder is an object that provides a fluent interface to build an expression. An expression is
 * represented internally as an array of components. Components will be run one by one through the
 * compiler and concatenated to produce the resulting SQL expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

/*
 Note :
 	Using a "nested" fluent API (builders that may return children builders, not always $this) seems the most natural
 	way to go about this, since boolean and joins expressions are nested in nature. But as tempting as it may be, it is
 	NOT the right way to go about this. It is too confusing when the query has to be built in several steps, instead of
 	just one long "sentence" :

		// suppose addthis() returns a child builder :
 		$query1->addthis()->addthat();

		// You might think this is the same as the above :
		$query2->addthis();
		$query2->addthat(); // But it does NOT work !

		// It shoud be :
		$childbuilder = $query2->addthis();
		$childbuilder->addthat();

	Just too confusing...and that's only the tip of the pile of problems that arise from it when things get more complex.
 */

abstract class GlueDB_Builder {
	/**
	 * @var GlueDB_Query The query that is the context of this builder.
	 */
	protected $query;

	/**
	 * @var array Components of current expression.
	 */
	protected $parts;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Query $query
	 */
	public function __construct(GlueDB_Query $query, $forward = false) {
		$this->parts = array();
		$this->query = $query;
		$this->forward = $forward;
	}

	/**
	 * Removes all expression components added so far.
	 *
	 * @return GlueDB_Builder
	 */
	public function reset() {
		$this->parts = array();
		return $this;
	}

	/**
	 * Whether or not current expression is empty.
	 *
	 * @return boolean
	 */
	public function isempty() {
		return count($this->parts) === 0;
	}

	/**
	 * Returns expression components.
	 *
	 * @return array
	 */
	public function parts() {
		return $this->parts;
	}

	/**
	 * Any call to an unkown function is forwarded to the query.
	 *
	 * @param string $name
	 * @param array $arguments
	 *
	 * @return mixed|NULL
	 */
	public function __call($name, $arguments) {
		if ($this->forward)
			return call_user_func_array(array($this->end(), $name), $arguments);
		else {
			$trace = debug_backtrace();
	        trigger_error(
				'Undefined method via __call(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
        	return null;
		}
	}
}