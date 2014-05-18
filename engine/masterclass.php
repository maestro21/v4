<?php

class masterclass{
  	public $id;
	public $className;
	public $get;
	public $post;
	public $limit;
	public $content = '';
	public $do = '';
	public $fields = '';
	public $options = '';
	public $page = 0;
	public $perpage = 20;
	public $pages = 1;
	public $prefix = BASE_PATH; //'?q=';
	public $sql = array();
	public $tpl = 'default';
	public $adminMode = false;
	public $params = array();
	public $orderby = '';
	public $rights = array();
	public $files = '';
	public $defmethod = 'items';
	public $title = '';
	public $buttons = array(); 

	
  	function __construct($className = ''){
  		global $_GET, $_POST, $_SESSION, $_PATH, $_FILES, $_REQUEST , $_DB;
		$this->db 		= new mysql();
		$this->schema	= & $_DB;
		
  		$this->get 		= & $_GET;
		$this->request 	= & $_REQUEST;
  		$this->post 	= & $_POST;
		$this->session 	= & $_SESSION;
		$this->path 	= & $_PATH;
		$this->files 	= & $_FILES;
		
		$this->className= ($className != '' ? $className : (@$_PATH[0]!=''?$_PATH[0]:get_class($this)) );
		$this->do 		= (@$_PATH[1]!=''?$_PATH[1]:''); 
		$this->id 		= (isset($this->post['id'])?$this->post['id']:@$_PATH[2]);
		$this->page 	= (int)@$_PATH[2];
		$this->search 	= @$_PATH[3];

		$this->tpl 		= (file_exists( "tpl/".$this->className.".html")?$this->className:$this->tpl);		
		$this->title 	= ($this->id!=''?T(S($this->className)) .' #'.$this->id:T($this->className));
		
		$this->setOptions();
		$this->setButtons();
		$this->extend();
		$this->preParse();
		
  	}
	
	function setButtons(){
		$this->buttons = array(
			'admin' => array( 'items' => 'list', 'item' => 'add' ),
			'view'  => array( 'items' => 'list', 'item/'.$this->id => 'edit' ),
			'table' => array( 'item/{id}' => 'edit',  'view/{id}' => 'view', ),
		);			
	}
	
	function setOptions() {}
	
	function checkRights(){ 	
		return true;		
	}
	
	function getFields(){	
		if(isset($this->schema[$this->className])) 
			return $this->schema[$this->className]['fields'];
				
	}
	
	
  	function items(){
		$cc = '';
		$oQuery = $this->db->getItemsQuery();
		$oCountQuery = $this->db->getCountItemsQuery();

		if($this->search!=''){
			foreach($this->schema as $k => $v){
				if($v['search']) {
					$oQuery->where(dbEq($k,$v));
					$oCountQuery->where(dbEq($k,$v));
				}
			}		
		}
		$this->pages = ceil(DBfield($this->sql['count'].$cc) / $this->perpage);
		
		/** filter **/
		$filter = explode("_",getVar('sort_'.$this->className)); 
		if(isset($this->fields[$filter[0]]) && ($filter[1]=='ASC' || $filter[1] == 'DESC')) {
			
		}
		
		return DBall($this->sql['items'].$cc);
  	}


	
    function item(){	
		if($this->id>0){ 
			$aData = $this->view();
			foreach ($aData as $key => $value){
				if(isset($this->schema[$key])) { 
					$this->schema[$key]['value'] = $value;
				}
			}
		}
		return $this->schema;
    }
     
    function view(){
		return DBrow($this->db->getItemQuery($this->id)->compose()->getRawQuery());
    }     

     
    /** save item **/
    function save($check=false){
     	$res = ''; $tmp = array();
     	foreach ($this->post['form'] as $k=>$v){
		if($this->fields[$k]['type']=='pass' && trim($v)=='') continue;
     	 	$tmp[] = "`$k`='".sqlFormat($this->fields[$k]['type'],$v)."'";
     	 } 
		 
		 $data = join(",",$tmp);
		 if($this->id>0){ 
			$sql = sprintf($this->sql['update'],$data); 
			return DBquery($sql);
		 }else{ 	
			$sql = sprintf($this->sql['insert'],$data);
			$ret = DBquery($sql);
			if($ret) $this->id = DBinsertId();
			return $ret;
		 }         
     }

     
    /** delete item **/
    function del(){
        return DBquery($this->sql['delete']);
    }

