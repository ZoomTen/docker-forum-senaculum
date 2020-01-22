<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

require_once("support.php");

class process {

	function process() {}

	function text($text)
	{
		//print_r($text);
		//die();
		if(is_array($text) && !isset($text['text'])) {
			foreach($text as $element) {
				if(!empty($element)) {
					if(is_array($element))
						$element2 = $element['text'];
					else
						$element2 = $element;
					$element2 = str_replace("&#039;", "'", $element2);
					$element2 = htmlspecialchars($element2);
					$element2 = nl2br($element2);
					if(is_array($element)) {
						$element['text'] = $element2;
					}
					else
						$element = $element2;
				}
				$processed[] = $element;
			}
			$processed = $this->smilie($processed);
		    $processed = $this->bbcode($processed);
			$processed = $this->censur($processed);

			foreach($processed as $element) {
				if(!empty($element)) {
					if(is_array($element))
						$element = $element['text'];
					$element = " ".$element;
					$pattern = "§[^=][\n ](http:\/\/[a-z_\/\.\%,0-9]+(\?[a-z_\.\/\%=&;\-0-9]+)?)§i";
					$element = preg_replace($pattern,"<A href=\"\$1\" target=\"_blank\" class=\"link\">\$1</A>",$element);
					$element = substr_replace($element,"",0,0);
				}
				$processed2[] = $element;
			}
			$processed = $processed2;
			return $processed;
		}
		else {
			if(!empty($text)) {
				if(is_array($text))
					$text2 = $text['text'];
				else
					$text2 = $text;
				$text2 = str_replace("&#039;", "'", $text2);
				$text2 = htmlspecialchars($text2);
				$text2 = nl2br($text2);
	    		if(is_array($text))
					$text['text'] = $text2;
				else
					$text = $text2;
				$text = $this->smilie($text);
	    		$text = $this->bbcode($text);
				$text = $this->censur($text);

				if(is_array($text))
					$text = $text['text'];

				$text = " ".$text;
				$pattern = "§[^=][\n ](http:\/\/[a-z_\/\.\%,0-9]+(\?[a-z_\.\/\%=&;\-0-9]+)?)§i";
				$text = preg_replace($pattern,"<A href=\"\$1\" target=\"_blank\" class=\"link\">\$1</A>",$text);
				$text = substr_replace($text,"",0,0);
			}
		}
		return $text;
	}

	function headline($headline)
	{
		if(is_array($headline)) {
			foreach($headline as $element){
				$element = htmlspecialchars($element);
				$processed[] = $element;
			}
			$processed = $this->censur($processed);
			return $processed;
		}
		else {
			$headline = htmlspecialchars($headline);
			$headline = $this->censur($headline);

			return $headline;
		}
	}

	function name($name)
	{
		if(is_array($name)) {
			foreach($name as $element) {
				$element = str_replace("&#039;", "'", $element);
				$processed[] = htmlspecialchars($element);
			}
			$processed = $this->censur($processed);
			return $processed;
		}
		else {
			$name = str_replace("&#039;", "'", $name);
			$name = htmlspecialchars($name);
			$name = $this->censur($name);

			return $name;
		}
	}

	function SQLsecure($text)
	{
		$text = mysql_real_escape_string($text);
		//$text = str_replace("'", "''", $text);
		return $text;
	}

	function bbcode2($text,$find,$rep) //Addon to bbcode() for code that has a replace width one more §
 	{	//if(preg_match("§url§i",$text))
			//die($text);
		$find = addcslashes($find, "[]");
		/*$findArray = explode("§",$find);
		$find = "§".str_replace("§", "([^[]+?)",$find)."§i";
		$rep2 = str_replace("§§", "$", $rep);
		$rep = str_replace("§§", "$1", $rep);
		$rep = str_replace("§", "$1", $rep);
		$repArray = explode("$",$rep2);
		$find2 = array(0, 1);
		$find2[0] = $repArray[0];
		$find2[1] = $findArray[1];
		$find2 = implode("§",$find2);
		$find2 = "§".str_replace("§", "([^[]+?)",$find2)."§i";
		$find2 = addcslashes($find2, "<>");
		$text1 = preg_replace($find2, $rep, $text);
		$text1 = preg_replace($find, $rep, $text);
		*/

		$find = "§".str_replace("§", "([^[]+?)",$find)."§i";
		$rep = str_replace("§", "$", $rep);

    $count = 1;
    while(($text1 != $text) and ($count < 4)) {
        $count++;
        $text = $text1;
        $text1 = preg_replace($find, $rep, $text);
    }

		return $text;
	}

