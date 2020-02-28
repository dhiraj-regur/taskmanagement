<?php
require_once 'Isd/Grid/Abstract.php'; 

class Isd_Grid_Vertical extends Isd_Grid_Abstract {
	
	#czy pierwszy rzad jest wyrózniony, podobnie jak 
	public $first_row_is_distinct = false;
	
	public $empty_cell_value = '&nbsp;';
	
	public $wrap_td_start = '';
	
	public $wrap_td_end = '';
	
	public $tr_alt_class_name = 'alt';
	
	public $every_tr_class_name = '';
	
	public $td_head_attributes = '';
	
	#owiniecie pierwsze komórki (header)
	public $wrap_td_first_start = '';
	
	#zakonczenie owiniecia pierwsze komórki (header)
	public $wrap_td_first_end = '';
	
   	function __construct($params = NULL) {
	   parent::__construct($params);
	}
	
	#zapelnianie macierzy danymi
	public function populate($data) {
		$this->grid = $data;
		$this->_countRowsNum();
		
		$this->html .= "<table ".$this->table_attributes.">\n";
		$this->populateHeader();
		$this->populateBody();
		$this->html .= "</table>";
	}
	
	public function populateHeader() {
		if($this->first_row_is_distinct) {
			$this->html .=  $this->tabs(1)."<thead ".$this->thead_attributes.">\n";
			$this->html .=  $this->tabs(2)."<tr>\n";
			$this->html .= 	$this->_getTheadCells();
			$this->html .=  $this->tabs(2)."</tr>\n";
			$this->html .=  $this->tabs(1)."</thead>\n";	
		}	
	}
	
	public function populateBody() {
		$this->html .=  $this->tabs(1)."<tbody>\n";
		$i = 0;
		if($this->rows_num > 0 ) {
			foreach($this->headers as $key=>$value) {
				$i++;
				$alt_class = $this->_getSwitchName($i, '', $this->tr_alt_class_name);
				$this->html .= $this->tabs(2)."<tr class=\"".$alt_class." ".$this->every_tr_class_name."\">\n";
				$this->html .= $this->_getBodyVerticalRow();
				$this->html .= $this->tabs(2)."</tr>\n";
				$this->rows_cnt ++;
			}
		} else {
			$this->html .= $this->getNoDataTr();	
		}
		$this->html .= $this->tabs(1)."</tbody>\n";
		//echo htmlspecialchars($this->html);
	}
	
	private function _getBodyVerticalRow() {
		$str = '';	
		for($i = 0;  $i <= $this->rows_num; $i++) {
			if($i==0) {
				$str .= $this->tabs(3).'<td '.$this->td_head_attributes.'>'.$this->wrap_td_first_start.$this->headers[$this->rows_cnt].$this->wrap_td_first_end."</td>\n";
			} else {
				$str .= $this->tabs(3).'<td '.$this->td_attributes.'>'.$this->wrap_td_start.$this->grid[$i-1]['values'][$this->rows_cnt]['value'].$this->wrap_td_end."</td>\n";
			}
		}
		return $str;
	}
	
	#wartosc dla pustej pierwsze komórki w trybie V
	public function setEmptyCell($str) {
		$this->empty_cell_value = $str;
	}
	
	public function firstRowIsDistinct($distinct = true) {
		$this->first_row_is_distinct = $distinct;	
	}
	
	protected function _getTheadCells() {
		$str = '';	
		$str .= $this->tabs(3)."<th ".$this->td_head_attributes.">".$this->empty_cell_value."</th>\n";

		foreach($this->grid as $k=>$v) {
			$str .=  $this->tabs(3)."<th ".$this->th_attributes.">".@$v['group_name']."</th>\n";
		}
		return  $str;
	}
}
?>