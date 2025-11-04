<?php
 include realpath('../../header.php'); ?>
<?php include_once realpath("../modal.html");?>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

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
if (isset($_POST['cvMode']))
{
  $cvMean = $_POST['cvMode'];
}else {
  $cvMean= 0;
}

$minExpr = 0;
if (isset($_POST['minExpr']) && is_numeric($_POST['minExpr']) ) {
  $minExpr = floatval($_POST['minExpr']);
}

if($cvMean)
  { echo "<p class=\"text-center\"><b>Variation between sample means</b></p>";
    echo "<p class=\"text-center\">(Minimum mean expression threshold: <b>$minExpr</b>)</p>";}
else{
  echo "<p class=\"text-center\"><b>Variation between all replicates</b></p>";
}


// get each sample and their dataset and save it in a hash key=dataset-file value=array of experiments from that dataset
  $sample_hash = [];
  $found_categories = [];
  $newer_found = 0;
  $multicategory_found = false;

  foreach ($_POST['sample_names'] as $sample) {
    list($file,$exp) = explode("@", $sample); // file is the dataset file, exp is the sample name

    if ($_POST['categories']) {
      // echo "Categories $file <br>";
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

  // check if more than one category was selected
  $category_number = count(array_keys($found_categories));

  if ($category_number > 1) {
    $multicategory_found = true;
    echo "<p id=\"category_warning\" style=\"animation: l1 1s linear 4 alternate; text-align:center; color:red\"><b>WARNING!</b> Samples from multiple categories were selected (".join(", ", array_keys($found_categories)) ."). Ensure that this selection is consistent with your analysis</p>";
  }else
    {

      //------------if only one category, load json info -------------- 
      $annot_hash=[];
      $link_found = false;
      $annot_json_file_found = false;

      //load expression_info.json
        if ( file_exists("$json_files_path/tools/expression_info.json") ) {
          $annot_json_file = file_get_contents("$json_files_path/tools/expression_info.json");
          $annot_hash = json_decode($annot_json_file, true);
        }

 // check if any link with query_id exists in the selected datasets
      foreach (array_keys($sample_hash) as $dataset_path) {

        if (isset($annot_hash[explode("/",$dataset_path)[count(explode("/",$dataset_path))-1]]['link'])) {

          $gene_link= $annot_hash[explode("/",$dataset_path)[count(explode("/",$dataset_path))-1]]['link'];

          if (preg_match('/g=query_id/', $gene_link)) {
              $link_found = true;
              break;
           }
        }
      }

  // if no link found , check if any annotation file exists in the selected datasets
    if (!$link_found) {
      foreach (array_keys($sample_hash) as $dataset_path) {

        if (isset($annot_hash[explode("/",$dataset_path)[count(explode("/",$dataset_path))-1]]['annotation_file'])) {

          $gene_link= $annot_hash[explode("/",$dataset_path)[count(explode("/",$dataset_path))-1]]['link'];
          $annot_link= $annot_hash[explode("/",$dataset_path)[count(explode("/",$dataset_path))-1]]['annotation_file'];

            if($gene_link != "#" && preg_match('/.txt/', $annot_link)) { // check annotation file exists and link is not #
              $annot_json_file_found = true;
              break;
            }
          }
        }
      }
  }
  
?>

<div class="page_container" style="margin-top:20px">

<?php

$columns = [];
$replicates = [];
$header = [];

ini_set( 'memory_limit', '2048M' ); // increase memory limit

// iterate each dataset selected in the comparator input
foreach($sample_hash as $expr_file => $comparator_samples_array) {

// check dataset file exists and open it. Get header line and save sample names in header
  if ( file_exists("$expr_file") ) {
    
    $tab_file = file("$expr_file");
    $first_line = array_shift($tab_file); // get and remove first line from array
    $header = explode("\t", rtrim($first_line)); 
    $header_index = array_intersect($header, $comparator_samples_array); //array_intersect returns the intersection of two arrays and returns the values that are common to both arrays

//gets each replicate value for each gene
    foreach ($tab_file as $line) {
    $columns = explode("\t", trim($line)); // trim to remove any extra spaces or new lines
    $gene_name = $columns[0]; // first column is gene name

        // iterate each sample in the header_index array (which contains only the samples selected in the input form)

      foreach ($header_index as $index => $sample_name) {
          if (!isset($replicates[$gene_name][$sample_name])) { // if gene and sample not set, initialize as empty dictionary
              $replicates[$gene_name][$sample_name] = [];
          }
          $replicates[$gene_name][$sample_name][] = $columns[$index]; // add replicate value
        }
    }
  } // if file exists
  else {
    echo "<p style=\"color:red\"><b>ERROR!</b> The dataset file $expr_file does not exist. Please, contact the server administrator.</p>";
  }

} // end foreach sample_hash

$replicates_means = [];

// calculate mean for each replicate
if ($cvMean) { // if mean mode, calculate mean for each sample
  foreach ($replicates as $gene_name => $samples) {
      foreach ($samples as $sample_name => $values) {
          if (count($values) > 0) {
              $mean = array_sum($values) / count($values);
          } else {
              $mean = null; // if no values, mean is null
          }

          if ($mean >= $minExpr && $mean !== null) { // only include means that are equal or above the minimum expression threshold and not null
              $replicates_means[$gene_name][$sample_name] = $mean;
          }
          else{
              $replicates_means[$gene_name][$sample_name] = null; // if mean is below threshold or null, set it to null
          }
      }
  }
} else {
  // $replicates_means = $replicates; // if not mean mode, use replicates values directly
  foreach ($replicates as $gene_name => $samples) {
      foreach ($samples as $sample_name => $values) {
        if(max($values) >= 1) { // only include replicates if at least one of them is equal or above the 1
          $replicates_means[$gene_name][$sample_name] = $values; // if not mean mode, use replicates values directly
        }else{
          $replicates_means[$gene_name][$sample_name] = [null]; // if  are below 1, set to null
        }
      }
  }
}

// print_r($replicates['Vamp7']); // debug line

// calculate CV for each gene across all samples
$gene_cv = [];
foreach ($replicates_means as $gene_name => $samples_means) {
    $all_values = [];

  if (!$cvMean) { // if not mean mode, merge all replicates values into a single array
        foreach ($samples_means as $values) {
        $all_values = array_merge($all_values, $values);
      }  
  }else {  // if mean mode, push each sample value array into the all_values array
    foreach ($samples_means as $values) {
        array_push($all_values, $values);
    }
  }

    $n = count($all_values);

    if ($n > 1 && !in_array(null, $all_values)) { // need at least 2 values to calculate std dev and cv, and no null values into the array
      $avg_all_samples = array_sum($all_values) / $n;
      $sum_sq_diff = 0;
      foreach ($all_values as $v) {
        $sum_sq_diff += pow($v - $avg_all_samples, 2);
      }
      $std_dev = sqrt($sum_sq_diff / ($n - 1)); // sample standard deviation
      //$std_dev = sqrt($sum_sq_diff / ($n)); // population standard deviation
      $cv = ($avg_all_samples != 0) ? ($std_dev / $avg_all_samples)*100 : null; //if mean is 0, cv is not defined because it would involve division by zero  
    } 
    else { // if not enough values or null values, set cv to null
        $cv = null;
    }

    $gene_cv[$gene_name] = $cv;
}

// debug line
// echo $gene_cv['Pp3c20_17010V3.1']."<br>"; 
// echo $gene_cv['Pp3c3_1510V3.1']."<br>";
// echo $gene_cv['Pp3c14_24450V3.1']."<br>";
// print_r($replicates_means['Pp3c7_1420V3.1']);
// echo $gene_cv['Pp3c7_1420V3.1']."<br>";
// 

//filter out null values
$filtered_gene_cv = array_filter($gene_cv, function($cv) {
    return $cv !== null;
});

// sort the filtered array by CV values in ascending order
asort($filtered_gene_cv);


// get top 10 genes with lowest CV
$top_genes = array_slice($filtered_gene_cv, 0, 10, true);

// print_r($top_genes); // debug line

// get all sample names for table header
$sample_names = [];
// print_r(array_keys($top_genes)[0]);

if(empty($top_genes)) {
  echo "<div class=\"alert alert-danger\" style=\"text-align:center;\" role=\"alert\"><p style=\"margin:0px\">No genes found with the selected parameters</p></div>";
}else {
  if($category_number > 1) { // if more than one category was selected, get all sample names from all top genes
  
    if($cvMean) {
      foreach (array_keys($top_genes) as $gene) {
        foreach ($replicates_means[$gene] as $sample_name => $mean_value) {
            $sample_names[$sample_name] = true;
        }
      }
    $sample_names = array_keys($sample_names); // get only the keys (sample names)
    }else {
      foreach (array_keys($top_genes) as $gene) {
        foreach ($replicates_means[$gene] as $sample_name => $mean_value) {
            if($mean_value !== [null]) {
            $sample_names[$sample_name] = count($mean_value);
            // echo $sample_name." ".count($mean_value)." -> ".implode(", ", $mean_value)."<br>";
            }
          }
        }
    } 
  }
  else { // if only one category was selected, get all sample names from the first top_gene
    if($cvMean) {
      foreach ($replicates_means[array_keys($top_genes)[0]] as $sample_name => $mean_value) { // get sample names from the first top gene
          $sample_names[$sample_name] = true;
      }
      $sample_names = array_keys($sample_names);
    }else {
      foreach ($replicates_means[array_keys($top_genes)[0]] as $sample_name => $mean_value) {
          $sample_names[$sample_name] = count($mean_value);
        }
    }
  }
echo "<br>";

// ---------------------------------- GENE SEARCH ----------------------------------------
echo '<br><div class="input-group" style="max-width:700px; margin:auto">
  <input id="autocomplete_gene" type="text" class="form-control" placeholder="Find your gene" aria-label="Find your gene" aria-describedby="button_search">
  <button class="btn btn-primary" type="button" id="button_search" style="margin-left:10px">Search</button>
  </div>
  <div id="search_results_frame" style="display:none">
     <button type="button" id="close_search_results" class="close" title="Close">
      <span aria-hidden="true">&times;</span>
     </button>
    <br><div id="search_results_table"></div><br><br>
    <h3 id="search_results_boxplot_title" style="text-align:center;"></h3>
    <div id="search_results_boxplot" style="display:none;"></div>
  </div><br><br>';
// ------------------------------------ create table with 10 genes with lowest CV ------------------------------------
echo '<div class="collapse_section pointer_cursor banner" data-toggle="collapse" data-target="#table_frame" aria-expanded="true">
  <i class="fas fa-sort"></i> 10 genes with lowest CV
    </div>';

echo '<div id="table_frame" class="collapse show">';
echo '<div id="load" class="loader"></div>';

echo "<div id=\"table_container\" style=\"display:none\">";
echo "<br><table id=\"cv_table\" class=\"table table-striped table-bordered\">";
echo "<thead><tr><th>Gene ID</th><th>Coef. Variation (%)</th>";

  if (!$cvMean) { // if not mean mode
    foreach ($sample_names as $sample_name => $replicates_count) { // $sample_names is an associative array with sample name as key and number of replicates as value
      for ($i=0; $i<$replicates_count; $i++) {
        echo "<th>$sample_name</th>";
      }
    }
    echo "</tr></thead><tbody>";

    foreach ($top_genes as $gene_name => $cv) { 
      echo "<tr>";
      if($link_found && !$multicategory_found) {
        $gene_url = str_replace("query_id", $gene_name, $gene_link);
        echo "<td><a href=\"$gene_url\" target=\"_blank\">$gene_name</a></td>";
      } else {
        if($annot_json_file_found && !$multicategory_found) {
            echo "<td><a href=\"/easy_gdb/gene.php?name=$gene_name&annot=$annot_link\" target=\"_blank\">$gene_name</a></td>";
        }else {
            echo "<td>$gene_name</td>";
        }
      }
      echo "<td><b>" . sprintf("%1\$.2f", $cv) . "</b></td>";

      foreach ($sample_names as  $sample_name => $replicates_count) {
        for ($i=0; $i<$replicates_count; $i++) {
          $mean_data = isset($replicates_means[$gene_name][$sample_name]) ? sprintf("%1\$.2f",$replicates_means[$gene_name][$sample_name][$i]) : "-";
          echo "<td>$mean_data</td>";
        }
      }
      echo "</tr>";
    }
  } else { // if mean mode

  foreach ($sample_names as $sample_name) {
      echo "<th>$sample_name</th>";
  }
  echo "</tr></thead><tbody>";

    foreach ($top_genes as $gene_name => $cv) {
      echo "<tr>";
      if($link_found && !$multicategory_found) {
        $gene_url = str_replace("query_id", $gene_name, $gene_link);
        echo "<td><a href=\"$gene_url\" target=\"_blank\">$gene_name</a></td>";
      } else {
        if($annot_json_file_found && !$multicategory_found) {
            echo "<td><a href=\"/easy_gdb/gene.php?name=$gene_name&annot=$annot_link\" target=\"_blank\">$gene_name</a></td>";
        }else {
            echo "<td>$gene_name</td>";
        }
      }
      echo "<td><b>" . sprintf("%1\$.2f", $cv) . "</b></td>";

      foreach ($sample_names as $sample_name) {
          $mean_data = isset($replicates_means[$gene_name][$sample_name]) ? sprintf("%1\$.2f",$replicates_means[$gene_name][$sample_name]) : "-";
          echo "<td>$mean_data</td>";
      }
      echo "</tr>";
    } 
  }  
  echo "</tbody></table><br><br>"; // end table and container

  $replicates_filtered = [];
  foreach (array_keys($filtered_gene_cv) as $gene_name) {
    $replicates_filtered[$gene_name] = $replicates[$gene_name] ?? null;
  }
}
?>

  <!-- ------------------ boxplot ----------------  -->
  <center><div id="boxplot_frame" style="display: none;"><div class="form-group d-inline-flex" style="width: 450px;">
    <label for="sel_gene" style="width: 150px; margin-top:7px"><b>Select gene:</b></label>
    <select class="form-control" id="sel_gene">
      <?php
        foreach (array_keys($top_genes) as $gene) {
          echo "<option value=\"$gene\">$gene</option>";
        }
      ?>
    </select>
  </div></div></center>
<div id="boxplot"></div>
</div></div>


<?php
include realpath('../../footer.php');
?>

<style>
 table.dataTable td,th  {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; 
  }
</style>

<script src="https://unpkg.com/simple-statistics@latest/dist/simple-statistics.min.js"></script>
<script src="../../js/datatable.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  
      $('#load').hide();
      $('#table_container').show();
      // datatable("#tblResults","");
      datatable_basic("#cv_table");
      // $(".td-tooltip").tooltip();
});



