<?php
// HASH ANNOTATION
if (file_exists("$json_files_path/tools/annotation_links.json")) {
  $annot_json_file = file_get_contents("$json_files_path/tools/annotation_links.json");
  $annot_hash = json_decode($annot_json_file, true);
}
if (file_exists("$json_files_path/tools/coexpression.json")) {
  $datasets = json_decode(file_get_contents("$json_files_path/tools/coexpression.json"), true);
  $annot_file = $annotations_path . "/" . $datasets[basename($lookup_file)];
}

// INPUT
if ($quoted_search) {
  $desc_input = preg_replace('/["\<\>\t\;]+/','',strtolower($raw_input));
} elseif (preg_match('/\s+/', $search_input)) {
  $desc_input = preg_replace('/\s+/','\|',strtolower($search_input));
} else {
  $desc_input = strtolower($search_input);
}

// GREP1
$desc_arg = escapeshellarg($desc_input);
$folder_arg = escapeshellarg($lookup_file) . '/*.txt';
$grep_gene = "zgrep -i $desc_arg $folder_arg";
exec($grep_gene, $gene_module);

$gene_module_string = $gene_module[0];
list($gene, $module) = explode("\t", $gene_module_string);

// NO GENE FOUND
if (!$gene) {
  echo '<div class="alert alert-danger" role="alert" style="text-align:center">No gene was found in the selected dataset</div><br>';
}

