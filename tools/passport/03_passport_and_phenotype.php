<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include_once realpath("$easy_gdb_path/tools/common_functions.php");?>

<!-- Load the QR library -->
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<!-- Load map libraries -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<?php
//----- Get info from different JSON

  $pass_dir = test_input($_GET["pass_dir"]);
  $sp_name = "";
  // get info from passport.json
  if ( file_exists("$passport_path/$pass_dir/passport.json") ) {
    $pass_json_file = file_get_contents("$passport_path/$pass_dir/passport.json");
    $pass_hash = json_decode($pass_json_file, true);

    $numeric_to_cathegoric_json = $pass_hash["convert_to_cathegoric"]; 
    $translator_file = $pass_hash["translator"];
    $sp_name = $pass_hash["sp_name"];
    $featured_descriptors_file = $pass_hash["featured_descriptors"];
    
  }

//----- NUMERIC TO CATHEGORIC
  $convert_json_path = "$passport_path/$pass_dir/$numeric_to_cathegoric_json";
  //echo $convert_json_path;
  $convert_json = [];
  if (file_exists($convert_json_path) ) {
    $convert_json_file = file_get_contents($convert_json_path);
    $convert_json = json_decode($convert_json_file, true);
    //var_dump($convert_json);
  } else {
    //echo "<br>NOT FOUND convert_to_cathegoric";
    //var_dump($convert_json_path);
  }

//----- TRANSLATOR
  $translator_json_path = "$passport_path/$pass_dir/$translator_file";
  //echo $translator_json_path;
  $translator_json = [];
  if (file_exists($translator_json_path) ) {
    $translator_json_file = file_get_contents($translator_json_path);
    $translator_json = json_decode($translator_json_file, true);
    //var_dump($translator_json);
  } else {
    //echo "<br>NOT FOUND translator";
    //var_dump($translator_json_path);
  }

//----- FEATURED DESCRIPTORS
  $featured_descriptors_path = "$passport_path/$pass_dir/$featured_descriptors_file";
  //echo $featured_descriptors_path;
  $featured_descriptors = [];
  if (file_exists($featured_descriptors_path) ) {
      $featured_descriptor_json_file = file_get_contents($featured_descriptors_path); // Get the info
      $featured_descriptors_json = json_decode($featured_descriptor_json_file, true); // Convert JSON -> array
      //var_dump($featured_descriptors_json); // Funciona
  } else {
    //echo "<br> NOT FOUND featured descriptors file";
  }
?>


<?php

$descriptor_primary_name = "";