function boxplot(data_array, boxplot_id="boxplot") {
  var boxplot_chart="";

  var options = {

        legend: {
          show: true,
          position: 'bottom', 
          horizontalAlign: 'center', 
          // showForSingleSeries: true,
        },
          series: [
          {
            name: 'box',
            type: 'boxPlot',
            data: data_array
          },
        ],
          chart: {
          type: 'boxPlot',
          height: 350,
          zoom: {
            enabled: true,   
            type: 'xy',
            allowMouseWheelZoom: false  
          }
        },
        // colors: ['#008FFB', '#FEB019'],
        title: {
          text: 'BoxPlot chart',
          align: 'left',
        },
        xaxis: {
          type: 'category',
          labels: {
            rotate: -50,
            rotateAlways: true,
            hideOverlappingLabels: false,
            trim: false
          }
        },
        grid: {
          padding: { left: 40, right: 40, top: 10, bottom: 10 } // grid padding to avoid cutting  value labels
        },
      };

    boxplot_chart = new ApexCharts(document.querySelector("#"+boxplot_id), options);
    return boxplot_chart;
}

// ---------------------------------- GENE SEARCH SCRIPT ----------------------------------------
var top_genes_name = <?php echo json_encode(array_keys($top_genes)); ?>; // get top genes names 

