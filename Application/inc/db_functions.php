<?php
/**
 * 
 * Central place for 'DB_FUNCTIONS' callback functions.
 * 
 */


function insertMultiple($tableName, $fields, $data)
{
	$db = LMVC_DB::getInstance()->getDB();
	
	$field_list = implode(",", $fields);
	
	$value_list = rtrim(str_repeat("?,", count($fields)),",");
	
	$sql = "INSERT INTO $tableName ($field_list) VALUES (".  $value_list .")";
	
	$sth = $db->prepare($sql);
	
	$res = $db->executeMultiple($sth, $data);
	
	return true;
	
}
