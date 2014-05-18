<?php 
require_once('functions.php');
class DBquery {

	public $parts = array();
	public $queryType;
	public $table;
	public $rawQuery;
	
	
	function __construct($table = '') {
		$this->table = $table;
	}
	
	function setTable($table) {
		$this->table = $table;
	}
	
	function getTable() {
		return $table;
	}
	
	/** types **/
	
	function select($query = '*', $shortname = '') {
		$this->queryType = 'select';		
		if ($shortname == '')
			$this->parts['select'][] = $query;
		else 
			$this->parts['select'][$shortname] = $query . ' AS ' . $shortname;		
		return $this;
	}
	
	function delete() {
		$this->queryType = 'delete';
		return $this;
	}
	
	function update($table) {
		$this->queryType = 'update';
		$this->parts['update'] = "`$table`";
		return $this;
	}
	
	function insert($ignore = FALSE) {
		$this->queryType = 'insert';
		$this->parts['insert'] = 'INSERT';
		if($ignore) $this->parts['insert'] .= ' IGNORE';
		return $this;
	}
	
	function replace() {
		$this->queryType = 'insert';
		$this->parts['insert'] = 'REPLACE';
		return $this;
	}
	
	/** vars **/
	
	// select & delete & update
	function from($table, $shortname = '') {
		if($this->queryType == 'select') {
			if ($shortname == '') {
				$this->parts['from'][] = "`$table`";
			} else {
				$this->parts['from'][$shortname] = "`$table` `$shortname`";
			}
		} else {
			$this->parts['from'] = "`$table`";
		}
		return $this;
	}	
		
	function where($query, $op = 'AND') {
		$this->parts['where'][] = array( 'op' => $op, 'query' => $query );
		return $this;
	}
	
	function order($query) {
		$this->parts['order'][] = $query;
		return $this;
	}
	
	function limit($start, $end) {
		$this->parts['limit'] = array( 'start' => $start, 'end' => $end );
		return $this;
	}
	
	// select	
	function join($table, $cond, $shortname = '',$type = '') {
		if($type != '') $type .= ' ';
		if ($shortname == '')
			$this->parts['join'][] = array( 'type' => $type, 'cond' => $cond, 'table' => $table);
		else 
			$this->parts['join'][$shortname] = array( 'type' => $type, 'cond' => $cond, 'table' =>  $table . ' AS ' . $shortname);
		return $this;
	}
	
	function group($query) {
		$this->parts['group'][] = $query;
		return $this;
	}
	
	function having($query, $op = 'AND') {
		$this->parts['having'][] = array( 'op' => $op, 'query' => $query );
		return $this;
	}
	
	// insert & replace & update
	function into($table) {
		$this->parts['into'] = "`$table`";
		return $this;
	}	
	
	function set($key, $value) {
		$this->parts['set'][$key] = "'$value'";
		return $this;
	}	
	
	/** system functions **/
	
	function run() {}
	function compose() {}	
	
	function clear() {
		$this->parts = array();
		$this->rawQuery = '';
		return $this;
	}
	
	function getRawQuery() {
		$this->compose();
		return $this->rawQuery;
	}
	
	/** default queries **/
	
	function getItemQuery($id, $query = '*') {
		$id = (int) $id;
		if($id > 0) {		
			$this->clear()
			
				 ->select($query)
				 ->from($this->table)
				 ->where("id = $id");
				 
		}
		return $this;
	}
	
	function getCountItemsQuery() {
		$this->clear()
		
			 ->select(dbCount('id'))
			 ->from($this->table);
			 
		return $this;	 
	}
	
	
	function getItemsQuery($query = '*', $where = FALSE, $page = 0, $perpage = 10) {	
		$this->clear()
		
			 ->select($query)
			 ->from($this->table)
			 ->limit($page * $perpage, ($page + 1) * $perpage);
		
		return $this;	 
	}
	
	function deleteItemQuery($id) {
		$id = (int) $id;
		if($id > 0) {
			$this->clear()
			
				 ->delete()
				 ->from($this->table)
				 ->where("id = $id");
				 
		}
		return $this;
	}
	
	function saveItemQuery($params = array()) {
		if(sizeof($params > 0)) {
			$this->clear()->replace()->into($this->table);
			foreach ($params as $key => $value) {
				$this->set($key, $value);
			}
		}
		return $this;
	}
	
	function insertItemQuery($params = array()) {
		if(sizeof($params > 0)) {
			$this->clear()->insert()->into($this->table);
			foreach ($params as $key => $value) {
				$this->set($key, $value);
			}
		}
		return $this;
	}
	
	function updateItemQuery($params = array(), $query) {
		if(sizeof($params > 0)) {
			$this->clear()
			
				->update($this->table)
				->where($query);	
				
			foreach ($params as $key => $value) {
				$this->set($key, $value);
			}
		}
		return $this;
	}
	
	function test() {
		
	}
}