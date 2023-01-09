<?php include realpath('../../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<?php 
  $expr_file = $_POST["expr_file"];
  $gene_list = $_POST["gids"];
  $dataset_name_ori = preg_replace('/.+\//',"",$expr_file);
  $dataset_name = preg_replace('/_/'," ",$dataset_name_ori);
  $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
  
  $gids = [];
  
  
  if(isset($gene_list)) {
    
    $time_start = microtime(true); 
    
    foreach (explode("\n",$gene_list) as $one_gene) {
      $one_gene = rtrim($one_gene);
      
      if (preg_match('/\.\d+$/',$one_gene)) {
        $one_gene2 = preg_replace('/\.\d+$/',"",$one_gene);
        array_push($gids,$one_gene2);
      }
      if (!preg_match('/\.\d+$/',$one_gene)) {
        $one_gene2 = $one_gene.".1";
        array_push($gids,$one_gene2);
      }
      array_push($gids,$one_gene);
    }
    
    // $time_end = microtime(true);
    // $execution_time = ($time_end - $time_start);
    // echo '<p><b>Total Execution Time:</b> '.$execution_time.'</p>';
    
    
    //$gids = explode("\n", rtrim($gene_list));
    
    // $gids=array_map(function($row) {
    //   return rtrim($row);
    // },explode("\n",$gene_list));
    
  }
  
?>

<a href="/easy_gdb/tools/expression/expression_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<div class="page_container" style="margin-top:20px">
  <br>
<?php
  // ############################################################### DATASET TITLE AND DESCRIPTION
  
  $expr_img_array = [];
  
  if ( file_exists("$expression_path/expression_info.json") ) {
    $annot_json_file = file_get_contents("$expression_path/expression_info.json");
    $annot_hash = json_decode($annot_json_file, true);
    
    if ($annot_hash[$dataset_name_ori]["description"]) {
    
      $desc_file = $annot_hash[$dataset_name_ori]["description"];

      if ( file_exists("$custom_text_path/expr_datasets/$desc_file") ) {
        
        echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
        
        echo "<h2 style=\"font-size:20px\">$r_key</h2>";
        include("$custom_text_path/expr_datasets/$desc_file");
        echo"<br>";
      }
      else {
        echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
      }
    }
    
    
    if ($annot_hash[$dataset_name_ori]["images"]) {
      $expr_img_array = $annot_hash[$dataset_name_ori]["images"];
    }
    
    // print("<pre>".print_r($expr_img_array,true)."</pre>");
    
  }
?>
  
  


<?php

$sample_names = [];
$heatmap_one_gene = [];
$heatmap_series = [];
// $scatter_one_gene = [];
$scatter_one_sample = [];
$scatter_all_genes = [];

$found_genes = [];

$table_code_array = [];

if ( file_exists("$expr_file") && isset($gids) ) {
  $tab_file = file("$expr_file");
  
   // echo "<div style=\"width:95%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">";
   array_push($table_code_array,"<div style=\"width:95%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">");

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
          // echo "<thead><tr><th>".$header[0]."</th>";
          array_push($table_code_array,"<thead><tr><th>".$header[0]."</th>");
          
          foreach ($replicates as $r_key => $r_value) {
            // echo "<th>$r_key</th>";
            array_push($table_code_array,"<th>$r_key</th>");
          }
          // echo "</tr></thead>";
          array_push($table_code_array,"</tr></thead>");
          
          $header_printed = 1;
          $sample_names = array_keys($replicates);
        }
        
        $q_link = "";
        if ($annot_hash[$dataset_name_ori]) {
          if ($annot_hash[$dataset_name_ori]["link"]) {
            if ($annot_hash[$dataset_name_ori]["link"] == "#") {
              // echo "<tr><td>$gene_name</td>";
              array_push($table_code_array,"<tr><td>$gene_name</td>");
            }
            else {
              $q_link = $annot_hash[$dataset_name_ori]["link"];
              $q_link = preg_replace('/query_id/',$gene_name,$q_link);
              // echo "<tr><td><a href=\"$q_link\" target=\"_blank\">$gene_name</a></td>";
              array_push($table_code_array,"<tr><td><a href=\"$q_link\" target=\"_blank\">$gene_name</a></td>");
            }
          }
          else {
            // echo "<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>";
            array_push($table_code_array,"<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>");
          }
        }
//        else {
          // echo "<tr><td>$gene_name</td>";
//          array_push($table_code_array,"<tr><td>$gene_name</td>");
//        }
        
        
        $scatter_pos = 1;
        
        // print expression average values $r_key is like "Sample1" and $r_value is like [4.4,2.3,8.1]
        foreach ($replicates as $r_key => $r_value) {
          
          $a_sum = array_sum($r_value);
          $a_reps = count($r_value);
        
          $average = sprintf("%1\$.2f",$a_sum/$a_reps);
          // echo "<td>$average</td>";
          array_push($table_code_array,"<td>$average</td>");
          
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
        // echo "</tr>";
        array_push($table_code_array,"</tr>");
        
        array_push($heatmap_series, $heatmap_one_gene);
        
        
        
        
        $replicates = [];
        $heatmap_one_gene = [];
        // $scatter_one_gene = [];
        $scatter_one_sample = [];
      } // end if gene in input list
      
      
    } // each line, each gene foreach
    // echo "</table></div>";
    array_push($table_code_array,"</table></div>");
    
  // } // if gene_list
  
} // if expr file exists

?>


  
  
  
  
  
  
  <center>

