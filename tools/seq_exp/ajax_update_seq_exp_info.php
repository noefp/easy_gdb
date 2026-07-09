<?php
error_reporting(0);

$seq_dir         = $_POST['seq_dir'];
$seq_path        = $_POST['seq_path'];
$json_files_path = $_POST['json_files_path'];

$seq_json_file = file_get_contents($json_files_path."/tools/seq_exp.json");
$seq_hash = json_decode($seq_json_file, true);

$genes_array = [];

// Construye la ruta al archivo de nombres de genes, dependiendo de si el genoma tiene un directorio específico o no
if($seq_dir != "") {
  $gene_names_file = $_POST['seq_path']."/".$seq_hash[$seq_dir]['gene_names_file'];
} else {
  $gene_names_file = $_POST['seq_path']."/".$seq_hash['gene_names_file'];
}

// Lee el archivo de nombres de genes, si existe, y lo guarda en un array
if(file_exists($gene_names_file)) {
  $tab_file = file($gene_names_file);
  foreach($tab_file as $line) {
    array_push($genes_array, trim($line));
  }
}

// Lee el dataset de jb para este genoma, si existe, si no existe se devuelve un string vacío
$jb_dataset = isset($seq_hash[$seq_dir]['jb_data_folder']) ? $seq_hash[$seq_dir]['jb_data_folder'] : "";

// Lee la URL de JBrowse directamente del campo 'jbrowse' en seq_exp.json
// Si no existe, se usará jb_data_folder en el frontend como fallback
$jbrowse_url = (!empty($seq_hash[$seq_dir]['jbrowse'])) ? $seq_hash[$seq_dir]['jbrowse'] : "";

echo json_encode(array(
  'genes_array' => $genes_array,
  'jb_dataset'  => $jb_dataset,
  'jbrowse_url' => $jbrowse_url
));
?>
