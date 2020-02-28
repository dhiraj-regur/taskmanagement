<?php 
/*śćźżąęółć*/
/* klasa matematyczna, zwraca tylko liczby do wyrażenie SQL */
class Isd_Pagination_Calendar extends Isd_Pagination {
	public $page = 1;
	public $current_page = 1;
	public $total_num = 0;
	public $total_pages_num = 120;
	public $pagination_class = 'pagination-control-classic';
	public $base_url = '';
	public $page_param = 'page';
	public $current_year = '';
	public $current_month = '';
	public $current_month_days = array();
	
	#id kontenera
	public $ponto = '';
	
	#ile max cyferek linkowych
	public $numbers_threshold = 10;
	
	public $dates = array();
	
	#nie ma items_per_page bo zakldamy ze bedzie tyle ile dni w miesiacu
	function __construct () {
		parent::__construct();
	}
	
	public function getItems($page_no) {
		if(!is_numeric($page_no) || $page_no<1 || $page_no>$this->total_pages_num) return array();
		$this->current_page = $page_no;
		$this->dates =  array();
		$this->setCurrentDays( $page_no );
		for($i = 1; $i <= $this->current_month_days; $i++) {
			$day = $this->_getDayRight($i);
			$date_str = $this->current_year.'-'.$this->current_month.'-'.$day;
			$tab = array();
			$tab['date'] = $date_str;
			$tab['weekday_num'] = $this->_getWeekday($date_str);
			$tab['weekday_name_full'] = $this->_getWeekdayNameFull($tab['weekday_num']);
			$tab['weekday_name_short'] = $this->_getWeekdayNameShort($tab['weekday_num']);
			$tab['is_weekend'] = $this->_isWeekend($tab['weekday_num']);
			$this->dates[] = $tab;
		}
		return $this->dates;
	}
	
	private function _getWeekday($date) {
		//setlocale(LC_ALL, 'pl_PL');
		$weekday = date('w', strtotime($date));
		return $weekday;
	}
	
	private function _getWeekdayNameFull($day_num) {
		$str = '';
		switch($day_num) {
			case 1:
				$str = "poniedziałek";
			break;
			case 2:
				$str = "wtorek";
			break;
			
			case 3:
				$str = "środa";
			break;
			
			case 4:
				$str = "czwartek";
			break;
			
			case 5:
				$str = "piątek";
			break;
			
			case 6:
				$str = "sobota";
			break;
			
			case 0:
				$str = "niedziela";
			break;
		}
		
		return $str;
	}
	
	private function _getWeekdayNameShort($day_num) {
		$str = '';
		switch($day_num) {
			case 1:
				$str = "pon";
			break;
			case 2:
				$str = "wt";
			break;
			
			case 3:
				$str = "śr";
			break;
			
			case 4:
				$str = "czw";
			break;
			
			case 5:
				$str = "pt";
			break;
			
			case 6:
				$str = "sb";
			break;
			
			case 0:
				$str = "ndz";
			break;
		}
		
		return $str;
	}
	
	private function _isWeekend($weekday_num) {
		if($weekday_num==6 || $weekday_num==0) return 1;
		return 0;
	}
	
	private function _getDayRight($i) {
		if($i<10) return '0'.$i;
		return $i;
	}
	
	private function setCurrentDays($page_num) {
		$months_back = $page_num - 1;
		$date = date("Y-m-d");
		
		//ee($months_back, '$months_back');
		if($months_back==0) {
			$this->current_month = date('m'); 
			$this->current_year = date('Y'); 
		} else {
			$month_back_d = strtotime(date("Y-m-d", strtotime($date)) . " -".$months_back." month");
			//ee($month_back_d, '$month_back_d');
			$this->current_month = date("m", $month_back_d);
			$year_back_d = strtotime(date("Y-m-d", strtotime($date)) . " - ".$months_back." month");
			//ee($year_back_d, '$year_back_d');
			$this->current_year = date("Y", $year_back_d);
		}
		
		//ee($this->current_month, 'obecny miesiac');
		//ee($this->current_year, 'obecny rok');
		
		$this->current_month_days = cal_days_in_month(CAL_GREGORIAN, $this->current_month, $this->current_year);
		//ee($this->current_month_days);
	}
	
	public function getLinks() {
		return $this->_getLinksHtml();	
	}
	
	private function _getLinksHtml() {
		$str = '';
		$str .= '<div class="'.$this->pagination_class.'">';
		//$str .= $this->tabs(1).'<div class="pagination-visible-items">'.$this->_getPaginationVisibleItems($this->total_num).'</div>';
		$str .= $this->tabs(1).'<ul>';
		$str .= $this->_getFirstPagHtml();
		$str .= $this->_getPrevPagHtml();
		$str .= $this->_getPageNumbersHtml();
		$str .= $this->_getNextPagHtml();
		$str .= $this->_getLastPagHtml();
		$str .= $this->tabs(1).'</ul>';
		$str .= '</div>';
		return $str;	
	}
	

################################################################
###### HTML
################################################################

	private function _getFirstPagHtml() {
		if($this->current_page == 1) {
			$str = ' <li class="pagination-first"><span class="disabled">&laquo; Pierwsza</span></li>';	
		} else {
			$str = ' <li class="pagination-first"><a href="'.$this->base_url.$this->page_param.'/1">&laquo; Pierwsza</a></li>';		
		}
		
		return $str;
	}
	
	private function _getPrevPagHtml() {
		$prev = $this->current_page - 1;
		if($this->current_page <= 1) {
			$str = ' <li class="pagination-previous"><span class="disabled">< Poprzednia</span></li>';	
		} else {
			$str = ' <li class="pagination-previous"><a href="'.$this->base_url.$this->page_param.'/'.$prev.'">< Poprzednia</a></li>';		
		}
		
		return $str;
	}
	
	private function _getPageNumbersHtml() {
		$str = '';
		
		$first_num = $this->current_page - ceil($this->numbers_threshold/2);
		
		if($first_num>1) {
			$last_num = $first_num + $this->numbers_threshold;
			if($last_num > $this->total_pages_num) $last_num = $this->total_pages_num;
		} else {
			$first_num =  1;
			$last_num = $this->numbers_threshold;
		}
		for($i = $first_num; $i <= $last_num; $i++) {
			$str .= '<li class="pagination-item">';
			if($i == $this->current_page) {
				$str .= '<span class="current">'.$i.'</span>';	
			} else {
				$str .= '<a href="'.$this->base_url.$this->page_param.'/'.$i.'">'.$i.'</a>';
			}
		}
		
		return $str;
	}
	
	private function _getNextPagHtml() {
		$next = $this->current_page + 1;
		if($this->current_page == $this->total_pages_num) {
			$str = ' <li class="pagination-next"><span class="disabled">Następna ></span></li>';	
		} else {
			$str = ' <li class="pagination-next"><a href="'.$this->base_url.$this->page_param.'/'.$next.'">Następna ></a></li>';		
		}
		
		return $str;
	}
	
	
	
	private function _getLastPagHtml() {
		if($this->page == $this->total_pages_num) {
			$str = ' <li class="pagination-previous"><span class="disabled">Ostatnia &raquo;</span></li>';	
		} else {
			$str = ' <li class="pagination-previous"><a href="'.$this->base_url.$this->page_param.'/'.$this->total_pages_num.'">Ostatnia &raquo;</a></li>';		
		}
		
		return $str;
	}
}
?>