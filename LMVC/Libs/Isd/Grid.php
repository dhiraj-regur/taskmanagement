<?php
require_once 'Isd/Grid/Horizontal.php'; 
require_once 'Isd/Grid/Vertical.php'; 	

class Isd_Grid {
    public static function factory($mode='h') {
	   	if($mode=='h') {
			return new Isd_Grid_Horizontal();
		} else {
			return new Isd_Grid_Vertical();
		}
	}
}
?>