function write_descriptor_files($file_path, $acc_name, $descriptors_obj, $root_path, $path_img, $convert_json, $translator_json, $featured_descriptors_json, $all_featured_descriptors, $sp_name) {
  //var_dump($convert_json); // funciona
  //var_dump($translator_json); // funciona

  $file = preg_replace('/.+\//', '', $file_path);

  // Create $featured_list to print it later
  if ($featured_descriptors_json[$file]) {
    $featured_list = $featured_descriptors_json[$file];
    //echo "featured_list is <b>correct</b>"; / funciona
  } else {
    $featured_list = [];
    //echo "featured_list is empty";
  }
  
  $obj_average = [];
  
  if (file_exists($file_path) ) {
    $tab_file = file($file_path);
    $header_line = trim(array_shift($tab_file) );
    $header = explode("\t", $header_line);
    
    foreach ($tab_file as $line) {
      $cols = explode("\t", $line);
      
      if (array_filter($cols, function($value) use ($acc_name) { 
        return strnatcasecmp($value, $acc_name) == 0; 
      } ) ) {
          
        foreach ($cols as $col_index => $col_value) {
          
          $descriptor_name = $header[$col_index];
          $descriptor_value = $col_value;
          //echo "<p>descriptor_name: $descriptor_name, descriptor_value: $descriptor_value</p>";
          
          if ($descriptor_value){
            //save data in obj_average to print when all possible replicates are collected
            if ($obj_average[$descriptor_name] ) {
              array_push($obj_average[$descriptor_name], $descriptor_value);
            } else {
              $obj_average[$descriptor_name] = []; // If not, create it
              array_push($obj_average[$descriptor_name], $descriptor_value);
            }
          } // end IF descriptor value
        } // col foreach
      } // end array_filter
    } // line foreach
    

    $section = str_replace("_", " ", $file);
    $section = str_replace(".txt", "", $section);
    echo "<div style=\"background-color:#e0ea68 ; border: 2px solid #e0ea68\"><i class=\"fa\">&#xf105;</i><b> $section</b></div><br>";
    
    foreach($obj_average as $descriptor_name => $descriptor_value_list) {
          
      $unique_list = array_unique($descriptor_value_list);
      $joint_unique_list = join("/", $unique_list);
      //echo "$descriptor_name: $joint_unique_list <br>";
      
      // get info from JSON
      if (($translator_json[$file][$descriptor_name] ) ) {
        $translator_obj = $translator_json[$file][$descriptor_name];
        $descriptor_primary_name = $translator_obj["primary_descriptor"];
        $descriptor_secondary_name = $translator_obj["secondary_descriptor"];
      } else {
        //echo "Translation not found<br>";
        $descriptor_primary_name = $descriptor_name;
      }

//----- Cathegoric data
      $pattern = "/[a-z]/i";
      if (preg_match($pattern, $joint_unique_list) ) {
        
        // Count value options 
        $value_counts = array_count_values($descriptor_value_list);

        // Show counts only if there are more than one value
        if (count($value_counts) > 1) {
          $value_summary = [];
          foreach ($value_counts as $value => $count) {
            $value_summary[] = str_replace("_", " ", "$value ($count)");
          }
          $joint_unique_list_printed = join("/", $value_summary);
        } else {

          $joint_unique_list_printed = str_replace("_", " ", array_keys($value_counts)[0]);
        }
        
        
        //$joint_unique_list_printed = str_replace("_", " ", $joint_unique_list);
        
        // Get info from JSON
        $descriptor_img = $descriptors_obj[$descriptor_name]["img_name"];
        $img_opt_array = $descriptors_obj[$descriptor_name]["options"];
        //echo "img_opt_array: "print_r($img_opt_array);
        
        if ($descriptor_primary_name && $descriptor_secondary_name) {
          echo "<b>$descriptor_primary_name</b>: $joint_unique_list_printed<br><span style='color: #777772;'>($descriptor_secondary_name)</span><br>"; // primary AND secondary descriptor names from json
        } elseif ($descriptor_primary_name) {
          echo "<b>$descriptor_primary_name</b>: $joint_unique_list_printed<br>"; // ONLY primary descriptor name from json
        } else {
          echo "<b>$descriptor_name</b>: $joint_unique_list_printed<br>"; // descriptor name from file
        }

        // if $descriptor_name is in featured_list, add to $all_featured_descriptors
        if (in_array($descriptor_name, $featured_list) ) {
          if ($descriptor_primary_name && $descriptor_secondary_name) {
            array_push($all_featured_descriptors, "<b>$descriptor_primary_name</b>: $joint_unique_list_printed<br><span style='color: #777772;'>($descriptor_secondary_name)</span><br>");
          } elseif ($descriptor_primary_name) {
            array_push($all_featured_descriptors, "<b>$descriptor_primary_name</b>: $joint_unique_list_printed<br>");
          } else {
            array_push($all_featured_descriptors, "<b>$descriptor_name</b>: $joint_unique_list_printed<br>");
          }
        }
        
        //image and image option list found
        if ($img_opt_array && $descriptor_img) {
          
          $descriptor_value = $joint_unique_list;

          //--- ADD IF to comprobate if 'img_name' contains 'help'
          if (strpos($descriptor_img, 'help') !== false) {
            // print img
            $help_img = $descriptor_img;

            if ($sp_name) {
              echo "<img src=\"$path_img/$help_img\" width=\"300\" style='border: 1px solid grey;'><br>";
              // echo "<img src=\"$path_img/$sp_name/$help_img\" width=\"300\" style='border: 1px solid grey;'><br>";
              echo "<i><span style='color: grey; font-size: 12px;'>Help image</span></i><br>";
            } else {
              //echo "$ root_path/$ path_img/$ help_img: $root_path$path_img/$help_img";
              echo "<img src=\"$path_img/$help_img\" width=\"300\" style='border: 1px solid grey;'><br>";
              echo "<i><span style='color: grey; font-size: 12px;'>Help image</span></i><br>";
            }

            
          } else {
            $img_upov = str_replace("value", $descriptor_value, $descriptor_img);
            
            echo "<table style=\"border: 2px solid\"><head><tr>";
          
            foreach($img_opt_array as $one_option) {
             
              $one_img = str_replace("value", $one_option, $descriptor_img);

              if ($sp_name) {
                if ($one_option && $one_img && file_exists("$root_path/$path_img/$one_img") ) {
                // if ($one_option && $one_img && file_exists("$root_path/$path_img/$sp_name/$one_img") ) {
                  $one_option_printed = str_replace("_", " ", $one_option);
                  if ($one_img == $img_upov) {
                    echo "<th style=\"text-align: center;border: 2px solid red;\">$one_option_printed</th>";
                  } else {
                  echo "<th style=\"text-align: center;\">$one_option_printed</th>";
                  }
                }
              } else {
                if ($one_option && $one_img && file_exists("$root_path/$path_img/$one_img") ) {
                  $one_option_printed = str_replace("_", " ", $one_option);
                  if ($one_img == $img_upov) {
                    echo "<th style=\"text-align: center;border: 2px solid red;\">$one_option_printed</th>";
                  } else {
                    echo "<th style=\"text-align: center;\">$one_option_printed</th>";
                  }
                }
              }
              

            } // end foreach
          
            echo "</tr></head><body><tr>";
            
            foreach($img_opt_array as $one_option) {
            
              $one_img = str_replace("value", $one_option, $descriptor_img);

              if ($sp_name){
                if ($one_img && file_exists("$root_path/$path_img/$one_img") ) {
                // if ($one_img && file_exists("$root_path/$path_img/$sp_name/$one_img") ) {
              
                  if ($one_img == $img_upov) {
                    echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>"; // red border
                    // echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$sp_name/$one_img\" width=\"100\"></td>"; // red border
                  } else {
                    echo "<td><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>";
                    // echo "<td><img class=\"center\" src=\"$path_img/$sp_name/$one_img\" width=\"100\"></td>";
                  }
                }
              } else {
              
                if ($one_img && file_exists("$root_path/$path_img/$one_img") ) {
                
                  if ($one_img == $img_upov) {
                    echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>"; // red border
                  } else {
                    echo "<td><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>";
                  }
                }
              }
            } // end foreach
          
            echo "</tr></body></table><br>";
          } // close ELSE

        } // close if img_options
        
      } else {
        //----- Numeric data

        $array_length = count($unique_list);
        $average = array_sum($unique_list)/$array_length;
       
        if ($convert_json[$file][$descriptor_name] ) {
          $category_obj = $convert_json[$file][$descriptor_name];
          $ranges = $category_obj["ranges"];
          $categories = $category_obj["categories"];

          foreach($ranges as $index => $range) {
            list($min, $max) = array_pad(explode('-', $range), 2, null);
            if ( $max == null) {
              //$max = $min; // not specify "<"
              $max = PHP_INT_MAX; // same as $min < $average
            }

            if ($average >= $min && $average < $max) {
              $category = $categories[$index];
              break;
            } else {
              $category = "<i>Category not assigned</i>";
            }
          }          
        } else{
          //echo "No conversion available<br>";
        }
                
        $average_printed = number_format($average, 2);
        $all_ranges_descriptors = [];
        $unique_id = "collapse_" . uniqid();
        
        if ($descriptor_primary_name && $descriptor_secondary_name && $category) {
          $category_printed = str_replace("_", " ", $category);
          // echo "<b>$descriptor_primary_name</b>: $average_printed ($category_printed) <br><span style='color: #777772;'>($descriptor_secondary_name)</span><br>";
          echo "<b>$descriptor_primary_name</b>: $average_printed ($category_printed) <i class=\"fas fa-info-circle info-icon\" style=\"color: #c4d03f; cursor: pointer;\" data-toggle=\"collapse\" data-target=\"#$unique_id\"></i><br><span style='color: #777772;'>($descriptor_secondary_name)</span><br>";
          
          echo "<div id=\"$unique_id\" class=\"collapse\">";
          echo "<table style='color: grey;min-width:250px;margin:20px'>";
          
          foreach ($categories as $index => $cat) {
            $range = $ranges[$index];
            $cat_printed = str_replace("_", " ", $cat);
            
            $popup_ranges = "$cat_printed: $range<br>";
            
            echo "<tr><td>$cat_printed:</td><td style=\"text-align: right\">$range</td></tr>";
            
            array_push($all_ranges_descriptors, $popup_ranges);
          }
          
          echo "</table></div>";
          
        } elseif ($descriptor_primary_name && $category) {
          echo "<b>$descriptor_primary_name</b>: $average_printed ($category_printed)<i class=\"fas fa-info-circle info-icon\" style=\"color: #c4d03f; cursor: pointer;\" data-bs-toggle=\"collapse\" data-bs-target=\"#$unique_id\"></i><br>";
          
          foreach ($categories as $index => $cat) {
            $range = $ranges[$index];
            $cat_printed = str_replace("_", " ", $cat);
            $popup_ranges = "$cat_printed: $range<br>";
            //echo "<button onclick=\"openPopup()\">Info</button>";
            
            echo "<div id=\"$unique_id\" class=\"collapse\" style=\"margin-top: 10px;\"><span style='color: grey; font-size: 12px;'>$popup_ranges</span></div>";

            array_push($all_ranges_descriptors, $popup_ranges);
          }
          
        } elseif ($descriptor_primary_name && $descriptor_secondary_name) {
          echo "<b>$descriptor_primary_name</b>: $average_printed<br><span style='color: #777772;'>($descriptor_secondary_name)</span><br><br>";
        } else {
          echo "<b>$descriptor_name</b>: $average<br>";
        }

        // if $decriptor_name is in $featured_list, add to $all_featured_descriptors
        if (in_array($descriptor_name, $featured_list) ) {
          if ($descriptor_primary_name && $descriptor_secondary_name && $category) {
            array_push($all_featured_descriptors, "<b>$descriptor_primary_name</b>: $average_printed ($category_printed)<br><span style='color: #777772;'>($descriptor_secondary_name)</span><br><br>");
          } elseif ($descriptor_primary_name && $category) {
            array_push($all_featured_descriptors, "<b>$descriptor_primary_name</b>: $average_printed ($category_printed)<br>");
          } elseif ($descriptor_primary_name && $descriptor_secondary_name) {
            array_push($all_featured_descriptors, "<b>$descriptor_primary_name</b>: $average_printed<br><span style='color: #777772;'>($descriptor_secondary_name)</span><br><br>");
          } else {
            array_push($all_featured_descriptors, "<b>$descriptor_name</b>: $average_printed<br>");
          }
        }

        // Print images
        $descriptor_img = $descriptors_obj[$descriptor_name]["img_name"];
        $img_opt_array = $descriptors_obj[$descriptor_name]["options"];
        if ($img_opt_array && $descriptor_img) {
          
          $descriptor_value = $category;

          //--- ADD IF to comprobate if 'img_name' contains 'help'
          if (strpos($descriptor_img, 'help') !== false) {
            // print help image
            $help_img = $descriptor_img;

            if ($sp_name) {
              echo "<img src=\"$path_img/$help_img\" width=\"300\" style='border: 1px solid grey;'><br>";
              // echo "<img src=\"$path_img/$sp_name/$help_img\" width=\"300\" style='border: 1px solid grey;'><br>";
              echo "<i><span style='color: grey; font-size: 12px;'>Help image</span></i><br>";
            } else {
              //echo "$ root_path/$ path_img/$ help_img: $root_path$path_img/$help_img";
              echo "<img src=\"$path_img/$help_img\" width=\"300\" style='border: 1px solid grey;'><br>";
              echo "<i><span style='color: grey; font-size: 12px;'>Help image</span></i><br>";
            }

          } else {
            $img_upov = str_replace("value", $descriptor_value, $descriptor_img);
            
            echo "<table style=\"border: 2px solid\"><head><tr>";
          
            foreach($img_opt_array as $one_option) {
             
              $one_img = str_replace("value", $one_option, $descriptor_img);

              if ($sp_name){
                if ($one_option && $one_img && file_exists("$root_path/$path_img/$one_img") ) {
                // if ($one_option && $one_img && file_exists("$root_path/$path_img/$sp_name/$one_img") ) {
                  $one_option_printed = str_replace("_", " ", $one_option);
                  if ($one_img == $img_upov) {
                    echo "<th style=\"text-align: center;border: 2px solid red;\">$one_option_printed</th>";
                  } else {
                    echo "<th style=\"text-align: center;\">$one_option_printed</th>";
                  }
                }
              } else {
                if ($one_option && $one_img && file_exists("$root_path/$path_img/$one_img") ) {
                  $one_option_printed = str_replace("_", " ", $one_option);
                  if ($one_img == $img_upov) {
                    echo "<th style=\"text-align: center;border: 2px solid red;\">$one_option_printed</th>";
                  } else {
                    echo "<th style=\"text-align: center;\">$one_option_printed</th>";
                  }
                }
              }
              
            } // end foreach
          
            echo "</tr></head><body><tr>";
            
            foreach($img_opt_array as $one_option) {
            
              $one_img = str_replace("value", $one_option, $descriptor_img);

              if ($sp_name){
                if ($one_img && file_exists("$root_path/$path_img/$one_img") ) {
                // if ($one_img && file_exists("$root_path/$path_img/$sp_name/$one_img") ) {
              
                  if ($one_img == $img_upov) {
                    echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>"; // red border
                    // echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$sp_name/$one_img\" width=\"100\"></td>"; // red border
                  } else {
                    echo "<td><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>";
                    // echo "<td><img class=\"center\" src=\"$path_img/$sp_name/$one_img\" width=\"100\"></td>";
                  }
                }
              } else {
                if ($one_img && file_exists("$root_path/$path_img/$one_img") ) {
              
                  if ($one_img == $img_upov) {
                    echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>"; // red border
                  } else {
                    echo "<td><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>";
                  }
                }
              }
              
            } // end foreach
          
            echo "</tr></body></table><br>";

          }

        } // close if img_options
        
      } // else str or numeric
      
      echo "<div style=\"display:none\"> $joint_unique_list </div>";
    } // foreach descriptor
  
    if (!$acc_name) { // If don't find the acc, print a message
      echo "No data available";
    }
    
    if (empty($descriptor_name) ) {

      echo "No phenotype data available";
    }

  } // if input file exist  

  return $all_featured_descriptors; // corresponde con $featured_array

} // Close function


