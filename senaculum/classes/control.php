<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once("dbHandler.php");
require_once("process.php");

class control {

	function control() {}
	
	function userName($userName)
	{
		global $lang;
		$userName=strip_tags($userName);
		$userName=strtolower($userName);		
		
		if(empty($userName))
		{
			$error=$lang['usernameFieldEmpty'];
			return $error;
		}
		
		if($this->maxLenght(15, $userName))
		{
			$error=$lang['usernameToLongMax15'];
			return $error;
		}
		if($this->censur($userName))
		{
			$error=$lang['stringCensored1'].$userName.$lang['stringCensored2'];
		}
		
		if(!preg_match("/^[a-zA-Z0-9_]+$/", $userName))
		{
			$error=$lang['unalovedCharactersA-Z0-9_'];
			return $error;
		}
		
		$db=new dbHandler();
		$userName = $db->SQLsecure($userName);
		$sql="SELECT userName FROM _'pfx'_members WHERE userName='".$userName."'";
		$result=$db->runSQL($sql);
		if($db->numRows($result)>0)
		{
			$error=$lang['usernameAlreadyInUse'];
			return $error;
		}
	}
	
	function name($name) 
	{
		global $lang;
		$name=strip_tags($name);
		if($this->maxLenght(20, $name))
		{
			$error=$lang['nameToLongMax20'];
			return $error;
		}
		
		if($this->censur($name))
		{
			$error=$lang['stringCensored1'].$name.$lang['stringCensored2'];
			
			return $error;
		}
		
		$process=new process();
		$name = $process->SQLsecure($name);
		
		if($this->maxLenght(20, $name))
		{
			$error=$lang['nameToLongAfterConvertedSpecialChars'];
			return $error;
		}
	}	

	function password($pass1, $pass2)
	{		
		global $lang;
		if($pass1!=$pass2)
		{
			$error=$lang['passwordBoxesNotMatch'];
			return $error;
		}
		if($this->maxLenght(20, $pass1))
		{
			$error=$lang['passwordToLongMax20'];
			return $error;
		}
		if($this->minLenght(6, $pass1))
		{
			$error=$lang['passwordToShortMin6'];
			return $error;
		}
	}

	function website($website)
	{
		global $lang;
		if($this->maxLenght(50, $website))
		{
			$error=$lang['websiteURLToLongMax50'];
			return $error;
		}
		if($this->censur($website))
		{
			$error=$lang['stringCensored1'].$website.$lang['stringCensored2'];
			
			return $error;
		}
		
		if(!preg_match("§(http:\/\/[a-z_\/\.\%0-9]+(\?[a-z_\.\/\%=&\-0-9]+)?)§i",$website))
		{
			$error=$lang['websiteURLNotCorrect'];
		}
		
		//if (!preg_match("/.*@.*..*/", $email) | preg_match("/(<|>')/", $email)) 
		//{
		//	$error="Invalid e-mail address";
		//	return $error;
		//}
	}

	function checkMail($email) 
	{
		global $lang;
		if($this->maxLenght(50, $email))
		{
			$error=$lang['emailToLongMax50'];
			return $error;
		}
		if($this->censur($email))
		{
			$error=$lang['stringCensored1'].$email.$lang['stringCensored2'];
			
			return $error;
		}
		if(!empty($email))
		{
			if (!preg_match("/.*@.*..*/", $email) | preg_match("/(<|>')/", $email)) 
			{
				$error=$lang['invalidEmail'];
				return $error;
			}
		}
	}

	function email($email, $memberID=0) 
	{
		global $lang;
		if($this->minLenght(1, $email))
		{
			$error=$lang['fieldEmpty'];
			return $error;
		}
		if($this->maxLenght(50, $email))
		{
			$error=$lang['emailToLongMax50'];
			return $error;
		}
		if($this->censur($email))
		{
			$error=$lang['stringCensored1'].$email.$lang['stringCensored2'];
			
			return $error;
		}
		if (!preg_match("/.*@.*..*/", $email) | preg_match("/(<|>')/", $email)) 
		{
			$error=$lang['invalidEmail'];
			return $error;
		}
		$db=new dbHandler();
		$email = $db->SQLsecure($email);
		$memberID = $db->SQLsecure($memberID);
		$sql="SELECT email FROM _'pfx'_members WHERE email='".$email."' AND NOT memberID = '".$memberID."'";
		$result=$db->runSQL($sql);
		if($db->numRows($result)>0)
		{
			$error=$lang['emailAlreadyUsed'];
			return $error;
		}
	}

	function text($text, $minLenght, $maxLenght)
	{
		global $lang;
		$name=strip_tags($text);
		
		if($this->minLenght($minLenght, $text))
		{
			$error=$lang['textEmptyOrToShort1'].$minLenght.$lang['textEmptyOrToShort2'];
			return $error;
		}

		if($this->maxLenght($maxLenght, $text))
		{
			$error=$lang['textToLong1'].$maxLenght.$lang['textToLong2'];
			return $error;
		}
		
		$process=new process();
		$process->SQLsecure($text);
		
		if($this->maxLenght($maxLenght, $text))
		{
			$error=$lang['textToLongAfterConvertedSpecialChars1'].$maxLenght.$lang['textToLongAfterConvertedSpecialChars2'];
			return $error;
		}
		
	}
	
	function censur($text)
	{
		return false;
	}

