<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include_once realpath("$easy_gdb_path/tools/common_functions.php");?>

<!-- Load the QR library -->
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<?php
function write_descriptor_files($file_path, $acc_name, $descriptors_obj, $root_path, $path_img) {
  
  $obj_average = [];
  
  if (file_exists($file_path) ) {
    $tab_file = file($file_path);
    $header_line = array_shift($tab_file);
    $header = explode("\t", $header_line);
    
    foreach ($tab_file as $line) {
      
      $cols = explode("\t", $line);
      
      if ( in_array($acc_name,$cols) ) {
        // echo "<p>line: $line</p>";
          
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
          } // IF descriptor value
          
        } // col foreach
      } // in array
    } // line foreach
    
    $file = preg_replace('/.+\//', '', $file_path);
    
    
    foreach($obj_average as $descriptor_name => $descriptor_value_list){
          
      $unique_list = array_unique($descriptor_value_list);
      $joint_unique_list = join("||", $unique_list);
      //echo "$descriptor_name: $joint_unique_list <br>";
      
      // get info from JSON
      $descriptor_primary_name = $descriptor_name;
      // $descriptor_primary_name = $descriptors_obj[$file][$descriptor_name]["principal_descriptor"]; //code for secondary language
      
//----- Cathegoric data
      $pattern = "/[a-z]/i";
      if (preg_match($pattern, $joint_unique_list) ) {
        
        // get info from JSON
        $descriptor_img = $descriptors_obj[$descriptor_name]["img_name"];
        $img_opt_array = $descriptors_obj[$descriptor_name]["options"];
        //echo "img_opt_array: ".print_r($img_opt_array);
        
        if ($descriptor_primary_name) {
          echo "<b>$descriptor_primary_name</b>: $joint_unique_list<br>"; // primary descriptor name from json
        } else {
          echo "<b>$descriptor_name</b>: $joint_unique_list<br>"; // descriptor name from file
        }
        
        //image and image option list found
        if ($img_opt_array && $descriptor_img) {
          
          $descriptor_value = $joint_unique_list;
          $img_upov = str_replace("value", $descriptor_value, $descriptor_img);
            
          echo "<table style=\"border: 2px solid\"><head><tr>";
          
          foreach($img_opt_array as $one_option){
              
            $one_img = str_replace("value", $one_option, $descriptor_img);
              
            if ($one_option && $one_img && file_exists("$root_path/$path_img/$one_img") ) {
              if ($one_img == $img_upov) {
                echo "<th style=\"text-align: center;border: 2px solid red;\">$one_option</th>";
              } else {
                echo "<th style=\"text-align: center;\">$one_option</th>";
              }
            }
          } // end foreach
          
          echo "</tr></head><tr><body>";
            
          foreach($img_opt_array as $one_option){
              
            $one_img = str_replace("value", $one_option, $descriptor_img);
              
            if ($one_img && file_exists("$root_path/$path_img/$one_img") ) {
              
              if ($one_img == $img_upov) {
                echo "<td style=\"border: 2px solid red;padding-left:15px; padding-right:15px\"><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>"; // red border
              } else {
                echo "<td><img class=\"center\" src=\"$path_img/$one_img\" width=\"100\"></td>";
              }
            }
          } // end foreach
          
          echo "</tr></body></table><br>";
        } // close if img_options
            
      } else {
        //numeric data
        $array_length = count($unique_list);
        $average = array_sum($unique_list)/$array_length;
      
        if ($descriptor_primary_name) {
          echo "<b>$descriptor_primary_name</b>: $average<br>";
        } else {
          echo "<b>$descriptor_name</b>: $average<br>";
        }
      } // else str or numeric
      
      echo "<div style=\"display:none\"> $joint_unique_list </div>";
    } // foreach descriptor
  
    if (!$acc_found){ // If don't find the acc, print a message
      echo "No data available";
    }
    
  } // if input file exist
  echo "<br>";
  echo "<br>";
} // Close function