function file_to_table($file_path, $acc_name) {

  if (file_exists ("$file_path") ) {
    $tab_file = file("$file_path");
    $header_line = array_shift($tab_file);
    $header = explode("\t", $header_line);

    //Generate unique ID based on $file
    $file = preg_replace('/.+\//', "", $file_path);
    $collapse_id = str_replace(".txt", "", $file) . "_collapse";

    // Crear la sección colapsable con un ID único
    echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#$collapse_id\" aria-expanded=\"false\" style=\"color: white; background-color: grey\">";
    echo "<span class=\"fas fa-sort\" style=\"color:#e0ea68\"></span> Raw data </div>";
    echo "<div id=\"$collapse_id\" class=\"collapse\">";

    // Tabla con los datos crudos
    //echo "<div style=\"overflow:scroll\">";
    echo "<table id=\"table_$collapse_id\" class=\"table tblResults table-striped table-bordered\" style=\"display:none\"><thead><tr>";

    echo "<div id=\"load_$collapse_id\" class=\"loader\"></div>"; // loading icon
    
    foreach ($header as $col_name) {
      $descriptor_name = $col_name;
      echo "<th>$descriptor_name</th>";
    }
    echo "</tr></thead><tbody>";
    
    foreach ($tab_file as $line) {
      $columns = explode("\t", $line);

      if (array_filter($columns, function($value) use ($acc_name) {
        return strnatcasecmp($value, $acc_name) == 0;
      } ) ) {
        echo "<tr>";
        
        foreach ($columns as $col) {
          echo "<td>$col</td>";
        }
        echo "</tr>";

      }
    } // each line
    echo "</tbody></table>";
    //echo "</div><br>";

    echo "</div>"; // close DIV collapse-section

  } else { // file exist
    //echo "No phenotype data available"; // Comprobate but do not print    
  }
}

