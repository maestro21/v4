<?php 
require_once('functions.php');
class schemaDBQuery {

	public $parts = array();
	public $queryType;
	public $table;
	public $rawQuery;
	
	
	function __construct($table = '') {
		$this->table = $table;
	}
	
	function createTable($type, $part) {
		$this->parts['action'] = 'create';
		$this->parts['element'] = $type;
		$this->parts['part'] = '';
		return $this;
	}	
}

function buildSchema() {
	include('../db/dbupdate.php');
	for($i = 0; $i < $total; $i++) {
		$f = 'update_' . $i;
		$DB = $f($DB);
	}
	$code = '<?php $DB = ' . var_export($DB);
	file_put_contents('../db/schema.php');
}
