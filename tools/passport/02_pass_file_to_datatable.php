<!-- HEADER -->
<?php include_once realpath("../../header.php");?>

<?php 
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
  // $pass_dir = test_input($_GET["dir_name"]); // get passport directory with files to list
  // $pass_dir_title = str_replace("_", " ", $pass_dir);
?>

<!-- Load the QR library -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<!-- Load the map library -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<!-- Load the CLUSTER LIBRARIES -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<div class="margin-20">
<!-- <a href="/easy_gdb/index.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a> -->
</div>
<br>

<div id="load" class="loader"></div> 
<div id="body" class="page_container" style="display:none">
  <!-- <h1 class="text-center"><?php //echo "$pass_dir_title" ?></h1> -->
  <!-- <br> -->
  <!-- <div class="data_table_frame"> -->

<?php


$collapse_show=[]; //This variable storage the datatable ids than are showed

function print_table($file_path,$unique_link,$file_name,$show)
{
  $display_select=['hide collapse','collapse show'];

  $dataset_name1 = preg_replace('/\.[a-z]{3}$/',"",$file_name);
  $dataset_name = str_replace("_"," ",$dataset_name1);

  echo "<div id=ban_$dataset_name1 class=\"bg-secondary collapse_section pointer_cursor \" data-toggle=\"collapse\" data-target=#$dataset_name1 aria-expanded=\"true\">";
  echo"<i class=\"fas fa-sort\" style=\"color:#229dff\"></i><b style=\"font-size: 30px\"> $dataset_name </b><i class=\"fas fa-sort\" style=\"color:#229dff\"></i></div>";
  echo "<div id=$dataset_name1 class=\"p-7 my-3 $display_select[$show] table_collapse\">";

  if(!$show)
  {echo "<div id=\"load_$dataset_name1\" class=\"loader\"></div>";} // loader icon

  if($show){
    $tab_file = file($file_path);
    // get header array by columns
    $file_header = array_shift($tab_file);
    $header_cols = explode("\t", $file_header);

        // echo "<div class=\"data_table_frame\">";
        echo"<table id=\"tblAnnotations_$dataset_name1\" class=\"tblAnnotations table table-striped table-bordered\">\n";

        $field_number = -1;

        // //   TABLE HEADER
        echo "<thead><tr>\n";

      foreach ($header_cols as $head_index => $hcol) {
          echo "<th>$hcol</th>";
          // find column index for unique identifier that will link to accession info
          if ($unique_link == $hcol) {
            $field_number = $head_index;
          }
      } //close foreach
      
      echo "</tr></thead><tbody>";
      
      foreach ($tab_file as $line) {
        $columns = explode("\t", $line);
        echo "<tr>";
          
        foreach ($columns as $col_index => $col) {
            if ($col_index == $field_number) {
              echo "<td><a href=\"03_passport_and_phenotype.php?pass_dir=$GLOBALS[pass_dir]&acc_id=$col\">$col</a></td>";
              // echo "<td><a href=\"03_passport_and_phenotype.php?pass_dir=$pass_dir&row_num=$row_count\">$col</a></td>";
              // echo "<td><a href=\"row_data.php?row_data=".$table_file.",".$row_count.",".($field_number-1)."\">$col</a></td>";
            } else {  
              echo "<td>$col</td>";
            }
        } // end foreach columns
        echo "</tr>";
      } // end foreach lines

      echo "</tbody></table><br><br>";
      // echo"</div>";

      array_push($GLOBALS['collapse_show'],$dataset_name1);
    }
      echo"</div>";
} // end pasport file exist


// _______________main__________________________________________

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
 
  // start printing table and header
  
if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
  $file="$passport_path/$pass_dir/$passport_file";
  print_table($file,$unique_link,$passport_file,true); 
}

foreach ($phenotype_file_array as $phenotype_file) {
if ( file_exists("$passport_path/$pass_dir/$phenotype_file") ) {
  $file ="$passport_path/$pass_dir/$phenotype_file";
  print_table($file,$unique_link,$phenotype_file,false); 
}
}
?>

  <!-- CHICKPEA GERMPLASM PRINTED ON A MAP -->

  <div id="explore_ban" class="bg-secondary collapse_section pointer_cursor" data-toggle="collapse" data-target="#explore_map" aria-expanded="true">
    <!-- <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> -->
    <i class="fas fa-sort" style="color:#229dff"></i> <i class="fas fa-globe-americas" style="color:#ffff"></i><b style="font-size: 30px"> Explore the map </b><i class="fas fa-globe-americas" style="color:#ffff"></i> <i class="fas fa-sort" style="color:#229dff"></i>
    <!-- </div> -->
  </div>
  
  <div id="explore_map" class="p-7 my-3 collapse">
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