function file_to_table($file_path, $acc_name) {

  if (file_exists ("$file_path") ) {
    $tab_file = file("$file_path");
    $header_line = array_shift($tab_file);
    $header = explode("\t", $header_line);
    
    echo "<div style=\"overflow:scroll\">"; //print data table
    echo "<table class=\"table\" id=\"tblResults\"><thead><tr>"; //print data table
    
    foreach ($header as $col_name) {
      echo "<th>$col_name</th>";
    }
    echo "</tr></thead><tbody>";
    
    foreach ($tab_file as $line) {
      $columns = explode("\t", $line);
      
      if ( in_array($acc_name,$columns) ) {
        echo "<tr>";
        
        foreach ($columns as $col) {
          echo "<td>$col</td>";
        }
        echo "</tr>";
      } // acc found in line
    } // each line
    echo "</tbody></table>";
    echo "</div>";
  } // file exist
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
  
  echo "<a href=\"02_pass_file_to_datatable.php?dir_name=$pass_dir\"><span class='fas fa-reply' style='color:229dff'></span><i> Back</i></a>";
  echo "<div class=\"container\">";
  
  
  if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
          
    $passport_lines = file("$passport_path/$pass_dir/$passport_file");
    $header_line = array_shift($passport_lines);
    $header = explode("\t", $header_line);
    
    // echo "<p>acc_header: $acc_header</p>";
    // echo "header:<br>";
    // print_r($header);
    
    $title_col = (array_search($acc_header,$header)+1);
    
    //echo "<p>title_col: $title_col</p>";
    
    $passport_cmd = "awk -F \"\\t\" '$$title_col == \"$acc_id\" {print $0}' $passport_path/$pass_dir/$passport_file";
    
    //awk -F "\t" '$1 == "ICC 10544" {print $0}' Chickpea_10K_Passport.txt
      
    //echo "<p>passport_cmd: $passport_cmd</p>";
    
    $acc_line = shell_exec($passport_cmd);
    //echo "<p>acc_line: $acc_line</p>";
    
    
    
    
    $cols = explode("\t", $acc_line);
    // $cols = explode("\t", $passport_lines[$row_count]);
    $header = explode("\t", $header_line);
    
    
    // $acc_name = $cols[$title_col];
    echo "<center><h1><b>".$acc_name."</b></h1></center><br>";
    echo "<div class=\"row\">";
    echo "<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6\">";
    
    foreach ($cols as $col_count => $col_value) {
      if ($header[$col_count]) {
        if ($header[$col_count] == "DOI" ) {
          echo "<p><b>$header[$col_count]:</b> <a href=\"https://doi.org/$col_value\" target=\"_blank\"> $col_value</a></p>";
        }
        else {
          echo "<p><b>".$header[$col_count].":</b> $col_value</p>";
        }
      }
    }
    echo "</div>";
    
  } // Close if
?>
    <br>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 p-7 my-3">
      <div id="qrcode"></div>
    </div> 
    
  </div> <!-- close row -->
</div><!-- close passport container -->
<br>



  <!-- LOCATION -->
  <div class="container p-1 my-1 bg-secondary text-white">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <center><h1><b> Location </b></h1></center>
    </div>
  </div>

  <!-- <div class="row"> -->
  <div class ="container p-7 my-3 border">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

<!-- COORDENADAS HARDCODE -->
<?php
  
$latitude_index = array_search('Latitude', $header);
$longitude_index = array_search('Longitude', $header);
$country_name_index = array_search('Country', $header);
$country_code_index = array_search('Country code', $header);
$collection_site_index = array_search('Collection site', $header);

$latitude = $cols[$latitude_index];
$longitude = $cols[$longitude_index];
$country_name = $cols[$country_name_index];
$country_code = $cols[$country_code_index];
$collection_site = $cols[$collection_site_index];


$numeric_pattern = "/[0-9]/";