?>


<!-- PASSPORT -->

<div class="container" style="max-width:1500px; margin:auto">
  <br>
<?php
  
  $pass_dir = test_input($_GET["pass_dir"]);
  //$row_count = test_input($_GET["row_num"]);
  $acc_id = test_input($_GET["acc_id"]);
  $acc_name = $acc_id;
  
  if ( file_exists("$passport_path/$pass_dir/passport.json") ) {
    $pass_json_file = file_get_contents("$passport_path/$pass_dir/passport.json");
    $pass_hash = json_decode($pass_json_file, true);
  
    $passport_file = $pass_hash["passport_file"];
    $phenotype_file_array = $pass_hash["phenotype_files"];
    $acc_header = $pass_hash["acc_link"];
  }
  
  //echo "<a href=\"02_pass_file_to_datatable.php?dir_name=$pass_dir\"><span class='fas fa-reply'></span><i> Back</i></a>";
  echo "<div class=\"container\">";
  
  
  if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
          
    $passport_lines = file("$passport_path/$pass_dir/$passport_file");
    $header_line = trim(array_shift($passport_lines));
    $header = explode("\t", $header_line);
    
    // echo "<p>acc_header: $acc_header</p>";
    // echo "header:<br>";
    // print_r($header);
    
    $title_col = (array_search($acc_header,$header)+1);
    
    //echo "<p>title_col: $title_col</p>";
    
    $passport_cmd = "awk -F '\t' 'tolower($$title_col) == tolower(\"$acc_id\") {print $0}' $passport_path/$pass_dir/$passport_file";
    //$passport_cmd = "awk -F \"\\t\" '$$title_col == \"$acc_id\" {print $0}' $passport_path/$pass_dir/$passport_file";
    
    //awk -F "\t" '$1 == "ICC 10544" {print $0}' Chickpea_10K_Passport.txt
      
    //echo "<p>passport_cmd: $passport_cmd</p>";
    
    $acc_line = shell_exec($passport_cmd);
    $acc_line = trim($acc_line);

    //echo "<p>acc_line: $acc_line</p>";
    
    
    $cols = explode("\t", $acc_line);
    // $cols = explode("\t", $passport_lines[$row_count]);
    $header = explode("\t", $header_line);
    
    
    // $acc_name = $cols[$title_col];
    echo "<center><h1><b>".$acc_name."</b></h1></center><br>";
    echo "<div class=\"row\">";

    echo "<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6\">";
    
    foreach ($cols as $col_count => $col_value) {
      if ($header[$col_count] ) {
        if ($header[$col_count] == "DOI" ) {
          echo "<p><b>$header[$col_count]:</b> <a href=\"https://doi.org/$col_value\" target=\"_blank\"> $col_value</a></p>";
        } elseif ($header[$col_count] == "Species") {
          echo "<p><b>$header[$col_count]:</b> <i>$col_value</i></p>"; 
        } else {
          echo "<p><b>".$header[$col_count].":</b> $col_value</p>";
        }
      }
    }
    echo "</div>";
    
  } // Close if
