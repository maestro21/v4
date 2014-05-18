<?php 
include('../autoload.php');
echo "<pre>";
echo ":::::::::: DB TEST :::::::::: \n\r";
$oDB = new db('testtable');
$data = array( 'id' => 1 , 'text' => 'blablabla');

echo $oDB->getItemQuery(1)->compose()->getRawQuery() . "\n\r"; 
echo $oDB->getItemsQuery()->compose()->getRawQuery() . "\n\r";
echo $oDB->deleteItemQuery(2)->compose()->getRawQuery() . "\n\r";
echo $oDB->saveItemQuery($data)->compose()->getRawQuery() . "\n\r";
echo $oDB->insertItemQuery($data)->compose()->getRawQuery() . "\n\r";
echo $oDB->updateItemQuery($data, dbEq('id',1))->compose()->getRawQuery() . "\n\r";

$oDB->select()
	->select('testfield')
	->select('testfield','tf')
	->from('testtable')
	->from('testtable', 'tt')
	->join('testtable2',dbEq('tt.id','`testtable2.tt_id`'))
	->join('testtable3',dbEq('tt.id','`testtable2.tt_id`'), 'tt3')
	->join('testtable4',dbEq('tt.id','`tt4.tt_id`'), 'tt4','LEFT')
	->group('')
	->compose();
	
echo $oDB->getRawQuery() . "\n\r";
