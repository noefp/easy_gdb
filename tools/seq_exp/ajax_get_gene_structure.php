<?php
error_reporting(0);

$gene_name       = $_POST['gene_name'];
$seq_dir         = $_POST['seq_dir'];
$seq_path        = $_POST['seq_path'];
$json_files_path = $_POST['json_files_path'];

$seq_json_file = file_get_contents($json_files_path."/tools/seq_exp.json");
$seq_hash = json_decode($seq_json_file, true);

if($seq_dir != "") {
  $gff_file = $_POST['seq_path']."/".$seq_hash[$seq_dir]['gff_file'];
} else {
  $gff_file = $_POST['seq_path']."/".$seq_hash['gff_file'];
}

function parse_attributes($col8) {
  $attributes = [];
  foreach(explode(";", $col8) as $attr) {
    $attr_parts = explode("=", $attr, 2);
    if(count($attr_parts) == 2) {
      $attributes[trim($attr_parts[0])] = trim($attr_parts[1]);
    }
  }
  return $attributes;
}

function parse_gff_lines($lines, &$gene_structure) {
  // two passes: first gene and mRNA, then children (exon, CDS, UTRs)
  // this handles GFFs where children appear before parents in grep output
  foreach ([['gene','mRNA'], ['exon','CDS','five_prime_UTR','three_prime_UTR']] as $pass_features) {
  foreach ($lines as $line) {
    if(empty($line) || $line[0] == '#') continue;
    $cols = explode("\t", $line);
    if(count($cols) < 9) continue;
    if(!in_array($cols[2], $pass_features)) continue;

    $attributes = parse_attributes($cols[8]);

    if($cols[2] == 'gene') {
      $gene_structure['chr']    = $cols[0];
      $gene_structure['start']  = (int)$cols[3];
      $gene_structure['end']    = (int)$cols[4];
      $gene_structure['strand'] = $cols[6];
    }
    elseif($cols[2] == 'mRNA') {
      $mrna_id = $attributes['ID'];
      if(!isset($gene_structure['mRNAs'][$mrna_id])) {
        $gene_structure['mRNAs'][$mrna_id] = [
          'start'           => (int)$cols[3],
          'end'             => (int)$cols[4],
          'exons'           => [],
          'CDS'             => [],
          'five_prime_UTR'  => [],
          'three_prime_UTR' => []
        ];
      }
      // if no gene feature found yet, infer from mRNA using geneID or ID
      if(!isset($gene_structure['chr'])) {
        $gene_id = isset($attributes['geneID']) ? $attributes['geneID'] : 
                   (isset($attributes['Parent']) ? $attributes['Parent'] : null);
        $gene_structure['chr']    = $cols[0];
        $gene_structure['start']  = (int)$cols[3];
        $gene_structure['end']    = (int)$cols[4];
        $gene_structure['strand'] = $cols[6];
      } else {
        // update gene boundaries if mRNA extends beyond current gene coords
        $gene_structure['start'] = min($gene_structure['start'], (int)$cols[3]);
        $gene_structure['end']   = max($gene_structure['end'],   (int)$cols[4]);
      }
    }
    elseif($cols[2] == 'exon') {
      $parent_id = $attributes['Parent'];
      if(isset($gene_structure['mRNAs'][$parent_id])) {
        $gene_structure['mRNAs'][$parent_id]['exons'][] = [
          'start' => (int)$cols[3],
          'end'   => (int)$cols[4]
        ];
      }
    }
    elseif($cols[2] == 'CDS') {
      $parent_id = $attributes['Parent'];
      if(isset($gene_structure['mRNAs'][$parent_id])) {
        $gene_structure['mRNAs'][$parent_id]['CDS'][] = [
          'start' => (int)$cols[3],
          'end'   => (int)$cols[4]
        ];
      }
    }
    elseif($cols[2] == 'five_prime_UTR') {
      $parent_id = $attributes['Parent'];
      if(isset($gene_structure['mRNAs'][$parent_id])) {
        $gene_structure['mRNAs'][$parent_id]['five_prime_UTR'][] = [
          'start' => (int)$cols[3],
          'end'   => (int)$cols[4]
        ];
      }
    }
    elseif($cols[2] == 'three_prime_UTR') {
      $parent_id = $attributes['Parent'];
      if(isset($gene_structure['mRNAs'][$parent_id])) {
        $gene_structure['mRNAs'][$parent_id]['three_prime_UTR'][] = [
          'start' => (int)$cols[3],
          'end'   => (int)$cols[4]
        ];
      }
    }
  }
  } // end pass
}

// First grep: search by gene name
$grep_cmd = "zgrep '$gene_name' \"$gff_file\"";
$grep_out = shell_exec($grep_cmd);
$gff_lines = array_filter(explode("\n", $grep_out));

$gene_structure = [];
parse_gff_lines($gff_lines, $gene_structure);

// If mRNAs were found but have no exons/CDS, do a second grep for each mRNA ID
// This handles NCBI GFFs where exons/CDS have different Parent IDs
if(!empty($gene_structure['mRNAs'])) {
  $needs_second_grep = false;
  foreach($gene_structure['mRNAs'] as $mrna_id => $mrna_data) {
    if(empty($mrna_data['exons']) && empty($mrna_data['CDS'])) {
      $needs_second_grep = true;
      break;
    }
  }

  if($needs_second_grep) {
    foreach($gene_structure['mRNAs'] as $mrna_id => $mrna_data) {
      $grep_cmd2 = "zgrep '$mrna_id' \"$gff_file\"";
      $grep_out2 = shell_exec($grep_cmd2);
      $gff_lines2 = array_filter(explode("\n", $grep_out2));
      parse_gff_lines($gff_lines2, $gene_structure);
    }
  }
}

// helper functions for GFF table
function extract_note($col8) {
  foreach(explode(";", $col8) as $attr) {
    $parts = explode("=", $attr, 2);
    if(count($parts) == 2 && trim($parts[0]) == 'Note') return trim($parts[1]);
  }
  return "";
}
function extract_id_attr($col8) {
  foreach(explode(";", $col8) as $attr) {
    $parts = explode("=", $attr, 2);
    if(count($parts) == 2 && trim($parts[0]) == 'ID') return trim($parts[1]);
  }
  return "";
}

// collect all grep lines avoiding duplicates
$all_grep_lines = array_filter(explode("\n", $grep_out));
if(isset($grep_out2)) {
  $all_grep_lines = array_unique(array_merge(
    $all_grep_lines,
    array_filter(explode("\n", $grep_out2))
  ));
}

// build GFF table rows - all features
$gff_table_rows = [];
foreach($all_grep_lines as $line) {
  if(empty($line) || $line[0] == '#') continue;
  $cols = explode("\t", $line);
  if(count($cols) < 9) continue;
  $feature = $cols[2];

  // build info column
  if($feature == 'mRNA') {
    $id   = extract_id_attr($cols[8]);
    $note = extract_note($cols[8]);
    $info = $id;
    if($note) $info .= " — " . $note;
  } else {
    $info = extract_id_attr($cols[8]);
    if(!$info) $info = $cols[8]; // fallback to full attributes
  }

  $gff_table_rows[] = [
    'chr'     => $cols[0],
    'feature' => $feature,
    'start'   => $cols[3],
    'end'     => $cols[4],
    'strand'  => $cols[6],
    'info'    => $info
  ];
}

$gene_structure['gff_lines'] = $gff_table_rows;

echo json_encode($gene_structure);
?>