?>
    <br>

<?php
  if ($show_qr) {
    echo "<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6 p-7 my-3\"><div id=\"qrcode\"></div></div>";
  }
?>
    
  </div> <!-- close row -->
</div><!-- close passport container -->
<br>

<?php include_once realpath("$easy_gdb_path/tools/passport/gallery.php"); ?> 



  <!-- FEATURED DESCRIPTORS -->
   <!-- container for Javascript -->

<?php 

if (!empty($featured_descriptors_file) ) {
  $featured_descriptors_path = "$passport_path/$pass_dir/$featured_descriptors_file";
  
  if (file_exists($featured_descriptors_path) ) {
    
    // containers
    echo "<div class=\"container p-1 my-1 text-white feature-desc-cont\" style=\"background-color: #e0ea68; border: 1px solid grey;\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><center><h1><i class=\"fa-regular fa-star\"></i><b> Featured descriptors </b></h1></center></div></div>";
    echo "<div class =\"container p-7 my-3 border feature-desc-cont\"\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><br>";
    echo "<div id=\"featured-descriptors\"></div>"; 
    echo "<br></div></div>";

  }
}

?>



  <!-- MAP -->

<!-- COORDENADAS -->
<?php

if (array_search('Latitude', $header)) {
  $latitude_index = array_search('Latitude', $header);
  $latitude = $cols[$latitude_index];
} else {
  $latitude = null;
}

