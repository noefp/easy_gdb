<!-- GERMPLASM PRINTED ON A MAP-->
<div class="p-1 my-1 bg-secondary text-white">
  <!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">-->
    <center><h1><i class="fa-solid fa-location-crosshairs"></i><b> Explore the map </b></h1></center>
  <!-- </div>-->
</div>
  <div class="p-7 my-3 border">

<?php
  //echo "<br>The map shows all de <b>Acc ID</b>, which have coordinates, that we have in the database.<br><br>";

  $rows = [];

  // Get map info

  if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
    $tab_file = file("$passport_path/$pass_dir/$passport_file");

    // get header array by columns
    $file_header = trim(array_shift($tab_file));
    $header_cols = explode("\t", $file_header);

    $field_number = 0;

  // foreach ($header_cols as $head_index => $hcol) {
  // if (in_array($head_index,$map_array)){
  //
  // if ($unique_link == $hcol) {
  // $field_number = $head_index;
  // }
  // } //close in_array
  // } //close foreach

    foreach ($tab_file as $row_count => $line) {
      $columns = explode("\t", $line);
      $row_data = [];
    
      foreach ($columns as $col_index => $col) {
    
        if ( in_array($col_index,$map_array) ) {
          $row_data[$header_cols[$col_index] ] = $col;
    
        } // end in_array
      } // end foreach columns

      $rows[] = $row_data;

    } // end foreach lines

  } // end passport file exist

  //print_r($rows);
  $total_rows = count($rows);
  //echo "<br>total rows:$total_rows<br>";

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

  // Read phenotype file to get color marker 
  $phenotype_file = "$passport_path/$pass_dir/$phenotype_file_data";
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


  // Array- keep data to Javascript
  $data_map = [];
  //$country_acc_map =[];

  // Iterate $rows 
  foreach ($rows as $row) {
    if(isset($row["$unique_link"] ) ) {
      $acc = $row["$unique_link"];
    }
    if(isset($row['Country'] ) ) {
      $country = $row['Country'];
    }
    if(isset($row['Latitude'] ) ) {
      $latitude = $row['Latitude'];
    }
    if(isset($row['Longitude'] ) ) {
      $longitude = $row['Longitude'];
    }

    //if (!empty($rows)){
    //echo "<pre>";
    //var_dump(array_keys($rows[0])); // comprobación
    //echo"</pre>";
    //}

    // Verificate if lat & long are empty-> search in country_coords
    if (empty($latitude) || empty($longitude) ) {
      foreach($country_coords as $country_name => $coords) {
        if (strnatcasecmp($country, $country_name) == 0) {
          $latitude = $coords['latitude'];
          $longitude = $coords['longitude'];
          break;
        }
      }
    }
    //if (empty($latitude) || empty($longitude) ) {
    //if ($country !== null && isset($country_coords[$country] ) ) {
    //$latitude = $country_coords[$country]['latitude'];
    //$longitude = $country_coords[$country]['longitude'];
    //}
    //}

    // Store data in data_map array
    if (!empty($latitude) && !empty($longitude) ) {
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
  // list($latitude,$longitude) = explode(',', $coords);
  // $data_map[] = [
  // 'acc' => implode(", ", $acc_list), // Listar Acc ID's
  // 'country' => $country,
  // 'latitude' => $latitude,
  // 'longitude' => $longitude,
  // ];
  // }


  // Convert $data to JSON- to use it in Javascript part
  $json_data_map = json_encode($data_map);
  //echo $json_data_map; // funciona

  // Personalized marker
  $marker_path = "$images_path/map_labels/"; 
  $marker_colors_array = json_encode($colors_array);

  echo "<div id=\"map\" style=\"height: 600px;\"></div>"; // print the map

?>

  </div>


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
      iconUrl = '<?php echo $sp_name;?>_marker_default.png';
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
    var marker = L.marker([item.latitude, item.longitude], {icon: icon} );

    //var accList = item.acc.split(", ").map(acc => `<a href="03_passport_and_phenotype.php?pass_dir=<?php //echo $pass_dir; ?>&acc_id=${acc}">${acc}</a>`).join("<br>"); 

    var markerLabel = `<b>Acc ID:</b> <a href="03_passport_and_phenotype.php?pass_dir=<?php echo $pass_dir; ?>&acc_id=${item.acc}">${item.acc}</a><br><b>Country:</b> ${item.country}`; // con link

    marker.bindPopup(markerLabel);
    markers.addLayer(marker);
  }
});

// Añadir el grupo de clusters al mapa
map.addLayer(markers);


</script>