// Convert $data to JSON - to use it in Javascript part
$json_data_map = json_encode($data_map);
//echo $json_data_map; // funciona

// Personalized marker
$marker_path = "$images_path/map_labels/"; 
$marker_colors_array = json_encode($colors_array);

// echo "</div>";

echo "<div id=\"map\" class=\"rounded\" style=\"height: 500px;\"></div>"; // print the map
echo "</div>";

?>
  <!-- </div> -->
  </div>



<br>
<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>
<!-- ------------------------------------------------------------------------------------------------------------------------------------------>

 <!--------------------------------- Cs -------------------------------------------------------------------------------->
 <style>
  table.dataTable td,th  {
    max-width: 500px;
    white-space: nowrap;
    overflow: hidden;
    text-align: center;
  }
  
  .td-tooltip {
    cursor: pointer;
  }

  .collapse_section{
    color:white;
    border-radius: 5px;
    text-align:center;
  }
/*  */
  .collapse_section:hover{
    background-color: #465156  !important;
  }

</style>


<!------------------------------------------- JS DATATABLE ---------------------------------------------------------------------------------->

<script type="text/javascript">
  let show_col=<?php echo json_encode($collapse_show);?>; //This variable contains the ids of the tables shown for not to reload later
  let map_loaded=false;
  // alert(show_col);
  
function load_data_table(table_id){

  $(table_id).dataTable({
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
    "sScrollXInner": "100%",
    "bScrollCollapse": false,
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
$(".dataTables_info").addClass("float-left");
$(".dataTables_paginate").addClass("float-right");

};



$(document).ready(function(){ 
// //when data table is ready -> show the class datatable
  $('#body').css("display","block");
  $('#load').remove();
  load_data_table(".tblAnnotations");

});


//------------------------ Ajax function where  data table is load for  show after push the collapse -------------------------------------
function get_ajax_options(table_id,file_path,unique_link,pass_dir) {

    // $("#" + table_id).html("<p id=\"load\" style=\"text-align: center; margin:10px\"><b>Table Loading...</b></p>")  
    // let load_id= <?php //echo "<div id=\"load_id\" class=\"loader\"></div>";?>;
    // $("#" + table_id).html(load_id);

    jQuery.ajax({
      type: "POST",
      url: 'passport_table_ajax.php',
      data: {'id': table_id, 'path': file_path, 'link': unique_link, 'pass':pass_dir},

      success: function (opt_array) {
        // alert("opt_array: "+opt_array);
        let opt_lines = JSON.parse(opt_array);
        // alert("opt_lines:"+opt_lines);
        $("#" + table_id).html(opt_lines.join(""));
        // alert("tabla");
        load_data_table("#tblAnnotations_"+table_id);            
     }
  });

};

//--------------------------- when a datatable collapse is pushed-------------------------------------------------------------------------->

$('.collapse').on('shown.bs.collapse', function() {
  let collapsed_show=false;
  id=this.id;
  // alert(id);

  if(id=="explore_map"){ //when de banner map is push load map
    if(!map_loaded){
      draw_map();
      map_loaded=true;
    }
  }
  else{
      show_col.forEach(f=>{
      if(f == id)
        {collapsed_show=true}
      });

      if(!collapsed_show) // If this table has not been displayed yet, call the ajax function and add to list.
      {
          let file_path=<?php echo json_encode($passport_path."/".$pass_dir."/") ?>;
          file_path=file_path+id+".txt";
          let unique_link=<?php echo json_encode($unique_link)?>;
          let pass_dir = <?php echo json_encode($GLOBALS['pass_dir'])?>;
          // alert(pass_dir);


        get_ajax_options(id,file_path,unique_link,pass_dir);
        $('#load'+id).remove();

        show_col.push(id);
      } 
    }
});


// ---------- MAP function ------------------------------------
function draw_map(){

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
    }
</script>
