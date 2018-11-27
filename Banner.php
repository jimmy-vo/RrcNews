<?php if (!defined("Include_HeaderScript"))	include("HeaderScript.php");?>

<div id="banner">
	<img src="image/logo.png" alt="#">
	<h1><a href="Index.php" >RrcNews</a></h1>
	<p>
		<?=Sql::getInstance()->authentication()["UserName"]?>, 
		<a href="<?= (Sql::getInstance()->authentication()["Id"]==="-1")?"Authentication.php":"Authentication.php?logout" ?>">
			<?= (Sql::getInstance()->authentication()["Id"]==="-1")?"Log In":"Log Out" ?>
		</a>
	</p>
	<a href="<?= HeaderScript::getInstance()->conf_BannerLink ?>" id="bannerlink"><?= HeaderScript::getInstance()->conf_BannerText?></a>	
</div>