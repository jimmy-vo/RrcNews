var fileTypes = 
[
  'image/jpeg',
  'image/pjpeg',
  'image/png'
]

var imageNone = 'image/none.jpeg';           

function validFileType(file) 
{
  for(var i = 0; i < fileTypes.length; i++) 
  {
    if(file.type === fileTypes[i]) 
    {
      return true;
    }
  }
  return false;
}

function previewFile() 
{
  var file    = $('#image').prop('files')[0];
  var reader  = new FileReader();

  reader.onloadend = function () 
  {
    $('#preview').attr('src', reader.result);
  }
  
  if (file && validFileType(file))  
  {
    reader.readAsDataURL(file);
     $('#remove').show();
  } 
  else 
  {
    $('#preview').attr('src', imageNone);
     $('#remove').hide();
  }
}

$(window).on("load", function () {	
  $('#remove').click(function(){
    $("#image").val('');
  	previewFile();
  });

 if ($('#preview').attr('src') === imageNone)
 {
   $('#remove').hide();
 }
});
