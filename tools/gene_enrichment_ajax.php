
<?php
$lookup_file = $_POST["lookup_db"];
$input_gene_list = $_POST["gene_list"];
$file_array = [];

if ( file_exists("$lookup_file") ) {

  $gene_hash = [];
  $gNamesArr=array_filter(explode("\n",trim($_POST["gene_list"])),function($gName) {return ! empty($gName);});

  $tab_file = file($lookup_file);
  $columns = [];
  $count = 1;
  foreach ($tab_file as $line) {
    $trimmed_line = trim($line);
    $columns = str_getcsv($trimmed_line,"\t");
  
    $gene_hash[trim($columns[0])] = trim($columns[1]);
  }

  foreach ($gNamesArr as $input_gene) {
    $converted_gene = $gene_hash[trim($input_gene)];
  
    if ( preg_match("/;/", $converted_gene ) ) {
    
      $multi_genes = explode(";",$converted_gene);
    
      foreach ($multi_genes as $one_gene) {
        array_push($file_array,$one_gene);
      }
    } else {
      array_push($file_array,$converted_gene);
    }
  }

  echo json_encode($file_array);
}

if ($lookup_file == "none") {
  $gNamesArr=array_filter(explode("\n",trim($_POST["gene_list"])),function($gName) {return ! empty($gName);});
  echo json_encode($gNamesArr);
}


?>
