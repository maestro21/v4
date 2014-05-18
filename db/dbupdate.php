<?php
/**
module 
 fields:
   type
   widget
   in_list
   search - using in text search
   index - is unique?
   null - ?
   ai - auto increment
 pk
 fk
**/
$updates = 1;
$_DB = array (

	'globals' => array (
		'fields' => array(
			'id' => array(
				'type' 		=> 'int',
				'widget' 	=> 'hidden',
				'null'		=> FALSE,
				'ai'		=> TRUE,
			),
			'name' => array(
				'type' 		=> 'string',
				'widget' 	=> 'string',
				'in_list'	=> TRUE,
				'null'		=> FALSE,
				'index'		=> TRUE,
			),
			'value' => array(
				'type' 		=> 'text',
				'widget' 	=> 'text',
				'null'		=> TRUE,
			),			
		),
		'pk' => array ('id'),
	),
	
	'module' => array(
		'fields' => array(
			'id' => array(
				'type' 		=> 'int',
				'widget' 	=> 'hidden',
				'null'		=> FALSE,
				'ai'		=> TRUE,
			),
			'name' => array(
				'type' 		=> 'string',
				'widget' 	=> 'string',
				'in_list'	=> TRUE,
				'null'		=> FALSE,
				'index'		=> TRUE
			),
			'active' => array(
				'type' 		=> 'bool',
				'widget' 	=> 'checkbox',
				'null'		=> TRUE,
			),			
		),
		'pk' => array ('id'),		
	),
	
	'users' => array(
	);		
);

function update_1($DB) {
	$var = 'delete';
	
}

/** http://php.net/manual/en/function.var-export.php **/