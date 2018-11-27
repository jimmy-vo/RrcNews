<?php
 /**
 * Edit.php
 *
 *	Edit, create
 *
 * @author     Jimmy Vo
 * @project    Assignment 4
 */
    session_start();
	if  (!defined("Include_Sql"))	include("Sql.php");	
	if  (!defined("Include_HeaderScript"))	include("HeaderScript.php");		
	HeaderScript::getInstance()->script_reset();	
	

	include("EditImage.php");	

	$imageNone = "image/none.jpeg";

	if (!isset($_POST['action']))
	{		
		
		if ((($id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT))===FALSE) || (strlen($id)===0))
		{
			unset($id);
		}

		if (isset($id))
		{
			HeaderScript::getInstance()->script_BannerText("Back to Article");
			HeaderScript::getInstance()->script_BannerLink("Index.php?id=".$id);

			if (($result = Sql::getInstance()->getNewsById($id))=== FALSE)
			{
				HeaderScript::getInstance()->script_alert("Fail to read news' ID ".$id);
				HeaderScript::getInstance()->script_navigate("Index.php");
			}
			$conf_imageSrc= ((isset($result["image"])) && $result["image"] !== "")? 
							"data:image/png;base64," .  base64_encode($result["image"]): $imageNone;

			HeaderScript::getInstance()->script_PageTitle("Editing ".$result["title"]);
		}
		else
		{			
			HeaderScript::getInstance()->script_PageTitle("Post News");
			$result = ['id' => "",'title' => "", 'content'=>""] ;
			$conf_imageSrc = $imageNone;
		}
	}
	else
	{
		$UserId = Sql::getInstance()->authentication()["Id"];

		if ($UserId === "-1")
		{
			HeaderScript::getInstance()->script_alert("Authentication error");
			HeaderScript::getInstance()->script_navigate("Index.php");

		}
		else
		{
			if ((($id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT))===FALSE) || (strlen($id)===0))
			{
				unset($id);
			}
			
			if (($isDelete = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Delete")
			{
				unset($isDelete);
			}

			if (($isRemoveImage = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING)) !== "Remove")
			{
				unset($isRemoveImage);
			}
			
			if (isset($isDelete))
			{
				if (Sql::getInstance()->deleteNewsById($id) === FALSE)
				{
					HeaderScript::getInstance()->script_alert("Fail delete news");
				}
				HeaderScript::getInstance()->script_navigate("Index.php");
			}
			else if (isset($isRemoveImage))
			{
				if (Sql::getInstance()->deleteImage($_POST, $UserId) === FALSE)
				{
					HeaderScript::getInstance()->script_alert("Fail delete image");
				}
				HeaderScript::getInstance()->script_navigate("Edit.php?id=".$id);
			}
			else
			{		
				$conf_imageSrc = $imageNone;
				
				if ((($content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING))===FALSE) || (strlen($content)<1))
				{
					$validateError = "The content can't be empty";
				}

				if ((($title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING))===FALSE) || (strlen($title)<1))
				{
					$validateError = "The title can't be empty";
				}

		    	if (($image = get_selected_image()) === FALSE)
		        {
					$validateError = "The image is invalid";
		        }
		        else
		        {
		        	$_POST['image']  = $image;
		        }				

		        if (isset($validateError))
		        {		    
					HeaderScript::getInstance()->script_alert($validateError);
		        }
		        else if (isset($id))
				{
					$conf_PageTitle = "Update";
					if (Sql::getInstance()->updateNews($_POST, $UserId) === FALSE)
					{
						HeaderScript::getInstance()->script_alert("Fail to update news");
					}
					else
					{
						// HeaderScript::getInstance()->script_alert("Update news successfully");
						HeaderScript::getInstance()->script_navigate("Index.php?id=".$id);
					}
				}
				else
				{
					$conf_PageTitle = "Insert";
					if (Sql::getInstance()->insertNews($_POST, $UserId) === FALSE)
					{
						HeaderScript::getInstance()->script_alert("Fail to post news");
					}
					else
					{
						// HeaderScript::getInstance()->script_alert("Post news successfully");
						HeaderScript::getInstance()->script_navigate("Index.php?id=".$id);
					}
				}
				$result = ['id' => $id,'title' => $title, 'content'=>$content] ;   	
			}
		}
	}	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include("Header.php");?>
	<script src="Edit.js"></script>
</head>
<body>
	<?php include("Banner.php");?>
    <div id="wrapper">			
		<?php include("menu.php");?>
		<form action="Edit.php" method="post"  enctype="multipart/form-data">
			<fieldset>
				<p id="leftedit">
					<input type="hidden" name="id"  value="<?php echo $result['id'] ?>" />
					<label for="title">Title</label>
					<input name="title" id="title" class="textbox" value="<?php echo $result['title'] ?>" />
					<label for="content">Content</label>
					<textarea name="content" class="textbox"  id="content"  ><?php echo $result['content'] ?></textarea>
					<br/>
					<input  class="button" type="submit"  name="action" value="<?php echo isset($id)? "Update" : "Create"?>" /> 
					<?php if (isset($id)): ?>
						<input  class="button" type="submit" name="action" value="Delete" />
					<?php endif  ?>
				<p>
				<p id="rightedit">
					<img src="image/remove.jpg" id="remove" alt="X">
					<label for="image">
						<img src="<?= $conf_imageSrc ?>" id="preview" alt="Image preview..."/>	
				        <input style="display: none;" type="file" name="image" id="image" onchange="previewFile();" onload="previewFile();" >
					</label>	
				<p>
			</fieldset>
		</form>
    </div>
</body>
</html>
