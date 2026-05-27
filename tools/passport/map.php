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

      //--- get acc_id, latitude,longitude, country name and country code index from passport file----
    foreach($header_cols as $index => $col_name) {
      if (in_array($col_name, ["$unique_link","Latitude","Longitude","Country","Country code"])) {
        $map_array[] = $index;
        
        //-- // get index acc_id
        if($col_name == $unique_link){
          $unique_link_index = $index;
        }
      }
    }
    // echo "Unique link column: $unique_link_index<br>";
    // --------------------------------------------------------------------------------------------
    
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
      $cols = explode("\t", $line);
      $latitude = $cols[4];
      $longitude = $cols[5];
      $country_name_coords[$cols[0]] = ['latitude' => $latitude, 'longitude' => $longitude];
      $country_code_coords[$cols[2]] = ['latitude' => $latitude, 'longitude' => $longitude];

    }
  }

  // Read phenotype file to get trait marker 
  if ( $phenotype_file_marker_trait && isset($marker_column)) {
    $phenotype_file = "$passport_path/$pass_dir/$phenotype_file_marker_trait";
    $acc_traits = [];

    if (file_exists($phenotype_file) ) {
      $lines = file($phenotype_file, FILE_IGNORE_NEW_LINES);

      foreach ($lines as $line) {
        $cols = explode ("\t", $line);
        // $acc = $cols[$marker_acc_col];
        $acc = $cols[$unique_link_index];
        $trait = str_replace(" ", "_", $cols[$marker_column]);
        // $acc_traits[$acc] = $trait;
        //echo "trait: $trait<br> $acc<br>"; 

        // Get all traits for each acc in an array
        if(!isset($acc_traits[$acc])){
          $acc_traits[$acc] =  [];
        }
        $acc_traits[$acc][] = $trait;
      }
      // ($acc_traits[$acc] ??=[])[]= $trait;
    }
      // Get most common trait for each acc if there are more than 1 trait
      foreach (array_keys($acc_traits) as $acc) {
        if (count($acc_traits[$acc]) > 1) {
          // echo "Accession: $acc<br>";
          // echo(array_count_values($acc_traits[$acc]));
          $acc_traits[$acc] = array_keys(array_count_values($acc_traits[$acc]), max(array_count_values($acc_traits[$acc])))[0];  
        }else {
          $acc_traits[$acc] = $acc_traits[$acc][0];
        }
      }
    }


  // Array- keep data to Javascript
  $data_map = [];

  // Iterate $rows 
  foreach ($rows as $row) {
    if(isset($row["$unique_link"])){
    $acc = $row["$unique_link"];
    }

    $country = isset($row["Country"]) ? $row["Country"] : null;
    $country_code = isset($row["Country code"]) ? $row["Country code"] : null;

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

    // Verify if lat and long are empty
    if (empty($latitude) || empty($longitude)){
      $country_coords = !empty($country) ? [$country_name_coords, $country] : [$country_code_coords,$country_code];
        foreach($country_coords[0] as $country_name =>$coords){
          // echo "Comparing $country_name with $country_coords[1]<br>";
          if(strnatcasecmp($country_coords[1], $country_name) == 0){
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
        'country_code' => $country_code,
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

  $marker_traits_array = json_encode(get_dir_and_files($root_path.'/'.$marker_path));
  
//   // if $traits_array is empty, use 'default'
//   if (empty($traits_array)){
//     $marker_traits_array = json_encode(['default']);
//   } else {
//   $marker_traits_array = json_encode($traits_array);
// }

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

  // species name for marker icon
  spName = '<?php echo $sp_name; ?>';

  // function to get the trait image
  function getIcon(trait) {
  
    // Get the trait for the marker, if the trait image exist as png in map_labels folder, use it.(case insensitive)
    const trait_image_found = validTraits.find(name => name.toLowerCase() === (spName + '_' + trait + '.png').toLowerCase());
     //if not, use default.png (case insensitive)
    const default_image_found = validTraits.find(name => name.toLowerCase() === (spName + '_default.png').toLowerCase());
    
    // If the trait image exist, use it, if not, use default image, if not exist, use 'marker_default.png' as last option
    const iconUrl = trait_image_found || default_image_found || ('marker_default.png');

    return new L.Icon ({
    iconUrl: markersPath + iconUrl,
    iconSize: [25, 25],
    iconAnchor: [12, 12],
    });
  }

  // MAP
  var map = L.map('map', {scrollWheelZoom: false}).setView([0, 0], 2);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(map);

  // Create a cluster groups
  var markers = L.markerClusterGroup();

  // Add markers 
  data.forEach(item => {
    if (item.latitude && item.longitude) { // Verify coords 
    
      var icon = getIcon(item.trait);
      var marker = L.marker([item.latitude, item.longitude], {icon: icon} );

      //var accList = item.acc.split(", ").map(acc => `<a href="03_passport_and_phenotype.php?pass_dir=<?php //echo $pass_dir; ?>&acc_id=${acc}">${acc}</a>`).join("<br>"); 

  //  var markerLabel = `<b>Acc ID:</b> <a href="03_passport_and_phenotype.php?pass_dir=<?php //echo $pass_dir; ?>&acc_id=${item.acc}">${item.acc}</a><br><b>Country:</b> ${item.country}`; // con link

      if (item.country) {
          var markerLabel = `<b>Acc ID:</b> <a target="_blank" href="03_passport_and_phenotype.php?pass_dir=<?php echo $pass_dir; ?>&acc_id=${item.acc}">${item.acc}</a><br><b>Country:</b> ${item.country}`; // con link
        }else
        { if (item.country_code) {
            var markerLabel = `<b>Acc ID:</b> <a target="_blank" href="03_passport_and_phenotype.php?pass_dir=<?php echo $pass_dir; ?>&acc_id=${item.acc}">${item.acc}</a><br><b>Country code:</b> ${item.country_code}`; // con link
          }
        }

      marker.bindPopup(markerLabel);
      markers.addLayer(marker);
    }
  });

    map.addLayer(markers);

    // Function to adjust map view to fit markers
    function adjustMap() {
    if (markers.getLayers().length === 0) return; // No markers
    map.invalidateSize(true); // Force a map update
    map.fitBounds(markers.getBounds(), { padding: [50, 50], maxZoom: 3, minZoom: 3 }); // center map on markers with padding
    }

    // Adjust map when it's ready and when the window is resized
    map.whenReady(function() {
    setTimeout(adjustMap, 150);
    });

    // Adjust map when the window is resized
    window.addEventListener('resize', function() {
    setTimeout(adjustMap, 150);
    });

}

</script>