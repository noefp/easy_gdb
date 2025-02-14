<!-- HEADER -->
<?php include_once realpath("../header.php");?>
<?php include_once realpath("$root_path/easy_gdb/tools/common_functions.php");?>

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/05_annotation_extraction.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a onClick="history.back()" class="float-left pointer_cursor" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>

<!-- HTML -->
<div class="page_container">


<!-- POST INPUT -->
<?php
  $gNamesArr=array_filter(explode("\n",trim($_POST["txtGenes"])),function($gName) {return ! empty($gName);});
?>



<!-- Declare function to print table -->
<?php
  function print_annot_table($desc_input, $annot_file, $annot_hash, $dataset_name, $table_counter, $annotations_path) {
    $annot_file = str_replace(" ", "\\ ", $annot_file);

    $head_command = "head -n 1 $annot_file";
    $output_head = exec($head_command);


    $grep_input = implode("\|",$desc_input);
    // $grep_input = implode($desc_input,"\|");
    $grep_command = "grep -i '$grep_input' $annot_file";
    exec($grep_command, $output);

  if($output)
  {
    $count_character = [];

    echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#Annot_table_$table_counter\" aria-expanded=\"true\"><i class=\"fas fa-sort\" style=\"color:#229dff\"></i> $dataset_name</div>";

    // TABLE BEGIN
    echo "<div id=\"Annot_table_$table_counter\" class=\"collapse show\"><div class=\"data_table_frame\"><table id=\"tblAnnotations\" class=\"tblAnnotations table table-striped table-bordered\">\n";

    echo "<div id=\"load\" class=\"loader\"></div>";

    // TABLE HEADER
    echo "<thead><tr>\n";
    $columns = explode("\t", $output_head);
    $col_number = count($columns);

    foreach ($columns as $col) {
      echo "<th>$col</th>\n";
    }
    echo "</tr></thead>\n";


    // TABLE BODY
    echo "<tbody>\n";

    foreach ($output as $line) {
      echo "<tr>\n";
      $data = explode("\t", $line);

      for ($n = 0; $n <= $col_number-1; $n++) {
        if ($data[$n]) {

          $data_count[$n] = strlen($data[$n]);
          if ($data_count[$n] > 100 ) {
            array_push($count_character, $n);
          }

          if ($n == 0) {
            $annot_encode = str_replace($annotations_path."/", "", $annot_file);
            echo "<td><a href=\"/easy_gdb/gene.php?name=$data[$n]&annot=$annot_encode\" target=\"_blank\">$data[$n]</a></td>\n";
          }
          else {
            $header_name = $columns[$n];
            if ($header_name == "TAIR10" || $header_name == "Araport11") {
              $query_id = preg_replace(['/query_id/', '/\.\d+$/'], [$data[$n], ''], $annot_hash[$header_name]);
              echo "<td><a href=\"$query_id\" target=\"_blank\">$data[$n]</a></td>\n";
            }
            elseif (preg_match("/Phytozome/i", $header_name) && !preg_match("/Description/i", $header_name) ) {
              $query_id = preg_replace(['/query_id/', '/V\d+\.\d+/'], [$data[$n], ''], $annot_hash[$header_name]);
              echo "<td><a href=\"$query_id\" target=\"_blank\">$data[$n]</a></td>\n";
            }
            elseif (strpos($data[$n], ';') && !preg_match("/Description/i", $header_name) ) {
              $link_data = explode(';', $data[$n]);
              $link_links = '';
              foreach ($link_data as $link_id) {
                $query_id = str_replace('query_id', $link_id, $annot_hash[$header_name]);
                $link_links .= "<a href=\"$query_id\" target=\"_blank\">$link_id</a>;<br>";
              }
              $link_links = rtrim($link_links, ';<br>');
              echo "<td>$link_links</td>\n";
            }
            elseif (strpos($data[$n], ';')) {
              $data_semicolon = str_replace(';', ';'."<br>", $data[$n]);
              echo "<td>$data_semicolon</td>\n";
            }
            elseif ($annot_hash[$header_name]) {
              $query_id = str_replace('query_id', $data[$n], $annot_hash[$header_name]);
              echo "<td><a href=\"$query_id\" target=\"_blank\">$data[$n]</a></td>\n";
            }
            else {
              echo "<td>$data[$n]</td>\n";
            }
          }
        }
        else {
          echo "<td></td>\n";
        }
      }
      echo "</tr>\n";
    }
    echo "</tbody></table></div></div><br>\n";

    $output = [];
    return($count_character);
  }
  else{
    echo '<br><div class="alert alert-danger" role="alert" style="text-align:center">
    No gene was found in the selected dataset
    </div>';
  }
} // TABLE END
?>



<!-- INCLUDE TABLE  -->
<?php
  if(empty($gNamesArr)) {
    echo "<br>";
    echo "<h2>No genes to search provided</h2>";
  }
  else {
    $search_query = [];

    foreach ($gNamesArr as $gene_name) {
      $one_gene = test_input2($gene_name);
      array_push($search_query, $one_gene);
    }

    echo '<br><div class="alert alert-dismissible show" style="background-color:#f0f0f0">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
      <span aria-hidden="true">&times;</span>
    </button>';
    echo "<h3 style=\"display:inline\"><i>Search Input</i></h3>";
    echo "<div class=\"card-body\" style=\"padding-top:10px;padding-bottom:0px\">".implode("\t",$search_query)."</div></div>";

    // create HASH with ANNOTATION links
    if (file_exists("$json_files_path/tools/annotation_links.json")) {
      $annot_json_file = file_get_contents("$json_files_path/tools/annotation_links.json");
      $annotation_hash = json_decode($annot_json_file, true);
    }

    // COMMANDS AND PRINT
    $table_counter = 1;

    if ($_POST['sample_names']) {
      foreach ($_POST['sample_names'] as $sample) {
        list($annot_file,$dataset_name) = explode("@", $sample);
        $count_character = print_annot_table($search_query, $annot_file, $annotation_hash, $dataset_name, $table_counter, $annotations_path);
        $table_counter++;
      }
    } else {
      include_once realpath("$easy_gdb_path/tools/common_functions.php");
      $all_datasets = get_dir_and_files($annotations_path);
      $annot_file = $annotations_path."/".$all_datasets[0];
      $dataset_name = $all_datasets[0];
      $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$all_datasets[0]);
      $dataset_name = str_replace("_"," ",$dataset_name);
      $count_character = print_annot_table($search_query, $annot_file, $annotation_hash, $dataset_name, $table_counter, $annotations_path);
    }
  }
?>
<!-- END TABLE -->


<br>
<br>
</div>
<!-- END HTML -->

<!-- JS DATATABLE -->
<script src="../js/datatable.js"></script>
<script type="text/javascript">

  $('#load').remove();
  $('.tblAnnotations').css("display","table");
  datatable(".tblAnnotations","");

</script>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>