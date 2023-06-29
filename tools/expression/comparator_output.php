<?php include realpath('../../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/02_expression_comparator.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<a href="/easy_gdb/tools/expression/comparator_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>
<h1 class="text-center">Comparison results</h1>


<?php 
$hk_genes=[];
if ($_POST['denominator_genes']) {
  $hk_genes = explode("\n",$_POST['denominator_genes']);
  $hk_genes = array_filter($hk_genes);
  $hk_genes = array_map('trim', $hk_genes);
}

$fc_log2 = $_POST['fc_log2'];

if ($hk_genes) {
  echo "<p> Your data are normalized using ".join(", ",$hk_genes)." as reference.";
  if ($fc_log2) {
    echo " Log2 was applied.";
  }
  echo "</p>";
}

// get each sample and their dataset and save it in a hash key=dataset-file value=array of experiments from that dataset
  $sample_hash = [];
  
  foreach ($_POST['sample_names'] as $sample) {
    list($file,$exp) = explode("@", $sample);
  
    if ($sample_hash[$file]) {
      array_push($sample_hash[$file],$exp);
    } else {
      $sample_hash[$file] = [];
      array_push($sample_hash[$file],$exp);
    }
  }

// get input genes
  $gene_list = $_POST["gids"];
  $gids = [];
  
  if(isset($gene_list)) {
    
//iterate by each gene and add genes with/without isoform version (.1) to the list $gids
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
    }// end foreach
    
  } //end isset
?>

<div class="page_container" style="margin-top:20px">

<?php

$sample_names = [];
$heatmap_one_gene = [];
$heatmap_series = [];
$scatter_one_sample = [];
$scatter_all_genes = [];
$table_code_array = [];


$columns = [];
$replicates = [];
$hk_replicates = [];
$average = [];

$full_header = [];
$header = [];
$found_genes = [];
$annot_hash = [];

if ( file_exists("$expression_path/expression_info.json") ) {
  $annot_json_file = file_get_contents("$expression_path/expression_info.json");
  $annot_hash = json_decode($annot_json_file, true);
}


// iterate each dataset selected in the comparator input
foreach($sample_hash as $expr_file => $comparator_samples_array) {

// check dataset file exists and open it. Get header line and save sample names in header
  if ( file_exists("$expr_file") ) {
    
    // IT IS NOT POSSIBLE TO ADD MULTIPLE LINKS FOR THE SAME GENES, so this is not needed.
    // $dataset_name_ori = preg_replace('/.+\//',"",$expr_file);
    // $dataset_name = preg_replace('/_/'," ",$dataset_name_ori);
    // $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
    
    $tab_file = file("$expr_file");
    
    $first_line = array_shift($tab_file);
    
    $header = explode("\t", rtrim($first_line));
    foreach ($header as $one_exp) {
      if (in_array($one_exp,$comparator_samples_array)) {
        array_push($full_header,$one_exp);
      }
    }
    
//gets each replicate value for each gene
    foreach ($tab_file as $line) {
      $columns = explode("\t", rtrim($line));
      
      $col_count = 0;
      $gene_name = $columns[0];
      
// if gene found in input list save it in found_genes hash
      if ( in_array($gene_name,$gids) ||  ($hk_genes && in_array($gene_name,$hk_genes)) ) {
        
        if ( in_array($gene_name,$gids) ) {
          $found_genes[$gene_name] = 1;
        }

// create object with replicates of each sample and gene
        foreach ($columns as $col) {
         
          $sample_name = $header[$col_count];
          
          if ( in_array($gene_name,$gids) && in_array($sample_name, $comparator_samples_array) && $col_count != 0 ) {
            if ($replicates[$sample_name][$gene_name]) {
             array_push($replicates[$sample_name][$gene_name], $col);
            } else {
             $replicates[$sample_name][$gene_name] = [];
             array_push($replicates[$sample_name][$gene_name], $col);
            }
          }
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          if ($hk_genes) {
            // print_r($hk_genes);
            // echo " hk true -> $gene_name $sample_name $col_count";
            // foreach ($hk_genes as $test) {
            //   echo ".$test. :$gene_name:<br>";
            // }
            // if ( in_array($gene_name,$hk_genes) ) {
              // echo "hk found -> $gene_name<br>";
            // }
            // if ( in_array($sample_name,$comparator_samples_array) ) {
            //   echo "sample found -> $sample_name";
            // }
            
            if ( in_array($gene_name,$hk_genes) && in_array($sample_name, $comparator_samples_array) && $col_count != 0) {
              // echo "hk true2 -> $gene_name";
              
              if ($hk_replicates[$sample_name][$gene_name]) {
               array_push($hk_replicates[$sample_name][$gene_name], $col);
              } else {
               $hk_replicates[$sample_name][$gene_name] = [];
               array_push($hk_replicates[$sample_name][$gene_name], $col);
              }
            }
            // echo "<br>";
          }
          
          $col_count++;
        } // end column foreach
      } // end if in_array
      
      
    } //end foreach line
    
  } // end if expression file exists
  
} // end foreach sample_hash

$full_header = array_unique($full_header);
$sample_names = array_values($full_header);

