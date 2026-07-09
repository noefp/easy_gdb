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


// Función para obtener el complemento inverso de una secuencia
function reverse_complement($seq) {
  $complement = strtr(strtoupper($seq), 'ACGT', 'TGCA');
  return strrev($complement);
}


// Función para extraer un fragmento de la secuencia genómica basándose en las coordenadas de la característica y las coordenadas del gen
//el offset es la distancia relativa al inicio del gen, y se extrae la longitud correspondiente a la característica
function extract_fragment($genomic_fwd, $feat_start, $feat_end, $gene_start) {
  $offset = $feat_start - $gene_start;
  $length = $feat_end - $feat_start + 1;
  return substr($genomic_fwd, $offset, $length);
}

$seq_json_file = file_get_contents($json_files_path."/tools/seq_exp.json");
$seq_hash = json_decode($seq_json_file, true);

if($seq_dir != "") {
  $fasta_file = $_POST['seq_path']."/".$seq_hash[$seq_dir]['fasta_file'];
} else {
  $fasta_file = $_POST['seq_path']."/".$seq_hash['fasta_file'];
}

// Extrae la secuencia genómica de la región del gen utilizando samtools faidx, basándose en las coordenadas del gen obtenidas del GFF.
$samtools_cmd = "samtools faidx \"$fasta_file\" $chr:$start-$end";
$samtools_out = shell_exec($samtools_cmd); //samtools es un programa de linea de comandos por lo que se debe ejecutar con shell_exec


$lines = explode("\n", $samtools_out);
array_shift($lines); //elimina la primera linea, que es la cabecera
$genomic_fwd = strtoupper(implode("", $lines));  //une las lineas siguientes para obtener la secuencia genómica completa, y la convierte a mayúsculas para estandarizarla

$sequences = [];

if(!empty($mrnas)) {
  foreach($mrnas as $mrna_id => $mrna_data) {

    $regions = [];
    if(!empty($mrna_data['exons'])) {
      $regions = $mrna_data['exons'];
    } else {
      //combinas las regiones de UTRs y CDS para construir la secuencia del transcript, en caso de que el GFF no tenga exones pero sí tenga CDS y UTRs
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


    // ordena las regiones por coordenadas, teniendo en cuenta el strand, para luego extraer la secuencia en el orden correcto
    if($strand == '-') {
      usort($regions, function($a, $b) { return $b['start'] - $a['start']; });
    } else {
      usort($regions, function($a, $b) { return $a['start'] - $b['start']; });
    }


  // extrae la secuencia del transcript combinando las regiones de exones o, si no hay exones, 
  // combinando las regiones UTR y CDS, y aplica reverse complement si la hebra es negativa
    $transcript_seq = "";
    foreach($regions as $region) {
      $frag = extract_fragment($genomic_fwd, $region['start'], $region['end'], $start);
      if($strand == '-') $frag = reverse_complement($frag);
      $transcript_seq .= $frag;
    }

    // caso 5: solo exones sin CDS -> cds_seq vacío
    // caso 6: sin exones ni CDS -> transcript_seq y cds_seq vacíos
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
