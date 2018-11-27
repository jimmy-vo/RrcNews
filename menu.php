<div id="menu">
	<h1>Articles:</h1>
	<ul>
		<?php foreach(Sql::getInstance()->getNewsTitleAll() as $item): ?>
			<li>
				<a href="Index.php?id=<?= $item["id"] ?>"><?= ((strlen($item["title"])>30) ? (substr($item["title"],0,30)." ...") : ($item["title"])) ?></a>
			</li>
		<?php endforeach ?>
	</ul>
</div>