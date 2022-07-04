<h1 style="font-size:26px">Expression Datasets</h1>
<br>
<?php

if ( file_exists("$expression_path/expression_info.json") ) {
  $annot_json_file = file_get_contents("$expression_path/expression_info.json");
  $annot_hash = json_decode($annot_json_file, true);
  
  foreach ($annot_hash as $r_key => $r_value) {
    if ($annot_hash[$r_key]["description"]) {
      
      $desc_file = $annot_hash[$r_key]["description"];
      if ( file_exists("$expression_path/$desc_file") ) {
        
        echo "<h2 style=\"font-size:20px\">$r_key</h2>";
        include("$expression_path/$desc_file");
        echo"<br>";
      }
    }
  }
}

?>
