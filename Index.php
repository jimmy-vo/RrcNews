<?php	
 /**
 * Index.php
 *
 *	show 5 blogs or show full blog
 *
 * @author     Jimmy Vo
 * @project    Assignment 4
 */
    session_start();
	if  (!defined("Include_Sql"))	include("Sql.php");	
	if  (!defined("Include_HeaderScript"))	include("HeaderScript.php");	
	HeaderScript::getInstance()->script_reset();	
	HeaderScript::getInstance()->script_PageTitle("RRCNEWS");
	if (Sql::getInstance()->authentication()["UserTypeId"] === "0")
	{
		HeaderScript::getInstance()->script_BannerText("Create News");
		HeaderScript::getInstance()->script_BannerLink("Edit.php");
	}
	
	if ((($id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT))===FALSE) || (strlen($id)===0))
	{
		unset($id);
	}
	
	if (isset($id))
	{	

		if ((($commentId = filter_input(INPUT_POST, 'commentid', FILTER_SANITIZE_NUMBER_INT))===FALSE) || (strlen($id)===0))
		{
			unset($commentId);
		}
		if (($isDeleteComment = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Delete")
		{
			unset($isDeleteComment);
		}
		if (($isHideComment = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Hide")
		{
			unset($isHideComment);
		}
		if (($isShowComment = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Show")
		{
			unset($isShowComment);
		}
		if (($isNewComment = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Comment")
		{
			unset($isNewComment);
		}

		if (isset($isDeleteComment))
		{
			if (Sql::getInstance()->deleteCommentById($commentId) === FALSE)
			{
				HeaderScript::getInstance()->script_alert("Fail to delete comment");
			}

		}
		else if (isset($isNewComment))
		{
			if (Sql::getInstance()->insertComment($_POST, Sql::getInstance()->authentication()["Id"], $id) === FALSE)
			{
				HeaderScript::getInstance()->script_alert("Fail to insert comment");
			}
		}
		else if (isset($isHideComment)||isset($isShowComment))
		{
			if (Sql::getInstance()->setCommentVisibility($commentId, isset($isHideComment)? "1":"0") === FALSE)
			{
				HeaderScript::getInstance()->script_alert("Fail to switch comment's visibility");
			}
		}

		if ((($news = Sql::getInstance()->getNewsById($id)) === FALSE) && (count($news) ===0))
		{
			HeaderScript::getInstance()->script_alert("Fail to fetch news id ".$id);
			HeaderScript::getInstance()->script_navigate("Index.php");
		}

		$comments = Sql::getInstance()->getComments($id);
	}
	else
	{		
		if ((($sortby = filter_input(INPUT_POST, 'sortby', FILTER_SANITIZE_STRING)) === FALSE) || (strlen($sortby)===0) || ($sortby === "FALSE"))
		{
			$sortby = FALSE;
		}

		if (($news = Sql::getInstance()->getNewsTop($sortby)) === FALSE)
		{
			HeaderScript::getInstance()->script_alert("Fail to fetch news");
			HeaderScript::getInstance()->script_navigate("Index.php");
		}
		else if(count($news) ===0)
		{
			HeaderScript::getInstance()->script_alert("No news in the database");
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head><?php include("Header.php");?></head>
<body>
	<?php include("Banner.php");?>
    <div id="wrapper">
		<?php include("menu.php");?>
		<div id="all_blogs">	
			<!-- single -->
			<?php if (isset($id)): ?>
				<div class="blog_post">
					<h2>
						<a href="Index.php?id=<?= $news["id"] ?>"><?= $news["title"] ?></a>
					</h2>
					<p>
						<small>
							Posted on <?= $news["timecreated"] ?> by <?= $news["username"] ?>
							<?php if ($news["timecreated"] !== $news["timeupdated"]): ?>
								, modified on <?= $news["timeupdated"] ?>
							<?php endif  ?>
							<?php if (Sql::getInstance()->authentication()["Id"] === $news["userid"]): ?>	
								<a href="Edit.php?id=<?= $news["id"] ?>" >edit</a>
							<?php endif  ?>
						</small>
					</p>

					<?php if ((isset($news["image"])) && $news["image"] !== ""): ?>
						<img class="big_image" src="data:image/png;base64,<?= base64_encode($news["image"]) ?>" alt="#" />
					<?php endif  ?>
					<div class="blog_content\"> <?= $news["content"] ?> </div>
					<!-- comments -->
					<div class="comments_wrapper">	
						<?php foreach($comments as $item): ?>		    					
							<?php if ((Sql::getInstance()->authentication()["UserTypeId"] === "0") || ($item["hidden"] === "0")): ?>
								<form class="comments" action="Index.php?id=<?= $news["id"] ?>" method="post">
									<p class="commentinfo">
										<?= ($item["userid"] !== "-1")? $item["username"] : $item["guestname"] ?>
										<small>
											commented on <?= $item["timecreated"] ?>	
											<?php if ((Sql::getInstance()->authentication()["Id"] === $item["userid"] && $item["userid"] != "-1") || 
													(Sql::getInstance()->authentication()["UserTypeId"] === "0")): ?>	
												<input type="submit" name="action" value="Delete" />
											<?php endif  ?>
											<?php if (Sql::getInstance()->authentication()["UserTypeId"] === "0"): ?>
												<input type="submit" name="action" value="<?=($item["hidden"] === "0")?"Hide":"Show"?>" />													
											<?php endif  ?>
										</small>
									</p>
									<input type="hidden" name="commentid"  value="<?php echo $item['id'] ?>" />
									<p class="commentContent"><?= $item["content"] ?></p>
								</form>
							<?php endif  ?>
						<?php endforeach ?>
						<form id="commentinput" action="Index.php?id=<?= $news["id"] ?>" method="post">
								<?php if (Sql::getInstance()->authentication()["Id"] === "-1"): ?>
									<label  for="guestname">Name</label>
								<?php endif  ?>
								<input <?= (Sql::getInstance()->authentication()["Id"] === "-1")? "":"type=\"hidden\""?> id="guestname" name="guestname" class="textbox" value="guestuser" />
								<label for="content">Comment</label>
							<textarea name="content" class="textbox" id="content" ></textarea>
							<input class="button" type="submit"  name="action" value="Comment" />
						</form>
					</div>		
				</div>
				
			<!--  Index  -->
			<?php else: ?>	
				<div>
					<?php if (Sql::getInstance()->authentication()["Id"] !== "-1"): ?>		
						<div id="sort" >	
							<form action="Index.php" method="post" >
								<select name="sortby" onchange="this.form.submit();" >
									<option <?= ($sortby=== FALSE)? 			"selected":"" ?> value="FALSE"				>None</option>
									<option <?= ($sortby=== "Title ASC")? 		"selected":"" ?> value="Title ASC"			>Title (ACS)</option>
									<option <?= ($sortby=== "Title DESC")? 		"selected":"" ?> value="Title DESC"			>Title (DSC)</option>
									<option <?= ($sortby=== "TimeCreated ASC")? "selected":"" ?> value="TimeCreated ASC"	>Created (ACS)</option>
									<option <?= ($sortby=== "TimeCreated DESC")?"selected":"" ?> value="TimeCreated DESC"	>Created (DSC)</option>
									<option <?= ($sortby=== "TimeUpdated ASC")? "selected":"" ?> value="TimeUpdated ASC"	>Updated (ACS)</option>
									<option <?= ($sortby=== "TimeUpdated DESC")?"selected":"" ?> value="TimeUpdated DESC"	>Updated (DSC)</option>
								</select>
							</form>
							<p>Sort by </p> 
						</div>
					<?php endif  ?>

    				<?php foreach($news as $item): ?>
						<div class="blog_post">
							<?php if ((isset($item["image"])) && $item["image"] !== ""): ?>
								<img class="small_image left" src="data:image/png;base64,<?= base64_encode($item["image"]) ?>" alt="#" />
							<?php else: ?>		
								<img class="small_image left" src="image/none.jpeg" alt="#"/>
							<?php endif  ?>
							<h2>
								<a href="Index.php?id=<?= $item["id"] ?>"><?= ((strlen($item["title"])>50) ? (substr($item["title"],0,50)." ...") : ($item["title"])) ?></a>
							</h2>
							<p>
								<small>
									Posted on <?= $item["timecreated"] ?> by <?= $item["username"] ?>
									<?php if ($item["timecreated"] !== $item["timeupdated"]): ?>
										, modified on <?= $item["timeupdated"] ?>
									<?php endif  ?>
									<?php if (Sql::getInstance()->authentication()["Id"] === $item["userid"]): ?>	
										<a href="Edit.php?id=<?= $item["id"] ?>" >edit</a>
									<?php endif  ?>
								</small>
							</p>

							<div class="blog_content\"> 
								<?= ((strlen($item["content"])>250) ? (substr($item["content"],0,250)." <a href=\"Index.php?id=".$item["id"]."\">Read More...</a>") : ($item["content"])) ?> 
							</div>
							<div style="clear: both;"></div>
						</div>
						<?php endforeach ?>
					</div>	
			<?php endif  ?>
		</div> 
		<div style="clear: both;"></div>
    </div> 
</body>
</html>
