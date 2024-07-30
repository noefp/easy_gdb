<!-- HEADER -->
<?php include_once realpath("../../header.php");?>

<?php 
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
  $pass_dir = test_input($_GET["dir_name"]); // get passport directory with files to list
  $pass_dir_title = str_replace("_", " ", $pass_dir);
?>

<!-- Load the QR library -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<!-- Load the map library -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<!-- Load the CLUSTER LIBRARIES -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>


<div class="page_container" style="margin-top:20px">
  <h1 class="text-center"><?php echo "$pass_dir_title" ?></h1>
  <br>
  <div class="data_table_frame">

<?php
// get info from passport.json
if ( file_exists("$passport_path/$pass_dir/passport.json") ) {
  $pass_json_file = file_get_contents("$passport_path/$pass_dir/passport.json");
  $pass_hash = json_decode($pass_json_file, true);
  
  $passport_file = $pass_hash["passport_file"];
  $phenotype_file_array = $pass_hash["phenotype_files"];
  $unique_link = $pass_hash["acc_link"];
  $hide_array = $pass_hash["hide_columns"];
  $map_array = $pass_hash["map_columns"];
  
  $colors_array = $pass_hash["marker_colors"];
  
}
  
  
if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
  $tab_file = file("$passport_path/$pass_dir/$passport_file");
  
  // get header array by columns
  $file_header = array_shift($tab_file);
  $header_cols = explode("\t", $file_header);
  
  $field_number = 0;
  
  // start printing table and header
  echo "<table class=\"table\" id=\"tblResults\"><thead><tr>";

  foreach ($header_cols as $head_index => $hcol) {
    
    if ( !in_array($head_index,$hide_array) ) {
      echo "<th>$hcol</th>";
      
      // find column index for unique identifier that will link to accession info
      if ($unique_link == $hcol) {
        $field_number = $head_index;
      }
    } //close in_array
  } //close foreach
  
  echo "</tr></thead><body>";
  
  foreach ($tab_file as $row_count => $line) {
    
    $columns = explode("\t", $line);

    echo "<tr>";
      
    foreach ($columns as $col_index => $col) {
      
      if ( !in_array($col_index,$hide_array) ) {
        if ($col_index == $field_number) {
          echo "<td><a href=\"03_passport_and_phenotype.php?pass_dir=$pass_dir&acc_id=$col\">$col</a></td>";
          // echo "<td><a href=\"03_passport_and_phenotype.php?pass_dir=$pass_dir&row_num=$row_count\">$col</a></td>";
          // echo "<td><a href=\"row_data.php?row_data=".$table_file.",".$row_count.",".($field_number-1)."\">$col</a></td>";
        } else {
          echo "<td>$col</td>";
        }
      } // end in_array
    } // end foreach columns
    echo "</tr>";
  } // end foreach lines

  echo "</body></table>";

} // end passport file exist
?>

  </div>
</div>
<br>
<br>
  <!-- CHICKPEA GERMPLASM PRINTED ON A MAP -->
  <div class="p-1 my-1 bg-secondary text-white">
    <!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> -->
    <center><h1><b> Explore the map </b></h1></center>
    <!-- </div> -->
  </div>
  <div class="p-7 my-3 border">
    <!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> -->

<?php
//echo "<br>The map shows all de <b>Acc ID</b>, which have coordinates, that we have in the database.<br><br>";

$rows = [];

// Get map info

if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
  $tab_file = file("$passport_path/$pass_dir/$passport_file");
  
  // get header array by columns
  $file_header = array_shift($tab_file);
  $header_cols = explode("\t", $file_header);
  
  $field_number = 0;
  
  // foreach ($header_cols as $head_index => $hcol) {
  //   if (in_array($head_index,$map_array)){
  //
  //     if ($unique_link == $hcol) {
  //       $field_number = $head_index;
  //     }
  //   } //close in_array
  // } //close foreach
  
  foreach ($tab_file as $row_count => $line) {
    $columns = explode("\t", $line);
    $row_data = []; // array a llenar
      
    foreach ($columns as $col_index => $col) {
      
      if ( in_array($col_index,$map_array) ) {
        $row_data[$header_cols[$col_index] ] = $col;

      } // end in_array
    } // end foreach columns

    $rows[] = $row_data; // array lleno, FUNCIONA

  } // end foreach lines

} // end passport file exist

//print_r($rows); // array completo
$total_rows = count($rows);
//echo "<br>total rows:$total_rows<br>"; // correcto

// Read country_coordinates.txt
$country_coords_file = "$root_path/easy_gdb/tools/passport/country_coordinates.txt";
$country_coords = [];

