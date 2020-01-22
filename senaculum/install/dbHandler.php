<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

require_once('errorHandler.php');
	
class dbHandler	//Manage the database functions
{
	function dbHandler() 
	{
		global $dbConnection;
		if(!isset($dbConnection)) {
			global $lang;
			
			$error = new errorHandler;
			if(!file_exists("../conf/conf.php")) {						//Get the database login variables
				$error->error($lang['confFileNotExists1'],$lang['confFileNotExists2']);	
			}
			require("../conf/conf.php");	
			$dbConnection = mysql_connect($dbHost, $dbUser, $dbPassword) or $error->error($lang['mySQLError'],$lang['couldNotConnectDatabase']);	//Connect to the database
			mysql_select_db($dbName, $dbConnection) or $error->error($lang['mySQLError'],$lang['couldNotChooseDatabase']);	
		}
	}
	
	function runSQL($SQL)		//Runs a SQL-command
	{	
		global $lang;
		global $dbConnection;
		$error = new errorHandler;
		require("../conf/conf.php");
		
		$SQL = str_replace("_'pfx'_",$dbTablePrefix,$SQL);				//Input tableprefixes to tablenames in the SQL-code
		  
		$result = mysql_query($SQL) or $error->error($lang['mySQLError'],$lang['couldNotRunSQLCommand1'].mysql_error().$lang['couldNotRunSQLCommand2'].$SQL);				//Runs the SQL-comman
		global $dbLastID; 
		$dbLastID = mysql_insert_id($dbConnection);

		global $masterCount;
		if(empty ($masterCount))
		{
			$masterCount = 1;
		}
		else
		{
			$masterCount ++;
		}
		global $sqls;
		$sqls .= $SQL."<br/><br/>";
		
		return $result;												//Returns the result
	}
	
	function SQLsecure($text,$numeric=false) {
		if($numeric && !is_numeric($text))
			return false;
		global $dbConnection;
		$text = mysql_real_escape_string($text,$dbConnection);
		//$text = str_replace("'", "''", $text);
		return $text;
	}
}	
?>