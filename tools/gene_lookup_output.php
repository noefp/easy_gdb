<!-- HEADER -->
<?php 
  include realpath('../header.php');
  include_once realpath("$root_path/easy_gdb/tools/common_functions.php");
?>

<!-- RETURN AND HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/06_gene_lookup.php" target="_blank">
    <i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help
  </a>
</div>

<a class="float-left pointer_cursor" style="text-decoration: underline;" onClick="history.back()">
  <i class="fas fa-reply" style="color:#229dff"></i> Back to input
</a>
<br>

<!-- HTML -->
<h1 class="text-center margin-20">Gene Version Lookup <i class="fas fa-search" style="color:#555;"></i></h1>
<div class="page_container">

<?php
function get_lookup_lines($desc_input, $lookup_file) {
  $lookup_file = str_replace(" ", "\\ ", $lookup_file);

  usort($desc_input, function($a, $b) {
    return strlen($b) - strlen($a);
  });

  $temp_pattern_file = tempnam(sys_get_temp_dir(), 'pattern_');
  file_put_contents($temp_pattern_file, implode("\n", $desc_input));

  $grep_command = "grep -i -f" . escapeshellarg($temp_pattern_file) . " " . escapeshellarg($lookup_file);
  exec($grep_command, $output);
  unlink($temp_pattern_file);

  return $output;
}

function highlightLine($line, $desc_input) {
  $max_highlight = 10000;
  if (count($desc_input) > $max_highlight) {
    return $line;
  }

  $chunkSize = 50;
  $chunks = array_chunk($desc_input, $chunkSize);

  foreach ($chunks as $chunk) {
    $pattern = '/' . implode('|', array_map(function($gene) {
        return preg_quote($gene, '/');
    }, $chunk)) . '/i';

    $line = preg_replace_callback(
      $pattern,
      function($matches) {
        return "<mark>{$matches[0]}</mark>";
      },
      $line
    );
  }
  return $line;
}


function print_lookup_table($desc_input, $output, $lookup_file) {
  if (empty($output)) {
    echo '<div class="alert alert-danger" role="alert" style="text-align:center">
            No gene was found in the selected dataset
          </div><br>';
    return;
  }

  $head_command = "head -n 1 $lookup_file";
  $output_head = trim(shell_exec($head_command));
  $columns = explode("\t", $output_head);
  $col_number = count($columns);

  echo "<div id=\"Lookup_table\" class=\"collapse show\">";
  echo "<table id=\"tblLookup\" class=\"tblLookup table table-striped table-bordered\">\n";
  echo "<div id=\"load\" class=\"loader\"></div>";

  // TABLE HEADER
  echo "<thead><tr>\n";
  foreach ($columns as $col) {
    echo "<th>$col</th>";
  }
  echo "</tr></thead>\n";

  // TABLE BODY
  echo "<tbody>\n";
  foreach ($output as $line) {
    $line = highlightLine($line, $desc_input);

    $data = explode("\t", $line);

    echo "<tr>\n";
    for ($n = 0; $n < $col_number; $n++) {
      if (!isset($data[$n])) {
        echo "<td></td>\n";
        continue;
      }

      if (strpos($data[$n], ';') !== false) {
        $data_semicolon = str_replace(';', "<br>", $data[$n]);
        $lines_semicolon = explode("<br>", $data_semicolon);
        $show_tooltip = false;
        foreach ($lines_semicolon as $lineItem) {
          if (strlen($lineItem) >= 68) {
            $show_tooltip = true;
            break;
          }
        }
        if ($show_tooltip) {
          $title = implode("\t", $lines_semicolon);
          echo "<td class=\"td-tooltip\" title=\"$title\">$data_semicolon</td>\n";
        } else {
          echo "<td>$data_semicolon</td>\n";
        }

      } else {
        if (strlen($data[$n]) >= 68) {
          echo "<td class=\"td-tooltip\" title=\"{$data[$n]}\">{$data[$n]}</td>\n";
        } else {
          echo "<td>{$data[$n]}</td>\n";
        }
      }
    }
    echo "</tr>\n";
  }
  echo "</tbody></table></div><br><br>\n";
}
?>

<?php
// POST INPUT
$gNamesArr = array_filter(
  preg_split('/[\s;,:]+/', trim($_POST["txtGenes"])),
  function($gName) { return $gName !== ''; }
);

if(empty($gNamesArr)) {
  echo '<div class="alert alert-danger" role="alert" style="text-align:center">
          No genes to search provided
        </div><br>';
} else {
  $search_query = [];
  foreach ($gNamesArr as $gene_name) {
    $search_query[] = test_input2($gene_name);
  }

  $annot_file = $_POST["lookup_db"];

  $lines_found = get_lookup_lines($search_query, $annot_file);

  if (!empty($search_query)) {
    $notFound = [];
    foreach ($search_query as $gene) {
      $found = false;
      foreach ($lines_found as $line) {
        if (stripos($line, $gene) !== false) {
          $found = true;
          break;
        }
      }
      if (!$found) {
        $notFound[] = $gene;
      }
    }
    if (!empty($notFound)) {
      echo '<div class="alert alert-warning" role="alert" style="display:block;">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <h3 style="display:inline">Genes not found</h3>
              <div class="card-body" style="padding-top:0px;padding-bottom:0px">'
                . implode("<br>", $notFound) .
              '</div>
            </div>';
    }
  }

  print_lookup_table($search_query, $lines_found, $annot_file);
}
?>

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
  mark {
    background-color: #c0ffc8;
    padding: 0.2em 0;
  }
</style>

<!-- JS DATATABLE -->
<script src="../js/datatable.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#tblLookup').addClass('show');
    $('#load').remove();
    $('#tblLookup').css("display","table");
    datatable("#tblLookup",'1');

    $(".collapse").on('shown.bs.collapse', function(){
      var id = $(this).attr("id");
      id = id.replace("Lookup_table","");
      $('#load').remove();
      $('#tblLookup').css("display","table");
      datatable("#tblLookup", id);
    });

    $(".td-tooltip").tooltip();
  });
</script>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>