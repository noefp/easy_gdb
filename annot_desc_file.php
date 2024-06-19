<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#annot_section" aria-expanded="true">
    <i class="fas fa-sort" style="color:#229dff"></i> Functional descriptions
</div>

<div id="annot_section" class="collapse show">

  <!-- COMMANDS -->
  <?php
    if (file_exists("$annotation_links_path/annotation_links.json")) {
      $annot_json_file = file_get_contents("$annotation_links_path/annotation_links.json");
      $annot_hash = json_decode($annot_json_file, true);
    }

    if (empty($gene_name)) {
      echo "<br>";
      echo "<h2>No words to search provided</h2>";
    }
    else {
      $annot_file = str_replace(" ", "\\ ", $annot_file);

      $head_command = "head -n 1 $annot_file";
      exec($head_command, $output_head);

      $grep_command = "grep -i '$search_input' $annot_file";
      exec($grep_command, $output);

      $sources = explode("\t", $output_head[0]);
      array_shift($sources);

      $rows = explode("\t", $output_head[0]);
      array_shift($rows);
      $row_number = count($rows);
    }
  ?>


  <!-- TABLE BEGIN -->
  <table class="table annot_table">

    <!-- TABLE HEADER -->
    <thead>
      <tr><th>ID</th><th>Description</th><th>Source</th></tr>
    </thead>

    <!-- TABLE BODY -->
    <tbody>

      <?php

        // INITIALIZE ARRAYS
        $db_link = "";
        $db_description = "";
        $db_source = "";
        $query_id = "";

        $data = explode("\t", $output[0]);
        array_shift($data);
        $row_count = count($data);

        // DB_TABLE
        for ($n = 0; $n < $row_count; $n += 2) {
            $header_name = $rows[$n];

            if ($header_name == "TAIR10" || $header_name == "Araport11") {
                $query_id = preg_replace(['/query_id/', '/\.\d$/'], [$data[$n], ''], $annot_hash[$header_name]);
                if ($data[$n]) {
                    $db_link = "<a href=\"$query_id\" target=\"_blank\">$data[$n]</a>";
                }

            } elseif (strpos($data[$n], ';')) {
                $ipr_data = explode(";", $data[$n]);
                $desc_data = explode(";", $data[$n + 1]);
                $ipr_count = count($ipr_data);

                for ($i = 0; $i < $ipr_count; $i++) {
                    $ipr_id = $ipr_data[$i];
                    $desc_ipr = $desc_data[$i];

                    $query_id = str_replace('query_id', $ipr_id, $annot_hash[$header_name]);
                    $db_link = "<a href=\"$query_id\" target=\"_blank\">$ipr_id</a>";
                    $db_description = $desc_ipr;
                    $db_source = $sources[$n];

                    if ($db_link) {
                        echo "<tr><td>$db_link</td><td>$db_description</td><td>$db_source</td></tr>\n";
                    }

                    $db_link = "";
                    $db_description = "";
                    $db_source = "";
                }

            } elseif (isset($annot_hash[$header_name])) {
                $query_id = str_replace('query_id', $data[$n], $annot_hash[$header_name]);
                if ($data[$n]) {
                    $db_link = "<a href=\"$query_id\" target=\"_blank\">$data[$n]</a>";
                }

            } else {
                if ($data[$n]) {
                    $db_link = $data[$n];
                }
            }

            if ($data[$n + 1]) {
                $db_description = $data[$n + 1];
            }

            $db_source = $sources[$n];

            if ($db_link) {
                echo "<tr><td>$db_link</td><td>$db_description</td><td>$db_source</td></tr>\n";
            }

            $db_link = "";
            $db_description = "";
            $db_source = "";
        }
      ?>


    </tbody>
  </table>
  <!-- TABLE END -->

</div>