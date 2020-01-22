<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
global $forumVariables;
global $forumSettings; 
?>
<link rel="stylesheet" type="text/css" href="style.css"/>
<?php
if(!empty($_GET['alert'])) {
?>
<script type="text/javascript">
	alert("<?php echo $_GET['alert']; ?>");
</script>
<?php
}
global $alert;
if(!empty($alert)) {
?>
<script type="text/javascript">
	<!--
	alert("<?php echo $alert; ?>");
	//-->
</script>
<?php
}
?>
<script type="text/javascript">
<!--
<?php
if(isset($forumVariables['inloggedNow']) && $forumVariables['inloggedNow']) { //If you logged in, reload page so that the cookie will be saved
?>
	window.location = "<?php echo $page; if(!empty($_GET['id'])) echo "?id=".$_GET['id'];?>";
<?php
	die("//-->\n</script>".$lang['javascriptMustBeEnabled']);
}
?>
	
//Functions that is used for the whole forum

function popup(page,width,height) {
	var name = page.replace('.php','');
	while(name.match('/'))
		name = name.replace('/','');
	while(name.match('&'))	
		name = name.replace('&','');
	name = name.replace('?','');
	var regex = new RegExp('\=','g');
	name = name.replace(regex,'');
	var popupWindow;
	popupWindow = window.open(page,name,'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+width+', height='+height+', top=100, left=100');
	if(!popupWindow)
		alert("<?php echo $lang['popupsMustBeAllowed']; ?>");
}

function confirmProcess(text,trueUrl) {
	if(confirm(text)) {
		window.location = trueUrl;
	}
}	
<?php
global $runJavascript;
if(!empty($runJavascript)) {
	foreach($runJavascript as $javascript) {
		echo $javascript."\n";
	}
}
?>
//-->
</script>