	function preParse(){ 
		$do =  $this->do; if($do == '') $do = $this->do = $this->defmethod; $data = ''; 
	
		if($this->checkClass() AND $this->checkRights()){   
			if(method_exists($this,$do)){ 
				$data = $this->$do(); 
				if(isset($data['id'])) unset($data['id']);
				//inspect($data); die();
			}		
			//inspect($this->params);	
			$this->params['data']	 = $data;
			
			$this->params['class']	 = $this->className;	
			$this->params['do']		 = $this->do; 
			$this->params['id']		 = $this->id;
			$this->params['pre']	 = $this->prefix;    //prefix in case .htaccess don't work
			$this->params['fields']  = $this->fields;
			$this->params['p']		 = $this->page;
			$this->params['pages']   = $this->pages;
			$this->params['search']  = $this->search;
			$this->params['options'] = $this->options;
			$this->params['rights']	 = $this->rights;
			$this->params['buttons'] = $this->buttons;
			$this->params['path']	 = $this->path;
			$this->params['langs']	 = getVar('langs');
			//inspect($this->params);
		}
		
	}
     
     // parse content
     function parse(){ 		
		$this->content = tpl( $this->tpl , $this->params,$this->adminMode);			
		return $this->content;
     }
     
     
     
     // инсталляция
     function ini(){		
	$sql = "DROP TABLE IF EXISTS `{$this->className}`";
	DBquery($sql) or die (mysql_error() . $sql);
	$sql = "CREATE TABLE `{$this->className}`(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY";
			foreach ($this->fields as  $field){
				$type = '';
				switch($field['type']){
					case 'pass' :
					case 'blob': $type = ' BLOB'; break;
					case 'text': $type = ' TEXT'; break;
					case 'int' : $type = ' INT DEFAULT 0'; break;
					case 'date' : $type = ' DATETIME'; break;
					case 'float' : $type = ' FLOAT DEFAULT 0'; break;					
				}
				//echo $type. ' '.$tmp[0];
				if($type!='' && $field['name']!='') $sql .= ",`".$field['name']."` $type";
			}
	$sql.=	");";
	DBquery($sql) or die (mysql_error() . $sql);	
	
	return true;	
     }

	//пустая функция для расширения наследниками, вызывается в конце __construct
	function extend(){}		

	
}


class tree{
	var $treeTMPList = Array(); //МАССИВ ДЕТЕЙ
	var $treeList = Array(); //МАССИВ ДЕРЕВА
	var $options = Array('---');
	
	function __construct($data = '') {
		if(is_array($data)) {
			$data = $this->fetch($data);
			$this->fetchDraw($data);
		}
	}
	
	function getPathToRoot($id, $ret = array()) { 
		$ret[] = $id;
		if($this->treeTMPList[$id]['id'] > 0)
			$ret = $this->getPathToRoot($this->treeTMPList[$id]['pid'], $ret);
		return $ret;	
	}
	
	function fetch($data){
		//inspect($data);
		foreach ($data as $k=>$row){	
			foreach ($row as $k=>$v) $this->treeTMPList[$row['id']][$k] = $v; //writing data to current element
			$this->treeTMPList[$row['pid']]['_children'][] = $row['id']; // grouping all children;						
		}
		//inspect($this->treeTMPList);
		
		$this->treeList	= $this->branch(0); //building array
		//inspect($this->treeList);
		return $this->treeList;
	}

	function branch($id) //returns single branch based on parent id
	{
		$tmpArr = Array();
	
		//echo $id .'=>';print_r($this->treeTMPList[$id]['_children']);
		if(sizeof(@$this->treeTMPList[$id]['_children'])>0)
			foreach ($this->treeTMPList[$id]['_children'] as $child)
			{					
				$tmpArr[$child] = $this->treeTMPList[$child];
				unset($tmpArr[$child]['_children']);
				$tmpArr[$child]['children'] = $this->branch($child);			
			}	
		
		return $tmpArr;		
	}
	
	
	function fetchDraw($data,$lvl=-1){
		 $lvl++;
		foreach ($data as $row){
			for($i=0;$i<$lvl;$i++) $row['name'] ="--".$row['name'];
			$this->options[$row['id']] = $row['name'];
			if($row['children']!='') $this->fetchDraw($row['children'],$lvl);
		}
	}
}

?>