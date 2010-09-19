<?php

Route::set('glue_test', 'glue/test')
	->defaults(array(
		'controller' => 'GlueDB',
		'action'     => 'test',
	));	