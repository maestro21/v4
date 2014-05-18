<?php
require_once('dbquery.php');
class db extends dbquery {
	
	function compose() {
		switch($this->queryType) {
			case 'select':
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
			break;
		}
		$this->rawQuery .= ";";
		return $this;
	}
	
	function composeSelectQuery(){
		$this->rawQuery = 'SELECT ' . implode(",\r\n ", $this->parts['select'])  . "\r\n";
		$this->rawQuery .= 'FROM ' . implode(',', $this->parts['from'])  . "\r\n";
		if($this->parts['where']) {
			$this->rawQuery .= 'WHERE 1 '  . "\r\n";
			foreach($this->parts['where'] as $where) {
				$this->rawQuery .= ' ' . $where['op'] . ' ' . $where['query']  . "\r\n";
			}
		}
		if($this->parts['join']) {
			foreach($this->parts['join'] as $join) {
				$this->rawQuery .= $join['type'] . 'JOIN ' . $join['table'] . "\r\n" . ' ON ' . $join['cond']  . "\r\n";
			}
		}
		if($this->parts['group']) {
			$this->rawQuery .= 'GROUP BY ' . implode(',', $this->parts['group'])  . "\r\n";
		}
		if($this->parts['having']) {
			$this->rawQuery .= 'HAVING 1 '  . "\r\n";
			foreach($this->parts['having'] as $having) {
				$this->rawQuery .= ' ' . $having['op'] . ' ' . $having['query']  . "\r\n";
			}
		}
		if($this->parts['order']) {
			$this->rawQuery .= 'ORDER BY ' . implode(',', $this->parts['order'])  . "\r\n";
		}
		if($this->parts['limit']) {
			$this->rawQuery .= 'LIMIT ' . implode(',', $this->parts['limit'])  . "\r\n";
		}
		return $this;
	}
	
	function composeDeleteQuery() {
		$this->rawQuery = 'DELETE FROM ' . $this->parts['from'] . ' '  . "\r\n";
		if($this->parts['where']) {
			$this->rawQuery .= 'WHERE 1 '  . "\r\n";
			foreach($this->parts['where'] as $where) {
				$this->rawQuery .= ' ' . $where['op'] . ' ' . $where['query']  . "\r\n";
			}
		}
		if($this->parts['order']) {
			$this->rawQuery .= 'ORDER BY ' . implode(',', $this->parts['order'])  . "\r\n";
		}
		if($this->parts['limit']) {
			$this->rawQuery .= 'LIMIT ' . implode(',', $this->parts['limit'])  . "\r\n";
		}
		return $this;
	}
	
	function composeUpdateQuery() {
		if($this->parts['set']) {
			$this->rawQuery = 'UPDATE ' . $this->parts['update'] . " SET";
			
			$tmp = array();	
			foreach ($this->parts['set'] as $key => $value) {
				$tmp[] = "\r\n `{$key}` = $value";
			}
			$this->rawQuery .= implode(',', $tmp);
			
			if($this->parts['where']) {
				$this->rawQuery .= "\r\n" . 'WHERE 1 ';
				foreach($this->parts['where'] as $where) {
					$this->rawQuery .= "\r\n " . $where['op'] . ' ' . $where['query'];
				}
			}
			if($this->parts['order']) {
				$this->rawQuery .= 'ORDER BY ' . implode(',', $this->parts['order'])  . "\r\n";
			}
			if($this->parts['limit']) {
				$this->rawQuery .= 'LIMIT ' . implode(',', $this->parts['limit'])  . "\r\n";
			}
		}
	}
	
	function composeInsertQuery() {
		if($this->parts['set']) {
			$this->rawQuery =  $this->parts['insert'] . ' INTO ' . $this->parts['into'] . ' SET';
			$tmp = array();	
			foreach ($this->parts['set'] as $key => $value) {
				$tmp[] = "\r\n `{$key}` = $value";
			}
			$this->rawQuery .= implode(',', $tmp);
		}
	}
			
