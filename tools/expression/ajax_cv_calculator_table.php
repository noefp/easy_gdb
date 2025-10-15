<?php

  
$replicates_means = $_POST["replicates"];
$replicates_cv = $_POST["cv"];
$cvMean = isset($_POST["cvMean"]) ? $_POST["cvMean"] : false;
$gene_name = $_POST["gene_name"];

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
  array_push($table_array,"</tr></thead><tbody>");

//   foreach ($top_genes as $gene_name => $cv) { 
  array_push($table_array,'<tr>
      <td>'.$gene_name.'</td>');
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
  array_push($table_array,"</tr></thead><tbody>");

  array_push($table_array,'<tr>
      <td>'.$gene_name.'</td>');
      array_push($table_array,'<td><b>'.sprintf("%1\$.2f", $replicates_cv).'</b></td>');


    foreach ($replicates_means as $replicates_values => $mean_value ) { // $sample_names is an associative array with sample name as key and number of replicates as value
          $mean_data = isset($mean_value) ? sprintf("%1\$.2f",$mean_value) : "-";
          array_push($table_array,"<td>$mean_data</td>");
      }
  array_push($table_array,"</tr>");
  // } 
}  
array_push($table_array,"</tbody></table></div>");

  //rsort($file_array);
  //echo "hello";
  echo json_encode($table_array);

?>