if (array_search('Longitude', $header)) {
  $longitude_index = array_search('Longitude', $header);
  $longitude = $cols[$longitude_index];
} else {
  $longitude = null;
}

// $latitude_index = array_search('Latitude', $header);
// $longitude_index = array_search('Longitude', $header);
$country_name_index = array_search('Country', $header);
$country_code_index = array_search('Country code', $header);
$collection_site_index = array_search('Collection site', $header);

// $latitude = $cols[$latitude_index];
// $longitude = $cols[$longitude_index];
$country_name = $cols[$country_name_index];
$country_code = $cols[$country_code_index];
$collection_site = $cols[$collection_site_index];


$numeric_pattern = "/[0-9]/";

if ($show_map) {
  echo "<div class=\"container p-1 my-1 bg-secondary text-white\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><center><h1><i class=\"fa-solid fa-location-dot\"></i><b> Location </b></h1></center></div></div>";
  echo "<div class =\"container p-7 my-3 border\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">";

  if (preg_match($numeric_pattern, $latitude and $longitude) ) { // Print map

    echo "<br>";
    echo "<div id=\"map\" style=\"height: 350px;\"></div>";
    
  } else if ($country_name) { // Close 'if' - real coords
    
    echo "<br>Latitude and longitude not available, using country coordinates instead.<br><b>Country name:</b> $country_name<br>";
    
    $coords_file = "$root_path/easy_gdb/tools/passport/country_coordinates.txt"; // file with coords info
  
    if ( file_exists("$coords_file") ) {
            
      $country_to_coords_file = file_get_contents("$coords_file");        

      $rows_coords = explode("\n", $country_to_coords_file);
      $cols_coords = explode("\t", $rows_coords[$row_count]);  //no es necesario
      $header_coords = explode("\t", $rows_coords[0]);  //no es necesario
  
  
      //  Defining $var of $coords_file - all options
      $full_name = "";
      $country_latitude = "";
      $country_longitude = "";
  
      // Associate lat&long with $country_name
      foreach ( $rows_coords as $row ) {
        $cols_coords = explode("\t", $row);
        if ($cols_coords[0] == $country_name || $country_code == $cols_coords[2]) {
          // GET var independent values - it depends on the file distribution
          $full_name = $cols_coords[0];  
          $country_latitude = $cols_coords[4]; 
          $country_longitude = $cols_coords[5];
          break; // finish when finds the country
        }
      }
  
      // // COMPROBACIÓN
      // if (!$country_latitude) {
      //   echo "<br>No location data available.";
      // }
  
      echo "<div id=\"map\" style=\"height: 350px;\"></div>"; // print the map
    } // close if $coords_file exists 
    
    
  } // Use CountryCode
    else {
      echo "No location data available.";
  }
          
  // Frame Reference 
  echo "<br>It is used <a href=\"https://leafletjs.com/\" tardet=\"_blank\">Leaflet</a>, an open-source JavaScript library for mobile-fiendly interactive maps, to create the map frame, importing the CSS file. The map frame used is made available under the <a href=\"https:\/\/opendatacommons.org/licenses/odbl/1.0/\" target=\"_blank\">Open Database Licence</a>. Any rights in individual contents of the database are licensed under the <a href=\"http://opendatacommons.org/licenses/dbcl/1.0/\" tardet=\"_blank\">Database Contents License</a>. More info in cookies's section.";
  
}
?>

    <br>
    <br>
    </div>
  </div>  


