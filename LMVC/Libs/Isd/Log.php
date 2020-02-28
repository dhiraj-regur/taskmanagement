<?php
class Isd_Log {
	
	/*
	
	@3 - $mode:
		1 - na początku poprzednich danych
		2 - na końcu poprzednich danych
		3 - czyść dane zawsze
	*/
	public static function log($content, $path='', $show_date=true, $mode=1) {
		if($path=='') $path = 'error_log.txt';
		if($show_date) {
			$content = date("Y-m-d H:i:s").": ".$content;
		} 
		$content .= "\n";
		
		if($mode==1) {
			$content = $content .@ file_get_contents($path);
			$hn = fopen($path, 'w+');
		} elseif($mode==2) {
			$hn = fopen($path, 'a');
		} else {
			$hn = fopen($path, 'w');
		}
		echo $mode."\n";
		if(!is_file($path)) {
			//die('brak pliku'."\n");		
		}
		fwrite($hn, $content);
		fclose($hn);
	}
}
?>