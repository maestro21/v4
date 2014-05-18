<?php
require_once('schemadbquery.php');
class schemaDB extends schemaDBQuery {
	
	function compose() {
		switch($this->queryType) {
			/*case 'select':
				$this->composeSelectQuery();
			break;
			
			case 'delete':
				$this->composeDeleteQuery();
			break;

			case 'insert':
				$this->composeInsertQuery();
			break;
			
			case 'update':
				$this->composeUpdateQuery();
			break;

			case 'replace':
				$this->composeInsertQuery();
			break;*/
		}
		$this->rawQuery .= ";";
		return $this;
	}	

	/** compose queries **/	
	
	function run($debug = 0) {
		DBquery($this->rawQuery, $debug);
	}
}