if(top_genes_name.length > 0) { // if there are top genes to plot

  var genes = {};
  var values=[];
  var data = [];

  var replicates_filtered  = <?php echo json_encode($replicates_filtered); ?>;
  var filtered_gene_cv = <?php echo json_encode($filtered_gene_cv); ?>; // get all genes no nulls
  var replicates_means = <?php echo json_encode($replicates_means); ?>;


  Object.keys(replicates_filtered).forEach(function(gene_name, index) {
    genes[gene_name] = [];

    Object.keys(replicates_filtered[gene_name]).forEach(function(sample, index) {
      const values = Object.values(replicates_filtered[gene_name][sample]);
      const sortedValues = values.slice().sort((a, b) => a - b).map(Number);
        genes[gene_name].push({
          x: sample,
          y: [
            sortedValues[0],
            ss.quantileSorted(sortedValues, 0.25),
            ss.quantileSorted(sortedValues, 0.5),
            ss.quantileSorted(sortedValues, 0.75),
            sortedValues[sortedValues.length - 1]
          ]
        });
    });
  })
  // console.log(ss.min(Object.values(top_genes_data['Ubr4']['EphrinB2_cko'])));
  // console.log(genes[top_genes_name[0]]);
  $('#boxplot_frame').show();
  
  var boxplot_chart = boxplot(genes[top_genes_name[0]]);
  boxplot_chart.render();

  var boxplot_chart_search = boxplot([],"search_results_boxplot");
  boxplot_chart_search.render();
}

  $('#sel_gene').change(function() {
    // console.log(genes[this.value]);
    // boxplot(genes[this.value]);
    boxplot_chart.updateSeries(
        [
          {
            name: 'box',
            type: 'boxPlot',
            data: genes[this.value]
          },
        ],);

  });