<!-- #####################             Lines             ################################ -->
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#line_chart_frame" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Lines
    </div>

    <div id="line_chart_frame" class="collapse show" style="width:95%; border:2px solid #666; padding-top:7px">
      <div id="chart_lines" style="min-height: 550px;"></div>
    </div>
  </center>
  
<!-- #####################             Cards             ################################ -->
    
<?php

  if ($expr_cards) {
    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#cards_frame" aria-expanded="true">';
      echo '<i class="fas fa-sort" style="color:#229dff"></i> Expression Cards';
    echo '</div>';

    echo '<div id="cards_frame" class="row collapse hide" style="padding-top:7px">';


      echo '<div class="form-group d-inline-flex" style="width: 450px; margin-left:15px">';
        echo '<label for="card_sel1" style="width: 150px;">Select gene:</label>';
        echo '<select class="form-control" id="card_sel1">';
          
            foreach ($gids as $gene) {
              echo "<option value=\"$gene\">$gene</option>";
            }
        
        echo '</select>';
      echo '</div>';


      echo '<div class="d-inline-flex" style="margin:10px">';
        echo '<span class="circle" style="background-color:#000000"></span> Lowest <2';
        echo '<span class="circle" style="background-color:#fff"></span> <1';
        echo '<span class="circle" style="background-color:#ffe999"></span> >=1';
        echo '<span class="circle" style="background-color:#fb4"></span> >=2';
        echo '<span class="circle" style="background-color:#ff7469"></span> >=10';
        echo '<span class="circle" style="background-color:#de2515"></span> >=50';
        echo '<span class="circle" style="background-color:#b71005"></span> >=100';
        echo '<span class="circle" style="background-color:#7df"></span> >=200';
        echo '<span class="circle" style="background-color:#0f0"></span> >=5000';
        echo '<span class="circle gold"></span> Highest';
      echo '</div>';


      echo '<div id="card_code" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>';
    echo '</div>';
    
  }
?>

<style>
  
  .expr_card_body {
/*    background-color: #363;*/
    background-image: url("card_pattern.png");
    background-repeat: repeat;
    background-color: #f63;
    height: 280px;
    width: 220px;
    padding: 10px;
    border: 1px solid #000;
    margin-right:5px;
  }
  
  .expr_card_title {
    font: 16px "Lucida Grande", "Trebuchet MS", Verdana, sans-serif;
    background-color: #ec7;
    text-align: center;
    vertical-align: middle;
    width: 200px;
    height: 50px;
    margin-bottom:10px;
    padding-left:3px;
    padding-right:3px;
    border: 1px solid #000;
    line-height: 50px;
  }
  
  .expr_card_image {
    width: 200px;
    height: 200px;
    border: 1px solid #000;
  }
  
  .expr_card_value {
    text-align: center;
    vertical-align: middle;
    font: 16px "Lucida Grande", "Trebuchet MS", Verdana, sans-serif;
    background-color: #ec7;
    width: 50px;
    height: 50px;
    left: 9px;
    position: relative;
    bottom: 42px;
    border: 1px solid #000;
    line-height: 50px;
  }
  
  
  .gold {
    background-image: linear-gradient(160deg, #8f6B29, #FDE08D, #DF9F28);
  }
  
  .circle {
    height: 15px;
    width: 15px;
    border-radius: 50%;
    border-style: solid;
    border-color: #ccc;
    display: inline-block;
    margin: 5px;
    margin-left: 15px;
  }
  
  
  
/* FLIP CARD EFFECT*/
  
  /* The flip card container - set the width and height to whatever you want. We have added the border property to demonstrate that the flip itself goes out of the box on hover (remove perspective if you don't want the 3D effect */
  .flip-card {
    background-color: transparent;

    height: 280px;
    width: 220px;
/*    padding: 10px;*/
    margin-right:5px;
    margin-bottom:5px;
 
/*    width: 300px;
    height: 200px;
    border: 1px solid #f1f1f1;
*/    perspective: 1000px; /* Remove this if you don't want the 3D effect */
  }

  /* This container is needed to position the front and back side */
  .flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 1s;
    transform-style: preserve-3d;
  }

  /* Do an horizontal flip when you move the mouse over the flip box container */
/*  .flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
  }
*/  
  
  /* Position the front and back side */
  .flip-card-front, .flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden; /* Safari */
    backface-visibility: hidden;
  }

  /* Style the front side (fallback if image is missing) */
  .flip-card-front {
    background-color: #363;
    color: white;
  }

  /* Style the back side */
  .flip-card-back {
/*    background-image: linear-gradient(180deg, #8f6B29, #FDE08D, #DF9F28);*/
    color: black;
    transform: rotateY(180deg);
  }
  
</style>
    
  
<!-- #####################             Heatmap             ################################ -->

  <center>
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#heatmap_graph" aria-expanded="true">
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
  
    <!-- #####################             Replicates           ################################ -->
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#replicates_graph" aria-expanded="true">
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
  
  echo implode("\n", $table_code_array);
  
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
  
  var db_title = <?php echo json_encode($dbTitle) ?>;
  var db_logo = <?php echo json_encode("$images_path/$db_logo") ?>;
  var img_path = <?php echo json_encode($images_path) ?>;
  var expr_img_array = <?php echo json_encode($expr_img_array) ?>;
    
    
  if (gene_list.length == 0) {
    $( "#chart1" ).css("display","none");
    $( "#chart2" ).css("display","none");
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
