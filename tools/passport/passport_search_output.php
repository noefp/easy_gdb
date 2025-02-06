<!-- HEADER -->
<?php include_once realpath("../../header.php"); 
include_once realpath("$easy_gdb_path/tools/common_functions.php");?>

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a class="float-left pointer_cursor " style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>

<!-- <p id="load" style="text-align: center"><b>Table Loading...</b></p> -->

<!-- HTML -->
<div class="page_container" style="display:show" id="body">


<!-- GET INPUT -->
<?php
  $raw_input = trim($_GET["search_keywords"]);
  // $quoted_search = 0;
  $datasets_select = $_GET["checkboxes"];

  
  if ( preg_match('/^".+"$/',$raw_input ) ) {
    $quoted_search = 1;
  }
?>


<!-- IS BETTER TO SET IN ANOTHER FILE -->
<?php
 
  function print_search_table($grep_input, $annot_file, $dataset_name, $table_counter, $dir_or_file,$quoted_search,$json_info) {

    $output = []; 
    $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
    $dataset_name = str_replace("_"," ",$dataset_name);
    
    // $annot_file = str_replace(" ", "\\ ", $annot_file);

    // $grep_command = "grep -n -i '$grep_input' $annot_file";
    $grep_command = "grep -n -i '$grep_input' '$annot_file' | cut -d: -f1";
    exec($grep_command, $output);

    if ($output) {
      
      echo("<script>$('#info_$dir_or_file').hide();</script>");


      echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#Annot_table_$table_counter\" aria-expanded=\"true\"><i class=\"fas fa-table\" style=\"color:#229dff\"></i> $dataset_name table <i class=\" fas fa-sort\" style=\"color:#229dff\"></i></div>";      
      // TABLE BEGIN
      echo "<div id=\"Annot_table_$table_counter\" class=\"collapse hide\"><table style=\"display:none\" id=\"tblAnnotations_$table_counter\" class=\"tblAnnotations table table-striped table-bordered\">\n";
      

      echo "<div id=\"load_$table_counter\" class=\"loader\"></div>";

      // create table
      $tab_file = file("$annot_file");
      $first_line = array_shift($tab_file);
      $columns = explode("\t",trim($first_line));


      // TABLE HEADER
      echo "<thead><tr>\n";
      $field_number = -1;

      foreach ($columns as $index => $col) {
        if($col == $json_info["acc_link"])
        {
          $field_number=$index;
          break;
        }}

      foreach ($columns as $index => $col) {
        echo "<th>$col</th>\n";
      }
      echo "</tr></thead>";

    //   // TABLE BODY
    echo "<tbody>";
    foreach ($output as $n_line) {
      echo "<tr>";
      $datas = explode("\t", $tab_file[$n_line-2]);
      foreach($datas as $index => $data){
        // echo "<td>$datas[$n]</td>";
        if($index == $field_number)
          {echo "<td><a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$dir_or_file&acc_id=$data\" target=\"_blank\">$data</a></td>\n";
          }else
          {echo "<td>$data</td>\n";}
          
        }
      echo "</tr>";
    }
  // }
      echo "</tbody>";
      echo "</table><br><br><br></div>";
} //end if output
    
    
} // End of function

  function get_info_table($passport_path,$dir_or_file,$search_query,$table_counter,$quoted_search)
  {
    if (is_dir($passport_path."/".$dir_or_file)){ // get dirs and print categories

      $dir_name = str_replace("_"," ",$dir_or_file);
        
      // get info from passport.json
      $path_json = "$passport_path/$dir_or_file/passport.json";
      $path_json = str_replace("//","/",$path_json);

      if ( file_exists("$path_json") ) {
        $pass_json_file = file_get_contents("$path_json");
        $pass_hash = json_decode($pass_json_file, true);


        echo "<br><div style=\"text-align:center\"><h1><i>$dir_name</i></h1></div>";
        echo "<div id=\"info_$dir_or_file\" class=\"alert alert-warning\" role=\"alert\" style=\"text-align:center\">Results not found</div>";

        $passport_file = $pass_hash["passport_file"];
        $path_passport_file=str_replace("//","/","$passport_path/$dir_or_file/$passport_file");
        if(file_exists("$path_passport_file")){
           print_search_table($search_query,$path_passport_file ,$passport_file, "passport".$table_counter, $dir_or_file,$quoted_search,$pass_hash);
        }

        $phenotype_file_array = $pass_hash["phenotype_files"];
          
        foreach ($phenotype_file_array as $index => $phenotype_file) {
          $path_phenotypes_file=str_replace("//","/","$passport_path/$dir_or_file/$phenotype_file");
          if(file_exists("$path_phenotypes_file")){
          print_search_table($search_query,$path_phenotypes_file,$phenotype_file, "phenotype".$table_counter.$index, $dir_or_file,$quoted_search,$pass_hash);
          }
        }
      }    
  }// close if is_dir   
}
  
?>



<!-- INCLUDE TABLE  -->

<!-- ------------------------------ MAIN --------------------------------------------------------------->
<?php
  // if(empty($raw_input)) {
  //   echo "<br>";
  //   echo "<h1>No words to search provided.</h1>";
  // }
  // else {
    $search_input = test_input2($raw_input);
    
    echo '<br><div class="alert alert-dismissible show" style="background-color:#f0f0f0">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
      <span aria-hidden="true">&times;</span>
    </button>';
    echo "<h3 style=\"display:inline\"><i>Search Input</i></h3>";
    echo "<div class=\"card-body\" style=\"padding-top:10px;padding-bottom:0px\">$search_input</div></div>";
    
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

    include_once realpath("$easy_gdb_path/tools/common_functions.php");
      
    // $all_datasets = get_dir_and_files($passport_path); // find dirs in passport path



    if (!empty($datasets_select))
    { $all_datasets=$datasets_select;
      asort($all_datasets);
      foreach ($all_datasets as $table_counter => $dir_or_file) {
        get_info_table($passport_path,$dir_or_file,$search_query,$table_counter,$quoted_search);
      }//foreach all_dir    
    } else{
      get_info_table($passport_path,"",$search_query,1,$quoted_search);
    }  
  // } //close else
?>
<!-- END TABLE  -->
</div>
<!-- END HTML -->

<!-- Cs -->
<style>
  table.dataTable td,th  {
    max-width: 500px;
    overflow: hidden;
    white-space: nowrap;
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
<script src="../../js/datatable.js"></script>
<script type="text/javascript">
$(document).ready(function(){  
  // //when data table is ready -> show the data table
  
$(".collapse").on('shown.bs.collapse', function(){
    var id=$(this).attr("id");
    id=id.replace("Annot_table_","");
    // alert('#load_'+id);

    $('#load_'+id).remove();

    $('#tblAnnotations_'+id).css("display","table");

    datatable('#tblAnnotations_'+id,id);

});

$(document).ready(function(){
  $(".td-tooltip").tooltip();
});
});

</script>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>


