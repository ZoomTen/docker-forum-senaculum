<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
$parts = explode("§",$findText);
$nrParts = count($parts);
if($nrParts > 2) {
	$replaceText = "<div class=\"postListQuoteHeading\">";
	if(!empty($parts[1]))
		$replaceText .= $lang['wrote1'].$parts[1].$lang['wrote2'].":";
	else
		$replaceText .= $lang['quote2'].":";
	$replaceText .= "<div class=\"postListQuoteArea\">".$parts[2]."</div></div>\n";
}	
else
	$replaceText = $findText;
?>