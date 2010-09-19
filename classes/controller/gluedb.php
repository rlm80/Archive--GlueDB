<?php

class Controller_GlueDB extends Controller {
	public function action_test() {
		echo ("<pre>");
		$this->test_fragments();
	}
	
	private function test_fragments() {
		$tests = array(
			'value - string'	=> array(
					gluedb::value("test'test"),
					"'test\'test'"
				),			
			'value - integer' 	=> array(
					gluedb::value(10),	
					"10"
				),
			'value - array'	=> array(
					gluedb::value(array("test'test", 10)),
					"('test\'test',10)"
				),					
			'value - float'		=> array(
					gluedb::value(10.5),
					"10.5"
				),
			'value - boolean'	=> array(
					gluedb::value(false),
					"FALSE"
				),
			'value - null'		=> array(
					gluedb::value(null),
					"NULL"
				),
			'template - no replacements' => array(
					gluedb::template("test template"),
					"test template"
				),
			'template - two replacements' => array(
					gluedb::template("? test ? template", "test'test", 10),
					"'test\'test' test 10 template"
				),
			'boolean - simple' => array(
					gluedb::boolean("'test' = ?", "qsdf")->or("'test' IN ?", array('azer', 'qsdf')),
					"'test' = 'qsdf' OR 'test' IN ('azer','qsdf')"
				),
			'boolean - nested' => array(
					gluedb::boolean("'test' = 'test' OR ( ? )", gluedb::boolean("1 = 0")),
					"'test' = 'test' OR ( 1 = 0 )"
				),
		);
		
		// Checks :
		foreach($tests as $type => $data) {
			list($f, $res) = $data;
			echo ("Testing fragments : " . $type . " ...");
			$sql = $f->sql();
			if ($sql === $res)
				echo "ok \n";
			else {
				echo "error ! " . $sql . " instead of " . $res . "\n";
				return false;
			}			
		}

		return true;
	}
}