<?php

class Controller_GlueDB extends Controller {
	public function action_test() {
		echo ("<pre>");
		$this->test_fragments();
		$this->test_columns();
	}

	private function test_fragments() {
		$tests = array(
			'value - string'	=> array(
					gluedb::value("test'test"),
					"/'test\\\'test'/"
				),
			'value - integer' 	=> array(
					gluedb::value(10),
					"/10/"
				),
			'value - array'	=> array(
					gluedb::value(array("test'test", 10)),
					"/('test\\\'test',10)/"
				),
			'value - float'		=> array(
					gluedb::value(10.5),
					"/10.5/"
				),
			'value - boolean'	=> array(
					gluedb::value(false),
					"/FALSE/"
				),
			'value - null'		=> array(
					gluedb::value(null),
					"/NULL/"
				),
			'template - no replacements' => array(
					gluedb::template("test template"),
					"/test template/"
				),
			'template - two replacements' => array(
					gluedb::template("? test ? template", "test'test", 10),
					"/'test\\\'test' test 10 template/"
				),
			'boolean - simple' => array(
					gluedb::boolean("'test' = ?", "qsdf")->or("'test' IN ?", array('azer', 'qsdf')),
					"/'test' = 'qsdf' OR 'test' IN \\('azer','qsdf'\\)/"
				),
			'boolean - nested' => array(
					gluedb::boolean("'test' = 'test' OR ( ? )", gluedb::boolean("1 = 0")),
					"/'test' = 'test' OR \\( 1 = 0 \\)/"
				),
			'join - simple' => array(
					gluedb::join("mytable")->left("yourtable")->on("1=1")->and("2=2")->or("3=3")->right("histable")->on("4=4"),
					"/ `mytable` AS `mytable0`  LEFT OUTER JOIN `yourtable` AS `yourtable0` ON \\( 1=1 AND 2=2 OR 3=3 \\)  RIGHT OUTER JOIN `histable` AS `histable0` ON \\( 4=4 \\)/" // TODO
				),
			'join - nested' => array(
					gluedb::join(gluedb::join("mytable")->left("yourtable")->on("1=1"))->right("histable")->on("2=2"),
					"/ \\(  `mytable` AS `mytable1`  LEFT OUTER JOIN `yourtable` AS `yourtable1` ON \\( 1=1 \\)  \\)  RIGHT OUTER JOIN `histable` AS `histable1` ON \\( 2=2 \\) /" // TODO
				),
		);

		// Checks :
		foreach($tests as $type => $data) {
			list($f, $pat) = $data;
			echo ("Testing fragments : " . $type . " ...");
			if (preg_match($pat, $sql = $f->sql()))
				echo "ok \n";
			else {
				echo "error ! " . $sql . " doesn't match " . $pat . "\n";
				return false;
			}
		}

		return true;
	}

	private function test_columns() {
		$j = gluedb::join('users', $u);
		echo $u['login'];
	}
}