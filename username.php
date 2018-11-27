<?php

  if  (!defined("Include_Sql")) include("Sql.php"); 

  $response = [
    'success' => false,
    'usernameAvailable' => false
  ];

  if (isset($_GET['username']) && (strlen($_GET['username']) !== 0)) {
    $response['usernameAvailable'] = ! Sql::getInstance()->IsRegisterdUserName($_GET['username']);
    $response['success'] = true;
  } 

  header('Content-Type: application/json');

  echo json_encode($response);
?>