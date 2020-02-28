<?php 
/*śćźżąęółć*/
abstract class Isd_Pagination extends Isd_Abs {
	protected $items_per_page;
	var $pages_number;
	var $records_num;
	
	function __construct ($items_per_page = NULL) {
		parent::__construct();
		$this->items_per_page = $items_per_page;
	}
	
	public function getCutInfoSet($page_address='', $page_no, $distance=5, $qsa='', $script_name='') {
		#jesli jest tekstem, znaczy ze chodzi o paramtetr GET
		if(!is_numeric($page_no)) {
			$page_no = $_GET[$page_no];	
		}
		
		if(!isset($page_no) || empty($page_no) || is_null($page_no)) {
			$page_no = 1;
		}
		$info = array();
		
		if(isset($page_no) && $page_no != '' && $page_no > 0 && $page_no < $this->pages_number) {
			$nextPageNo =  $page_no+1;
		} else {
			$nextPageNo = 1;
		}
		
		if(isset($page_no) && $page_no != '' && $page_no > 1) {
			$prevPageNo = $page_no-1;
		} else {
			$prevPageNo =  1;
		}
		$numberSet = $this->getPagesNumberSet($page_no);
		
		$arr = array();
		$cnt =0;
		
		foreach($numberSet as $key=>$value) {
			if($value['no'] <= $page_no && $value['no']+$distance >= $page_no) {
				$arr[$cnt] = $value;
				$cnt++;
			} elseif($value['no'] > $page_no && $value['no']-$distance <= $page_no) {
				$arr[$cnt] = $value;
				$cnt++;
			}
		
		}
		
		// czy maj± byc kropki
		if(isset($arr[0]['no']) && $arr[0]['no']!=1) {
			$left_dots = 1;
		} else {
			$left_dots = 0;
		}
		if(isset($arr[count($arr)-1]['no']) && $arr[count($arr)-1]['no']!= count($numberSet)) {
			$right_dots = 1;
		} else {
			$right_dots = 0;
		}
		
		$info['first'] = 1;
		$info['last'] = count($numberSet);
		
		$info['current_page'] = $page_no;
		$info['next'] = $nextPageNo;
		$info['prev'] = $prevPageNo;
		$info['script_name'] = $script_name;
		
		$page_address = trim_last_slash($page_address);
		$info['page_address'] = $page_address;
		$info['left_dots'] = $left_dots;
		$info['right_dots'] = $right_dots;
		$info['numberSet'] = $arr;
		$info['items_per_page'] = $this->items_per_page;
		$info['lp_factor'] = $info['current_page'] * $info['items_per_page'] - $info['items_per_page'] + 1;
		$info['qsa'] = $qsa;
		$info['records_num'] = $this->records_num;
		
		return $info;
	}
	
	#pobiera zestaw po warto¶ci zmienej GET, podanej jako parametr
	public function getDataSet($page_no) {
		if(!is_numeric($page_no)) {
			$page_no = $_GET[$page_no];	
		}
		return $this->getCurrentItems($page_no);
	}
	
	public function getCutInfoSetNum($page_address='', $current_page=1, $distance=5, $qsa='') {
		$page_no = $current_page;
		$info = array();
		
		if($page_no > 0 && $page_no < $this->pages_number) {
			$nextPageNo =  $page_no+1;
		} else {
			$nextPageNo = 1;
		}
		
		if($page_no > 1) {
			$prevPageNo = $page_no-1;
		} else {
			$prevPageNo =  1;
		}
		$numberSet = $this->getPagesNumberSet($page_no);
		
		$arr = array();
		$cnt =0;
		
		foreach($numberSet as $key=>$value) {
			if($value['no'] <= $page_no && $value['no']+$distance >= $page_no) {
				$arr[$cnt] = $value;
				$cnt++;
			} elseif($value['no'] > $page_no && $value['no']-$distance <= $page_no) {
				$arr[$cnt] = $value;
				$cnt++;
			}
		
		}
		
		// czy maj± byc kropki
		if(isset($arr[0]['no']) && $arr[0]['no']!=1) {
			$left_dots = 1;
		} else {
			$left_dots = 0;
		}
		if(isset($arr[count($arr)-1]['no']) && $arr[count($arr)-1]['no']!= count($numberSet)) {
			$right_dots = 1;
		} else {
			$right_dots = 0;
		}
		
		$info['first'] = 1;
		$info['last'] = count($numberSet);
		
		$info['current_page'] = $page_no;
		$info['next'] = $nextPageNo;
		$info['prev'] = $prevPageNo;
		$info['page_address'] = $page_address;
		$info['left_dots'] = $left_dots;
		$info['right_dots'] = $right_dots;
		$info['numberSet'] = $arr;
		$info['items_per_page'] = $this->items_per_page;
		$info['lp_factor'] = $info['current_page'] * $info['items_per_page'] - $info['items_per_page'] + 1;
		$info['qsa'] = $qsa;
		$info['records_num'] = $this->records_num;
		
		return $info;
	}
	
	#pobiera zestaw wg podanej zmiennej 
	public function getDataSetNum($num) {
		if(isset($num) && is_numeric($num)) {
			$page_no = $num; 
		} else {
			$page_no = 1;
		}
		return $this->getCurrentItems($page_no);
	}
	
	
	private function getPagesNumber () {
		//echo $this->mode;
		$set = $this->getAll($this->table_name, $this->fields, $this->mode, $this->question_params);
		
		$amount = count($set);
		$this->records_num = $amount;
		return ceil($amount/$this->items_per_page);
	}
	
	public function getCurrentItems($page_no=1) {
		if(isset($page_no) && $page_no != '' && $page_no > 0 &&  $page_no <= $this->pages_number) {
			$set = $this->getItems($page_no); 			
		} else {
			$set = $this->getItems(1);
		}
		return $set ;
	}
	
	public function getItems($page_no) {
		$first_arg = $page_no * $this->items_per_page - $this->items_per_page;
		$aqs = $this->mode." LIMIT ".$first_arg.", ".$this->items_per_page;
		
		$set = $this->getAll($this->table_name, $this->fields, $aqs, $this->question_params);
		return $set;
	}	
	
	public function getInfoSet($script_name, $get_var_name) {
		if(isset($_GET[$get_var_name]) && is_numeric($_GET[$get_var_name])) {
			$page_no = $_GET[$get_var_name]; 
		} else {
			$page_no = 1;
		}
	
		$info = array();
		
		if(isset($page_no) && $page_no != '' && $page_no > 0 && $page_no < $this->pages_number) {
			$nextPageNo =  $page_no+1;
		} else {
			$nextPageNo = 1;
		}
		
		if(isset($page_no) && $page_no != '' && $page_no > 1) {
			$prevPageNo = $page_no-1;
		} else {
			$prevPageNo =  1;
		}
		$numberSet = $this->getPagesNumberSet($page_no);
		
		$info['nextPageNo'] = $nextPageNo;
		$info['prevPageNo'] = $prevPageNo;
		$info['script_name'] = $script_name;
		$info['numberSet'] = $numberSet;
		
		return $info;
	}
		
	
	public function getPagesNumberSet($current_page_no) {
		$arr= array();
		for($i=0; $i<$this->pages_number; $i++) {
			$arr[$i]['no'] = $i+1;
			if($current_page_no==$i+1) {
				$arr[$i]['current'] = 1;
			} else {
				$arr[$i]['current'] = 0;
			}
		}
		return $arr;
	}
}
?>