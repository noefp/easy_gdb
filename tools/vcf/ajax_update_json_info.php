<?php
$json_files_path = $_POST['json_files_path'];
$vcf_dir = $_POST['vcf_dir'];
$vcf_path = $_POST['vcf_path'];



if (file_exists($json_files_path."/tools/vcf.json")) {
  $vcf_json_file = file_get_contents($json_files_path."/tools/vcf.json");
  $vcf_hash = json_decode($vcf_json_file, true);
}

if($vcf_dir != "") {
  $chr_file_array = $vcf_hash[$vcf_dir]["chr_files"];
  $gene_names_file = "$vcf_path"."/".$vcf_dir."/".$vcf_hash[$vcf_dir]["gene_names_file"];
  $gff_file = "$vcf_path"."/".$vcf_dir."/".$vcf_hash[$vcf_dir]["gff_file"];
  $jb_dataset = $vcf_hash[$vcf_dir]["jb_data_folder"];
}else{
  $chr_file_array = $vcf_hash["chr_files"];
  $gene_names_file = "$vcf_path"."/".$vcf_hash["gene_names_file"];
  $gff_file = "$vcf_path"."/".$vcf_hash["gff_file"];
  $jb_dataset = $vcf_hash["jb_data_folder"];
}

$genes_array = [];

// array_push($genes_array,$gene_names_file);

if (file_exists($gene_names_file) ) {
  $tab_file = file($gene_names_file);

  //gets each replicate value for each gene
  foreach ($tab_file as $line) {
    $gene_name = trim($line);

    array_push($genes_array,$gene_name);
  }
}

// }
foreach ($chr_file_array as $chr => $chr_file) {
  $chr_select .= "<option value=\"$chr\">$chr</option>";
}


echo json_encode(array(
  'chr_file_array' => $chr_file_array,
  'gene_names_file' => $gene_names_file,
  'gff_file' => $gff_file,
  'jb_dataset' => $jb_dataset,
  'genes_array' => $genes_array,
  'chr_select' => $chr_select
));
?>
