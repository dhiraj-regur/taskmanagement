<?php
require_once 'Isd/Grid/Abstract.php'; 

class Isd_Grid_Horizontal extends Isd_Grid_Abstract {
	public $header_is_separate = false;
	public $tr_head_attributes = '';
		
   	function __construct($params = NULL) {
	   	parent::__construct($params);
	}
	
	#zapelnianie macierzy danymi
	public function populate($data) {
		$this->grid = $data;
		$this->populateHorizontally();
		$this->populateBody();
	}
	
	public function populateHorizontally() {
		$this->html .= "<table ".$this->table_attributes.">\n";
		$this->html .= $this->tabs(1).'<tr '.$this->tr_head_attributes.'>';
		foreach($this->headers as $key => $value) {
			$this->html .= '<td>';
			$this->html .= $value;
			$this->html .= '</td>';
		}
		$this->html .= $this->tabs(1).'</tr>';
		$this->html .= '</table>';
	}
	
	public function populateBody() {
		$this->html .=  $this->tabs(1)."<tbody>\n";
		$i = 0;
		if(count($this->grid)-1 > 0 ) {
			foreach($this->grid as $key=>$value) {
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
	
}