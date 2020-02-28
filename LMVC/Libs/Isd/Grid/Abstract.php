<?php
require_once 'Isd/Html.php'; 
abstract class Isd_Grid_Abstract extends Isd_Html {
	#html macierzy
	public $html =  '';
	
	#dane do wrzucenia w tabele, bez naglówków
	public $grid = array();
	
	#naglówki
	public $headers = array();
	
	#ilosc wierszy, w trybie pionowym: ilosc grup
	protected $rows_num = 0;

	public $table_attributes = '';
	
	public $thead_attributes = '';
	
	public $td_attributes = '';
	
	public $th_attributes = '';
	
	public $empty_cell_value = '&nbsp;';
	
	public $table_first_row_attributes = '';
	#atrybuty dla komórek pierwszej kolumnmy - w trybie V
	
	#counter rekordów podczas tworzenie tabeli
	protected $rows_cnt = 0;
	
	#czy naglowek ma byc w jednej komórce 
	public $head_one_cell = false;

	function __construct($params=NULL) {
		parent::__construct($params);
	}
	
	#zwraca ostateczny html macierzy
	public function html($debug=false) {
		if(empty($this->grid) || !is_array($this->grid) || count($this->grid)<0) {
			//throw new Exception('Matrix undefined');
			//return '';
		}
		if($debug) $this->html = htmlspecialchars($this->html);
		return $this->html;
	}
	
####################################################################################################### ##########################################################	
	#ustawia naglówki - w wersji pionowej jest to pierwsza kolumna tabeli
	public function setHeaders($headers) {
		$this->headers = $headers;	
	}
	
    public function setTableAttributes($arr=array()) {
    	$html = $this->attributes($arr);
    	$this->table_attributes = $html;
   	}
	
	 public function setTheadAttributes($arr=array()) {
    	$html = $this->attributes($arr);
    	$this->thead_attributes = $html;
   	}
    
    public function setFirstRowAttributes($arr=array()) {
    	$html = $this->attributes($arr);
    	$this->table_first_row_attributes = $html;
   	}
	
	protected function _countRowsNum() {
		$this->rows_num = count($this->grid);
	}

	protected function _getCell_clean($iter, $field_value, $field_name = NULL) {
		if($iter==1 && $field_name!=NULL) $style = $this->_getHeaderRowStyle($field_name); else $style = '';
		$str = '<td '.$style.'><span>';	
		$str .= $field_value;
		$str .= '</span></td>';
		return $str;
	}
	
	protected function _getCell_num($iter,  $field_value, $field_name = NULL, $is_percent=NULL) {
		if($iter==1 && $field_name!=NULL) $style = $this->_getHeaderRowStyle($field_name); else $style = '';
		
		$str = '<td '.$style.'><span>';	
		$str .= $this->_formatNumber($field_value);
		if($is_percent==1 || $is_percent==true) $str .= ' %';
		$str .= '</span></td>';
		return $str;
	}
	

	
	protected function _getSwitchName($inter, $class, $other_class='') {
		$arr = array($other_class, $class);
		$num = count($arr);
		$remainder = $inter % $num;
		return $arr[$remainder];
	}
	
	protected function getNoDataTr() {
		$str = '<tr><td class="no-results" colspan="2">Brak danych</td></tr>';
		return $str;
	}
	
	protected function _formatNumber($data) {
		return number_format($data, 2, ',', ' ');
	}
}

/*
PRZYKLAD OCZEKIWANEJ TABLICY
Array
(
    [0] => Array
        (
            [values] => Array
                (
                    [0] => Array
                        (
                            [value] => 7963
                            [db] => s01
                        )

                    [1] => Array
                        (
                            [value] => 3119
                            [db] => s02
                        )

                    [2] => Array
                        (
                            [value] => 3463
                            [db] => s03
                        )

                    [3] => Array
                        (
                            [value] => 2778
                            [db] => s04
                        )

                )

            [left_fields] => Array
                (
                    [spolka] => 26435
                    [periodend] => 2011-12-31
                    [pubdate] => 2012-03-07
                    [dataa] => 2012-03-09 11:22:49
                    [periodtype] => Q

                    [cons] => t
                    [accstd] => MSR
                )

            [group_name] => I Q 2011
        )

)
*/
?>