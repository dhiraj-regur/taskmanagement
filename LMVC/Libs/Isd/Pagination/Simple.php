<?php 
/*śćźżąęółć*/
/* klasa matematyczna, zwraca tylko liczby do wyrażenie SQL */
class Isd_Pagination_Simple extends Isd_Pagination {
	public $page = 1;
	public $total_num = 0;
	public $total_pages_num = 0;
	public $links_type = 'html';
	public $pagination_class = 'pagination-control-classic';
	public $base_url = '';
	public $page_param = 'page';
	public $ajax_function = '';
	
	#id kontenera
	public $ponto = '';
	
	#ile max cyferek linkowych
	public $numbers_threshold = 10;
	
	function __construct ($items_per_page) {
		parent::__construct($items_per_page);	
	}
	
	public function actAsAjax() {
		$this->links_type = 'ajax';	
	}
	
	private function _setTotalPagesNum() {
		$this->total_pages_num = ceil($this->total_num/$this->items_per_page);	
	}
	
	public function getLimit($page=NULL, $with_limit=true) {
		if($page<=0) $page = 1;
		if($page==NULL) {
			if($with_limit) {
				return 'LIMIT 0,'.$this->items_per_page;
			} else {
				return '0,'.$this->items_per_page;	
			}
		}
		$this->page = $page;
		$str = '';
		if($with_limit) $str .= 'LIMIT ';
		$str .= (($page * $this->items_per_page) - $this->items_per_page).','.$this->items_per_page;
		return $str;
	}
	
	public function getLinks($total_num) {

		$this->total_num = $total_num;
		$this->_setTotalPagesNum();
		
		if($this->links_type == 'html') {
			return $this->_getLinksHtml();	
		} else {
			return 	$this->_getLinksAjax();	
		}
	}
	
	private function _getLinksHtml() {
		$str = '';
		$str .= '<div class="'.$this->pagination_class.'">';
		$str .= $this->tabs(1).'<div class="pagination-visible-items">'.$this->_getPaginationVisibleItems($this->total_num).'</div>';
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
	
	private function _getLinksAjax() {
		$str = '';
		$str .= '<div class="'.$this->pagination_class.'">';
		$str .= $this->tabs(1).'<div class="pagination-visible-items">'.$this->_getPaginationVisibleItems($this->total_num).'</div>';
		$str .= $this->tabs(1).'<ul>';
		$str .= $this->_getFirstPagAjax();
		$str .= $this->_getPrevPagAjax();
		$str .= $this->_getPageNumbersAjax();
		$str .= $this->_getNextPagAjax();
		$str .= $this->_getLastPagAjax();
		$str .= $this->tabs(1).'</ul>';
		$str .= '</div>';
		return $str;		
	}
	
	private function _getPaginationVisibleItems() {
		$first_num = ($this->page * $this->items_per_page) - $this->items_per_page;
		$sec_num = ($this->page * $this->items_per_page);
		return $first_num.' - '.$sec_num.' z '.$this->total_num;	
	}
	

################################################################
###### HTML
################################################################

	private function _getFirstPagHtml() {
		if($this->page == 1) {
			$str = ' <li class="pagination-first"><span class="disabled">&laquo; Pierwsza</span></li>';	
		} else {
			$str = ' <li class="pagination-first"><a href="'.$this->base_url.$this->page_param.'/1">&laquo; Pierwsza</a></li>';		
		}
		
		return $str;
	}
	
	private function _getPrevPagHtml() {
		$prev = $this->page - 1;
		if($this->page <= 1) {
			$str = ' <li class="pagination-previous"><span class="disabled">< Poprzednia</span></li>';	
		} else {
			$str = ' <li class="pagination-previous"><a href="'.$this->base_url.$this->page_param.'/'.$prev.'">< Poprzednia</a></li>';		
		}
		
		return $str;
	}
	
	private function _getPageNumbersHtml() {
		$str = '';
		
		$first_num = $this->page - ceil($this->numbers_threshold/2);
		
		if($first_num>1) {
			$last_num = $first_num + $this->numbers_threshold;
			if($last_num > $this->total_pages_num) $last_num = $this->total_pages_num;
		} else {
			$first_num =  1;
			$last_num = $this->numbers_threshold;
		}
		
		for($i = $first_num; $i <= $last_num; $i++) {
			$str .= '<li class="pagination-item">';
			if($i == $this->page) {
				$str .= '<span class="current">'.$i.'</span>';	
			} else {
				$str .= '<a href="'.$this->base_url.$this->page_param.'/'.$i.'">'.$i.'</a>';
			}
		}
		
		return $str;
	}
	
	private function _getNextPagHtml() {
		$next = $this->page + 1;
		if($this->page == $this->total_pages_num) {
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


################################################################
###### AJAX
################################################################
	
	private function _getFirstPagAjax() {
		if($this->page == 1) {
			$str = ' <li class="pagination-first"><span class="disabled">&laquo; Pierwsza</span></li>';	
		} else {
			$str = ' <li class="pagination-first"><span class="as_href" onclick='.$this->ajax_function.'(\''.$this->base_url.$this->page_param.'/1\')>&laquo; Pierwsza</span></li>';		
		}
		
		return $str;
	}
	
	private function _getPrevPagAjax() {
		$prev = $this->page - 1;
		if($this->page <= 1) {
			$str = ' <li class="pagination-previous"><span class="disabled">< Poprzednia</span></li>';	
		} else {
			$str = ' <li class="pagination-previous"><span class="as_href" onclick='.$this->ajax_function.'(\''.$this->base_url.$this->page_param.'/'.$prev.'\')>< Poprzednia</span></li>';		
		}
		
		return $str;
	}
	
	private function _getPageNumbersAjax() {
		$str = '';
		$first_num = $this->page - ceil($this->numbers_threshold/2);
		
		if($first_num>1) {
			$last_num = $first_num + $this->numbers_threshold;
			if($last_num > $this->total_pages_num) $last_num = $this->total_pages_num;
		} else {
			$first_num =  1;
			$last_num = $this->total_pages_num < $this->numbers_threshold ? $this->total_pages_num : $this->numbers_threshold;
		}
		
		for($i = $first_num; $i <= $last_num; $i++) {
			$str .= '<li class="pagination-item">';
			if($i == $this->page) {
				$str .= '<span class="current">'.$i.'</span>';	
			} else {
				$str .= '<span class="as_href" onclick="'.$this->ajax_function.'(\''.$this->base_url.$this->page_param.'/'.$i.'\')">'.$i.'</span>';
			}
		}
		
		return $str;
	}
	
	private function _getNextPagAjax() {
		$next = $this->page + 1;
		if($this->page == $this->total_pages_num) {
			$str = ' <li class="pagination-next"><span class="disabled">Następna ></span></li>';	
		} else {
			$str = ' <li class="pagination-next"><span class="as_href" onclick='.$this->ajax_function.'(\''.$this->base_url.$this->page_param.'/'.$next.'\')>Następna ></span></li>';		
		}
		
		return $str;
	}
	
	
	
	private function _getLastPagAjax() {
		if($this->page == $this->total_pages_num) {
			$str = ' <li class="pagination-previous"><span class="disabled">Ostatnia &raquo;</span></li>';	
		} else {
			$str = ' <li class="pagination-next"><span class="as_href" onclick='.$this->ajax_function.'(\''.$this->base_url.$this->page_param.'/'.$this->total_pages_num.'\')>Ostatnia &raquo;</span></li>';	
		
		}
		
		return $str;
	}
	

}
?>