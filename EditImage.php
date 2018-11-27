<?php

	include ('lib\php-image-resize-master\lib\ImageResize.php');
	
    function file_is_an_image($temporary_path, $new_path) 
	{
        $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
        
        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = getimagesize($temporary_path)['mime'];
        
        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
        
        return $file_extension_is_valid && $mime_type_is_valid;
    }
	
    function file_upload_path($original_filename)
	{
		$upload_subfolder_name = 'uploads';
		$current_folder = dirname(__FILE__);
		$path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
		return join(DIRECTORY_SEPARATOR, $path_segments);
    }

    function get_selected_image()
    {
    	
    	if (isset($_FILES['image']) && ($_FILES['image']['error'] === 0)) 
	    {
	        $image_filename       = $_FILES['image']['name'];
	        $temporary_image_path = $_FILES['image']['tmp_name'];
	        $new_image_path       = file_upload_path($image_filename);

	        if (file_is_an_image($temporary_image_path, $new_image_path)) 
			{ 
	            move_uploaded_file($temporary_image_path, $new_image_path);
				$image = new  \Gumlet\ImageResize($new_image_path);
				
				$image->scale(400);
				$image->save(file_upload_path(
					pathinfo($new_image_path, PATHINFO_FILENAME ).
					"_medium.".
					pathinfo($new_image_path, PATHINFO_EXTENSION)));
				
				$image->scale(50);
				$image->save(file_upload_path(
					pathinfo($new_image_path, PATHINFO_FILENAME ).
					"_thumbnail.".
					pathinfo($new_image_path, PATHINFO_EXTENSION)));


				return fopen($new_image_path, 'rb');
	        }
	        else
	        {
				return FALSE;
	        }
	    }
        else
        {
			return NULL;
        }
    }
?>