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
class avatarHandler {
	function avatarHandler() {}
	
	function add($fileArray, $fileName, $name, $personal=false)
	{
		$db = new dbHandler;
		$error = new errorHandler;
		$filename2 = $fileArray[$fileName]['name'];
		if($personal)
			$path = "./images/avatars/personal/";
		else
			$path = "./images/avatars/public/";	
		if(file_exists($path.$filename2)) {
			if(!$sufix = strrchr($filename2, "."))
				$sufix = "";
			$prefix = substr($filename2,0,-strlen($sufix));
			$i = 1;
			while(file_exists($path.$prefix."(".$i.")".$sufix))
				$i++;
			$filename2 = $prefix."(".$i.")".$sufix;	
			
  			/*$i = 0;
    		while(file_exists($path.$testName)) {
  				$i++;
	  			$count = strrev($i);
	    		$testName = $fileArray[$fileName]['name'];
	  			$testName = strrev($testName); //fig.jeh
	  			$arrName = explode ( ".", $testName, 2); // 1=fig 2=jeh
	    		$arrName[1] = ".)".$count."(emaneR".$arrName[1]; //.($i)emaner jeh
	    		$testName = $arrName[0].$arrName[1]; //fig.($i)emaner jeh
	  			$testName = strrev($testName); //hej rename($i).gif
    		}*/
  		}
		
		if(!move_uploaded_file($fileArray[$fileName]['tmp_name'], $path.$filename2)) {
			$error->guide("Major error", "Major error, this could be due to a permission error please check the permissions on the server.", false);
		}
		if(!$personal) {
			$name = $db->SQLsecure($name);
			$testName = $db->SQLsecure($filename2);
			$sql = "INSERT INTO _'pfx'_avatars (fileName, name) VALUES('".$filename2."','".$name."')";
			$db->runSQL($sql);
		}	
		return $filename2;
	}
	
	function getFolder($folder)
	{
		$error = new errorHandler;
		$images="";
		$folder = str_replace("/","",$folder);
		$path = "./images/avatars/public/".$folder;
		
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
	
	function addFolder($images,$shortName)
	{
		$lenght = count($images);
		$db = new dbHandler;
		
		$sql="INSERT INTO _'pfx'_avatars (name, fileName) VALUES ('";
		
		for($i=0; $i<$lenght; $i++)
		{
			if($i<$lenght-1)
			{
				$sql = $sql.$db->SQLsecure($shortName[$i])."','".$db->SQLsecure($images[$i])."'),('";
			}
			else
			{
				$sql = $sql.$db->SQLsecure($shortName[$i])."','".$db->SQLsecure($images[$i])."')";
			}
		}
		$db->runSQL($sql);
	}

	function getOne($avatarID) {
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_avatars WHERE avatarID = '".$avatarID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		if($db->numRows($result) == 0)
			return false;
		$avatar['avatarID'] = $row->avatarID;
		$avatar['fileName'] = $row->fileName;
		$avatar['name'] = $row->name;
		return $avatar;
	}

	function getAll() {
		$db = new dbHandler;
		$sql = "SELECT * FROM _'pfx'_avatars";
		$result = $db->runSQL($sql);
		if($db->numRows($result) == 0)
			return false;
		$i = 0;
		while($row = $db->fetchArray($result)) {
			$avatar[$i]['avatarID'] = $row['avatarID'];
			$avatar[$i]['fileName'] = $row['fileName'];
			$avatar[$i]['name'] = $row['name'];
			$i++;
		}
		return $avatar;	
	}

	function remove($avatarID)
	{
		$db = new dbHandler;
		$avatarID = $db->SQLsecure($avatarID);
		 
		$error = new errorHandler;
		$sql = "SELECT fileName FROM _'pfx'_avatars WHERE avatarID = '".$avatarID."'";
		$result = $db->runSQL($sql);
		$row = $db->fetchObject($result);
		$fileName = $row->fileName;
		//$dir = str_replace("avatarManagement.php", "", $_SERVER['PHP_SELF']);
		$dir = "./images/avatars/public/";
		$sql = "DELETE FROM _'pfx'_avatars WHERE avatarID = '".$avatarID."'";
		$db->runSQL($sql);
		$sql = "UPDATE _'pfx'_members SET avatar=NULL WHERE avatar='public/".$fileName."'";
		$db->runSQL($sql);

		if(file_exists($dir.$fileName))
		{
			if(!@unlink($dir.$fileName))
			{
				$error->error("Unable to remove", "Unable to remove avatar, this can be due to a permission error please control the permissions on the server.");
			}
  		}
		else
		{
			$error->error($_SERVER['PHP_SELF'], $dir.$fileName);
		}
		
	}

	function edit($avatarID,$fileName,$name) {
		$db = new dbHandler;
		$name = $db->SQLsecure($name);
		$fileName = $db->SQLsecure($fileName);
		
		$sql = "UPDATE _'pfx'_avatars SET fileName = '".$fileName."', name= '".$name."' WHERE avatarID = '".$avatarID."'";
		$db->runSQL($sql);
	}
}
?>