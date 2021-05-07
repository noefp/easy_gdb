<?php


function test_input($data) {
  $data = stripslashes($data);
  $data = preg_replace('/[\<\>]+/','',$data);
  $data = htmlspecialchars($data);

  return $data;
}


?>

