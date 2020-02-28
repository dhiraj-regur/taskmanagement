<?php
require_once 'Isd/Grid/Vertical.php';

class Isd_Grid_Vertical_SimplePairs extends Isd_Grid_Vertical {
	public $wrap_td_start = '<span>';
	public $wrap_td_end = '</span>';
	
	public $wrap_td_first_start = '<span>';
	public $wrap_td_first_end = '</span>';
	
	#klucz tablicy ma byc wyswietlony
	public $simple_mode = true;
	
   	function __construct($params=NULL) {
	   parent::__construct($params);
	 
	}
	
	public function populateBody() {
		$this->html .=  $this->tabs(1)."<tbody>\n";
		$i = 0;

		if($this->rows_num > 0 ) {
			foreach($this->grid as $key=>$value) {
				$i++;
				$alt_class = $this->_getSwitchName($i, '', $this->tr_alt_class_name);
				$this->html .= $this->tabs(2)."<tr class=\"".$alt_class." ".$this->every_tr_class_name."\">\n";
				$this->_getBodyVerticalRow($key, $value);
				$this->html .= $this->tabs(2)."</tr>\n";
				$this->rows_cnt ++;
			}
		} else {
			$this->html .= $this->getNoDataTr();	
		}
		$this->html .= $this->tabs(1)."</tbody>\n";
	}
	
	protected function _getBodyVerticalRow($key, $value) {
		if($this->simple_mode) {
			$this->html .= $this->tabs(3).'<td '.$this->td_head_attributes.'>'.$this->wrap_td_start.$key.$this->wrap_td_end."</td>\n";
			$this->html .= $this->tabs(3).'<td '.$this->td_attributes.'>'.$this->wrap_td_start.$value.$this->wrap_td_end."</td>\n";
		} else {
			$this->html .= $this->tabs(3).'<td '.$this->td_head_attributes.'>'.$this->wrap_td_start.$value['key'].$this->wrap_td_end."</td>\n";
			$this->html .= $this->tabs(3).'<td '.$this->td_attributes.'>'.$this->wrap_td_start.$value['value'].$this->wrap_td_end."</td>\n";	
		}
	}
	
	protected function _getTheadCells() {
		$str = '';
		$i = 0;
	
		if($this->head_one_cell) {
			
			if(isset($this->grid[0])) $colspan = count($this->grid[0]);
			else $colspan = 1;
			
			$str .=  $this->tabs(3)."<th ".$this->th_attributes." colspan=\"".$colspan."\">".@$this->headers[0]."</th>\n";
		} else {
			foreach($this->headers as $k=>$v) {
				if($i==0) {
					$str .=  $this->tabs(3)."<th ".$this->th_attributes." ".$this->td_head_attributes.">".@$v."</th>\n";
				} else {
					$str .=  $this->tabs(3)."<th ".$this->th_attributes.">".@$v."</th>\n";	
				}
				$i++;
			}
		}
		return  $str;
	}
}
?>