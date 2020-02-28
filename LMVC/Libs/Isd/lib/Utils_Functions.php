<?php
/*śćźżąłóć*/
#zwraca tablicę paramterów ogolnych
function get_params($ARRAY, $default=array()) {
	if(isset($ARRAY['controller']['gparams']['params']) && $ARRAY['controller']['gparams']['params']!='' && $ARRAY['controller']['gparams']['params']!==0) {
		return $ARRAY['controller']['gparams']['params'];	
	} else {
		return $default;	
	}
} 
function get_params_el($ARRAY, $default=0) {
	if(isset($ARRAY['controller']['gparams']['params'][0]) && $ARRAY['controller']['gparams']['params'][0]!='' && $ARRAY['controller']['gparams']['params'][0]!==0) {
		return $ARRAY['controller']['gparams']['params'][0];	
	} else {
		return $default;	
	}
}
function get_params_pg($ARRAY, $parameter_num=0, $default=1) {
	if(isset($ARRAY['controller']['gparams']['pagin'][$parameter_num]) && $ARRAY['controller']['gparams']['pagin'][$parameter_num]!='' && $ARRAY['controller']['gparams']['pagin'][$parameter_num]!==0) {
		return $ARRAY['controller']['gparams']['pagin'][$parameter_num];	
	} else {
		return $default;	
	}
}
function  get_params_site($ARRAY, $parameter_num, $default=0) {
	if(isset($ARRAY['controller']['gparams']['site_params'][$parameter_num]) && $ARRAY['controller']['gparams']['site_params'][$parameter_num] != '' && $ARRAY['controller']['gparams']['site_params'][$parameter_num] !== 0) {
		return $ARRAY['controller']['gparams']['site_params'][$parameter_num];	
	} else {
		return $default;	
	}	
}
function isset_gparams($ARRAY) {
	if(isset($ARRAY['controller']['gparams'])) return true;
	return false;
}
function isset_default_scheme($ARRAY) {
	if(isset($ARRAY['controller']['default_scheme'])) return true;
	return false;	
}
###########################################################################
#PLUGINS
#zwraca ostatni użyty schemat
function get_acc_scheme() {
	$last_scheme = '';
	$num = func_num_args();
	if($num>0) {
		$last_scheme = func_get_arg(0);
		for($i=0; $i<$num; $i++) {
			if(has_text(func_get_arg($num-1))) $last_scheme = 	func_get_arg($num-1);
		}
	}
	return $last_scheme;
}
#spłaszcza ze soba treści tego samego obszaru
function merge_contents() {
	$actual_contents =  array();
	$num = func_num_args();
	if($num>1) {
		for($i = 0; $i < $num; $i++) {
			#przechodzimy przez daną tablicę CONTENTS
			foreach(func_get_arg($i) as $key=>$value) {
				if(!isset($actual_contents[$key]) || (isset($actual_contents[$key]) && $value['content_is_set']==1)) $actual_contents[$key] = $value;
			}
		}
	}
	
	return $actual_contents;
}
#łączy treści z róznych obszarów tworząc nowe SCHEME_CONTENTS
function join_contents() {
	$new_contents =  array();
	$num = func_num_args();
	if($num>1) {
		for($i = 0; $i < $num; $i++) {
			$arr = func_get_arg($i);
			if(isset($arr[0])) {
				$new_contents[] = $arr[0];
			}
		}
	}
	
	return $new_contents;
}
?>