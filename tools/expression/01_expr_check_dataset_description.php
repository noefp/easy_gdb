<?php
  // ############################################################### DATASET TITLE AND DESCRIPTION
  
  $expr_img_array = [];
  $description_files_found=false; //Variable that indicates if the dataset information files exist to be displayed later
  
  if ($dataset_name) {
    echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
  }
  
  if ( file_exists("$expression_path/expression_info.json") ) {
    $annot_json_file = file_get_contents("$expression_path/expression_info.json");
    $annot_hash = json_decode($annot_json_file, true);
    
    if ($annot_hash[$dataset_name_ori]["description"]) {
    
      $desc_file = $annot_hash[$dataset_name_ori]["description"];

      if ( file_exists("$custom_text_path/expr_datasets/$desc_file") ) {
        
        // echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
        echo "<h2 style=\"font-size:20px\">$r_key</h2>";
        $description_files_found=true; // the dataset information files exist
      }
      // else {
      //   echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
      // }
    }
    
    if ($annot_hash[$dataset_name_ori]["images"]) {
      $expr_img_array = $annot_hash[$dataset_name_ori]["images"];
    }
    
    // print("<pre>".print_r($expr_img_array,true)."</pre>");
    
  }

?>