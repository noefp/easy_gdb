<?php

  
$replicates_means = isset($_POST["replicates"]) ? $_POST["replicates"] : [];
$replicates_cv = isset($_POST["cv"]) ? $_POST["cv"] : [];
$cvMean = isset($_POST["cvMean"]) ? json_decode($_POST["cvMean"],true) : false;
$gene_name = isset($_POST["gene_name"]) ? $_POST["gene_name"] : [];
$multicategory_found = isset($_POST["multicategory_found"]) ? json_decode($_POST["multicategory_found"], true) : true;
$link_found = isset($_POST["link_found"]) ? json_decode($_POST["link_found"],true) : false;
$gene_link = isset($_POST["gene_link"]) ? $_POST["gene_link"] : "";
$annot_json_file_found = isset($_POST["annot_json_file_found"]) ? json_decode($_POST["annot_json_file_found"],true) : false;
$annot_link = isset($_POST["annot_link"]) ? $_POST["annot_link"] : "";

$table_array = array();

array_push($table_array,'<div id="search_results_table">
<br><table id="cv_table_2" class="table table-striped table-bordered">
<thead><tr><th>Gene ID</th><th>Coef. Variation (%)</th>');

if (!$cvMean) { // if not mean mode
  foreach ($replicates_means as $sample_name => $replicates_count) { // $sample_names is an associative array with sample name as key and number of replicates as value
    for ($i=0; $i<count($replicates_count); $i++) {
      array_push($table_array,"<th>$sample_name</th>");
    }
  }
  array_push($table_array,"</tr></thead><tbody><tr>");
  if($link_found && !$multicategory_found) {
    $gene_url = str_replace("query_id", $gene_name, $gene_link);
    array_push($table_array,"<td><a href=\"$gene_url\" target=\"_blank\">$gene_name</a></td>");
  }else{
    if($annot_json_file_found && !$multicategory_found) {
      array_push($table_array,"<td><a href=\"/easy_gdb/gene.php?name=$gene_name&annot=$annot_link\" target=\"_blank\">$gene_name</a></td>");
    }else {
      array_push($table_array,"<td>$gene_name</td>");
    }
  }

  array_push($table_array,'<td><b>'.sprintf("%1\$.2f", $replicates_cv).'</b></td>');

  foreach ($replicates_means as $sample_name => $replicates_values) { // $sample_names is an associative array with sample name as key and number of replicates as value
    for ($i=0; $i<count($replicates_values); $i++) {
          $mean_data = isset($replicates_values) ? sprintf("%1\$.2f",$replicates_values[$i]) : "-";
          array_push($table_array,"<td>$mean_data</td>");
      }
    }
  array_push($table_array,"</tr>");
} 
else { // if mean mode
  foreach ($replicates_means as $sample_name => $mean_value) {
      array_push($table_array,"<th>$sample_name</th>");
  }

  array_push($table_array,"</tr></thead><tbody><tr>");

  if($link_found && !$multicategory_found) {
    $gene_url = str_replace("query_id", $gene_name, $gene_link);
    array_push($table_array,"<td><a href=\"$gene_url\" target=\"_blank\">$gene_name</a></td>");
  }else{
    if($annot_json_file_found && !$multicategory_found) {
      array_push($table_array,"<td><a href=\"/easy_gdb/gene.php?name=$gene_name&annot=$annot_link\" target=\"_blank\">$gene_name</a></td>");
    }else {
      array_push($table_array,"<td>$gene_name</td>");
    }
  }
  array_push($table_array,'<td><b>'.sprintf("%1\$.2f", $replicates_cv).'</b></td>');


    foreach ($replicates_means as $replicates_values => $mean_value ) { // $sample_names is an associative array with sample name as key and number of replicates as value
          $mean_data = isset($mean_value) ? sprintf("%1\$.2f",$mean_value) : "-";
          array_push($table_array,"<td>$mean_data</td>");
      }
  array_push($table_array,"</tr>");

}  
array_push($table_array,"</tbody></table></div>");

  //rsort($file_array);
  //echo "hello";
  echo json_encode($table_array);

?>
