<!-- HEADER-->
<?php include_once realpath("../../header.php");?>

<?php 
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
  $pass_dir = test_input($_GET["dir_name"]); // get passport directory with files to list
  $pass_dir_title = str_replace("_", " ", $pass_dir);

?>

<!-- Load the QR library-->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<!-- Load the map library-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<!-- Load the CLUSTER LIBRARIES-->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<!-- Load icon LIBRARY-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<br><br>
<div id="load" class="loader"></div> 
<div id="body" style="display:none">


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
              echo "<td><a href=\"03_passport_and_phenotype.php?pass_dir=$GLOBALS[pass_dir]&acc_id=$col\" target=\"_blank\">$col</a></td>";
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
}


  // get info from passport.json
  if ( file_exists("$passport_path/$pass_dir/passport.json") ) {
    $pass_json_file = file_get_contents("$passport_path/$pass_dir/passport.json");
    $pass_hash = json_decode($pass_json_file, true);

    $passport_file = $pass_hash["passport_file"];
    $phenotype_file_array = $pass_hash["phenotype_files"];
    $unique_link = $pass_hash["acc_link"];
    $map_array = $pass_hash["map_columns"];
    
    //to convert column index to natural, starting in 1 instead of 0
    foreach($map_array as &$val) {
      $val -= 1;
    }
    
    $traits_array = $pass_hash["map_markers"];
    $sp_name = $pass_hash["sp_name"];
    $phenotype_file_marker_trait = $pass_hash["phenotype_file_marker_trait"];
    
    if ($pass_hash["marker_column"]) {
      $marker_column = $pass_hash["marker_column"]-1;
    }
    if ($pass_hash["marker_acc_col"]) {
      $marker_acc_col = $pass_hash["marker_acc_col"]-1;
    }
    if ( file_exists("$passport_path/$pass_dir/$passport_file") ) {
      $tab_file="$passport_path/$pass_dir/$passport_file";
      print_table($tab_file,$unique_link,$passport_file,true); 
    }
    
    foreach ($phenotype_file_array as $phenotype_file) {
    if ( file_exists("$passport_path/$pass_dir/$phenotype_file") ) {
      $tab_file ="$passport_path/$pass_dir/$phenotype_file";
      print_table($tab_file,$unique_link,$phenotype_file,false); 
    }
  }
}
?>

<!-- MAP-->
<?php 

if ($show_map) {
  include_once realpath("$easy_gdb_path/tools/passport/map.php");
} 
?>
</div>
</div>
<br>
<br>

<br>
<!-- FOOTER-->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

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

  .fa-sort{
    color:white !important;
  }

</style>

<script src="../../js/datatable.js"></script>
<script type="text/javascript">
  let show_col=<?php echo json_encode($collapse_show);?>; //This variable contains the ids of the tables shown for not to reload later
  let map_loaded=<?php echo json_encode($show_map);?>
  // alert(show_col);
  
function load_data_table(table_id,button_id){

  // id=table_id.replace("#tblAnnotations_","");

  datatable(table_id,button_id);

//   $(table_id).dataTable({
//     dom:'Bfrtlpi',
//     "oLanguage": {
//       "sSearch": "Filter by:"
//       },
//     buttons: [
//       'copy', 'csv', 'excel',
//         {
//           extend: 'pdf',
//           orientation: 'landscape',
//           pageSize: 'LEGAL'
//         },
//       'print', 'colvis'
//       ],
//     "sScrollX": "100%",
//     "sScrollXInner": "100%",
//     "bScrollCollapse": false,
//     retrieve: true,
//     colReorder: true,
//     "drawCallback": function( settings ) {
//   // $('#body').css("display","inline");
//   // $(".td-tooltip").tooltip();
//     $("table.dataTable tbody tr").hover(
//         function() {
//             // Al pasar el mouse
//             $(this).css("background-color", "#d1d1d1");
//         }, function() {
//             // Al retirar el mouse
//             $(this).css("background-color", "");
//         }
//     );
//   },
// });

// $(".dataTables_filter").addClass("float-right");
// $(".dataTables_info").addClass("float-left");
// $(".dataTables_paginate").addClass("float-right");

};



$(document).ready(function(){ 
// //when data table is ready -> show the class datatable
  $('#body').css("display","block");
  $('#load').remove();
  load_data_table(".tblAnnotations","");

  if(map_loaded){
  draw_map();}

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
        load_data_table("#tblAnnotations_"+table_id,table_id);            
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
</script>