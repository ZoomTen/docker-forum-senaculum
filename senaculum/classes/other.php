<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class other {

	function other() {}
	
	function paginate($pageNum, $numRows, $limit, $page, $getVariables=false) {
		global $lang;
		$paginate = "";
		if($numRows == 0)
			$numOfPages = 1;
		else	
			$numOfPages = ceil($numRows / $limit);
		if($numOfPages > 1) {	
			$gets = "";
			if($getVariables) {
				foreach($getVariables as $variables) {
					$gets .= "&amp;".$variables['name']."=".$variables['value'];
				}
			}	
				
			if($pageNum > 1) 
				$paginate .= "<a href=\"".$page."?page=".($pageNum - 1).$gets."\">&lt;&lt; ".$lang['previous']."</a> ";			
			for($i=1;$i<=$numOfPages;$i++) {
				if($numOfPages > 9 && $pageNum > 5 && $pageNum < $numOfPages - 4 && ($i-2 == $pageNum || $i+1 == $pageNum)) 
					$paginate .=  "<b>...</b> ";
				elseif($numOfPages > 9 && $pageNum < 6 && $i == 7)
					$paginate .=  "<b>...</b> ";
				elseif($numOfPages > 9 && $pageNum >= $numOfPages - 4 && $i == $numOfPages - 5)
					$paginate .=  "<b>...</b> ";
				if($numOfPages <= 9 || (($pageNum <= 5 && $i <= 6) || $i <= 3) || (($pageNum >= $numOfPages - 4 && $i >= $numOfPages - 5) || $i >= $numOfPages - 2) || $pageNum == $i || $pageNum - 1 == $i || $pageNum + 1 == $i) {			
					if($pageNum == $i)
						$paginate .= "<a href=\"".$page."?page=".$i.$gets."\"><b>[".$i."]</b></a> ";
					else
						$paginate .= "<a href=\"".$page."?page=".$i.$gets."\">[".$i."]</a> ";
				}	
			}
			if($pageNum < $numOfPages) 
				$paginate .= "<a href=\"".$page."?page=".($pageNum + 1).$gets."\">".$lang['next']." &gt;&gt;</a>";
			return $paginate;
		}
		else			
			return false;
	}		
	
	function paginate2($numRows, $limit, $page, $getVariables=false) {
		$paginate = "";
		if($numRows == 0)
			$numOfPages = 1;
		else	
			$numOfPages = ceil($numRows / $limit);
		if($numOfPages > 1) {	
			$gets = "";
			if($getVariables) {
				foreach($getVariables as $variables) {
					$gets .= "&amp;".$variables['name']."=".$variables['value'];
				}
			}	
					
			for($i=1;$i<=$numOfPages;$i++) {
				if($numOfPages > 4) {
					if($i == 2) 
						$paginate .=  "<b>...</b> ";
					if($i > $numOfPages - 3 || $i == 1)	
						$paginate .= "<a href=\"".$page."?page=".$i.$gets."\">[".$i."]</a> ";	
				}	
				else
					$paginate .= "<a href=\"".$page."?page=".$i.$gets."\">[".$i."]</a> ";	
			}
			return $paginate;
		}
		else			
			return false;
	}		
	
	function dateParse($dateFormat,$date) {
		return date($dateFormat,$date);
	}
}
?>