	function bbcode($text) //Look up bbcode in a text and replace it
	{
		//Get diffrent bbcodes from bbcodetable
		global $BBCodeCodesResult;
		if(isset($BBCodeCodesResult))
			$BBCodeCodes = $BBCodeCodesResult;
		else {
			require_once('BBCodeHandler.php');
			$BBCode = new BBCodeHandler;
			$BBCodeCodesResult = $BBCodeCodes = $BBCode->getAll();
		}
		if(!$BBCodeCodes)
			return $text;
		//Irritate the result from the table
		if(is_array($text) && !isset($text['text'])) {
			foreach($text as $element) {
				$element2 = $element;
				if(!empty($element)) {
					if(is_array($element)) {
						if($element['disableBBCode']) {
							$processed[] = $element;
							continue;
						}
						else
							$element2 = $element['text'];
					}
					else
						$element2 = $element;
					foreach($BBCodeCodes as $BBCodeCode) {
						$find = $BBCodeCode['code']; 									//Put the code to find variable
			  			$rep = $BBCodeCode['result'];									//Put the replacement to rep variable
						if(empty($BBCodeCode['scriptName'])) {
							//if(preg_match("/§§/i",$rep))
							//	$text = $this->bbcode2($text,$find,$rep);
							//else {
				  			$findArray = explode("§",$find);						//Explode find by § and set to an array
				  			$repArray = explode("§",$rep);							//Explode rep by § and set to an array
		        			$changed = false;										//A variable that is used to check if current code is find in text
							$elements = count($findArray);						//Look how many elemts the findArray contains
				 			for($i=0;$elements-1 >= $i; $i++){						//Do by segment in code
		        				if($elements > 2) {								//If 2 or more §
									if(strpos($element2,$findArray[$i]) !== false) {	//if the text has current code
										$repPos = 0;
										$findLength = 0;
										if(isset($findArray[$i+1])) {			//If next segment of code exists
											if(strlen($findArray[$i+1]) == 1) {	//If segment of code is one character
												/*if(preg_match("§a href§i", $repArray[$i]))
												{
											    	if(!preg_match("§\[url=http://§i", $text))
													{
												    	$repArray[$i] = str_replace(" ","",$repArray[$i]);
		            									$repArray[$i] = str_replace("\"","",$repArray[$i]);
					    								$repArray[$i] = preg_replace("§ahref=§i", "a href=http://", $repArray[$i]);
											    	}
										    	}*/
												$element2 = str_replace($findArray[$i],$repArray[$i],$element2); 			//Replace code before the singel character
												$repPos = strpos($element2,$repArray[$i]);								//Startpos of codesegment in text
												$findLength = strlen($repArray[$i]);								//Length of the replaced text
												$repPosEnd = strpos($element2,$findArray[$i+1],$repPos+$findLength);	//Where the replaced text ends

												$repLength = strlen($repArray[$i]) - $findLength;					//Pos of the single character
												$element2 = substr_replace($element2,$repArray[$i+1],$repPosEnd,1);			//Replace the single character
												while(strpos($element2,$repArray[$i],$repPosEnd+1)) {					//Ensure that all code will be replaced
													$repPos = strpos($element2,$repArray[$i],$repPosEnd+1);
													$findLength = strlen($repArray[$i]);
													$repPosEnd = strpos($element2,$findArray[$i+1],$repPos+$findLength);
													$repLength = strlen($repArray[$i]) - $findLength;
													$element2 = substr_replace($element2,$repArray[$i+1],$repPosEnd,1);

												}
											}
											elseif(strlen($findArray[$i]) != 1)										//Replace codesegment that not has a single character before
												$element2 = str_replace($findArray[$i],$repArray[$i],$element2);
										}
										elseif(strlen($findArray[$i]) != 1)
											$element2 = str_replace($findArray[$i],$repArray[$i],$element2);
									}
		            			}
								else {						//If less than 2 segments of code
		      						$text2 = str_replace($findArray[$i],$repArray[$i],$element2);
									if($element2 != $text2 && $i == 0)
										$changed = true;
									if($changed)
										$element2 = $text2;
								}
							//}
				  			}
						}
						else
							$element2 = $this->BBCodeScript($find,$element2,$BBCodeCode['scriptName']);
					}
				}
				if(is_array($element))
					$element['text'] = $element2;
				else
					$element = $element2;
				$processed[] = $element;
			}
			return $processed;
		}
		elseif(!empty($text)) {
			if(is_array($text)){
				if($text['disableBBCode'])
					return $text;
				else
					$text2 = $text['text'];
			}
			else
				$text2 = $text;
			foreach($BBCodeCodes as $BBCodeCode) {
				$find = $BBCodeCode['code']; 									//Put the code to find variable
		  		$rep = $BBCodeCode['result'];										//Put the replacement to rep variable
				if(empty($BBCodeCode['scriptName'])) {
					//if(preg_match("/§§/i",$rep))
					//	$text2 = $this->bbcode2($text2,$find,$rep);
					//else {
			  		$findArray = explode("§",$find);						//Explode find by § and set to an array
			  		$repArray = explode("§",$rep);							//Explode rep by § and set to an array
	        		$changed = false;										//A variable that is used to check if current code is find in text
					$elements = count($findArray);						//Look how many elemts the findArray contains
			 		for($i=0;$elements-1 >= $i; $i++){						//Do by segment in code
	        			if($elements > 2) {								//If 2 or more §
								if(strpos($text2,$findArray[$i]) !== false) {	//if the text has current code
									$repPos = 0;
									$findLength = 0;
									if(isset($findArray[$i+1])) {			//If next segment of code exists
										if(strlen($findArray[$i+1]) == 1) {	//If segment of code is one character
											/*if(preg_match("§a href§i", $repArray[$i]))
											{
										  	 	if(!preg_match("§\[url=http://§i", $text2))
												{
											    	$repArray[$i] = str_replace(" ","",$repArray[$i]);
	            									$repArray[$i] = str_replace("\"","",$repArray[$i]);
				    								$repArray[$i] = preg_replace("§ahref=§i", "a href=http://", $repArray[$i]);
										    	}
									    	}*/
											$text2 = str_replace($findArray[$i],$repArray[$i],$text2); 			//Replace code before the singel character
											$repPos = strpos($text2,$repArray[$i]);								//Startpos of codesegment in text
											$findLength = strlen($repArray[$i]);								//Length of the replaced text
											$repPosEnd = strpos($text2,$findArray[$i+1],$repPos+$findLength);	//Where the replaced text ends

											$repLength = strlen($repArray[$i]) - $findLength;					//Pos of the single character
											$text2 = substr_replace($text2,$repArray[$i+1],$repPosEnd,1);			//Replace the single character
											while(strpos($text2,$repArray[$i],$repPosEnd+1)) {					//Ensure that all code will be replaced
												$repPos = strpos($text2,$repArray[$i],$repPosEnd+1);
												$findLength = strlen($repArray[$i]);
												$repPosEnd = strpos($text2,$findArray[$i+1],$repPos+$findLength);
												$repLength = strlen($repArray[$i]) - $findLength;
												$text2 = substr_replace($text2,$repArray[$i+1],$repPosEnd,1);

											}
										}
										elseif(strlen($findArray[$i]) != 1)										//Replace codesegment that not has a single character before
											$text2 = str_replace($findArray[$i],$repArray[$i],$text2);
									}
									elseif(strlen($findArray[$i]) != 1)
										$text2 = str_replace($findArray[$i],$repArray[$i],$text2);
								}
	            		}
						else {						//If less than 2 segments of code
	      				$textTemp = str_replace($findArray[$i],$repArray[$i],$text2);
							if($text2 != $textTemp && $i == 0)
								$changed = true;
							if($changed)
								$text2 = $textTemp;
						}
					//}
			   		}
				}
				else
					$text2 = $this->BBCodeScript($find,$text2,$BBCodeCode['scriptName']);
			}
		}
		if(is_array($text))
			$text['text'] = $text2;
		else
			$text = $text2;
		return $text;
	}

