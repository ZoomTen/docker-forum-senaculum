<?php
require("include/top.php");
global $forumVariables;
if(!$forumVariables['adminInlogged']) {
	header("location: ../index.php");
	die;
}
require_once("classes/menuHandler.php");
$menu = new menuHandler;
$forumAdminMenu = $menu->generateForumAdminMenu();
$generalAdminMenu = $menu->generateGeneralAdminMenu();
?>
<html>
	<head>
		<title>
			Menu
		</title>
		<link rel="stylescheet" type="text/css" href="style.css"/>
	</head>
	<body>
		<h2>Menu</h2>
		<table style="width:100%" cellpadding="0" cellspacing="0">
			<?php
				foreach($forumAdminMenu as $element) {
			?>
			<tr>
				<td>
					<a href="javascript: <?php echo $element['onClick']; ?>"><?php echo $element['name']; ?></a>	
				</td>
			</tr>
			<?php
				}
			?>
		</table>
		<br/>
		<table style="width:100%" cellpadding="0" cellspacing="0">
			<?php
				foreach($generalAdminMenu as $element) {
			?>
			<tr>
				<td>
					<a href="javascript: <?php echo $element['onClick']; ?>"><?php echo $element['name']; ?></a>	
				</td>
			</tr>
			<?php
				}
			?>
		</table>
	</body>
</html>
