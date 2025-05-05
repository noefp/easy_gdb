<?php
$file = $_POST["expr_file"];
$file_array = array();

if ( file_exists($file) ) {
  $tab_file = file("$file/gene_cluster.txt");
  $first_line = array_shift($tab_file);
  //echo json_encode($first_line);

  //gets each replicate value for each gene
  foreach ($tab_file as $line) {
    $columns = explode("\t", rtrim($line));
    $gene_name = $columns[0];

    array_push($file_array,$gene_name);
  }
}

  rsort($file_array);
  echo json_encode($file_array);
?>
