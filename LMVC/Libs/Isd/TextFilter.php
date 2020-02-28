<?php 
/*śćźżąłóć*/
class Isd_TextFilter {
	var $keys_set;
	var $shortcodes_scripts_dir = 'scripts';
	var $infoarr = array();
	function __construct () {
	}
	
	public function filter($content, $keys_set) {
		$this->keys_set = $keys_set;
		$regex = $this->getRegex();
		$new_content = preg_replace_callback('/'.$regex.'/s', array($this, 'shortcode_tags'), $content);
		//ee($new_content);
		return $new_content;
	}
	
	public function filterArray($contents_set, $keys_set) {
		if(!is_array($contents_set)) return array();
		foreach($contents_set as $key=>$value) {
			if(isset($value['content']) && has_text($value['content'])) {
				$contents_set[$key]['content'] = $this->filter($value['content'],  $keys_set)	;	
			}
		}
		return $contents_set;
	}
	
	private function getRegex() {
		$tagnames = $this->get_tag_names();
		$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
		return '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';
	}
	
	private function get_tag_names() {
		$arr = array();
		foreach($this->keys_set as $key=>$value) {
			$arr[] = $value['name'];	
		}
		
		#tablica tag names nie moze byæ puste, poniewa¿ b³êdnie zadzia³a przepisanie tre¶ci
		if(empty($arr)) {
			$arr[] =  'test_tag';
		}
		return $arr;
	}
	
	/*
	 * m[0] - cale wyrazenie
	 * m[2] - tylko g³ówny tag
	 * m[3] - atrybuty
	 */
 
	
	private function shortcode_tags($m) {
		//errp($m, 'M');
		$attr = $this->shortcode_parse_atts($m[3]);		
		$replace = $m[1].call_user_func(array($this, 'replace_code'), $attr, $m[2]) . $m[6];
		//err($replace, 'zwrot');
		return $replace;
	}
	
	private function replace_code($attrs, $key=NULL) {;
		$replace =  $this->getScriptBuffer($key, $attrs);
		return $replace;
	}
	
	private function getScriptBuffer($key, $attrs) {
		$buffer = '';
		if(!isset($ARRAY['plugin_attrs'])) $ARRAY['plugin_attrs'] = array();
		if(!empty($attrs)) {
			$ARRAY['plugin_attrs'][$key] = $attrs;
		}
		
		#splaszczamy z danymi z Controllera
		$ARRAY = array_merge($ARRAY, $this->infoarr);
		
		$set =  $this->getKeySet($key);
		if(!empty($set) && isset($set['name']) && isset($set['script']) && !empty($set['script'])) {
			
			$file = APPLICATION_PATH.'/shortcodes/'.$set['script'].'.php';
			if(is_file($file)) {
			include $file;
			$buffer = $VIEW;
			
			//$file = APPLICATION_PATH.'/../jobs/shortcodes/testing.php';
			//ee($file);
			//if(is_file($file))	 {
//				ob_start();
//				eval('include(\''.$file.'\');');
//				$buffer = ob_get_contents();
//				ob_end_clean();
			} else {
				$this->trigger_error('brak pliku', __LINE__, $this);	
			}
		}
		
		return $buffer;
	}
	
	private function getKeySet($shortcode) {
		$back = array();
		
		foreach($this->keys_set as $key=>$value) {
			if($value['name']==$shortcode) {
				$back =  $value;
				break;
			}	
		}
		
		return $back;
	}
	
	private function shortcode_parse_atts($text) {
		$atts = array();
		$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
			foreach ($match as $m) {
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) and strlen($m[7]))
					$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
					$atts[] = stripcslashes($m[8]);
			}
		} else {
			$atts = ltrim($text);
		}
		return $atts;
	}
	
	
}
?>