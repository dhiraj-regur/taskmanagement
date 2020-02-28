<?php

require_once('Libs/TCPDF/tcpdf.php');
class LMVC_TCPDF extends TCPDF
{
	protected function _getTrueTypeFontSubset()
	{
		// Alcal: $font2cache modification
		// This modification creates utf-8 fonts only the first time,
		// after that it uses cache file which dramatically reduces execution time
		if (!file_exists($fontfile.'.cached')){
			// calculate $font first time
			$subsetchars = array_fill(0, 512, true); // fill subset for all chars 0-512
			$font = $this->_getTrueTypeFontSubset($font, $subsetchars); // this part is actually slow!
			// and then save $font to file for further use
			$fp=fopen($fontfile.'.cached','w');
			$flat_array = serialize($font); //
			fwrite($fp,$flat_array);
			fclose($fp);
		}
		else {
			// cache file exist, load file
			$fp=fopen($fontfile.'.cached','r');
			$flat_array = fread($fp,filesize($fontfile.'.cached'));
			fclose($fp);
			$font = unserialize($flat_array);
		}
	}



}



?>