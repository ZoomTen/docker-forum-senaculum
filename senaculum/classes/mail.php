<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class mail {
	function mail() {}
	
	function checkError($socket, $response) {
		global $lang;
		
		$serverResponse = "";
		while(substr($serverResponse, 3, 1) != " ") 
		{
			if(!($serverResponse = fgets($socket, 256))) 
			{ 
				return $lang['couldNotGetResponseServer']; 
			} 
		} 
		if(!(substr($serverResponse, 0, 3) == $response)) 
		{ 
			return $lang['problemSendingMail1'].$serverResponse.$lang['problemSendingMail2']; 
		} 
	} 
	
	function send($sendTo, $subject, $message, $headers="") {
		global $forumSettings;
		global $lang;
		
		$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);
		if(!empty($headers))
			$headers = preg_replace("#(?<!\r)\n#si", "\r\n", $headers);
			
		if(!$forumSettings['useSmtp']) {
			if(mail($sendTo, $subject, $message, $headers))
				return false;
			else
				return $lang['couldNotSendMail'];	
		}		
		
		if(!$socket = fsockopen($forumSettings['smtpHost'], 25, $errno, $errstr, 20)) {
			return $lang['couldNotConnectSmtpHost'];
		}
		
		if($error = $this->checkError($socket, "220"))
			return $error;
		
		if(!empty($forumSettings['smtpUsername']) && !empty($forumSettings['smtpPassword'])) {
			fputs($socket, "EHLO ".$forumSettings['smtpHost']."\r\n");
			if($error = $this->checkError($socket, "250"))
				return $error;	
			fputs($socket, "AUTH LOGIN\r\n");
			if($error = $this->checkError($socket, "334"))
				return $error;
			fputs($socket, base64_encode($forumSettings['smtpUsername'])."\r\n");
			if($error = $this->checkError($socket, "334"))
				return $error;	
			fputs($socket, base64_encode($forumSettings['smtpPassword'])."\r\n");
			if($error = $this->checkError($socket, "235"))
				return $error;
		}
		else {
			fputs($socket, "HELO ".$forumSettings['smtpHost']."\r\n");
			if($error = $this->checkError($socket, "250"))
				return $error;	
		}		
		fputs($socket, "MAIL FROM: <".$forumSettings['adminEmail'].">\r\n");	
		if($error = $this->checkError($socket, "250"))
			return $error;
		fputs($socket, "RCPT TO: <".$sendTo.">\r\n");
		if($error = $this->checkError($socket, "250"))
			return $error;	
		fputs($socket, "DATA\r\n");
		if($error = $this->checkError($socket, "354"))
			return $error;
		fputs($socket, "Subject: ".$subject."\r\n");
		fputs($socket, "To: ".$sendTo."\r\n");
		fputs($socket, $headers."\r\n\r\n");
		fputs($socket, $message."\r\n");
		fputs($socket, ".\r\n");
		if($error = $this->checkError($socket, "250"))
			return $error;
		fputs($socket, "QUIT\r\n");
		fclose($socket);
	}
}

?>