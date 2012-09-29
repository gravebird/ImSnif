<?php
	include_once("conf.php");
	$statfolder = "../".$snifstatcat;
	
	function userinfo() 
	{ 
		$r = $_SERVER['HTTP_USER_AGENT']; 
		$d = array($r); 
		$os_p = array("|Windows\sNT\s5.1|",
					"|Windows\sNT\s5.0|", 
					"|Windows\s98|", 
					"|Linux\si686|", 
					"|Windows\sNT\s6.1|", 
					"|Windows\sNT\s6.0|");
		$os = array("Windows XP", 
					"Windows 2000", 
					"Windows 98", 
					"Linux", 
					"Windows 7", 
					"Windows Vista"); 
		for($j=0; $j<count($os); $j++) 
		{
			if(preg_match($os_p[$j], $r, $mas)) 
			{ 
				$h = str_replace($mas[0], $os[$j], $mas[0]); 
				array_push($d, $h); 
			} 

		} 
		return $d; 
	} 
	function GetRealIp() {
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR']." — HTTP_X_FORWARDED_FOR";
        } else {
                $ip = $_SERVER['REMOTE_ADDR']." — REMOTE_ADDR";
        }
        return $ip;
	}
?>