	function BBCodeScript($find, $text, $scriptName) {
		global $lang;
		while(1) {
			$findArray = explode("§", $find);
			$elements = count($findArray);
			if(($start = strpos($text, $findArray[0])) === false)
				return $text;
			if(($end = strpos(strrev($text), strrev($findArray[$elements-1]))) === false)
				return $text;
			$end = strlen($text) - $end;
			//$end += strlen($findArray[$elements-1]);
			$findText = substr($text,$start,$end-$start);
			$findText2 = "";
			$findTextLeft = $findText;
			$i = 0;
			foreach($findArray as $element) {
				if(!empty($findArray[$i+1]))
					$findText2 .= $temp = "§".substr($findTextLeft,strpos($findTextLeft,$element)+strlen($element),strpos($findTextLeft,$findArray[$i+1])-(strpos($findTextLeft,$element)+strlen($element)));
				else {
					$findTextLeft = substr($findTextLeft,strpos($findTextLeft,$temp)+strlen($temp)-1);
					$findText2 .= substr($findTextLeft,-strlen($findTextLeft),strlen($findTextLeft)-strlen($element))."§";
				}
					//$findText2 .= "§".substr($findTextLeft,strpos($findTextLeft,$element)+strlen($element));
				$findTextLeft = substr($findTextLeft,strpos($findTextLeft,$element)+strlen($element));
				$i++;
			}
			$findText = $findText2;
			require("./include/BBCodeScripts/".$scriptName);
			$textStart = substr($text,0,$start);
			$textEnd = substr($text,$end);
			$text = $textStart.$replaceText.$textEnd;
			//$text = str_replace($findText,$replaceText,$text);
		}
		return $text;
	}

