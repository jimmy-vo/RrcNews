// Hides or shows the username available and the username taken
// messages using CSS block property.
function usernameMessage(usernameAvailable) {
  let msgValid = document.querySelector('#username-valid');
  let msgInvalid = document.querySelector('#username-invalid');

  if (usernameAvailable) 
  {
    msgValid.style.display = 'inline';
    msgInvalid.style.display = 'none';
  } 
  else 
  {
    msgInvalid.style.display = 'inline';
    msgValid.style.display = 'none';
  }
}


$(window).on("load", function () {
  var target = $("#username");

  target.on('blur', function() {    

    let username = target.val();

    if (username === '') 
    {
      return;
    }

    // AJAX GET request to test the username for availability.
    fetch('username.php?username=' + username)
    .then(function(rawResponse) 
    { 
      return rawResponse.json(); // Promise for parsed JSON.
    })
    .then(function(response) 
    {
        // If the API check was successful.
      if (response['success']) {
        // Show the relevant username message (available / taken).
        usernameMessage(response['usernameAvailable'])

        // If the username is take put the focus back on the input
        // and select all text.
        if (! response['usernameAvailable']) {
          target.select();
        }
      }
    });
  });
});
