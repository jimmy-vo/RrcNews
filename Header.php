<?php if (!defined("Include_HeaderScript"))	include("HeaderScript.php");?>

<meta charset="utf-8">
<title><?= isset(HeaderScript::getInstance()->conf_PageTitle)? 
		HeaderScript::getInstance()->conf_PageTitle:
		"" ?></title>
<link rel="stylesheet" href="style.css" type="text/css">
<script >
	window.onload = function() {<?= 
		isset(HeaderScript::getInstance()->conf_Script)? 
		HeaderScript::getInstance()->conf_Script:
		"" ?>}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>