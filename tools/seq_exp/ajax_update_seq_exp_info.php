<?php
error_reporting(0);

$seq_dir         = $_POST['seq_dir'];
$seq_path        = $_POST['seq_path'];
$json_files_path = $_POST['json_files_path'];

$seq_json_file = file_get_contents($json_files_path."/tools/seq_exp.json");
$seq_hash = json_decode($seq_json_file, true);

$genes_array = [];
if($seq_dir != "") {
  $gene_names_file = $_POST['seq_path']."/".$seq_hash[$seq_dir]['gene_names_file'];
} else {
  $gene_names_file = $_POST['seq_path']."/".$seq_hash['gene_names_file'];
}
if(file_exists($gene_names_file)) {
  $tab_file = file($gene_names_file);
  foreach($tab_file as $line) {
    array_push($genes_array, trim($line));
  }
}
$jbrowse_url = (!empty($seq_hash[$seq_dir]['jbrowse'])) ? $seq_hash[$seq_dir]['jbrowse'] : "";

echo json_encode(array(
  'genes_array' => $genes_array,
  'jbrowse_url' => $jbrowse_url
));
?>
