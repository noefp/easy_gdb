<?php include realpath('../../header.php'); ?>
<br>
<h1 style="font-size:26px">Expression Datasets</h1>
<br>
<?php

if ( file_exists("$expression_path/expression_info.json") ) {
  $annot_json_file = file_get_contents("$expression_path/expression_info.json");
  $annot_hash = json_decode($annot_json_file, true);
  
  foreach ($annot_hash as $r_key => $r_value) {
    if ($annot_hash[$r_key]["description"]) {
      
      $desc_file = $annot_hash[$r_key]["description"];
      if ( file_exists("$custom_text_path/expr_datasets/$desc_file") ) {
        $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$r_key);
        $data_set_name = str_replace("_"," ",$data_set_name);
        
        echo "<h2 style=\"font-size:20px\">$data_set_name</h2>";
        include("$custom_text_path/expr_datasets/$desc_file");
        echo"<hr>";
        echo"<br>";
      }
    }
  }
}

?>
<?php include realpath('../../footer.php'); ?>
