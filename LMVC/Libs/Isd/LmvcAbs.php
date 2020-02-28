<?php 
require_once 'Catz.php';
/*
require_once 'isd_lib/Db.php';*/
/*śćźżąłóć*/
abstract class Isd_LmvcAbs extends Isd_Catz {
	
	protected $catz = NULL;
	public $now;
	public $today;
	public $ans = array();
	public $is_connected_to_db = false;

	function __construct () {
		parent::__construct();
		$this->is_connected_to_db = true;
		$this->now = date('Y-m-d H:i:s');
		$this->today = date('Y-m-d');
		$this->setAnswer();
		
	}
	private function setAnswer() {
		$this->ans = array();
		$this->ans['errors'] =  array();
		$this->ans['info'] =  array();
		$this->ans['warn'] =  array();
	}
	public function addErr($str) {
		$this->ans['errors'][] =  $str;
	}
	
	public function addErrArr($arr) {
		if(is_array($arr)) {
			$this->ans['errors'] =  array_merge($this->ans['errors'], $arr);
		}
	}
	
	public function addWarn($str) {
		$this->ans['warn'][] =  $str;
	}
	public function addInfo($str) {
		$this->ans['info'][] =  $str;
	}
	public function ans() {
		return $this->ans;
	}
	public function errors($before='- ', $as_string=true) {
		if($before!='') {
			foreach($this->ans['errors'] as $key=>$value) {
				$this->ans['errors'][$key] = $before.$value;
			}
		}
		if($as_string) {
			return implode('<br/>', $this->ans['errors']);
		} else {
			return $this->ans['errors'];
		}
	}
	public function info($before='- ', $as_string=true) {
		if($before!='') {
			foreach($this->ans['info'] as $key=>$value) {
				$this->ans['info'][$key] = $before.$value;
			}
		}
		if($as_string) {
			return implode('<br/>', $this->ans['info']);
		} else {
			return $this->ans['info'];
		}
	}
	public function warn($before='', $as_string=true) {
		if($before!='') {
			foreach($this->ans['warn'] as $key=>$value) {
				$this->ans['warn'][$key] = $before.$value;
			}
		}
		if($as_string) {
			return implode('<br/>', $this->ans['warn']);
		} else {
			return $this->ans['warn'];
		}
	}
	public function no_err() {
		if(empty($this->ans['errors'])) {
			return true;
		} else {
			return false;
		}
	}
	
