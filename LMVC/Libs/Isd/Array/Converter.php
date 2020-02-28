<?php 
include_once dirname(__FILE__).'/../Abs.php';
class Array_Converter extends Isd_Abs {
	var $table_name;
	var $user_defined_fields;
	function __construct () {
		parent::__construct();
	}
	
	public function getExcelAsArray($data) {
		$arr = array();
		if(!$this->baseCheck($data)) {
			$arr['succes'] = 0;
			$arr['data'] = array();
			return $arr;
		}
		$arr['succes'] = 1;
		$arr['data'] = $data->sheets[0]['cells'];
		return $arr;
	}
	
	public function addExcelToDb($data, $table_name, $user_defined_fields=array()) {
		$this->table_name = $table_name;
		$this->user_defined_fields = $user_defined_fields;
		
		if(!$this->baseCheck($data)) {
			return;
		}
		
		$cells = $data->sheets[0]['cells'];
		$excel_fields = $cells[1];
		
		# pobieramy informacje na temat tabeli z DB
		$table_fields_arr = $this->showFields($this->table_name);
		
		# z infomracji wyci±gamy tylko liste pól dla tej tabeli DB
		$table_fields_arr = $this->getSimpleTableFields($table_fields_arr);
		
		# badamy ró¿nice pól miedzy plikiem excel a tabel±
		$diff_arr = array_diff($excel_fields, $table_fields_arr);
		
		# plik excel posiada pola, które nie s± przewidziane w bazie
		if(!empty($diff_arr) && count($diff_arr)>0) {
			$this->addErr("-arkusz posiadał pola nieprzewidziane w bazie danych");
		}
		
		if($this->no_err()) {
			$this->addDataToDb($cells);		
		}
	}
	
	# sprawdza podstawowe inforamcje o excelu
	private function baseCheck($data) {
	
		# b³edy w strukturze
		if(empty($data)) {
			$this->addErr("arkusz zawierał błędy w strukturze");
			return  false;
		}
		$cells = $data->sheets[0]['cells'];
		# brak danych w arkuszu
		if(!$this->has_data($cells)) {
			$this->addErr("arkusz był pusty");
			return  false;
		}
		
		return true;
	}
	
	private function addDataToDb($cells) {
		$cnt = 0;
		$fields = $cells[1];
		$cells = array_splice($cells, 1);
		$add_arr = array();	
		
		foreach($cells as $key=>$value) {
			$temp_arr = array();	
			foreach($fields as $key2=>$value2) {
				if(!empty($value[$key2])) {
					$temp_arr[$value2] = $value[$key2]; 		
				} else {
					$temp_arr[$value2] = ""; 
				}
			}			
			$temp_arr = array_merge($temp_arr, $this->user_defined_fields);
			$add_arr[] = $temp_arr;	
		}
		
		
		if(!empty($add_arr)) {
			foreach($add_arr as $key=>$value) {
				$this->addOne($this->table_name, $value);
			}
		}
	}
	
	private function getSimpleTableFields($table_fields_arr) {
		if(!$this->has_data($table_fields_arr)) {
			$this->addErr("nieznany błąd (".__LINE__.")");
		}
		if($this->no_err()) {
			$new_arr = array();
			foreach($table_fields_arr as $kye=>$value) {
					$new_arr[] =  $value['Field'];		
			}
			return $new_arr;
		}
	}
	
	private function has_data($arr) {
		if(!is_array($arr) || empty($arr)) {
			return false;
		} else {
			return true;
		}
	}
	
	
}
?>