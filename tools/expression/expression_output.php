<?php include realpath('../../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<?php 
  $expr_file = $_POST["expr_file"];
  $gene_list = $_POST["gids"];
  $dataset_name_ori = preg_replace('/.+\//',"",$expr_file);
  $dataset_name = preg_replace('/_/'," ",$dataset_name_ori);
  $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
  
  if(isset($gene_list)) {
    $gids=array_map(function($row) {
      return rtrim($row);
    },explode("\n",$gene_list));
  }
  
?>

<a href="/easy_gdb/tools/expression/expression_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<div class="page_container" style="margin-top:20px">
  <br>
<?php
  // ############################################################### DATASET TITLE AND DESCRIPTION
  
  if ( file_exists("$expression_path/expression_info.json") ) {
    $annot_json_file = file_get_contents("$expression_path/expression_info.json");
    $annot_hash = json_decode($annot_json_file, true);
    
    if ($annot_hash[$dataset_name_ori]["description"]) {
    
      $desc_file = $annot_hash[$dataset_name_ori]["description"];

      if ( file_exists("$expression_path/$desc_file") ) {
        
        echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
        
        echo "<h2 style=\"font-size:20px\">$r_key</h2>";
        include("$expression_path/$desc_file");
        echo"<br>";
      }
      else {
        echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
      }
    }
    
  }
?>
  
  <center>

<!-- #####################             Lines             ################################ -->
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#line_chart_frame" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Lines
    </div>

    <div id="line_chart_frame" class="collapse show" style="width:95%; border:2px solid #666; padding-top:7px">
      <div id="chart_lines" style="min-height: 550px;"></div>
    </div>
  
<!-- #####################             Heatmap             ################################ -->
  
    <div id="heatmap_section" class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#heatmap_graph" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Heatmap
    </div>

    <div id="heatmap_graph" class="collapse hide">

      <div id="chart1_frame" style="width:95%; border:2px solid #666; padding-top:7px">
        <button id="red_color_btn" type="button" class="btn btn-danger">Red palette</button>
        <button id="blue_color_btn" type="button" class="btn btn-primary">Blue palette</button>
        <button id="range_color_btn" type="button" class="btn" style="color:#FFF">Color palette</button>

        <div id="chart1" style="min-height: 400px;"></div>

      </div>
    </div>
  
    <!-- #####################             Replicates             ################################ -->
  
    <div id="replicates_section" class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#replicates_graph" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Replicates
    </div>

    <div id="replicates_graph" class="collapse hide">

      <div id="chart2_frame" style="width:95%; border:2px solid #666; padding-top:7px">
        <div class="form-group d-inline-flex" style="width: 450px;">
          <label for="sel1" style="width: 150px; margin-top:7px">Select gene:</label>
          <select class="form-control" id="sel1">
            <?php
              foreach ($gids as $gene) {
                echo "<option value=\"$gene\">$gene</option>";
              }
            ?>
          </select>
        </div>
        <div id="chart2" style="min-height: 365px;"></div>
      </div>

    </div>
  
  
  
  </center>
  
  
  <div class="data_table_frame">

    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#avg_table" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Average values
    </div>

    <div id="avg_table" class="collapse hide">

<?php

$sample_names = [];
$heatmap_one_gene = [];
$heatmap_series = [];
// $scatter_one_gene = [];
$scatter_one_sample = [];
$scatter_all_genes = [];

$found_genes = [];


if ( file_exists("$expr_file") && isset($gids) ) {
  $tab_file = file("$expr_file");
  
   echo "<div style=\"width:95%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">";

    $columns = [];
    $replicates = [];
    $average = [];
    $header_printed = 0;
    
    $first_line = array_shift($tab_file);
    $header = explode("\t", rtrim($first_line));
    
    
    //gets each replicate value for each gene
    foreach ($tab_file as $line) {
      $columns = explode("\t", rtrim($line));
      
      $col_count = 0;
      $gene_name = $columns[0];
      
      // if gene found in input list
      if (in_array($gene_name,$gids)) {
        
        array_push($found_genes,$gene_name);
        
        foreach ($columns as $col) {
          
          $sample_name = $header[$col_count];
          
          if ($col_count != 0) {
            
            if ($replicates[$sample_name]) {
             array_push($replicates[$sample_name], $col);
            } else {
             $replicates[$sample_name] = [];
             array_push($replicates[$sample_name], $col);
            }
            
          }
          
          $col_count++;
        } // end column foreach
        
        //print header with sample names
        if (!$header_printed) {
          echo "<thead><tr><th>".$header[0]."</th>";
          foreach ($replicates as $r_key => $r_value) {
            echo "<th>$r_key</th>";
          }
          echo "</tr></thead>";
          $header_printed = 1;
          $sample_names = array_keys($replicates);
        }
        
        $q_link = "";
        if ($annot_hash[$dataset_name_ori]) {
          if ($annot_hash[$dataset_name_ori]["link"]) {
            if ($annot_hash[$dataset_name_ori]["link"] == "#") {
              echo "<tr><td>$gene_name</td>";
            }
            else {
              $q_link = $annot_hash[$dataset_name_ori]["link"];
              $q_link = preg_replace('/query_id/',$gene_name,$q_link);
              echo "<tr><td><a href=\"$q_link\" target=\"_blank\">$gene_name</a></td>";
            }
          }
          else {
            echo "<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>";
          }
        }
        else {
          echo "<tr><td>$gene_name</td>";
        }
        
        
        $scatter_pos = 1;
        
        // print expression average values $r_key is like "Sample1" and $r_value is like [4.4,2.3,8.1]
        foreach ($replicates as $r_key => $r_value) {
          
          $a_sum = array_sum($r_value);
          $a_reps = count($r_value);
        
          $average = sprintf("%1\$.2f",$a_sum/$a_reps);
          echo "<td>$average</td>";
          
          //save heatmap data
          $heatmap_one_gene["name"] = $gene_name;
          if ($heatmap_one_gene["data"]) {
            array_push($heatmap_one_gene["data"], $average);
          } else {
            $heatmap_one_gene["data"] = [];
            array_push($heatmap_one_gene["data"], $average);
          }
          
          //save scatter data
          //save replicates
          foreach ($r_value as $one_rep) {
            $one_replicate_pair = [$scatter_pos, $one_rep];
            
            //save samples and add replicates
            $scatter_one_sample["name"] = $r_key;
            if ($scatter_one_sample["data"]) {
              array_push($scatter_one_sample["data"], $one_replicate_pair );
            } else {
              $scatter_one_sample["data"] = [];
              array_push($scatter_one_sample["data"], $one_replicate_pair );
            }
            
          }
          $scatter_pos++;
          
          //save gene and add samples with replicates
          if ($scatter_all_genes[$gene_name]) {
            array_push($scatter_all_genes[$gene_name], $scatter_one_sample );
          } else {
            $scatter_all_genes[$gene_name] = [];
            array_push($scatter_all_genes[$gene_name], $scatter_one_sample );
          }
          $scatter_one_sample = [];
        }
        echo "</tr>";
        
        array_push($heatmap_series, $heatmap_one_gene);
        
        
        
        
        $replicates = [];
        $heatmap_one_gene = [];
        // $scatter_one_gene = [];
        $scatter_one_sample = [];
      } // end if gene in input list
      
      
    } // each line, each gene foreach
    echo "</table></div>";

  // } // if gene_list
  
} // if expr file exists

?>

    </div> <!-- avg_table end -->
  
  </div> <!-- data_table_frame end -->
</div>

<br>

<?php include realpath('../../footer.php'); ?>



<script type="text/javascript">
  
  var sample_array = <?php echo json_encode($sample_names) ?>;
  var heatmap_series = <?php echo json_encode(array_reverse($heatmap_series)) ?>;
  
  var gene_list = <?php echo json_encode($found_genes) ?>;
  var scatter_one_gene = <?php echo json_encode($scatter_all_genes[$found_genes[0]]) ?>;
  var scatter_all_genes = <?php echo json_encode($scatter_all_genes) ?>;
  
  if (gene_list.length == 0) {
    $( "#chart1" ).css("display","none");
    $( "#chart2_frame" ).css("display","none");
    $( "#dataset_title" ).html("No gene was found in the selected dataset. Please, check gene names.");
    
  }
  
  $("#tblResults").dataTable({
    "dom":'Bfrtip',
    "ordering": false,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
  });

  $("#tblResults_filter").addClass("float-right");
  $("#tblResults_info").addClass("float-left");
  $("#tblResults_paginate").addClass("float-right");

</script>
  
<script src="expression_graphs.js"></script>

<style>
  #range_color_btn{
/*  height: 50px;*/
  border-color: #b71005;
  background: -moz-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  background: -webkit-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  background: linear-gradient(90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0c320', endColorstr='#ff0000',GradientType=1 );
  }
</style>
