<?php
/*śćźżąłóć*/
#################################################################################################################
#MAIL 
function sendMail($do, $od_kogo, $temat, $tresc, $nadawca) {
	$do = strip_tags($do);
	$od_kogo = strip_tags($od_kogo);
	if(spam_check($do) &&  spam_check($od_kogo)) {
		//$temat = mail_escape_header($temat);
		$naglowki  = "MIME-Version: 1.0\r\n";
		$naglowki .= "Content-type: text/html; charset=iso-8859-2; \r\n";
		$naglowki .= "From: \"".$nadawca."\" <$od_kogo> \r\n";
		mail($do, $temat, $tresc, $naglowki);
	}		
}
	
function mail_escape_header($subject){
	$subject = preg_replace('/([^a-z ])/ie', 'sprintf("=%02x",ord(StripSlashes("\1")))', $subject);
	$subject = str_replace(' ', '_', $subject);
	return "=?utf-8?Q?$subject?=";
}	
function spam_check($field){
	$field=filter_var($field, FILTER_SANITIZE_EMAIL);
	if(filter_var($field, FILTER_VALIDATE_EMAIL)) {
		return TRUE;
	} else {
	    	return FALSE;
	}
}
?>