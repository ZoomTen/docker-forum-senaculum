<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
require_once('dbHandler.php');
require_once('errorHandler.php');
class smilieHandler {
	function smilieHandler() {}
	
	function add($fileArray, $find, $fileName, $description) {
		$db = new dbHandler;
		$error = new errorHandler;
		$testName = $fileArray[$fileName]['name'];
		$dir = "images/smilies/";
		
		if(file_exists($dir."/".$fileArray[$fileName]['name']))
		{
  		$i = 0;
  
    	while(file_exists($dir.$testName))
    	{
  			$i++;
  			$count = strrev($i);
    		$testName = $fileArray[$fileName]['name'];
  			$testName = strrev($testName); //fig.jeh
  			$arrName = explode ( ".", $testName, 2); // 1=fig 2=jeh
    		$arrName[1] = ".)".$count."(emaneR".$arrName[1]; //.($i)emaner jeh
    		$testName = $arrName[0].$arrName[1]; //fig.($i)emaner jeh
  			$testName = strrev($testName); //hej rename($i).gif
    	}
  	}
	if(!move_uploaded_file($fileArray[$fileName]['tmp_name'], $dir.$testName))
    {
		$error->guide("Major error", "Major error, this could be due to a permission error please check the permissions on the server.", false);
	}
		
		$find = $db->SQLsecure($find);
		$testName = $db->SQLsecure($testName);
		$description = $db->SQLsecure($description);
		
		$sql = "INSERT INTO _'pfx'_smilies (find, fileName, description) VALUES('".$find."','".$testName."','".$description."')";
		$db->runSQL($sql);
	}
	
	function getFolder($folder)
	{
		$error = new errorHandler;
		$images="";
		$folder = str_replace("/","",$folder);
		$path = "images/smilies/".$folder;
		
		if(is_dir($path))
		{
			if ($handle = @opendir($path))
			{
				$i=0;
				
				while (false !== ($file = readdir($handle))) 
				{
					if ($file != "." && $file != ".." && $file != "Thumbs.db")
					{
						$images[$i] = $file;
						$i++;
					}
				}
				closedir($handle);
			}
			else
			{
				$error->guide("Directory error", "Directory dosen't exist or no permission to read please check permissions.", false);
			}
		}
		else
		{	
			$error->guide("Directory dosen't exist", "Directory dosen't exist", false);
		}
		
		return $images;
		
	}
	
	function addFolder($find,$images,$description)
	{
		$lenght = count($images);
		$db = new dbHandler;
		
		$sql="INSERT INTO _'pfx'_smilies (find,fileName,description) VALUES ('";
		
		for($i=0; $i<$lenght; $i++)
		{
			if($i<$lenght-1)
			{
				$sql = $sql.$db->SQLsecure($find[$i])."','".$db->SQLsecure($images[$i])."','".$db->SQLsecure($description[$i])."'),('";
			}
			else
			{
				$sql = $sql.$db->SQLsecure($find[$i])."','".$db->SQLsecure($images[$i])."','".$db->SQLsecure($description[$i])."')";
			}
		}
		$db->runSQL($sql);
	}

	function getOne($smilieID) {
		$db = new dbHandler;
		$smilieID = $db->SQLsecure($smilieID);
		$sql = "SELECT * FROM _'pfx'_smilies WHERE smilieID = '".$smilieID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		if($db->numRows($result) == 0)
			return false;
		$smilie['smilieID'] = $row->smilieID;
		$smilie['find'] = $row->find;
		$smilie['fileName'] = $row->fileName;
		$smilie['description'] = $row->description;
		return $smilie;
	}

	function getAll() {
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_smilies";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i = 0;
		while($row = $db->fetchArray($result)) {
			$smilie[$i]['smilieID'] = $row['smilieID'];
			$smilie[$i]['find'] = $row['find'];
			$smilie[$i]['fileName'] = $row['fileName'];
			$smilie[$i]['description'] = $row['description'];
			$i++;
		}
		return $smilie;	
	}

	function remove($smilieID)
	{
		$db = new dbHandler;
		$smilieID = $db->SQLsecure($smilieID);
		$error = new errorHandler;
		$sql = "SELECT fileName FROM _'pfx'_smilies WHERE smilieID = '".$smilieID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$fileName = $row->fileName;
		//$dir = str_replace("smilieManagement.php", "", $_SERVER['PHP_SELF']);
		$dir = "images/smilies/";
		$sql = "DELETE FROM _'pfx'_smilies WHERE smilieID = '".$smilieID."'";
		$db->runSQL($sql);

		if(file_exists($dir.$fileName))
		{
			if(!unlink($dir.$fileName))
			{
				$error->error("Unable to remove", "Unable to remove avatar, this can be due to a permission error please control the permissions on the server.");
			}
  		}
		else
		{
			$error->error($_SERVER['PHP_SELF'], $dir.$fileName);
		}
	}

	function edit($smilieID,$find,$fileName,$description) {
		$db = new dbHandler;
		$smilieID = $db->SQLsecure($smilieID);
		$find = $db->SQLsecure($find);
		$fileName = $db->SQLsecure($fileName);
		$description = $db->SQLsecure($description);
		
		$sql = "UPDATE _'pfx'_smilies SET find = '".$find."',fileName = '".$fileName."', description = '".$description."' WHERE smilieID = '".$smilieID."'";
		$db->runSQL($sql);
	}
}
?>