if (file_exists($country_coords_file) ) {
  $lines = file($country_coords_file, FILE_IGNORE_NEW_LINES);

  foreach ($lines as $line) {
    $cols = explode("\t", $line); // Ajustar si el delimitador es diferente
    $country = $cols[0];
    $latitude = $cols[4];
    $longitude = $cols[5];
    $country_coords[$country] = ['latitude' => $latitude, 'longitude' => $longitude];
  }
}

// Read Chickpea_10K_Phenotype.txt to get de color marker 
$phenotype_file = "$passport_path/$pass_dir/Chickpea_10K_Phenotype.txt";
$acc_colors = [];

if (file_exists($phenotype_file) ) {
  $lines = file($phenotype_file, FILE_IGNORE_NEW_LINES);

  foreach ($lines as $line) {
    $cols = explode ("\t", $line);
    $acc = $cols[0];
    $color = str_replace(" ", "_", $cols[10]);
    $acc_colors[$acc] = $color;
  }
}


// Array - almacenar datos para Javascript
$data_map = [];
//$country_acc_map =[];

// Iterar $rows por filas
foreach ($rows as $row) {
  if(isset($row['Acc ID'])){
    $acc = $row['Acc ID'];
  }

  if(isset($row['Country'])){
    $country = $row['Country'];
  }
  if(isset($row['Latitude'])){
    $latitude = $row['Latitude'];
  }
  if(isset($row['Longitude'])){
    $longitude = $row['Longitude'];
  }
  
//  if (!empty($rows)){
//    echo "<pre>";
//    var_dump(array_keys($rows[0])); // comprobación
//    echo"</pre>";
//  }

  // Verificate if lat y long are empty -> search in country_coords
  if (empty($latitude) || empty($longitude)){
    if ($country !== null && isset($country_coords[$country])){
      $latitude = $country_coords[$country]['latitude'];
      $longitude = $country_coords[$country]['longitude'];
    }
  }

  // Store data in data_map array
  if (!empty($latitude) && !empty($longitude)){
    $data_map[] = [
      'acc' => $acc,
      'country' => $country,
      'latitude' => $latitude,
      'longitude' => $longitude,
      'color' => $acc_colors[$acc] ?? 'default'
    ];
  }

}

// Convert $country_acc_map to correct format y listar acc en el Popup
// foreach($country_acc_map as $coords => $acc_list) {
//   list($latitude,$longitude) = explode(',', $coords);
//   $data_map[] = [
//     'acc' => implode(", ", $acc_list), // Listar Acc ID's
//     'country' => $country,
//     'latitude' => $latitude,
//     'longitude' => $longitude,
//   ];
// }


// Convert $data to JSON - to use it in Javascript part
$json_data_map = json_encode($data_map);
//echo $json_data_map; // funciona

// Personalized marker
$marker_path = "$images_path/map_labels/"; 
$marker_colors_array = json_encode($colors_array);

echo "<div id=\"map\" style=\"height: 600px;\"></div>"; // print the map

?>

    <!-- </div> -->
  </div>





<br>
<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script type="text/javascript">
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


<script>

  // MAP 

// Get $data_map from PHP
const data = <?php echo $json_data_map; ?>;





// Personalized markers with different colors
markersPath = '<?php echo $marker_path; ?>';

// List options of markers (colors)
validColors = JSON.parse('<?php echo $marker_colors_array; ?>');

// function to get the color
function getIcon(color) {
  if (!validColors.includes(color) ) {
    color = 'default';
  }

  var iconUrl;
  if (color == 'default') {
    iconUrl = 'marker_default.png';
  } else {
    iconUrl = `marker_${color}.png`;
  }

  return new L.Icon ({
    iconUrl: markersPath + iconUrl,
    iconSize: [25, 25],
    iconAnchor: [12, 12],
  });
}





// MAP
var map = L.map('map').setView([0, 0], 2);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="">OpenStreetMap</a> contributors'}).addTo(map);

// Create a cluster groups
var markers = L.markerClusterGroup();

// Add markers 
data.forEach(item => {
  if (item.latitude && item.longitude) { // Verify coords 
    
    var icon = getIcon(item.color);
    var marker = L.marker([item.latitude, item.longitude], {icon: icon});

    //var accList = item.acc.split(", ").map(acc => `<a href="03_passport_and_phenotype.php?pass_dir=<?php //echo $pass_dir; ?>&acc_id=${acc}">${acc}</a>`).join("<br>"); 

    var markerLabel = `<b>Acc ID:</b> <a href="03_passport_and_phenotype.php?pass_dir=<?php echo $pass_dir; ?>&acc_id=${item.acc}">${item.acc}</a><br><b>Country:</b> ${item.country}`; // con link
    
    marker.bindPopup(markerLabel);
    markers.addLayer(marker);
  }
});

// Añadir el grupo de clusters al mapa
map.addLayer(markers);


</script>