	function run($type = 5, $debug = 0) {
		$result = FALSE;
		switch($type) {
			case 1 : $result = DBcell($this->rawQuery, $debug); break; 
			case 2 : $result = DBrow($this->rawQuery, $debug);  break; 
			case 3 : $result = DBall($this->rawQuery, $debug);  break;
			case 4 : $result = DBcol($this->rawQuery, $debug);  break; 
			case 5 : $result = DBquery($this->rawQuery, $debug);  break; 			
		}
		return $result;
	}
}

/** DATABASE FUNCTIONS **/
function DBconnect()
{
	$link = mysql_connect(HOST_SERVER, HOST_NAME , HOST_PASS) or die('cannot connect to server');
	define('HOST_LINK',$link);
	mysql_select_db(HOST_DB,$link) or die('cannot connect to database');
	mysql_query("SET CHARACTER SET 'UTF8'");
}

function DBsrvconnect()
{
	$link = mysql_connect(HOST_SERVER, HOST_NAME , HOST_PASS) or die('cannot connect to server');
	define('HOST_LINK',$link);
	mysql_query("SET CHARACTER SET 'UTF8'");
}

function DBselDB(){
	mysql_select_db(HOST_DB,HOST_LINK) or die('cannot connect to database');
		mysql_query("SET CHARACTER SET 'UTF8'");
}

function DBquery($sql, $echo = false)
{
	if($echo) print $sql;
	$res = mysql_query($sql,HOST_LINK) or die(mysql_error() . ' '.$sql);
	return $res;	
}

function DBrow($sql, $echo = false)
{
	$res = DBquery($sql, $echo); //echo $sql;
	if($res){
		$arr = mysql_fetch_assoc($res);
		striprow($arr);
		mysql_free_result($res);
		return $arr;
	}else return false;	
}

function DBcol($sql, $echo = false)
{	
	$arr = Array();
	$res = DBquery($sql, $echo);
	if($res){
		while ($row =mysql_fetch_row($res)) $arr[] = stripslashes($row[0]);	
		mysql_free_result($res);	
		return $arr;	
	}else return false;
}

function DBall($sql, $echo = false)
{
	$arr = Array();
	$res = DBquery($sql, $echo);
	//print_r($res);
	if($res){
		while ($row = @mysql_fetch_assoc($res)) $arr[] = striprow($row);
		@mysql_free_result($res);	
		//print_r($arr);
		return $arr;
	}else
		return false;
}

function DBfield($sql, $echo = false)
{
	$res = DBquery($sql, $echo);
	if($res){
		$arr = stripslashes(@mysql_result($res,0));	
		@mysql_free_result($res);	
		return $arr;
	}else	
		return false;
}

function DBcell($sql, $echo = false){
	return DBfield($sql, $echo); }

function DBnumrows($sql, $echo = false) //select
{
	$res = DBquery($sql, $echo);
	if($res){
		$arr = mysql_num_rows($res);	
		mysql_free_result($res);
		return $arr;
	}else
		return false;
}

function DBinsertId(){
	return mysql_insert_id();
}

function DBaffrows($sql, $echo = false) //insert update delete
{
	$res = DBquery($sql, $echo);
	if($res){
		$arr = mysql_affected_rows(DBquery($sql));
		mysql_free_result($res);
		return $arr;
	}else
		return false;
}

function DBfields($sql, $echo = false){ //returns fields
	$return = Array();
	$query = DBquery($sql, $echo);
	$field = mysql_num_fields( $query );   
	for ( $i = 0; $i < $field; $i++ ) {   
		$f = mysql_field_name( $query, $i );   
		$return[$f]=$f;
	}
	return $return;
}

function dbCount($field = '*', $as = ''){
	return "COUNT({$field})" . ($as !='' ? ' AS ' . $as : '');
}
// type : 1 - %var 2 - var% 3 - %var%
function dbLike($value, $type = 3) {
	if($type == (1 or 3)) $value = "%" . $value;
	if($type == (2 or 3)) $value .= "%";
	return " LIKE '$value' ";
}

function dbConcat($data) {
	return " CONCAT (" . implode(',',$data) . ") ";
}

function dbBetween($from, $to) {
	return " BETWEEN $from AND $to ";
}

function dbEq($key, $value) {
	return "`$key` = '$value'";
}
