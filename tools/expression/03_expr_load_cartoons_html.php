<!-- #####################             Cartoons             ################################ -->

<center>

<?php
  
if ( file_exists("$expression_path/expression_info.json") ) {
  
  if ($annot_hash[$dataset_name_ori]["cartoons"]) {
  
    $cartoon_conf = $annot_hash[$dataset_name_ori]["cartoons"];

    //echo "<p>annot_hash cartoons exists and was found!</p>";

    if ($expr_cartoons && file_exists($expression_path."/$cartoon_conf") ) {
      
      $cartoons_json = file_get_contents($expression_path."/$cartoon_conf");
      
      // echo "<p>annot_hash cartoons_json exists and was found!</p>";
      //var_dump($cartoons_json);

      $jcartoons = json_decode($cartoons_json, true);
  
      $max_w = 100;
      $max_h = 100;
      $max_x = 10;
      $max_y = 10;
      
      foreach($jcartoons["cartoons"] as $img) {
        echo "<img id='".$img["img_id"]."' src='".$images_path."/expr/cartoons/".$img["image"]."' style=\"display:none\">";
    
        if ($img["width"] > $max_w) {
          $max_w = $img["width"];
        }
        if ($img["height"] > $max_h) {
          $max_h = $img["height"];
        }
        if ($img["x"] > $max_x) {
          $max_x = $img["x"];
        }
        if ($img["y"] > $max_y) {
          $max_y = $img["y"];
        }
      } //end foreach
  
      $canvas_w = $max_w + $max_x;
      $canvas_h = $max_h + $max_y;

      echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#cartoons_frame" aria-expanded="true">';
      echo '<i class="fas fa-sort" style="color:#229dff"></i> Expression images';
      echo '</div>';

      echo '<div id="cartoons_frame" class="row collapse hide" style="margin:0px; border:2px solid #666; padding-top:7px">';
    
      echo "<div class=\"form-group d-inline-flex\" style=\"width: 450px;\">";
      echo "<label for=\"sel_cartoons\" style=\"width: 150px; margin-top:7px\">Select gene:</label>";
      echo "<select class=\"form-control\" id=\"sel_cartoons\">";
      foreach ($found_genes as $gene) {
        echo "<option value=\"$gene\">$gene</option>";
      }
      echo "</select>";
      echo "</div>";

      echo '<div class="d-inline-flex" style="margin:10px">';
      echo '<span class="circle" style="background-color:#C7FFED"></span> Lowest <1';
      echo '<span class="circle" style="background-color:#CCFFBD"></span> >=1';
      echo '<span class="circle" style="background-color:#FFFF5C"></span> >=2';
      echo '<span class="circle" style="background-color:#FFC300"></span> >=10';
      echo '<span class="circle" style="background-color:#FF5733"></span> >=50';
      echo '<span class="circle" style="background-color:#C70039"></span> >=100';
      echo '<span class="circle" style="background-color:#900C3F"></span> >=200';
      echo '<span class="circle" style="background-color:#581845"></span> >=5000';
      echo '</div>';

      echo "<div class=\"row\">";
    
      echo "<div class=\"pull-left\">";
        echo "<div class=\"cartoons_canvas_frame\">";
          echo "<div id=\"canvas_div\">";
            echo '<div id=myCanvas>';
              echo "Your browser does not support the HTML5 canvas";
            echo "</div>";
          echo "</div>";
          echo "<br>";
        echo "</div>";
      echo "</div>";
    
        echo "<div class=\"pull-right\">";
      
        echo "<ul id=\"cartoon_labels\" style=\"text-align:left\">";
        foreach ($cartoons_all_genes[$found_genes[0]] as $sample_name => $ave_value) {
        
          echo "<li class=\"cartoon_values pointer_cursor\" id=\"$sample_name"."_kj_image\">".$sample_name.": ".$ave_value."</li>";
        }
        
        echo "</ul>";
      
        echo "</div>";
    
      echo "</div>";

      echo '</div>';

    }//end cartoons conf
    // else {
    //   echo "<p>cartoons.json file was not found!</p>";
    // }
    
  }//end cartoons hash
  // else {
  //   echo "<p>cartoons hash was not found!</p>";
  // }
  
} //end expression_info.json
// else {
//   echo "<p>expression_info.json was not found!</p>";
// }
?>
</center>

