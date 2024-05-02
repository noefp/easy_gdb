<!-- HEADER -->
<?php include_once realpath("../../header.php");?>


<?php 
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
  $pass_dir = test_input($_GET["dir_name"]); // get passport directory with files to list
  $pass_dir_title = str_replace("_", " ", $pass_dir);
?>

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
          echo "<td><a href=\"03_passport_and_phenotype.php?pass_dir=$pass_dir&row_num=$row_count\">$col</a></td>";
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