	function image($fileArray, $file, $fileSize, $maxWidth, $maxHeight)
	{
		global $lang;
		$error = "";
	
		if(!file_exists($fileArray[$file]['tmp_name']))
		{
			$error = $lang['unableUploadFile'];
		}
		if($fileArray[$file]['size'] >= $fileSize)
		{
			$error = $lang['filesizeToLarge1'].$fileSize.$lang['filesizeToLarge2'];
		}
		
		$filetype = array("image/gif", "image/jpeg", "image/png", "image/bmp");
		
		$lenght = count($filetype);

		$ok = false;

		for($i=0; $i<$lenght; $i++)
		{

			if($fileArray[$file]['type']==$filetype[$i])
			{
				$ok = true;
				break;
			}
			else
			{
				$ok = false;
			}
		}
		if($ok == false)
		{
			$error = $lang['fileUnacceptableFiletype'];
		}
		
		list($width, $height, $type, $attr) = getimagesize($fileArray[$file]['tmp_name']);
		
		if($width > $maxWidth || $height > $maxHeight)
		{
			$error = $lang['imageToWideAndHigh1'].$maxWidth.$lang['imageToWideAndHigh2'].$maxHeight.$lang['imageToWideAndHigh3'];
		}
		else if($width > $maxWidth)
		{
			$error = $lang['imageToWide1'].$maxWidth.$lang['imageToWide2'];
		}
		else if($height > $maxHeight)
		{
			$error = $lang['imageToHigh1'].$maxHeight.$lang['imageToHigh2'];
		}
	
	return $error;
	}
	
	function maxFilesize($file,$size) {
		global $lang;
		$size *= 1024;
		if($file['size'] > $size) 
			return $lang['fileToBig1'].$size.$lang['fileToBig2'];
		else
			return false;	
	}
	
	function allowedExtensions($file,$extensions) {
		if(!empty($extensions)) {
			global $lang;
			$extensions2 = explode(",",$extensions);
			if(!in_array(str_replace(".","",strrchr($file, ".")), $extensions2))
				return $lang['fileHasForbiddenExstension']." ".$lang['filesAllowed1'].$extensions.$lang['filesAllowed2'];
			else
				return false;	
		}
		else
			return false;
	}
	
	function disallowedExtensions($file,$extensions) {
		if(!empty($extensions)) {
			global $lang;
			$extensions2 = explode(",",$extensions);
			if(in_array(str_replace(".","",strrchr($file, ".")), $extensions2))
				return $lang['fileHasForbiddenExstension']." ".$lang['filesDisallowed1'].$extensions.$lang['filesDisallowed2'];
			else
				return false;	
		}
		else
			return false;
	}
	
	function ICQ($userName)
	{
		global $lang;
		$error = "";
		
		if(!is_numeric($userName) && !empty($userName))
		{
			$error = $lang['notNumeralValue'];
		}
		return $error;
	}
	
	function AIM($userName)
	{
		return "";
	}
	
	function MSN($userName)
	{
		$error = $this->checkMail($userName);
		return $error;
	}
	
	function yahoo($userName)
	{
		$error = $this->checkMail($userName);
		return $error;
	}
	
	function BBCodeAccesskey($accesskey) {
		global $lang;
		if(empty($accesskey))
			return false;
		$db = new dbHandler;
		$accesskey = $db->SQLsecure($accesskey);
		$sql = "SELECT accesskey FROM _'pfx'_BBcode WHERE accesskey = '".$accesskey."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) != 0)
			return $lang['accesscodeAlreadyUsedBBCode'];
		else
			return false;	
	}
	
	function BBCodeCode($code) {
		global $lang;
		$db = new dbHandler;
		$code2 = $code;
		$code = $db->SQLsecure($code);
		$sql = "SELECT code FROM _'pfx'_BBcode WHERE code = '".$code."'";
		$result = $db->runSQL($sql);
		if($db->numRows($result) != 0) {
			while($row = $db->fetchArray($result)) {
				if($row['code'] == $code2)
					return $lang['BBCodeAlreadyExist'];
			}
			return false;
		}	
		else
			return false;	
	}
	
	function maxLenght($maxLenght, $text)
	{
		$length=strlen($text);
		if($length>$maxLenght)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function minLenght($minLenght, $text)
	{
		$length=strlen($text);
		if($length<$minLenght)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function numValue($value,$notEmpty=false,$not0=false) {
		global $lang;
		if($notEmpty) {
			if(empty($value) && $value != "0")
				return $lang['fieldEmptyMustNumber'];	
		}
		if(!preg_match("§[0-9]§",$value))
			return $lang['notANumber'];
		if($not0) {
			if($value < 1)
				return $lang['value1OrHigher'];
		}	
	}
	
	function postTimeLimit($threadID) {
		global $forumSettings;
		global $forumVariables;
		global $lang;
		if(empty($forumSettings['postTimeLimit']))
			return false;
		elseif($forumVariables['inlogged']) {
			$db = new dbHandler;
			$sql = "SELECT MAX(date) AS newestPost FROM _'pfx'_posts WHERE threadID = '".$db->SQLsecure($threadID)."' AND madeBy = '".$forumVariables['inloggedMemberID']."'";
			$result = $db->runSQL($sql);
			if($db->numRows($result) > 0) {
				$row = $db->fetchArray($result);
				//echo time()+$forumSettings['postTimeLimit']."<br/>".$row['newestPost'];
				//die();
				if(time() <= $row['newestPost']+$forumSettings['postTimeLimit'])
					return $lang['waitNotSpam'];
				else
					return false;	
			}
		}
		else {
			if(!empty($_SESSION['forumLastPostTime']) && !empty($_SESSION['forumLastPostThread'])) {
				if($_SESSION['forumLastPostThread'] == $threadID && time() <= $_SESSION['forumLastPostTime']+$forumSettings['postTimeLimit'])
					return $lang['waitNotSpam'];
				else
					return false;	
			}
			else
				return false;
		}	
	}
}
?>