	function censur($text) //Repelaces words that the admin has cencured on the forum
	{
		global $censurWordsResult;
		if(isset($censurWordsResult))
			$censurWords = $censurWordsResult;
		else {
			require_once('censurHandler.php');
			$censur = new censurHandler;
			$censurWordsResult = $censurWords = $censur->getAll();
		}
		if(!$censurWords)
			return $text;
		if(is_array($text) && !isset($text['text'])){
			foreach($text as $element){
				$element2 = $element;
				if(!empty($element)) {
					if(is_array($element))
						$element2 = $element['text'];
					else
						$element2 = $element;
					foreach($censurWords as $censurWord) {
						$find=$censurWord['find'];
						$rep=$censurWord['replace'];
						$byWord=$censurWord['byWord'];
						if($byWord) {				//If it is only a word
							$element2 = " ".$element2." ";	//Put a whitespace before and after the string så the replacefuntion will work correct
							$element2=preg_replace("§ ".$find." §i",$rep,$element2);
							$textLength = strlen($element2);
							$element2 = substr_replace($element2,"",0,0); //Delete the inserted whitespaces
							$element2 = substr_replace($element2,"",$textLength-1,0);
						}
						else						//if the text is in a word
							$element2=preg_replace("§".$find."§i",$rep,$element2);
					}
				}
				if(is_array($element))
					$element['text'] = $element2;
				else
					$element = $element2;
				$processed[] = $element;
			}
			return $processed;
		}
		elseif(!empty($text)) {
			if(is_array($text))
				$text2 = $text['text'];
			else
				$text2 = $text;
			foreach($censurWords as $censurWord) {
				$find=$censurWord['find'];
				$rep=$censurWord['replace'];
				$byWord=$censurWord['byWord'];
				if($byWord) {				//If it is only a word
					$text2 = " ".$text2." ";	//Put a whitespace before and after the string så the replacefuntion will work correct
					$text2=preg_replace("§ ".$find." §i",$rep,$text2);
					$textLength = strlen($text2);
					$text2 = substr_replace($text2,"",0,0); //Delete the inserted whitespaces
					$text2 = substr_replace($text2,"",$textLength-1,0);
				}
				else						//if the text is in a word
					$text2=preg_replace("§".$find."§i",$rep,$text2);
			}
		}
		if(is_array($text))
			$text['text'] = $text2;
		else
			$text = $text2;
		return $text;
	}

	function smilie($text) {
		global $smilieResult;
		if(isset($smilieResult))
			$smilies = $smilieResult;
		else {
			require_once('smilieHandler.php');
			$smilie = new smilieHandler;
			$smilieResult = $smilies = $smilie->getAll();
		}
		if(!$smilies)
			return $text;
		if(is_array($text) && !isset($text['text'])){
			foreach($text as $element){
				$element2 = $element;
				if(!empty($element)) {
					if(is_array($element)) {
						if($element['disableSmilies']) {
							$processed[] = $element;
							continue;
						}
						else
							$element2 = $element['text'];
					}
					else
						$element2 = $element;
					foreach($smilies as $smilieSmilie) {
						$find = $smilieSmilie['find'];
						$fileName = $smilieSmilie['fileName'];
						$description = $smilieSmilie['description'];
						$element2 = str_replace($find,"<img src=\"images/smilies/".$fileName."\" title=\"".$description."\"/>",$element2);
					}
				}
				if(is_array($element))
					$element['text'] = $element2;
				else
					$element = $element2;
				$processed[] = $element;
			}
			return $processed;
		}
		else {
			if(!empty($text)) {
				if(is_array($text)) {
					if($text['disableSmilies'])
						return $text;
					else
						$text2 = $text['text'];
				}
				else
					$text2 = $text;
				foreach($smilies as $smilieSmilie) {
					$find = $smilieSmilie['find'];
					$fileName = $smilieSmilie['fileName'];
					$description = $smilieSmilie['description'];
					$text2 = str_replace($find,"<img src=\"images/smilies/".$fileName."\" title=\"".$description."\"/>",$text2);
				}
			}
			if(is_array($text))
				$text['text'] = $text2;
			else
				$text = $text2;
			return $text;
		}
	}
}
?>