<!-- Load the map library-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


<!-- ALL GERMPLASM PRINTED ON A MAP-->
<div class="p-1 my-1 bg-secondary text-white">
  <!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">-->
    <center><h1><i class="fa-solid fa-location-crosshairs"></i><b> Explore the map </b></h1></center>
  <!-- </div>-->
</div>
  <div class="p-7 my-3 border">

<?php
  
  // Array to store species and their respective total counts
  $species_count = [];

  // Array with all data map 
  $all_data_map = [];
 
  foreach ($subdir_name as $species) {
    //echo "species: $species<br>"; 
    $rows= [];

    //----- get info from passport.json of each species
    if ( file_exists("$sub_path/$species/passport.json") ) {
      $pass_json_file = file_get_contents("$sub_path/$species/passport.json");
      $pass_hash = json_decode($pass_json_file, true);

      $passport_file = $pass_hash["passport_file"];
      $unique_link = $pass_hash["acc_link"];
      $map_array = $pass_hash["map_columns"];     

      $full_passport_path = "$sub_path/$species/$passport_file";
      //echo "<b>full_passport_path</b>: $full_passport_path<br>";


      // Get germplasm info to print the map
      if ($passport_file) {
        if(file_exists($full_passport_path) ) {
          //echo "FILE EXISTS<br>";
          //echo "passport_file: $passport_file<br>";
            
           $tab_file = file($full_passport_path);

          // get header array by columns
          $file_header = trim(array_shift($tab_file) );
          $header_cols = explode("\t", $file_header);

          $file_header = 0;

          foreach($tab_file as $row_count => $line) {
            $columns = explode("\t", $line);
            $row_data = [];

            foreach($columns as $col_index => $col) {
              if(in_array($col_index,$map_array) ) {
                $row_data[$header_cols[$col_index] ] = $col;
              } // end in_array
            } // end foreach columns

            $rows[] = $row_data;

          } //close foreach line

        $total_rows = count($rows);
        //echo "<b>Total $species</b>:$total_rows<br>";

        // Add species ant its total_rows
        $species_count[$species] = $total_rows;


        //ordenarlo (descendente) en función del $total_rows. arsort()
        //hacer loop para recorrer e imprimir de forma ordenada. imprimir en array (array_push($species_html, texto en html)), hacer json_encode() para poder leerlo en javascript e imprimir con javascript
        //.html es la función que se utiliza en javascript. para ello tiene que tener un id y así se localiza donde imprimirlo en el código.

        // Read country_coordinates.txt
        $country_coords_file = "$root_path/easy_gdb/tools/passport/country_coordinates.txt";
        $country_coords = [];

        if (file_exists($country_coords_file) ) {
          $lines = file($country_coords_file, FILE_IGNORE_NEW_LINES);

          foreach ($lines as $line){
              $cols = explode ("\t", $line);
              $country = $cols[0];
              $latitude = $cols[4];
              $longitude = $cols[5];
              $country_coords[$country] = ['latitude' => $latitude, 'longitude' => $longitude];
          }
        }

        $data_map = []; //  keep data for Javascript

        // Iterate $rows by rows
        foreach ($rows as $row){
          if(isset($row["$unique_link"])){
          $acc = $row["$unique_link"];
          }
          if(isset($row["Country"])){
            $country = $row["Country"];
          }
          if(isset($row["Latitude"])){
            $latitude = $row["Latitude"];
          }
          if(isset($row["Longitude"])){
            $longitude = $row["Longitude"];
          }

        // Verify if lat and long are empty
        if (empty($latitude) || empty($longitude)){
          foreach($country_coords as $country_name =>$coords){
            if(strnatcasecmp($country, $country_name) == 0){
              $latitude = $coords['latitude'];
              $longitude = $coords['longitude'];
              break;
            }
          }
        }

        // Store data in $data_map
        if (!empty($latitude) && !empty($longitude)){
          $data_map[] = [
              'acc' => $acc,
              'country' => $country,
              'latitude' => $latitude,
              'longitude' => $longitude,
              'color' => 'default', 
              'species' => $species
          ];
        }
        //print_r($data_map);

      } // end foreach rows

        // Unify array data of map
        $all_data_map = array_merge($all_data_map, $data_map);

        //Personalized marker
        $marker_path = "$images_path/map_labels/";

        } // close if file_exists $full_passport_path
      } // close if ($passport_file){}
    } // close if file_exists passport.json 

    // Sort species by total_counts - descending order
    arsort($species_count); 

  } // close foreach $species

  foreach ($species_count as $species => $total) {
    //echo "<b>$species</b>: $total<br>"; // Print species sorted
  }
  // Convert data to JSON - to use it in Javascript
  //$json_species_count = json_encode($species_count); // in view_subdirectories
  $json_data_map = json_encode($all_data_map);
  //echo "json_data_map. $json_data_map<br>"; // funciona

  echo "<div id=\"map\" style=\"height: 600px;\"></div>";

?>


<script>
  const data = <?php echo $json_data_map; ?>;
  markersPath = '<?php echo $marker_path; ?>';

  function getIcon(species, color) {
    color = 'default';

    var iconUrl = `${species}_marker_${color}.png`;

    return new L.Icon({
        iconUrl: markersPath + iconUrl,
        iconSize: [25, 25],
        iconAnchor: [12, 12],
    });
  }

  var map = L.map('map').setView([0, 0], 2);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="">OpenStreetMap</a> contributors'}).addTo(map);

  var markers = L.markerClusterGroup();

  data.forEach(item => {
      if (item.latitude && item.longitude) {
          var icon = getIcon(item.species, item.color);
          var marker = L.marker([item.latitude, item.longitude], {icon: icon});

          var markerLabel = `<b>Acc ID:</b> <a href="03_passport_and_phenotype.php?pass_dir=<?php echo $pass_dir; ?>/${item.species}&acc_id=${item.acc}">${item.acc}</a><br><b>Country:</b> ${item.country}`;

          marker.bindPopup(markerLabel);
          markers.addLayer(marker);
      }
  });

  map.addLayer(markers);

</script>
