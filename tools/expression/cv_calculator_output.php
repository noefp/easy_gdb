<?php include realpath('../../header.php'); ?>
<?php include_once realpath("../modal.html");?>

<!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
<script src="/easy_gdb/js/apexcharts.min.js"></script>

    <!-- <link rel="stylesheet" href="/easy_gdb/js/DataTables/Select-1.2.6/css/select.dataTables.min.css"> -->


<div class="margin-20">
  <!-- <a class="float-right" href="/easy_gdb/help/09_expression_comparator.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a> -->
</div>
<!-- <a href="/easy_gdb/tools/expression/comparator_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a> -->
<a class="float-left pointer_cursor " style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<br>
<h1 class="text-center">Coefficient of Variation Results</h1>


<?php

// get each sample and their dataset and save it in a hash key=dataset-file value=array of experiments from that dataset
  $sample_hash = [];
  $found_categories = [];
  $newer_found = 0;

  foreach ($_POST['sample_names'] as $sample) {
    list($file,$exp) = explode("@", $sample);

    if ($_POST['categories']) {
      //echo "Categories $file <br>";
      $path_array = explode("/", rtrim($file));
      $category = $path_array[count($path_array)-2];
      $found_categories[$category]=1;
    }

    if ($sample_hash[$file]) {
      array_push($sample_hash[$file],$exp);
    } else {
      $sample_hash[$file] = [];
      array_push($sample_hash[$file],$exp);
    }
  } 

?>

<div class="page_container" style="margin-top:20px">

<?php

$columns = [];
$replicates = [];
$header = [];

ini_set( 'memory_limit', '2048M' );

// iterate each dataset selected in the comparator input
foreach($sample_hash as $expr_file => $comparator_samples_array) {

// check dataset file exists and open it. Get header line and save sample names in header
  if ( file_exists("$expr_file") ) {
    

    $tab_file = file("$expr_file");

    $first_line = array_shift($tab_file);

    $header = explode("\t", rtrim($first_line));

    $header_index = array_intersect($header, $comparator_samples_array); //array_intersect

//gets each replicate value for each gene
    foreach ($tab_file as $line) {
    $columns = explode("\t", trim($line));
    $gene_name = $columns[0];

      foreach ($header_index as $index => $sample_name) {
          if (!isset($replicates[$gene_name][$sample_name])) {
              $replicates[$gene_name][$sample_name] = [];
          }
          $replicates[$gene_name][$sample_name][] = $columns[$index];
        }
    }
  } // if file exists
  else {
    echo "<p style=\"color:red\"><b>ERROR!</b> The dataset file $expr_file does not exist. Please, contact the server administrator.</p>";
  }

} // end foreach sample_hash

$replicates_means = [];

foreach ($replicates as $gene_name => $samples) {
    foreach ($samples as $sample_name => $values) {
        if (count($values) > 0) {
            $mean = array_sum($values) / count($values);
        } else {
            $mean = null; // if no values, mean is null
        }
        $replicates_means[$gene_name][$sample_name] = $mean;
    }
}

// calculate CV for each gene across all samples
$gene_cv = [];
foreach ($replicates_means as $gene_name => $samples_means) {
    $all_values = [];

    foreach ($samples_means as $values) {
        array_push($all_values, $values);
    }

    $n = count($all_values);
    if ($n > 1) {
        $avg_all_samples = array_sum($all_values) / $n;
        $sum_sq_diff = 0;
        foreach ($all_values as $v) {
            $sum_sq_diff += pow($v - $avg_all_samples, 2);
        }
        $std_dev = sqrt($sum_sq_diff / ($n - 1)); // sample standard deviation
        //$std_dev = sqrt($sum_sq_diff / ($n)); // population standard deviation
        $cv = ($avg_all_samples != 0) ? ($std_dev / $avg_all_samples)*100 : null; //if mean is 0, cv is not defined because it would involve division by zero  
    } else {
        $cv = null;
    }

    $gene_cv[$gene_name] = $cv;
}

//filter out null values
$filtered_gene_cv = array_filter($gene_cv, function($cv) {
    return $cv !== null;
});

// sort the filtered array by CV values in ascending order
asort($filtered_gene_cv);


// get top 10 genes with lowest CV
$top_genes = array_slice($filtered_gene_cv, 0, 10, true);

// get all sample names for table header
$sample_names = [];
foreach ($replicates_means as $gene => $samples) {
    foreach ($samples as $sample_name => $mean_value) {
        $sample_names[$sample_name] = true;
    }
}
$sample_names = array_keys($sample_names);


// create mean table and its header
echo "<table id=\"cv_table\" class=\"table table-striped table-bordered\">";
echo "<thead><tr><th>Gene ID</th><th>Coef. Variation (%)</th>";
foreach ($sample_names as $sample_name) {
    echo "<th>$sample_name</th>";
}
echo "</tr></thead><tbody>";

foreach ($top_genes as $gene_name => $cv) {
    echo "<tr>";
    echo "<td>$gene_name</td>";
    echo "<td><b>" . sprintf("%1\$.2f", $cv) . "</b></td>";

    foreach ($sample_names as $sample_name) {
        $mean_data = isset($replicates_means[$gene_name][$sample_name]) ? sprintf("%1\$.2f",$replicates_means[$gene_name][$sample_name]) : "-";
        echo "<td>$mean_data</td>";
    }
    echo "</tr>";
}
echo "</tbody></table>"; 
?>
<br><br>

<?php
include realpath('../../footer.php');
?>

 <style>
 table.dataTable td,th  {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; 
  }
  
    /* .td-tooltip {
      cursor: pointer;
    } */
  </style>


<script src="../../js/datatable.js"></script>
<script type="text/javascript">
$(document).ready(function(){

      // $('#table_container').css("display","show");
      // datatable("#tblResults","");
      datatable_basic("#cv_table");
      // $(".td-tooltip").tooltip();
});   
</script>

