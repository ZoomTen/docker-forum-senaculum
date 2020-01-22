<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

class control {

	function control() {}
	
	function userName($userName) {	
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

		if(!preg_match("/^[a-zA-Z0-9_]+$/", $userName))
		{
			$error=$lang['unalowedCharsUsedAZ09_'];
			return $error;
		}
	}
	
	function name($name) 
	{
		global $lang;
		
		$name=strip_tags($name);
		if(empty($name)) 
		{
			$error=$lang['fieldEmpty'];
			return $error;
		}
		if($this->maxLenght(20, $name))
		{
			$error="Name to long max 20 characters";
			return $error;
		}
	}	

	function password($pass1, $pass2)
	{
		global $lang;
		
		if($pass1!=$pass2)
		{
			$error=$lang['passwordNotMatch'];
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
		if(preg_match("/^[<>]+$/", $pass1))
		{
			$error=$lang['unalovedCharactersUsed'];
			return $error;
		}
	}

	function email($email) 
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
		if (!preg_match("/.*@.*..*/", $email) | preg_match("/(<|>)/", $email)) 
		{
			$error=$lang['invalidEmailAddress'];
			return $error;
		}
	}

	function text($text, $minLenght, $maxLenght)
	{
		global $lang;
		
		$name=strip_tags($text);
		
		if($this->minLenght($minLenght, $text))
		{
			$error=$lang['fieldEmptyOrTextToShort1'].$minLenght.$lang['fieldEmptyOrTextToShort2'];
			return $error;
		}

		if($this->maxLenght($maxLenght, $text))
		{
			$error=$lang['textToLong1'].$maxLenght.$lang['textToLong2'];
			return $error;
		}
	}
	
	function maxLenght($maxLenght, $text)
	{
		global $lang;
		
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
		global $lang;
		
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
}
?>