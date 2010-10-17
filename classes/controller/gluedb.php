<?php

class Controller_GlueDB extends Controller {
	public function action_test() {
		echo ("<pre>");
		$this->test_fragments();
		//$this->test_columns();
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
					gluedb::bool("'test' = ?", "qsdf")->or("'test' IN ?", array('azer', 'qsdf')),
					"('test' = 'qsdf') OR ('test' IN ('azer','qsdf'))"
				),
			'boolean - nested' => array(
					gluedb::bool(gluedb::bool("1=1")->or("2=2"))->and("3=3"),
					"((1=1) OR (2=2)) AND (3=3)"
				),
			'table' => array(
					$t = gluedb::alias('users', 'myalias'),
					"`users` AS `myalias`"
				),
			'column' => array(
					$t->login,
					"`myalias`.`login`"
				),
		);

		$select = new GlueDB_Fragment_Builder_List_Select(null);
		$select
			->init($t->login)
			->then($t->password)
			->then($t->login)->as('mylogin')
			->then($t->login)
			->then('?', 'test')
			->then('?', 'test');
		$tests['select'] = array(
			$select,
			"`myalias`.`login` AS `login`, `myalias`.`password` AS `password`, `myalias`.`login` AS `mylogin`, `myalias`.`login` AS `login2`, 'test' AS `computed`, 'test' AS `computed1`"
		);

		$select = new GlueDB_Fragment_Builder_List_Orderby(null);
		$select
			->init($t->login)
			->then($t->password)
			->then($t->login)->asc()
			->then('?', 'test')->desc();
		$tests['select'] = array(
			$select,
			"`myalias`.`login`, `myalias`.`password`, `myalias`.`login` ASC, ('test') DESC"
		);

		$join = gluedb::join('mytable')->as('t1')
					->left('yourtable')->as('t2')->on('?=?', 'test1', 'test2')->or('2=2')->and('3=3')
					->right('histable')->as('t3')->on('1=1');
		$tests['join simple'] = array(
			$join,
			"`mytable` AS `t1` LEFT OUTER JOIN `yourtable` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `histable` AS `t3` ON (1=1)"
		);

		$join2 = gluedb::join('mytable')->as('t3')
					->left($join)->on('5=5');
		$tests['join nested'] = array(
			$join2,
			"`mytable` AS `t3` LEFT OUTER JOIN (`mytable` AS `t1` LEFT OUTER JOIN `yourtable` AS `t2` ON ('test1'='test2') OR (2=2) AND (3=3) RIGHT OUTER JOIN `histable` AS `t3` ON (1=1)) ON (5=5)"
		);

		$alias = gluedb::alias('mytable','myalias');
		$join3 = gluedb::join('mytable')->as('t3')
					->left($alias)->on('1=1');
		$tests['join alias'] = array(
			$join3,
			"`mytable` AS `t3` LEFT OUTER JOIN `mytable` AS `myalias` ON (1=1)"
		);
		
		$select1 = gluedb::select('mytable')->as('test')->where("1=1")->and("2=2")->or("3=3")->andnot("4=4")->ornot("5=5")->query();
		$tests['query select basic'] = array(
			$select1,
			"SELECT * FROM `mytable` AS `test` WHERE (1=1) AND (2=2) OR (3=3) AND NOT (4=4) OR NOT (5=5)"
		);
		
		$select2 = gluedb::select('users', $u)->as('myusers')->where("$u->login = 'mylogin'")->query();
		$tests['query select alias'] = array(
			$select2,
			"SELECT * FROM `users` AS `myusers` WHERE (`myusers`.`login` = 'mylogin')"
		);		
		
		$select3 = gluedb::select('users', $a)->left('users', $b)->on("$a->login = $b->login")->query();
		$tests['query select default alias and join'] = array(
			$select3,
			"SELECT * FROM `users` AS `users__0` LEFT OUTER JOIN `users` AS `users__1` ON (`users__0`.`login` = `users__1`.`login`)"
		);			

		// Checks :
		foreach($tests as $type => $data) {
			list($f, $target) = $data;
			echo ("Testing fragments : " . $type . " ...");
			if ($f->sql() === $target)
				echo "ok \n";
			else {
				echo "error ! " . $f->sql() . " doesn't match target " . $target . "\n";
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