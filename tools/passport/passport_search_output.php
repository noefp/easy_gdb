<!-- HEADER -->
<?php include_once realpath("../../header.php"); 
include_once realpath("$easy_gdb_path/tools/common_functions.php");?>

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a href="passport_search_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>

<p id="load" style="text-align: center"><b>Table Loading...</b></p>

<!-- HTML -->
<div class="page_container" style="display:none" id="body">


<!-- GET INPUT -->
<?php
  $raw_input = trim($_GET["search_keywords"]);
  $quoted_search = 0;
  
  if ( preg_match('/^".+"$/',$raw_input ) ) {
    $quoted_search = 1;
  }
?>


<!-- IS BETTER TO SET IN ANOTHER FILE -->
<?php
 
  function print_search_table($grep_input, $annot_file, $dataset_name, $table_counter, $dir_or_file,$quoted_search) {
        
    $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
    $dataset_name = str_replace("_"," ",$dataset_name);
    
    $annot_file = str_replace(" ", "\\ ", $annot_file);

    $head_command = "head -n 1 $annot_file";
    $output_head = exec($head_command);

    $grep_command = "grep -n -i '$grep_input' $annot_file";
    exec($grep_command, $output);

    if ($output) {
      
      echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#Annot_table_$table_counter\" aria-expanded=\"true\"><i class=\"fas fa-sort\" style=\"color:#229dff\"></i> $dataset_name</div>";
      
      
      // TABLE BEGIN
      echo "<div id=\"Annot_table_$table_counter\" class=\"collapse show\"><div class=\"data_table_frame\"><table id=\"tblAnnotations\" class=\"tblAnnotations table table-striped table-bordered\">\n";


      // TABLE HEADER
      echo "<thead><tr>\n";
      $columns = explode("\t", $output_head);
      $col_number = count($columns);
      foreach ($columns as $col) {
        echo "<th>$col</th>\n";
      }
      echo "</tr></thead>\n";


      // TABLE BODY
      echo "<tbody>\n";

      foreach ($output as $line) {
        echo "<tr>\n";
        $data = explode("\t", $line);
        for ($n = 0; $n <= $col_number-1; $n++) {
          if ($n == 0) {
            list($line,$acc) = explode(":", $data[0]);
            $acc_line = $line-2;
            //echo "<td><a href=\"/easy_gdb/gene.php?name=$data[$n]@$annot_file\" target=\"_blank\">$data[$n]</a></td>\n";
            echo "<td><a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$dir_or_file&acc_id=$acc\" target=\"_blank\">$acc</a></td>\n";
          }
          else if ($data[$n]) {
            echo "<td>$data[$n]</td>\n";
          }
          else {
            echo "<td></td>\n";
          }
        }
        echo "</tr>\n";
      }
      echo "</tbody></table></div></div><br>\n";
      
    } //end if output
    
    
    $output = [];
  } // End of function
  
  
?>



<!-- INCLUDE TABLE  -->
<?php
  if(empty($raw_input)) {
    echo "<br>";
    echo "<h1>No words to search provided.</h1>";
  }
  else {
    $search_input = test_input2($raw_input);
    
    echo "\n<br><h3>Search Input</h3>\n<div class=\"card bg-light\"><div class=\"card-body\">$search_input</div></div><br>\n";
    
    // QUOTED INPUTS
    if ($quoted_search) {
      $search_query = preg_replace('/[\"\<\>\t\;]+/','',strtolower($raw_input) );
    }
    elseif (preg_match('/\s+/', $search_input)) {
      $search_query = preg_replace('/\s+/','\|',strtolower($search_input) );
    }
    else {
      $search_query = strtolower($search_input);
    }


    // COMMANDS AND PRINT
    $table_counter = 1;

    include_once realpath("$easy_gdb_path/tools/common_functions.php");
      
    $all_datasets = get_dir_and_files($passport_path); // find dirs in passport path
    asort($all_datasets);
      
    foreach ($all_datasets as $dir_or_file) {
      if (is_dir($passport_path."/".$dir_or_file)){ // get dirs and print categories
        
        $dir_name = str_replace("_"," ",$dir_or_file);
          
        // get info from passport.json
        if ( file_exists("$passport_path/$dir_or_file/passport.json") ) {
          $pass_json_file = file_get_contents("$passport_path/$dir_or_file/passport.json");
          $pass_hash = json_decode($pass_json_file, true);
  
          $passport_file = $pass_hash["passport_file"];
          print_search_table($search_query, $passport_path."/".$dir_or_file."/".$passport_file, $passport_file, $table_counter, $dir_or_file,$quoted_search);
          
          $table_counter++;
          
          $phenotype_file_array = $pass_hash["phenotype_files"];
            
          foreach ($phenotype_file_array as $phenotype_file) {
            print_search_table($search_query, $passport_path."/".$dir_or_file."/".$phenotype_file, $phenotype_file, $table_counter, $dir_or_file,$quoted_search);
            
            //read_passport_file("$passport_path/$dir_or_file",$phenotype_file,$unique_link);
          }
        }
        
      }// close if is_dir
          
    }//foreach all_dir
      
      
      
  } //close else
?>
<!-- END TABLE  -->


<br>
<br>
</div>
<!-- END HTML -->

<!-- Cs -->
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

  .collapse_section:hover  { 
  background-color: #6c757d;
  color:#fff;  
}
  
</style>


<!-- JS DATATABLE -->
<script type="text/javascript">
$(document).ready(function(){  
  // //when data table is ready -> show the data table
  $('#body').css("display","block");
  $('#load').remove();

  $(".tblAnnotations").dataTable({
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
    "drawCallback": function( settings ) {
    $(".td-tooltip").tooltip();},
    });

$(".dataTables_filter").addClass("float-right");
$(".dataTables_info").addClass("float-left");
$(".dataTables_paginate").addClass("float-right");
});

$(document).ready(function(){
  $(".td-tooltip").tooltip();
});

</script>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>


