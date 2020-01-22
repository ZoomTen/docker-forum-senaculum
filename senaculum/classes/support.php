<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
class support {

	function support() {}

	function replace($search, $replacement, $string){
	$string = preg_replace("§".$search."§i", $replacement, $string);
	return $string;
	}

}
?>