// GENE FOUND
else {
  $module_file = glob("$lookup_file/$module*");

  // GREP2
  $grep_gene_cor = "zgrep '$gene' '$module_file[0]'";
  exec($grep_gene_cor, $gene_cor);

  $gene_names = explode("\t", $gene_cor[0]);
  $gene_values = explode("\t", $gene_cor[1]);

  // CORRELATION ARRAY
  $gene_cor_array = [];
  foreach ($gene_names as $index => $gene_name) {
    $cor = trim($gene_values[$index]);
    if ($cor === '' || strtoupper($cor) == 'NA') {
      $cor = null;
    }
    if (is_numeric($cor)) {
      $gene_cor_array[$gene_name] = $cor;
    }
  }

  // ARRAY GENE COLUMN
  $gene_index = preg_grep("/$gene/", $gene_names);
  $gene_column = key($gene_index) + 1;

  $cut_command = "zcat '$module_file[0]' | cut -f1,'$gene_column'";
  exec($cut_command, $gene_column_array);

  foreach ($gene_column_array as $gene_line) {
    list($gene_name, $cor) = explode("\t", $gene_line);
    if (is_numeric($cor)) {
      $gene_cor_array[$gene_name] = $cor;
    }
  }

  // ANNOT FILE
  if (file_exists($annot_file)) {
    $head_command = "head -n 1 $annot_file";
    $output_head = exec($head_command);
    $head_columns = explode("\t", $output_head);
    array_shift($head_columns);
  
    foreach ($head_columns as &$category) {
      $category = preg_replace('/.*description/i', 'Description', $category);
    }
  
    $annots = [];
    foreach ($gene_cor_array as $gene => $cor) {
      $result = [];
      exec("grep -P '^$gene(\\.\\d+)?\\t' $annot_file", $result); // ISOFORMS
      if (!empty($result)) {
        $annots[] = $result[0];
      } else {
        $annots[] = null;
      }
    }
  
    $annots_array = [];
    foreach ($annots as $row) {
      if (!empty($row)) {
        $annots_array[] = explode("\t", $row);
      }
    }

    foreach ($annots_array as $array) {
      $gene_annot = $array[0];
      if (array_key_exists($gene_annot, $gene_cor_array) && is_scalar($gene_cor_array[$gene_annot])) {
        array_shift($array);
        $gene_cor_array[$gene_annot] = array_merge([$gene_cor_array[$gene_annot]], $array);
      }
    }

    // TABLE
    echo "<table id=\"tblCorrelations\" class=\"table table-striped table-bordered\">\n";

    // TABLE HEAD
$columns = array(
  'Gene',
  '<span class="coex-tooltip" data-toggle="tooltip" title="Correlation coefficient (Pearson) between the query gene and other genes within the same WGCNA module. Only positive correlations ≥ 0.8 are shown">Correlation</span>'
);
    $columns = array_merge($columns, $head_columns);
    $col_number = count($columns);
    echo "<thead><tr>";
    foreach ($columns as $index=>$col) {
      echo "<th>$col</th>\n";
    }
    echo "</tr></thead>\n";

    // TABLE BODY
    echo "<tbody>\n";
    foreach ($gene_cor_array as $gene => $info) {
      echo "<tr>";
      $annot_encode = str_replace($annotations_path."/", "", $annot_file);
      echo "<td><a href=\"/easy_gdb/gene.php?name=$gene&annot=$annot_encode\" target=\"_blank\">$gene</a></td>\n";

      if (is_array($info)) {
        for ($n = 0; $n < ($col_number - 1); $n++) {
          if (isset($info[$n])) {
            if (is_numeric($info[$n]) && $info[$n] <= 1) {
              echo "<td><b>$info[$n]</b></td>";
            }
            else {
              $header_name = $columns[$n + 1];
              if ($header_name == "TAIR10" || $header_name == "Araport11") {
                $query_id = preg_replace(['/query_id/', '/\.\d+$/'], [$info[$n], ''], $annot_hash[$header_name]);
                echo "<td><a href=\"$query_id\" target=\"_blank\">$info[$n]</a></td>\n";
              }
              elseif (preg_match("/Phytozome/i", $header_name) && !preg_match("/Description/i", $header_name) ) {
                $query_id = preg_replace(['/query_id/', '/V\d+\.\d+/'], [$info[$n], ''], $annot_hash[$header_name]);
                echo "<td><a href=\"$query_id\" target=\"_blank\">$info[$n]</a></td>\n";
              }
              elseif ( strpos($info[$n], ';') && !preg_match("/Description/i", $header_name) ) {
                $ipr_data = explode(';', $info[$n]);
                $ipr_links = '';
                foreach ($ipr_data as $ipr_id) {
                  $query_id = str_replace('query_id', $ipr_id, $annot_hash[$header_name]);
                  $ipr_links .= "<a href=\"$query_id\" target=\"_blank\">$ipr_id</a><br>";
                }
                $ipr_links = rtrim($ipr_links, ';<br>');
                echo "<td>$ipr_links</td>\n";
              }
              elseif (strpos($info[$n], ';')) {
                $data_semicolon = str_replace(';', ';'."<br>", $info[$n]);
                $lines = explode("<br>", $data_semicolon);
                $show_tooltip = false;

                foreach ($lines as $line) {
                  if (strlen($line) >= 66) {
                    $show_tooltip = true;
                    break;
                  }
                }
                if ($show_tooltip) {
                  $title = implode("\t", $lines);
                  echo "<td class=\"td-tooltip\" title=\"$title\">$data_semicolon</td>\n";
                } else {
                  echo "<td>$data_semicolon</td>\n";
                }
              }
                          elseif ($annot_hash[$header_name]) {
                $query_id = str_replace('query_id', $info[$n], $annot_hash[$header_name]);
                echo "<td><a href=\"$query_id\" target=\"_blank\">$info[$n]</a></td>\n";
              }
              else {
                $desc_length = strlen($info[$n]);
                //echo $desc_length." ".$data[$n]."<br>";
                
                if ($desc_length >= 66) {
                  echo "<td class=\"td-tooltip\" title=\"$info[$n]\">$info[$n]</td>\n";
                } else {
                  echo "<td>$info[$n]</td>\n";
                }
              }
            }
          }
          else {
            echo "<td></td>";
          }
        }
      }
      else {
        echo "<td>$info</td>";
        for ($n = 1; $n < ($col_number - 1); $n++) {
          echo "<td></td>";
        }
      }
      echo "</tr>\n";
    }
    echo "</tbody></table>\n";
  }

  //NOT ANNOT FILE
  else {
    echo "<table id=\"tblCorrelations\" class=\"table table-striped table-bordered\">\n";
    $columns = array('Gene', 'Correlation');
    echo "<thead><tr>";
    foreach ($columns as $col) {
      echo "<th>$col</th>\n";
    }
    echo "</tr></thead>\n";

    echo "<tbody>\n";
    foreach ($gene_cor_array as $gene => $cor) {
      echo "<tr>";
      echo "<td>$gene</td>";
      if (is_array($cor)) {
        echo "<td>{$cor[0]}</td>";
      }
      else {
        echo "<td>$cor</td>";
      }
      echo "</tr>\n";
    }
    echo "</tbody></table>\n";
  }
}
?>