// create average table and its header
array_push($table_code_array,"<div style=\"width:95%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">");
array_push($table_code_array,"<thead><tr><th>ID</th>");
  
foreach ($full_header as $exp_name) {
  array_push($table_code_array,"<th>$exp_name</th>");
}
array_push($table_code_array,"</tr></thead>");



// add links to average table genes. CREATE CONF VARIABLE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
foreach ($found_genes as $gene_name => $kk) {

  //$q_link = "";
  // if ($annot_hash[$dataset_name_ori]) {
  //   if ($annot_hash[$dataset_name_ori]["link"]) {
  //     if ($annot_hash[$dataset_name_ori]["link"] == "#") {
  //       array_push($table_code_array,"<tr><td>$gene_name</td>");
  //     }
  //     else {
  //       $q_link = $annot_hash[$dataset_name_ori]["link"];
  //       $q_link = preg_replace('/query_id/',$gene_name,$q_link);
  //       array_push($table_code_array,"<tr><td><a href=\"$q_link\" target=\"_blank\">$gene_name</a></td>");
  //     }
  //   }
  //   else {
  //     array_push($table_code_array,"<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>");
  //   }
  // }
  // else {
    array_push($table_code_array,"<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>");
  // }
  
  $scatter_pos = 1;
  
  // get expression average values like "Sample1" and values are like gene => [4.4,2.3,8.1]
  foreach ($replicates as $sample_name => $gene_reps_array) {
    $a_sum = array_sum($gene_reps_array[$gene_name]);
    $a_reps = count($gene_reps_array[$gene_name]);

    $average = sprintf("%1\$.2f",$a_sum/$a_reps);
    
    if ($hk_genes) {
      $hk_total_sum = 0;
      $hk_total_reps = 0;
      
      foreach ($hk_genes as $hk_genename) {
        
        $hk_sum = array_sum($hk_replicates[$sample_name][$hk_genename]);
        $hk_reps = count($hk_replicates[$sample_name][$hk_genename]);
        
        $hk_total_sum = $hk_total_sum + $hk_sum;
        $hk_total_reps = $hk_total_reps + $hk_reps;
      }
      
      $hk_ave = sprintf("%1\$.2f",$hk_total_sum/$hk_total_reps);
      // echo "$gene_name: $average / $hk_ave ";
      
      if ($hk_ave != $average && $hk_ave == 0) {
        $hk_ave = 0.001;
        $average = sprintf("%1\$.2f",$average/$hk_ave);
      } else if ($hk_ave == $average) {
        $average = sprintf("%1\$.2f",1);
      } else {
        $average = sprintf("%1\$.2f",$average/$hk_ave);
      }
      // echo "= $average<br>";
      
       if ($fc_log2) {
         $average = sprintf( "%1\$.2f",log($average, 2) );
         if ($average == "INF") {
           $average = 9999.99;
         }
       }
      // echo "log =  $average<br>";
      
    }
    
    
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
    //save replicates. Iterate each replicate of each gene
    foreach ($gene_reps_array[$gene_name] as $one_rep) {
      $one_replicate_pair = [$scatter_pos, $one_rep];

      //save samples and add replicates
      $scatter_one_sample["name"] = $sample_name;
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
  
  array_push($heatmap_series, $heatmap_one_gene);

  $heatmap_one_gene = [];
  $scatter_one_sample = [];
  
}
array_push($table_code_array,"</tr>");
array_push($table_code_array,"</table></div>");

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
$expr_cards = 0;
  if ($expr_cards) {
    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#cards_frame" aria-expanded="true">';
    echo '  <i class="fas fa-sort" style="color:#229dff"></i> Expression Cards';
    echo '</div>';

    echo '<div id="cards_frame" class="row collapse hide" style="padding-top:7px">';

    echo '  <div class="form-group d-inline-flex" style="width: 450px; margin-left:15px">';
    echo '    <label for="card_sel1" style="width: 150px;">Select gene:</label>';
    echo '    <select class="form-control" id="card_sel1">';
        
    foreach ($found_genes as $gene => $kk) {
      echo "      <option value=\"$gene\">$gene</option>";
    }
        
    echo '    </select>';
    echo '  </div>';

    echo '  <div class="d-inline-flex" style="margin:10px">';
    echo '    <span class="circle" style="background-color:#000000"></span> Lowest <2';
    echo '    <span class="circle" style="background-color:#fff"></span> <1';
    echo '    <span class="circle" style="background-color:#ffe999"></span> >=1';
    echo '    <span class="circle" style="background-color:#fb4"></span> >=2';
    echo '    <span class="circle" style="background-color:#ff7469"></span> >=10';
    echo '    <span class="circle" style="background-color:#de2515"></span> >=50';
    echo '    <span class="circle" style="background-color:#b71005"></span> >=100';
    echo '    <span class="circle" style="background-color:#7df"></span> >=200';
    echo '    <span class="circle" style="background-color:#0f0"></span> >=5000';
    echo '    <span class="circle gold"></span> Highest';
    echo '  </div>';

    echo '  <div id="card_code" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"></div>';
    echo '</div>';
    
  }
?>

<style>
  
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
              foreach ($found_genes as $gene => $kk) {
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
  $found_genes = array_keys($found_genes);
  
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
  
  // var db_title = <?php //echo json_encode($dbTitle) ?>;
  // var db_logo = <?php //echo json_encode("$images_path/$db_logo") ?>;
  // var img_path = <?php //echo json_encode($images_path) ?>;
  // var expr_img_array = <?php //echo json_encode($expr_img_array) ?>;
    
    
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
    border-color: #b71005;
    background: -moz-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
    background: -webkit-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
    background: linear-gradient(90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0c320', endColorstr='#ff0000',GradientType=1 );
  }
</style>

