<?php
class Isd_Order {
	public $possible_fields = array();
	public $request;
	public $current_order = NULL;
	public $current_by = 'ASC';
	public $current_url = '';
	private $key = 'order_349234923er923e2';
	public $default_by = '';
	private $base_link = "";
	
	function __construct($possible_fields = array(), $request, $base_link = "", $default_by = "ASC") {
		$this->default_by = $default_by; 
		$this->possible_fields = $possible_fields;
		$this->request = $request;
		$this->setCurrent();
		$this->base_link = $base_link;
		
	}
	
	private function setCurrent() {
		$by = strtoupper($this->request->getParam('by'));
		$this->current_order = $this->request->getParam('order');
		$fields_keys = array_keys($this->possible_fields);
		
		if($this->current_order==NULL) $this->current_order = $fields_keys[0];
	
		if($by!='ASC' && $by!='DESC') $by = $this->default_by;
		$this->current_by = $by;
		$this->current_url = $this->request->getRequestUri();
		if(!isset($_SESSION[$this->key])) {
			$_SESSION[$this->key] = array();
		}
	}
	
    public function current($field) {
		if($this->current_order != NULL && $this->current_order == $field ) {
			$this->toggle($field);
			return ' class="current"';
		}
		else return '';
	}
	
	public function toggle($field) {
		if(!isset( $_SESSION[$this->key][$field] )) {
			$_SESSION[$this->key][$field] = $this->default_by;
			return;
		}
		
		if( $_SESSION[$this->key][$field] == 'ASC' )  $_SESSION[$this->key][$field] = 'DESC';
		else $_SESSION[$this->key][$field] = 'ASC';
		
	}
	
	private function _getBy($field) {	
		if( isset($_SESSION[$this->key][$field]) ) return $_SESSION[$this->key][$field];
		else return $this->default_by;
	}
	
	public function link($field) {
		$link = $this->_getLink($field);
		//ee($link);
		$sign = $this->_getSign($field);
		return '<a href="'.$link.'">'.$sign.$this->possible_fields[$field].'</a>';
	}
	
	private function _getSign($field) {
		if($this->_getBy($field) == 'ASC' ) return '&uarr;';
		else return '&darr;';
	}
	
	private function _getLink($field) {
		$arr = Isd_Arrayer::real_explode('/', $this->current_url);
		if($this->base_link != "" && !in_array($this->base_link, $arr)) {
			$arr[] =  $this->base_link;	
		}
		if(!in_array($field, array_keys($this->possible_fields))) return '';
		
		//ee($this->_getBy($field));
		
		$new_arr = array();
		#podstawowy url
		$new_arr[] = $arr[0];
		$found_order = false;
		$found_by = false;
		if(is_array($arr) && count($arr) > 0) {
			for($i = 1; $i < count($arr); $i++) {
				if($arr[$i] == 'order' && in_array($field, array_keys($this->possible_fields))) {
					$new_arr[$i] = 'order';	
					$new_arr[$i+1] = $field;
					$found_order =  true;
					$i ++;
					continue;
				} elseif ($arr[$i]=='by') {
					$new_arr[$i] = 'by';	
					$new_arr[$i+1] = $this->_getBy($field);
					$found_by =  true;
					$i ++;
					continue;
				}  else {
					$new_arr[$i] = $arr[$i];			
				}
			}
		}
		
		if(!$found_order) {
			$new_arr[] = 'order';	
			$new_arr[] = $field;
		}
		
		if(!$found_by) {
			$new_arr[] = 'by';	
			$new_arr[] = $this->_getBy($field);
		}
		
		//ee($new_arr);
		
		return implode('/', $new_arr);
	}
}
/*
$order = new Isd_Order(array('pbaid'), $this->getRequest());
$mapper = new Pba_Companies_Mapper();
$mapper->setBasics($order);
$this->view->order = $order;
<td width="25" <?php echo $this->order->current('pbaid')?>><?php echo $this->order->link('pbaid') ?></td>

*/
?>