<?php 
  
if (!empty($phenotype_file_array)){

    echo "<div class=\"container p-1 my-1 bg-secondary text-white\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><center><h1><b> Phenotype descriptors </b></h1></center></div></div>";
    

  $phenotype_img_json = $pass_hash["phenotype_imgs"];
  
  if ($phenotype_img_json && file_exists("$passport_path/$pass_dir/$phenotype_img_json") ) {
    
    $pheno_json_file = file_get_contents("$passport_path/$pass_dir/$phenotype_img_json");
    $pheno_hash = json_decode($pheno_json_file, true);
  }
    $featured_array = [];

  foreach ($phenotype_file_array as $phenotype_file) {
    
    //TO DO check file exists
    
    $phenotype_file_full_path = "$passport_path/$pass_dir/$phenotype_file";
    
    echo "<div class =\"container p-7 my-3 border\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><br>";
    
    // $root_path and $phenotype_imgs_path are defined in easyGDB_conf.php
    $featured_array = write_descriptor_files($phenotype_file_full_path,$acc_name,$pheno_hash[$phenotype_file],$root_path,$phenotype_imgs_path."/$pass_dir",$convert_json,$translator_json,$featured_descriptors_json,$featured_array,$sp_name);
//    $featured_array = write_descriptor_files($phenotype_file_full_path,$acc_name,$pheno_hash[$phenotype_file],$root_path,$phenotype_imgs_path,$convert_json,$translator_json,$featured_descriptors_json,$featured_array,$sp_name);


    //print_r($featured_array); // array completo
    //$featured_array_json = json_encode($featured_array);

    file_to_table($phenotype_file_full_path, $acc_name);
    
    echo "</div></div>";
    
  }
  
  // Ref images used
  $img_src_msg = $pass_hash["img_src_msg"];
  echo "<center>$img_src_msg</center>"; // CONDICIONAR IMPRESIÓN DE LAS IMÁGENES


}
?>