	#alias do no_err
	public function no_errors() {
		if(empty($this->ans['errors'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function has_info() {
		if(!empty($this->ans['info'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function has_errors() {
		if(!empty($this->ans['errors'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function clear_errors() {
		$this->ans['errors'] = array();	
	}
##################################################################################################################
	public function setCoding($coding) {
		if(!$this->is_connected_to_db) return false;
		$this->setCoding($coding);
	}
	#pobierz jeden
	public function getOne($table, $fields, $by, $keys=array()) {
		if(!$this->is_connected_to_db) return false;
		return $this->getOne($table, $fields, $by, $keys);
	}
	#pobierz wiele
	public function getAll($table, $fields='*', $by='', $keys=array()){
		if($this->is_connected_to_db)
		return $this->getAll($table, $fields, $by, $keys);
	}
	#dodaj jeden
	public function addOne($table, $data) {
		if(!$this->is_connected_to_db) return false;
		return parent::addOne($table, $data);
	}
	#dodaje jeden (filtr)
	public function addOneFilter($table, $data) {
		if(!$this->is_connected_to_db) return false;
		return $this->addOneFilter($table, $data);
	}
	#nadpisuje jeden 
	public function updateOne($table, $data, $key, $value) {
		if(!$this->is_connected_to_db) return false;
		return $this->updateOne($table, $data, $key, $value);
	}
	#nadpisuje jeden (filtr)
	public function updateOneFilter($table, $data, $key, $value) {
		if(!$this->is_connected_to_db) return false;
		 return $this->updateOneFilter($table, $data, $key, $value);
	}
	#dodaje lub nadpisuje (filter)
	public function replaceOneFilter($table, $data, $key, $value) {
		if(!$this->is_connected_to_db) return false;
		 return $this->replaceOneFilter($table, $data, $key, $value);
	}
	#usuwa jeden
	public function deleteOne($table, $key, $value) {
		if(!$this->is_connected_to_db) return false;
		return $this->deleteOne($table, $key, $value);
	}
	
	#usuwa wiele
	public function deleteAll($table, $condition='1') {
		if(!$this->is_connected_to_db) return false;
		return $this->deleteAll($table, $condition);
	}
	#wykonuje i pobiera wiele
	public function execute($query) {
		if(!$this->is_connected_to_db) return false;
		return $this->execute($query);
	}
	#wykonuje zapytanie
	public function executeSQL($sql) {
		if(!$this->is_connected_to_db) return false;
		return $this->executeSQL($sql);
	}
	#pobiera ostatnie AUTOINCREMENT ID
	public function getLastId() {
		if(!$this->is_connected_to_db) return false;
		return $this->getLastId();
	}
	
	#pibeira liczbę wszystkich wierszy danej tablicy
	public function countRows($table, $field_key) {
		if(!$this->is_connected_to_db) return false;
		$sql = 'SELECT COUNT('.$field_key.') FROM '.$table;
		$ans = $this->execute($sql);
			
		return $ans[0]['COUNT('.$field_key.')'];
	}
##################################################################################################################
	#metoda generalna - zmienia wartośc pola switch_on 0/1
	public function changeStatus($table_name, $eid_name, $eid, $field='switch_on') {
		if(!$this->is_connected_to_db) return false;
		if(!has_text($eid)) {
			$this->addErr('bład wewnętrzny (brak parametru)');
			return '';
		}
		$check_set = $this->getOne($table_name, '*', "WHERE $eid_name=?", array($eid));		
		if(!empty($check_set) && isset($check_set[$field])) {
				if($check_set[$field]==1) { $on =  0; } else {$on =  1;}
				$tab = array($field => $on);
				$this->updateOne($table_name, $tab, $eid_name, $eid);
				return $on;
		}
		return '';
	}
	#metoda generalna - aktualizuje wartośc pola serial_settings. Istniejące wartości tablicy są scalane z tablicą nową. A więc klucze tablic numerycznych są przenumerowane.
	/*
		@$table_name - nazwa tabeli
		@$new_arr - nowa tablica do scalenia z istniejącą
		@$id_field_name - nazwa pola id tej tabeli, np. settings_id
		@$id - numer id rekordu, np. 1
		@$settings_field_name -  nazwa pola z ustawieniami
	*/	
	public function updateSerial($table_name, $new_array, $id_field_name, $id, $settings_field_name = 'serial_settings') {
		if(!has_text($table_name)) return;
		if(!is_full_array($new_array)) return;
		if(!has_text($id_field_name)) return;
		if(!has_text($id)) return;
		$old_set = $this->getOne($table_name, '*', "WHERE $id_field_name=?", array($id));	
		#jesli istnieje pole serial settings w pobranym secie 
		if(isset($old_set[$settings_field_name])) {
			$serial = $this->getMergedSerialStr($old_set[$settings_field_name] , $new_array);
		} else {
			$serial = $this->getMergedSerialStr('', $new_array);	
		}
		$tab = array(
			$settings_field_name => $serial			 
		);
		if(!empty($old_set)) {
			$this->updateOne($table_name, $tab, $id_field_name, $id);
		} else {
			$tab[$id_field_name] =  $id;
			$this->addOne($table_name, $tab);
		}
	}
	#metoda generalna - aktualizuje wartośc pola serial_settings. Istniejąca tablica jest ZASTĘPOWANA nową tablicą.
	/*
		@$table_name - nazwa tabeli
		@$new_arr - nowa tablica do scalenia z istniejącą
		@$id_field_name - nazwa pola id tej tabeli, np. settings_id
		@$id - numer id rekordu, np. 1
		@$settings_field_name -  nazwa pola z ustawieniami
	*/	
	public function rewriteSerial($table_name, $new_array, $id_field_name, $id, $settings_field_name = 'serial_settings') {
		if(!$this->is_connected_to_db) return false;
		if(!has_text($table_name)) return;
		if(!is_full_array($new_array)) return;
		if(!has_text($id_field_name)) return;
		if(!has_text($id)) return;
		
		$old_set = $this->getOne($table_name, '*', "WHERE $id_field_name=?", array($id));	
		//errp($old_set, 'OLD_SET');
		#serializacja nowej tablicy
		$serial = $this->getMergedSerialStr('', $new_array);	
		$tab = array(
			$settings_field_name => $serial			 
		);
		if(!empty($old_set)) {
			$this->updateOne($table_name, $tab, $id_field_name, $id);
		} else {
			$tab[$id_field_name] =  $id;
			$this->addOne($table_name, $tab);
		}
	}
	#metoda generalna - pobiera wartośc pola serial_settings - zapis tablicy do bazy
	public function getSerial($table_name, $id_field_name, $id=1, $settings_field_name = 'serial_settings') {
		if(!$this->is_connected_to_db) return false;
		if(!has_text($table_name)) return;
		if(!has_text($id_field_name)) return;
		if(!has_text($id)) return;
		$set = $this->getOne($table_name, '*', "WHERE $id_field_name=?", array($id));
		if(isset($set[$settings_field_name])) {
			$serial_arr = unserialize($set[$settings_field_name]);
			$serial_arr = array_stripslashes_if($serial_arr);			
			return $serial_arr;
		} else {
			return array();	
		}
	}
	private function getMergedSerialStr($old_param_str, $new_param_arr) {
		if(!$this->is_connected_to_db) return false;
		#dekodujemy istniejącą tablice w bazie danych		
		if(get_magic_quotes_gpc()) {
			$old_arr = unserialize(stripslashes($old_param_str));	
		} else {
			$old_arr = unserialize($old_param_str);	
		}
		if(is_array($old_arr)) {
			$merged_arr = array_merge($old_arr, $new_param_arr);	
		} else {
			$merged_arr = $new_param_arr;	
		}
		$serial =  serialize($merged_arr);			
		//err($serial);
		return $serial; 
	}
##############################################################################################################################################	
##############################################################################################################################################
	
	#alias do Isd_Html
	public function tabs($num) {
		$str = "";
		if(is_numeric($num) && $num>0) {
			for($i = 0; $i < $num; $i ++ ) {
				$str .= "\t";
			}	
		}
		return $str;
	}
}
?>