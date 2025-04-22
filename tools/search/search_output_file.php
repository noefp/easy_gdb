<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include_once realpath("$root_path/easy_gdb/tools/common_functions.php");?>

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<!-- <a href="search_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a> -->
<a class="float-left pointer_cursor " style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<br>

<!-- HTML -->
<div class="page_container">


<!-- GET INPUT -->
<?php
  $raw_input = trim($_GET["search_keywords"]);
  $quoted_search = 0;
  if ( preg_match('/^".+"$/',$raw_input ) ) {
    $quoted_search = 1;
  }
?>
  
  
  
<?php

  function print_search_table($grep_input, $annot_file, $annot_hash, $dataset_name, $table_counter, $annotations_path) {

    $annot_file = str_replace(" ", "\\ ", $annot_file);

    $head_command = "head -n 1 $annot_file";
    $output_head = exec($head_command);

    $grep_command = "grep -i '$grep_input' $annot_file";
    exec($grep_command, $output);

    echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#Annot_table_$table_counter\" aria-expanded=\"true\"><i class=\"fas fa-sort\" style=\"color:#229dff\"></i> $dataset_name</div>";

  if($output)
  {
    
    // TABLE BEGIN
    echo "<div id=\"Annot_table_$table_counter\" class=\"collapse hide\"><table id=\"tblAnnotations_$table_counter\" class=\"tblAnnotations table table-striped table-bordered\" style=\"display:none\">\n";
    
    echo "<div id=\"load_$table_counter\" class=\"loader\"></div>";

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
      for ($n = 0; $n < $col_number; $n++) {
        if ($data[$n]) {
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
            elseif ( strpos($data[$n], ';') && !preg_match("/Description/i", $header_name) ) {
              $ipr_data = explode(';', $data[$n]);
              $ipr_links = '';
              foreach ($ipr_data as $ipr_id) {
                $query_id = str_replace('query_id', $ipr_id, $annot_hash[$header_name]);
                $ipr_links .= "<a href=\"$query_id\" target=\"_blank\">$ipr_id</a><br>";
              }
              $ipr_links = rtrim($ipr_links, ';<br>');
              echo "<td>$ipr_links</td>\n";
            }
            elseif (strpos($data[$n], ';')) {
              $data_semicolon = str_replace(';', ';'."<br>", $data[$n]);
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
              $query_id = str_replace('query_id', $data[$n], $annot_hash[$header_name]);
              echo "<td><a href=\"$query_id\" target=\"_blank\">$data[$n]</a></td>\n";
            }
            else {
              $desc_length = strlen($data[$n]);
              //echo $desc_length." ".$data[$n]."<br>";
              
              if ($desc_length >= 66) {
                echo "<td class=\"td-tooltip\" title=\"$data[$n]\">$data[$n]</td>\n";
              } else {
                echo "<td>$data[$n]</td>\n";
              }
            }
          }
        }
        else {
          echo "<td></td>\n";
        }
      }
      echo "</tr>\n";
    }
    echo "</tbody></table><br><br></div>";
    $output = [];

  } // end if output
  else
  {
    echo '<div class="alert alert-danger" role="alert" style="text-align:center">
    No keyword was found in the selected dataset
    </div><br>';
  }
} // TABLE END
  
?>


<!-- SHOW INPUT -->
<?php
  $search_input = test_input2($raw_input);

  echo '<br>
  <div class="alert alert-primary" role="alert" style="display:block;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h3 style="display:inline">Search input</h3>
    <div class="card-body" style="padding-top:10px; padding-bottom:0;">' . $search_input . '</div>
  </div>';
?>


<!-- INCLUDE TABLE  -->
<?php
  if(empty($raw_input)) {
    echo "<br>";
    echo "<h2>No words to search provided</h2>";
  }

  else { 

    // HASH ANNOTATION
    if (file_exists("$json_files_path/tools/annotation_links.json")) {
      $annot_json_file = file_get_contents("$json_files_path/tools/annotation_links.json");
      $annotation_hash = json_decode($annot_json_file, true);
    }

    // QUOTED INPUTS
    if ($quoted_search) {
      $search_query = preg_replace('/[\"\<\>\t\;]+/','',strtolower($raw_input) );
    }
    elseif (preg_match('/\s+/', $search_input)) {
      $search_query = preg_replace('/\s+/','\|',strtolower($search_input) );
    }
    else {
      $search_query = strtolower($search_input);
    }


    // COMMANDS AND PRINT
    $table_counter = 1;

    if ($_GET['sample_names']) {
      foreach ($_GET['sample_names'] as $sample) {
        list($annot_file,$dataset_name) = explode("@", $sample);
        print_search_table($search_query, $annot_file, $annotation_hash, $dataset_name, $table_counter, $annotations_path);
        $table_counter++;
      }
    }

    if ($_GET['search_all']) {
      $all_items = get_dir_and_files($annotations_path);
      asort($all_items);
      // Get files
      foreach ($all_items as $item) {
        $full_path = "$annotations_path/$item";

        if (is_file($full_path) && !preg_match('/\.php$/i', $item) && !preg_match('/\.json$/i', $item)) {
          $dataset_name = preg_replace('/\.[a-z]{3}$/', "", $item);
          $dataset_name = str_replace("_", " ", $dataset_name);
          print_search_table($search_query, $full_path, $annotation_hash, $dataset_name, $table_counter, $annotations_path);
          $table_counter++;
        }
      }
      // Get files in subdirectories
      foreach ($all_items as $dir_or_file) {
        $subdir_path = $annotations_path . "/" . $dir_or_file;
    
        if (is_dir($subdir_path)) {
          $all_files_in_dir = get_dir_and_files($subdir_path);
          sort($all_files_in_dir);
    
          foreach ($all_files_in_dir as $dataset_file) {
            $full_path = "$subdir_path/$dataset_file";
    
            if (is_file($full_path) && !preg_match('/\.php$/i', $dataset_file) && !preg_match('/\.json$/i', $dataset_file)) {
              $dataset_name = preg_replace('/\.[a-z]{3}$/', "", $dataset_file);
              $dataset_name = str_replace("_", " ", $dataset_name);

              print_search_table($search_query, $full_path, $annotation_hash, $dataset_name, $table_counter, $annotations_path);
              $table_counter++;
            }
          }
        }
      }
    }
  }
?>
<!-- END TABLE  -->

<br>
<br>
</div>
<!-- END HTML -->


<!-- CSS DATATABLE -->
<style>
table.dataTable td {
  white-space: nowrap;  
  overflow: hidden; 
  text-overflow: ellipsis; 
}

.td-tooltip {
  cursor: pointer;
}
</style>


<!-- JS DATATABLE -->
<script src="../../js/datatable.js"></script>

<script type="text/javascript">
var sampleNamesSelected = <?php echo isset($_GET['sample_names']) ? 'true' : 'false'; ?>;
$(document).ready(function(){
  if (sampleNamesSelected) {
    $('#Annot_table_1').addClass('show');
    $('#load_1').remove();
    $('#tblAnnotations_1').css("display","table");
    datatable("#tblAnnotations_1",'1');
    $(".td-tooltip").tooltip();
  }

  $(".collapse").on('shown.bs.collapse', function(){
    var id=$(this).attr("id");
    id=id.replace("Annot_table_","");

    $('#load_'+id).remove();
    $('#tblAnnotations_'+id).css("display","table");
    datatable("#tblAnnotations_"+id,id);
    $(".td-tooltip").tooltip();
  });
});
</script>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>