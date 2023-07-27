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
$to_newest_v = $_POST['newest_v'];

if ($hk_genes) {
  echo "<p> Your data are normalized using ".join(", ",$hk_genes)." as reference.";
  if ($fc_log2) {
    echo " Log2 was applied.";
  }
  echo "</p>";
}

if ($to_newest_v) {
  echo "<p> Your gene list was converted to the latest gene version available.</p>";
}


if ( file_exists("$expression_path/comparator_lookup.txt") ) {
  $lookup_file = file_get_contents("$expression_path/comparator_lookup.txt");
  $lookup_hash = json_decode($lookup_file, true);
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
      
      // ############################ Lookup code
      // add newest gene versions to gids
      if ($to_newest_v) {
      
        //Add the newest version of the genes 
        if ($lookup_hash{$one_gene}) {

          $one_gene2 = $lookup_hash{$one_gene};
          // echo "$one_gene -> $one_gene2 <br>";

          array_push($gids,$one_gene2);
        }
        
        // get data from old genes if new ones are used as input
        $old_gene = array_search($one_gene, $lookup_hash);
        
        if ($old_gene) {
          array_push($gids,$old_gene);
        }
      }
      ###########################################
      
      array_push($gids,$one_gene);
    }// end foreach
    
  } //end isset
/////////////////////////////////////////////////////////////////echo var_dump($gids) . "<br>";
  
?>

<div class="page_container" style="margin-top:20px">

<?php

$sample_names = [];
$heatmap_one_gene = [];
$heatmap_series = [];
$scatter_one_sample = [];
$scatter_all_genes = [];
$replicates_all_genes = [];
$table_code_array = [];


$columns = [];
$replicates = [];
$hk_replicates = [];
$average = [];

$full_header = [];
$header = [];
$found_genes = [];


// iterate each dataset selected in the comparator input
foreach($sample_hash as $expr_file => $comparator_samples_array) {

// check dataset file exists and open it. Get header line and save sample names in header
  if ( file_exists("$expr_file") ) {
    
    $tab_file = file("$expr_file");
    
    $first_line = array_shift($tab_file);
    
    $header = explode("\t", rtrim($first_line));
    foreach ($header as $one_exp) {
      if (in_array($one_exp,$comparator_samples_array)) {
        array_push($full_header,$one_exp);
      }
    }
    
    // echo "comparator samples array<br>";
    // print_r($comparator_samples_array);
    // echo "<br>";
    
    
//gets each replicate value for each gene
    foreach ($tab_file as $line) {
      $columns = explode("\t", rtrim($line));
      
      $col_count = 0;
      $gene_name = $columns[0];
      
      // if gene found in input list save it in found_genes hash
      // when datasets have different gene IDs it is possible that genes are not found in some of the selected datasets
      if ( in_array($gene_name,$gids) || ($hk_genes && in_array($gene_name,$hk_genes)) ) {
        
        if ( in_array($gene_name,$gids) ) {
          $found_genes[$gene_name] = 1;
        }
        //echo "1 replicates -> $gene_name $sample_name $col <br>";

        // create object with replicates of each sample and gene
        foreach ($columns as $col) {
         
          $sample_name = $header[$col_count];
          
          //echo "2 replicates -> $gene_name $sample_name $col <br>";
          
          if ( in_array($gene_name,$gids) && in_array($sample_name, $comparator_samples_array) && $col_count != 0 ) {
            
            
            //########################################### Lookup code
            if ($to_newest_v) {

              // convert old versions to new ones
              if ($lookup_hash{$gene_name}) {
                $new_gene_v = $lookup_hash{$gene_name};

                if ($replicates[$sample_name][$new_gene_v]) {
                 array_push($replicates[$sample_name][$new_gene_v], $col);
                } else {
                 $replicates[$sample_name][$new_gene_v] = [];
                 array_push($replicates[$sample_name][$new_gene_v], $col);
                }
              } // close lookup hash check
              else {
                // add genes that do not need conversion
                if ($replicates[$sample_name][$gene_name]) {
                 array_push($replicates[$sample_name][$gene_name], $col);
                } else {
                 $replicates[$sample_name][$gene_name] = [];
                 array_push($replicates[$sample_name][$gene_name], $col);
                }
              }
            }
            else {
            
            //echo " replicates in -> $gene_name $sample_name $col <br>";
            
              if ($replicates[$sample_name][$gene_name]) {
               array_push($replicates[$sample_name][$gene_name], $col);
              } else {
               $replicates[$sample_name][$gene_name] = [];
               array_push($replicates[$sample_name][$gene_name], $col);
              }
              
            }
          }
          //########################################### end lookup code
          
          
          
          
          // ############################### Housekeeping normalization
          
          if ($hk_genes) {
            
            if ( in_array($gene_name,$hk_genes) && in_array($sample_name, $comparator_samples_array) && $col_count != 0) {
              //echo "hk true1 -> $gene_name";
              
              if ($hk_replicates[$sample_name][$gene_name]) {
               array_push($hk_replicates[$sample_name][$gene_name], $col);
              } else {
               $hk_replicates[$sample_name][$gene_name] = [];
               array_push($hk_replicates[$sample_name][$gene_name], $col);
              }
            }
            else {
            
            
            
            
              //########################################### Lookup code
              if ($to_newest_v) {

                // convert old versions to new ones
                $newest_gene = $lookup_hash{$gene_name};
                
                
                // echo "comparator samples array<br>";
                // print_r($comparator_samples_array);
                // echo "<br>";
                
                // get data from old genes if new ones are used as input
                $old_gene = array_search($gene_name, $lookup_hash);
                
                // echo "hk -> ori: $gene_name new: $newest_gene old:$old_gene sample: $sample_name <br>";
                
                if ( in_array($newest_gene,$hk_genes) && in_array($sample_name, $comparator_samples_array) && $col_count != 0) {
                  // echo "hk true new -> $newest_gene $sample_name <br>";
            
                  if ($hk_replicates[$sample_name][$newest_gene]) {
                   array_push($hk_replicates[$sample_name][$newest_gene], $col);
                  } else  {
                   $hk_replicates[$sample_name][$newest_gene] = [];
                   array_push($hk_replicates[$sample_name][$newest_gene], $col);
                  } 
                }
                else if ( in_array($old_gene,$hk_genes) && in_array($sample_name, $comparator_samples_array) && $col_count != 0) {
                  // echo "hk true old -> $old_gene $sample_name <br>";
                  
                  if ($hk_replicates[$sample_name][$old_gene]) {
                   array_push($hk_replicates[$sample_name][$old_gene], $col);
                  } else {
                   $hk_replicates[$sample_name][$old_gene] = [];
                   array_push($hk_replicates[$sample_name][$old_gene], $col);
                  }
                }
                
              
              }
              //################## End Lookup code
            
            } // end else
            
            
            
            
            
            
            
            
            
            
            
            // echo "<br>";
          }
          // ############################### End Housekeeping normalization
          
          $col_count++;
        } // end column foreach
      } // end if in_array
      
      
    } //end foreach line
    
  } // end if expression file exists
  
} // end foreach sample_hash