$( "#autocomplete_gene" ).autocomplete({
      source: function(request, response) {
        var results = $.ui.autocomplete.filter(Object.keys(filtered_gene_cv), request.term);
        response(results.slice(0, 15));
        if(results == "")
        {$('#autocomplete_gene').css("background-color",'#f17c7c')}
        else {$('#autocomplete_gene').css("background-color", "white")}
      }
    });


$('#autocomplete_gene').on('input', function() {
  if($(this).val() == "")
  {$('#autocomplete_gene').css("background-color", "white")}
});

//call PHP file ajax_get_names_array.php to get the gene list to autocomplete from the selected dataset file
function ajax_call_table(replicates, cv,cvMean, gene_name, multicategory_found, link_found, gene_link,  annot_json_file_found, annot_link) {
  jQuery.ajax({
    type: "POST",
    url: 'ajax_cv_calculator_table.php',
    data: {'replicates': replicates, 'cv': cv, 'cvMean': cvMean, 'gene_name': gene_name, 'multicategory_found': multicategory_found, 'link_found': link_found, 'gene_link': gene_link, 'annot_json_file_found': annot_json_file_found, 'annot_link': annot_link},

    success: function (table_array) {
      var row_table = JSON.parse(table_array);
      // console.log(row_table.join(""));
      $('#search_results_table').html(row_table.join(""));
      datatable_basic("#cv_table_2");
      
    }
  });
}; // end ajax_call function



$('#button_search').click(function () {
      var selected_gene = $('#autocomplete_gene').val().trim();
      // console.log(selected_gene);

    $('#search_results_frame').show();
    $('#search_results_boxplot').show();
    $('#search_results_boxplot_title').text(selected_gene);

    ajax_call_table(replicates_means[selected_gene], filtered_gene_cv[selected_gene], <?php echo json_encode($cvMean);?>, selected_gene, <?php echo json_encode($multicategory_found);?>, <?php echo json_encode($link_found);?>, <?php echo json_encode($gene_link);?>, <?php echo json_encode($annot_json_file_found);?>, <?php echo json_encode($annot_link);?>);
      


    // console.log(replicates_filtered[selected_gene]);
    // console.log(genes[selected_gene]);
    boxplot_chart_search.updateSeries(
        [
          {
            name: 'box',
            type: 'boxPlot',
            data: genes[selected_gene]
          },
        ],);

       
});

$("#close_search_results").click(function() {
  $('#search_results_frame').hide();
});
</script>