</div>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php"); ?>



<script type="text/javascript">

//----- QR CODE
var showQR = <?php echo $show_qr; ?>;

if (showQR) {
  url_qrcode = window.location.href;
  qr_id = document.getElementById("qrcode")

  new QRCode(qr_id,url_qrcode);
}

//----- FEATURED DESCRIPTORS
var featuredArrayJson = <?php echo json_encode($featured_array); ?>;

if ($('#featured-descriptors') && featuredArrayJson !== 'undefined' && featuredArrayJson != '') {
  $('#featured-descriptors').html(featuredArrayJson);
  $('.feature-desc-cont').css('display','block');
}
// Function to show featured descriptors
// function showFeaturedDescriptors(featuredDescriptors) {
//   var container = document.getElementById("featured-descriptors");
//   if (container && allFeaturedDescriptors) {
//     $('.feature-desc-cont').css('display','block');
//     $('.feature-desc-cont').show();
//     container.innerHTML = allFeaturedDescriptors; // Insert content in HTML div
//   }
// }


//----- PRINT MAP

var showMap = <?php echo $show_map; ?>;

if(showMap) {

  latitude = "<?php echo $latitude; ?>";
  latitude_printed = "<?php echo number_format($latitude,2); ?>";
  longitude = "<?php echo $longitude; ?>";
  longitude_printed = "<?php echo number_format($longitude,2); ?>";

    if (latitude && longitude) {
      marker_label = "<b>Collection site</b><br>Latitude: "+latitude_printed+"<br> Longitude: "+longitude_printed;
    }
    else {
      latitude = "<?php echo $country_latitude; ?>";
      longitude = "<?php echo $country_longitude; ?>";
      marker_label = "<b>Collection country</b><br><?php echo $country_name; ?>";
    }

    var map = L.map('map').setView([latitude, longitude], 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="">OpenStreetMap</a> contributors'}).addTo(map);

    var marker = L.marker([latitude, longitude]).addTo(map);
    marker.bindPopup(marker_label).openPopup();
}


  $(document).ready(function(){

$(".collapse").on('shown.bs.collapse', function(){    

  var id=this.id;

  $("#load_"+id).remove();
  $("#table_"+id).show();

    $("#table_"+id).dataTable({
      dom:'Bfrtlpi',
      "oLanguage": {
        "sSearch": "Filter by:"
        },
      buttons: [
        'copy', 'csv', 'excel',
          {
            extend: 'pdf',
            orientation: 'landscape',
            pageSize: 'LEGAL'
          },
        'print', 'colvis'
        ],
      "sScrollX": "100%",
      "sScrollXInner": "110%",
      "bScrollCollapse": true,
      retrieve: true,
      colReorder: true,
      "drawCallback": function( settings ) {
    // $('#body').css("display","inline");
    // $(".td-tooltip").tooltip();
      $("table.dataTable tbody tr").hover(
          function() {
              // Al pasar el mouse
              $(this).css("background-color", "#d1d1d1");
          }, function() {
              // Al retirar el mouse
              $(this).css("background-color", "");
          }
      );
    },
  });
 
$(".dataTables_filter").addClass("float-right");
$(".dataTables_filter").addClass("float-left");
$(".dataTables_filter").addClass("float-right");

});

});

</script>

<style>
  .center {
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
  
  table.dataTable td,th  {
    max-width: 500px;
    white-space: nowrap;
    overflow: hidden;
    text-align: center;
  }
  
  .feature-desc-cont{
    display:none;
  }
  
  .collapse_section:hover {
/*  text-decoration: underline;*/
  background-color: #999 !important;

}
</style>