// echo "REPLICATES<br>";
// print_r($replicates);
// echo "<br>";

// echo "HK REPLICATES<br>";
// print_r($hk_replicates);
// echo "<br>";




$full_header = array_unique($full_header);
$sample_names = array_values($full_header);

// create average table and its header
array_push($table_code_array,"<div style=\"width:95%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">");
array_push($table_code_array,"<thead><tr><th>ID</th>");
  
foreach ($full_header as $exp_name) {
  array_push($table_code_array,"<th>$exp_name</th>");
}
array_push($table_code_array,"</tr></thead>");

// echo "Found genes 1<br>";
// print_r($found_genes);
// echo "<br>";
  

//########################################### Lookup code
// remove old gene version ids if they were converted to the newest
if ($to_newest_v) {
foreach ($found_genes as $gene_name => $kk) {

    
    // convert old versions to new ones
    if ($lookup_hash{$gene_name} && $found_genes{$gene_name}) {
      unset($found_genes{$gene_name});
      $newest_gene = $lookup_hash{$gene_name};
      $found_genes{$newest_gene} = 1;
      continue;
      //$gene_name = $lookup_hash{$gene_name};
    }
    
  }
}
//###########################################

// echo "Found genes2<br>";
// print_r($found_genes);
// echo "<br>";

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
  
  foreach ($sample_names as $sample_name) {
    $gene_reps_array = $replicates[$sample_name];

  // get expression average values like "Sample1" and values are like gene => [4.4,2.3,8.1]
  //foreach ($replicates as $sample_name => $gene_reps_array) {
    
    $average = null;
    
    if ($gene_reps_array[$gene_name]) {
      $a_sum = array_sum($gene_reps_array[$gene_name]);
      // echo var_dump($a_sum) . "<br>";
      $a_reps = count($gene_reps_array[$gene_name]);

      $average = sprintf("%1\$.2f",$a_sum/$a_reps);
    }
    
    
    
    
    // ############################### Housekeeping normalization
    if ($hk_genes) {
      $hk_total_sum = 0;
      $hk_total_reps = 0;
      
      foreach ($hk_genes as $hk_genename) {
        
        // #### Lookup code
        // remove old gene version ids if they were converted to the newest
        if ($to_newest_v) {
        
          // get data from old genes if new ones are used as input
          $hk_old_gene = array_search($hk_genename, $lookup_hash);
          $hk_newest_gene = $lookup_hash{$gene_name};
          
          if ($hk_newest_gene && $hk_replicates[$sample_name][$hk_newest_gene]) {
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_newest_gene]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_newest_gene]);
            
            // echo "HK genes new: $hk_newest_gene: $sample_name , $hk_sum , $hk_reps <br>";
          } else if ($hk_old_gene && $hk_replicates[$sample_name][$hk_old_gene]) {
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_old_gene]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_old_gene]);
            
            // echo "HK genes old: $hk_old_gene: $hk_sum , $hk_reps <br>";
            
          } else if ($hk_replicates[$sample_name][$hk_genename]) {
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_genename]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_genename]);
            
            // echo "HK genes as it comes: $hk_genename: $sample_name , $hk_sum , $hk_reps <br>";
          }
          else if ($hk_replicates[$sample_name][$hk_genename]) {
            // echo "HK genes :\ $hk_genename: $sample_name , $hk_old_gene <br>";
            
            
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_newest_gene]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_newest_gene]);
            
            // echo "HK genes new: $hk_newest_gene: $sample_name , $hk_sum , $hk_reps <br>";
            
          }

        
          $hk_total_sum = $hk_total_sum + $hk_sum;
          $hk_total_reps = $hk_total_reps + $hk_reps;
          
        } else {
        
          $hk_sum = array_sum($hk_replicates[$sample_name][$hk_genename]);
          $hk_reps = count($hk_replicates[$sample_name][$hk_genename]);
        
          $hk_total_sum = $hk_total_sum + $hk_sum;
          $hk_total_reps = $hk_total_reps + $hk_reps;
          
          // echo "HK genes3: $hk_genename: $hk_sum , $hk_reps <br>";
        }
      }
      
      $hk_ave = 0;
      
      if ($hk_total_reps >0) {
        $hk_ave = sprintf("%1\$.2f",$hk_total_sum/$hk_total_reps);
      }
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
    // ################################# End Housekeeping normalization
    
    
    
    if ($gene_reps_array[$gene_name]) {
      array_push($table_code_array,"<td>$average</td>");
    } else {
      array_push($table_code_array,"<td>-</td>");
    }
    
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
    if ($gene_reps_array[$gene_name]) {
      
      
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
      
      } // end foreach
    } // end if
    $scatter_pos++;
    
    //save gene and add samples with replicates
    if ($scatter_all_genes[$gene_name]) {
      array_push($scatter_all_genes[$gene_name], $scatter_one_sample );
    } else {
      $scatter_all_genes[$gene_name] = [];
      array_push($scatter_all_genes[$gene_name], $scatter_one_sample );
    }
    
    
    // exception to get data for replicate plot when samples without data because of different gene versions
    if ($replicates[$sample_name]) {
      if ($replicates_all_genes[$gene_name]) {
        array_push($replicates_all_genes[$gene_name], $scatter_one_sample );
      } else {
        $replicates_all_genes[$gene_name] = [];
        array_push($replicates_all_genes[$gene_name], $scatter_one_sample );
      }
      
    }
    
    
    
    
    $scatter_one_sample = [];
  }
  
  array_push($heatmap_series, $heatmap_one_gene);

  $heatmap_one_gene = [];
  $scatter_one_sample = [];
  
}
array_push($table_code_array,"</tr>");
array_push($table_code_array,"</table></div>");

$samples_found = array_keys($replicates);
  
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
  
  // echo "replicates_all_genes<br>";
  // print_r($replicates_all_genes[$found_genes[3]]);
  // echo "<br>";
  
?>

    </div> <!-- avg_table end -->
  
  </div> <!-- data_table_frame end -->
</div>

<br>

<?php include realpath('../../footer.php'); ?>


<script type="text/javascript">
  
  var sample_array = <?php echo json_encode($sample_names) ?>;
  var samples_found = <?php echo json_encode($samples_found) ?>;
  var heatmap_series = <?php echo json_encode(array_reverse($heatmap_series)) ?>;
  
  var gene_list = <?php echo json_encode($found_genes) ?>;
  var replicates_one_gene = <?php echo json_encode($replicates_all_genes[$found_genes[0]]) ?>;
  var scatter_one_gene = <?php echo json_encode($scatter_all_genes[$found_genes[0]]) ?>;
  var scatter_all_genes = <?php echo json_encode($scatter_all_genes) ?>;
  var replicates_all_genes = <?php echo json_encode($replicates_all_genes) ?>;
    
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

