<!-- GERMPLASM PRINTED ON A MAP-->

<?php
  $rows = [];

  // Get map info

  if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
    $tab_file = file("$passport_path/$pass_dir/$passport_file");

    // get header array by columns
    $file_header = trim(array_shift($tab_file));
    $header_cols = explode("\t", $file_header);

    $field_number = 0;

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

  $total_rows = count($rows);

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

  // Read phenotype file to get trait marker 
  $phenotype_file = "$passport_path/$pass_dir/$phenotype_file_marker_trait";
  $acc_traits = [];

  if (file_exists($phenotype_file) ) {
    $lines = file($phenotype_file, FILE_IGNORE_NEW_LINES);

    foreach ($lines as $line) {
      $cols = explode ("\t", $line);
      $acc = $cols[$marker_acc_col];
      $trait = str_replace(" ", "_", $cols[$marker_column]);
      //echo "trait: $trait<br> $acc<br>"; //hay que conseguir unificar en uno solo valor
      $acc_traits[$acc] = $trait;
    }
  }

  // Array- keep data to Javascript
  $data_map = [];

  // Iterate $rows 
  foreach ($rows as $row) {
    if(isset($row["$unique_link"])){
    $acc = $row["$unique_link"];
    }
    if(isset($row["Country"])){
      $country = $row["Country"];
    }
    if(isset($row["Latitude"])){
      $latitude = $row["Latitude"];
    } else {
      $latitude = null;
      $row["Latitude"] = null;
    }
    if(isset($row["Longitude"])){
      $longitude = $row["Longitude"];
    } else {
      $longitude = null;
      $row["Longitude"] = null;
    }

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

    // Store data in data_map array
    if (!empty($latitude) && !empty($longitude) ) {
      
      $data_map[] = [
        'acc' => $acc,
        'country' => $country,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'trait' => $acc_traits[$acc] ?? 'default'
      ];
    }
  }

  // Convert $data to JSON- to use it in Javascript part
  $json_data_map = json_encode($data_map);

  // Personalized marker
  $marker_path = "$images_path/map_labels/"; 


  // if $traits_array is empty, use 'default'
  if (empty($traits_array)){
    $marker_traits_array = json_encode(['default']);
  } else {
  $marker_traits_array = json_encode($traits_array);
}

  if (!empty($map_array) ) {
    echo "<div class=\"p-1 my-1 bg-secondary text-white\"><center><h1><i class=\"fa-solid fa-location-crosshairs\"></i><b> Explore the map </b></h1></center></div><div class=\"p-7 my-3 border\">";

    echo "<div id=\"map\" style=\"height: 600px;\"></div>"; // print the map

    echo "</div>";
  }
?>



<script>

function draw_map(){
  // MAP 
  // Get $data_map from PHP
  const data = <?php echo $json_data_map; ?>;

  // Personalized markers with different traits
  markersPath = '<?php echo $marker_path; ?>';

  // Load $marker_traits_array in Javascript
  //markerTraitsArray = <?php //echo $marker_traits_array; ?>;

  // List options of markers (traits)
  validTraits = JSON.parse('<?php echo $marker_traits_array; ?>');

  // function to get the trait
  function getIcon(trait) {
  
    if (!validTraits.includes(trait) ) {
      trait = 'default';
    }

    if (trait == 'default') {
      if ('<?php echo $sp_name; ?>' != '') {
        iconUrl = '<?php echo $sp_name; ?>_default.png';
      } else {
        iconUrl = 'marker_default.png';
      }
    } else {
      if ('<?php echo $sp_name; ?>' != '') {
        iconUrl = '<?php echo $sp_name; ?>_' + trait + '.png';
      } else {
        iconUrl = `${trait}.png`;
      }
    }

    // Verifica el iconUrl en la consola del navegador
    //console.log("iconUrl:", iconUrl); // Esto imprimirá el valor de iconUrl

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
    
      var icon = getIcon(item.trait);
      var marker = L.marker([item.latitude, item.longitude], {icon: icon} );

      //var accList = item.acc.split(", ").map(acc => `<a href="03_passport_and_phenotype.php?pass_dir=<?php //echo $pass_dir; ?>&acc_id=${acc}">${acc}</a>`).join("<br>"); 

      var markerLabel = `<b>Acc ID:</b> <a href="03_passport_and_phenotype.php?pass_dir=<?php echo $pass_dir; ?>&acc_id=${item.acc}">${item.acc}</a><br><b>Country:</b> ${item.country}`; // con link

      marker.bindPopup(markerLabel);
      markers.addLayer(marker);
    }
  });

  // Añadir el grupo de clusters al mapa
  map.addLayer(markers);
}

</script>