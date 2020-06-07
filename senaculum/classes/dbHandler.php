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
	function dbHandler() {
		global $dbConnection;
		if(!isset($dbConnection)) {
			global $lang;
			$error = new errorHandler;
			if(!file_exists("./conf/conf.php")) {						//Get the database login variables
				if(file_exists("./install/install.php")) {
					header("location: install/install.php");
					die($lang['pleaseInstall']);
				}
				else
					$error->error($lang['configurationfileNotExist1'],$lang['configurationfileNotExist1']);	
			}
			require("./conf/conf.php");	
			$dbConnection = mysql_connect($dbHost, $dbUser, $dbPassword) or $error->error($lang['mySQLError'],$lang['couldNotConnectDatabase']);	//Connect to the database
			mysql_select_db($dbName, $dbConnection) or $error->error($lang['mySQLError'],$lang['couldNotChooseDatabase']);				//Chooses a database
		}	
	}
	
	function runSQL($SQL) {	//Runs a SQL-command	
		global $lang;
		global $dbConnection;
		$error = new errorHandler;
		require("./conf/conf.php");	
		$prev_SQL = $SQL;
		$SQL = str_replace("_'pfx'_",$dbTablePrefix,$SQL);				//Input tableprefixes to tablenames in the SQL-code
		  
		$result = mysql_query($SQL) or $error->error($lang['mySQLError'],$lang['couldNotRunSQL1'].'Original query: '.$prev_SQL.'<br><br>'.mysql_error().$lang['couldNotRunSQL2'].$SQL);				//Runs the SQL-comman
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
	
	function fetchArray($result) {
		return mysql_fetch_array($result);
	}
	
	function fetchObject($result) {
		return mysql_fetch_object($result);
	}
	
	function numRows($result) {
		return mysql_num_rows($result);
	}
	
	function dataSeek($result,$row) {
		mysql_data_seek($result,$row);
	}
	
	function affectedRows() {
		global $dbConnection;
		return mysql_affected_rows($dbConnection);
	}
	
	function insertID() {
		global $dbConnection;
		return mysql_insert_id($dbConnection);
	}
	
	function SQLsecure($text,$numeric=false) {
		if($numeric && !is_numeric($text))
			return false;
		global $dbConnection;
		/*if (get_magic_quotes_gpc()) {
			$text = stripslashes($text);
		}*/
		$text = mysql_real_escape_string($text,$dbConnection);
		//$text = str_replace("'", "''", $text);
		return $text;
	}
}	
?>
