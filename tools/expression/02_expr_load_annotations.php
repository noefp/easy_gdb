<?php
  if ($file_database) {
    // $annot_json_file = file_get_contents("$expression_path/expression_info.json");
    //  $annot_hash = json_decode($annot_json_file, true);
    if ($annot_hash) {
      $annot_file = $annotations_path.'/'.$annot_hash[$dataset_name_ori]["annotation_file"];

      $annotations_hash_file = [];
      $grep_input = implode("\|", $gids);
      $grep_command = "grep -i '$grep_input' $annot_file";
      exec($grep_command, $output);

      $annot_file = str_replace(" ", "\\ ", $annot_file);

      $head_command = "head -n 1 $annot_file";
      $output_head = exec($head_command);
      $columns = explode("\t", $output_head);
      array_shift($columns);
      $col_number = count($columns);
  
      $annot_json_file = file_get_contents("$json_files_path/tools/annotation_links.json");
      $annotation_hash = json_decode($annot_json_file, true);

      foreach ($output as $annot_line) {
        $annot_col = explode("\t", $annot_line);
        $gene_key = $annot_col[0];
        array_shift($annot_col);

        for ($n = 0; $n < $col_number; $n++) {
          $header_name = $columns[$n];

          # print id with link to TAIR modifying id to make it work
          if ($header_name == "TAIR10" || $header_name == "Araport11") {
            $query_id = preg_replace(['/query_id/', '/\.\d$/'], [$annot_col[$n], ''], $annotation_hash[$header_name ]);
            $annot_col[$n] = "<td><a href=\"$query_id\" target=\"_blank\">$annot_col[$n]</a></td>";
          }
        
          # split ids and descriptions containing several values separated by semicolon
          elseif ( strpos($annot_col[$n], ';') && !preg_match("/Description/", $header_name) ) {
            $ipr_data = explode(';', $annot_col[$n]);
            $ipr_links = '';
            foreach ($ipr_data as $ipr_id) {
              $query_id = str_replace('query_id', $ipr_id, $annotation_hash[$header_name]);
              $ipr_links .= "<a href=\"$query_id\" target=\"_blank\">$ipr_id</a><br>";
            }
            $ipr_links = rtrim($ipr_links, ';<br>');
            //echo "<td>$ipr_links</td>\n";
            $annot_col[$n] = "<td>".$ipr_links."</td>";
          }
          elseif (strpos($annot_col[$n], ';')) {
            $data_semicolon = str_replace(';', ';'."<br>", $annot_col[$n]);
            $lines = explode("<br>", $data_semicolon);
            $show_tooltip = false;

            foreach ($lines as $line) {
              if (strlen($line) >= 68) {
                $show_tooltip = true;
                break;
              }
            }
            if ($show_tooltip) {
              $title = implode("\t", $lines);
              $annot_col[$n] = "<td class=\"td-tooltip\" title=\"$title\">$data_semicolon</td>\n";
            } else {
              $annot_col[$n] = "<td>$data_semicolon</td>\n";
            }
          }
          # print id with link to database
          elseif ($annotation_hash[$header_name]) {
            $query_id = str_replace('query_id', $annot_col[$n], $annotation_hash[$header_name]);
            $annot_col[$n] = "<td><a href=\"$query_id\" target=\"_blank\">$annot_col[$n]</a></td>";
          }
        
          # add tooltip to long descriptions
          else {
            $desc_length = strlen($annot_col[$n]);
            //echo $desc_length." ".$data[$n]."<br>";
          
            if ($desc_length >= 68) {
              $annot_col[$n] = "<td class=\"td-tooltip\" title=\"$annot_col[$n]\">$annot_col[$n]</td>";
            } else {
              $annot_col[$n] = "<td>".$annot_col[$n]."</td>";
            }
          }
        
        }//close for

        $annot_string = implode("\n", $annot_col);
        $annotations_hash_file[strtoupper($gene_key)] = $annot_string;
      }//close foreach
    }//if annot_hash
    $annotations_hash_file_empty=implode("\n",array_fill(0,$col_number,"<td></td>"));

  }//if file db
?>
