<?php


function test_input($data) {
  $data = stripslashes($data);
  $data = preg_replace('/[\<\>]+/','',$data);
  $data = htmlspecialchars($data);

  return $data;
}

// function test_input2($data) {
//   $data = preg_replace('/[\<\>\t\;]+/', ' ', $data);
//   $data = htmlspecialchars($data);
//   if (preg_match('/\s+/', $data)) {
//     $data_array = explode(' ', $data, 99);
//     foreach ($data_array as $key => &$value) {
//       if (strlen($value) < 3) {
//         unset($data_array[$key]);
//         }
//       }
//       $data = implode(' ', $data_array);
//   }
//   $data = stripslashes($data);
//   return $data;
// }

function get_dir_and_files($dir_name) {
    $file_array = array();

    $pattern='/^\./';
    if (is_dir($dir_name)){
      if ($dh = opendir($dir_name)){
        while (($file_name = readdir($dh)) !== false){
          $is_not_file = preg_match($pattern, $file_name, $match);
          if (!$is_not_file) {
            // echo $file_name."<br>";
            array_push($file_array,$file_name);
          }
        }
      }
    }

    rsort($file_array);
    return $file_array;
}


?>