if (preg_match($numeric_pattern, $latitude and $longitude) ) { // Print map
  
  echo "<br>";
  echo "<div id=\"map\" style=\"height: 350px;\"></div>";
  
} else if ($country_name) { // Close 'if' - real coords
  
  echo "<br>Latitude and longitude not available, using country coordinates instead.<br><b>Country name:</b> $country_name<br>";
  
  $coords_file = "$root_path/easy_gdb/tools/passport/country_coordinates.txt"; // file with coords info

  if ( file_exists("$coords_file") ) {
          
    $country_to_coords_file = file_get_contents("$coords_file");        
    //echo $country_to_coords_file; // print file content
    $rows_coords = explode("\n", $country_to_coords_file);
    $cols_coords = explode("\t", $rows_coords[$row_count]);  //no es necesario
    $header_coords = explode("\t", $rows_coords[0]);  //no es necesario


    //        Defining $var of $coords_file - all options
    $full_name = "";
    //$alpha2_code = "";
    //$alpha3_code = ""; // útil para IHSM_SDB, puesto que incluyen el código en el archivo de datos
    //$num_code = "";
    $country_latitude = "";
    $country_longitude = "";

    // Associate lat&long with $country_name
    foreach ( $rows_coords as $row ) {
      $cols_coords = explode("\t", $row);
      if ($cols_coords[0] == $country_name || $country_code == $cols_coords[2]){
        // GET var independent values - it depends on the file distribution
        $full_name = $cols_coords[0];  
        // $alpha2_code = $cols_coords[1];
        // $alpha3_code = $cols_coords[2];
        // $num_code = $cols_coords[3];
        $country_latitude = $cols_coords[4]; 
        $country_longitude = $cols_coords[5]; 
        break; // ??? se podría quitar porque solo se están definiendo los valores
      }
    }

    // COMPROBACIÓN
    if ($country_latitude != null) { 
      // echo "<b>Country match info:</b><br>";
      // echo "Full Name: $full_name<br>";
      // //echo "Alpha2 Code: $alpha2_code<br>";
      // //echo "Alpha3 Code: $alpha3_code<br>";
      // //echo "Numeric Code: $num_code<br>";
      // echo "Latitude: $country_latitude<br>";
      // echo "Longitude: $country_longitude<br>";

    } else {
        echo "<br>No location data available.";
    }

    // if (empty($latitude&$longitude) ) { // llenar variable para Javascript
    //   $latitude = $country_latitude;
    //   $longitude = $country_longitude;
    // }

    echo "<div id=\"map\" style=\"height: 350px;\"></div>"; // print the map
  } // close if $coords_file exists 
  
  
} // Use CountryCode
  else {
    echo "No location data available.";
}
        
// Frame Reference 
echo "<br>It is used <a href=\"https://leafletjs.com/\" tardet=\"_blank\">Leaflet</a>, an open-source JavaScript library for mobile-fiendly interactive maps, to create the map frame, importing the CSS file. The map frame used is made available under the <a href=\"https:\/\/opendatacommons.org/licenses/odbl/1.0/\" target=\"_blank\">Open Database Licence</a>. Any rights in individual contents of the database are licensed under the <a href=\"http://opendatacommons.org/licenses/dbcl/1.0/\" tardet=\"_blank\">Database Contents License</a>. More info in cookies's section.";

?>

    <br>
    <br>
    </div>
  </div>  




  <!-- DESCRIPTORS -->
  <div class="container p-1 my-1 bg-secondary text-white">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <center><h1><b> Phenotype descriptors </b></h1></center>
    </div>
  </div>

  <!-- <div class="row"> -->
  <div class ="container p-7 my-3 border">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <br>
<?php 
  
  $obj_descriptors = []; // Create object 
  $phenotype_img_json = $pass_hash["phenotype_imgs"];
  
  if ($phenotype_img_json && file_exists("$passport_path/$pass_dir/$phenotype_img_json")) {
    
    $pheno_json_file = file_get_contents("$passport_path/$pass_dir/$phenotype_img_json");
    $pheno_hash = json_decode($pheno_json_file, true);
  }
  
  foreach ($phenotype_file_array as $phenotype_file) {
    $phenotype_file_full_path = "$passport_path/$pass_dir/$phenotype_file";
    
    // $root_path and $phenotype_imgs_path are defined in easyGDB_conf.php
    write_descriptor_files($phenotype_file_full_path,$acc_name,$pheno_hash[$phenotype_file],$root_path,$phenotype_imgs_path);
    file_to_table($phenotype_file_full_path, $acc_name);
  }
        
    // Ref images used
    $img_src_msg = $pass_hash["img_src_msg"];
    echo "$img_src_msg";
?>

    </div>
  </div>
  <br>

</div>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>


<script type="text/javascript">
  // $(document).ready(function () {
  
  url_qrcode = window.location.href;

  //qr_id = $("#qrcode");
  qr_id = document.getElementById("qrcode")
  
  new QRCode(qr_id,url_qrcode); 
// });
  
latitude = "<?php echo $latitude; ?>";
longitude = "<?php echo $longitude; ?>";
  
  if (latitude && longitude) {
    marker_label = "<b>Collection site</b><br>Latitud: "+latitude+"<br> Longitud: "+longitude;
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
  
  
  
  $("#tblResults").dataTable({
  	dom:'Bfrtlpi',
    "oLanguage": {
       "sSearch": "Filter by:"
     },
    "order": [],
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
  });
  
  $("#tblResults_filter").addClass("float-right");
  $("#tblResults_info").addClass("float-left");
  $("#tblResults_paginate").addClass("float-right");

</script>


<style>
  .center {
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
</style>

