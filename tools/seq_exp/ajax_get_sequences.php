<?php
error_reporting(0);

$seq_dir         = $_POST['seq_dir'];
$seq_path        = $_POST['seq_path'];
$json_files_path = $_POST['json_files_path'];
$chr             = $_POST['chr'];
$start           = (int)$_POST['start'];
$end             = (int)$_POST['end'];
$strand          = $_POST['strand'];
$mrnas           = json_decode($_POST['mrnas'], true); // array con la estructura del gen, que es el resultado de ajax_get_gene_structure.php 
function reverse_complement($seq) {
  $complement = strtr(strtoupper($seq), 'ACGT', 'TGCA');
  return strrev($complement);
}
function extract_fragment($genomic_fwd, $feat_start, $feat_end, $gene_start) {
  $offset = $feat_start - $gene_start;
  $length = $feat_end - $feat_start + 1;
  return substr($genomic_fwd, $offset, $length);
}

$seq_json_file = file_get_contents($json_files_path."/tools/seq_exp.json");
$seq_hash = json_decode($seq_json_file, true);

if($seq_dir != "") {
  $blast_db = $_POST['seq_path']."/".$seq_hash[$seq_dir]['blast_db'];
} else {
  $blast_db = $_POST['seq_path']."/".$seq_hash['blast_db'];
}

$blastdbcmd = "blastdbcmd -db \"$blast_db\" -entry $chr -range $start-$end -strand plus";
$blast_out = shell_exec($blastdbcmd);


$lines = explode("\n", $blast_out);
array_shift($lines); //elimina la primera linea, que es la cabecera
$genomic_fwd = strtoupper(implode("", $lines));  //une las lineas siguientes para obtener la secuencia genómica completa, y la convierte a mayúsculas para estandarizarla

$sequences = [];

if(!empty($mrnas)) {
  foreach($mrnas as $mrna_id => $mrna_data) {

    $regions = [];
    if(!empty($mrna_data['exons'])) {
      $regions = $mrna_data['exons'];
    } else {
      if(!empty($mrna_data['five_prime_UTR'])) {
        foreach($mrna_data['five_prime_UTR'] as $r) $regions[] = $r;
      }
      if(!empty($mrna_data['CDS'])) {
        foreach($mrna_data['CDS'] as $r) $regions[] = $r;
      }
      if(!empty($mrna_data['three_prime_UTR'])) {
        foreach($mrna_data['three_prime_UTR'] as $r) $regions[] = $r;
      }
    }
    if($strand == '-') {
      usort($regions, function($a, $b) { return $b['start'] - $a['start']; });
    } else {
      usort($regions, function($a, $b) { return $a['start'] - $b['start']; });
    }
    $transcript_seq = "";
    foreach($regions as $region) {
      $frag = extract_fragment($genomic_fwd, $region['start'], $region['end'], $start);
      if($strand == '-') $frag = reverse_complement($frag);
      $transcript_seq .= $frag;
    }
    $cds_regions = !empty($mrna_data['CDS']) ? $mrna_data['CDS'] : [];
    if($strand == '-') {
      usort($cds_regions, function($a, $b) { return $b['start'] - $a['start']; });
    } else {
      usort($cds_regions, function($a, $b) { return $a['start'] - $b['start']; });
    }

    $cds_seq = "";
    foreach($cds_regions as $cds) {
      $frag = extract_fragment($genomic_fwd, $cds['start'], $cds['end'], $start);
      if($strand == '-') $frag = reverse_complement($frag);
      $cds_seq .= $frag;
    }

    $sequences[$mrna_id] = [
      'transcript_seq' => $transcript_seq,
      'cds_seq'        => $cds_seq
    ];
  }
}

if($strand == '-') {
  $genomic_seq = reverse_complement($genomic_fwd);
} else {
  $genomic_seq = $genomic_fwd;
}

echo json_encode([
  'genomic_seq' => $genomic_seq,
  'sequences'   => $sequences
]); // devuelve un JSON con la secuencia genómica de la región del gen y un array con las secuencias de los transcripts e isoformas, por ejemplo:
/*
{
  "genomic_seq": "ATGCGTACGTAGCTAGCTAGCTAGCTAGCTAGCTAGCTAGCTAGCTAGCTAGC",
  "sequences": {
    "mRNA1": {
      "transcript_seq": "ATGCGTACGTAGCTAGCTAGCTAGC",
      "cds_seq": "ATGCGTACGTAGCTAGC"
    },
    "mRNA2": {
      "transcript_seq": "ATGCGTACGTAGCTAGCTAGCTAGCTAGC",
      "cds_seq": "ATGCGTACGTAGCTAGCTAGC"
    }
  }
}
*/
?>
