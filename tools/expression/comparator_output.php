<?php include realpath('../../header.php'); ?>
<?php include_once realpath("../modal.html");?>

<!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
<script src="/easy_gdb/js/apexcharts.min.js"></script>

    <!-- <link rel="stylesheet" href="/easy_gdb/js/DataTables/Select-1.2.6/css/select.dataTables.min.css"> -->


<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/09_expression_comparator.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<!-- <a href="/easy_gdb/tools/expression/comparator_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a> -->
<a class="float-left pointer_cursor " style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<br>
<h1 class="text-center">Comparison results</h1>


<?php
//check if relative gene normalization is enabled and get genes (fold change)
$hk_genes=[];
if ($_POST['denominator_genes']) {
  $hk_genes = explode("\n",$_POST['denominator_genes']);
  $hk_genes = array_filter($hk_genes);
  $hk_genes = array_map('trim', $hk_genes);
}



//check if log2 and conversion to newest version enabled
$fc_log2 = $_POST['fc_log2'];
$to_newest_v = $_POST['newest_v'];

if ($hk_genes) {
    echo '<div class="alert alert-info" role="alert" style="padding-top:10px;padding-bottom:0px;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
      <span aria-hidden="true">&times;</span>
    </button>
        <p style="text-align:center;">Your data are normalized using '.join(", ", $hk_genes).' as reference.</p>';
  if ($fc_log2) {
    echo " Log2 was applied.";
  }
  echo "</div>";
}

// if ($to_newest_v) {

//   // echo "<p> Your gene list was converted to the latest gene version available.</p>";
//   echo '<div class="alert alert-info margin-20" role="alert">
//       <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
//       <span aria-hidden="true">&times;</span>
//     </button>
//         <p>Your gene list was converted to the latest gene version available<p>';

// }

//if conversion to newest version was enabled and the comparator lookup file exist, save gene lookup in hash

if ( file_exists("$json_files_path/tools/comparator_lookup.json") && $to_newest_v) {

    echo '<div class="alert alert-info margin-20" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
      <span aria-hidden="true">&times;</span>
    </button>
        <p>Your gene list was converted to the latest gene version available<p>';

  $lookup_file = file_get_contents("$json_files_path/tools/comparator_lookup.json");
  $lookup_hash = json_decode($lookup_file, true);

  //get conversion from newest version to older ones
  $lookup_reverse_hash = [];
  foreach ($lookup_hash as $old_g => $new_g) {

    // if an older version matches with multiple genes of the newest version
    if ( preg_match('/\;/', $lookup_hash[$old_g]) ) {

      $genes_array = explode(";",$lookup_hash[$old_g]);
      // split and iterate by each one of the new genes
      foreach ($genes_array as $one_new_gene) {

        // save new gene correspondence to old genes, using ; if more than one found
        if (!$lookup_reverse_hash[$one_new_gene]) {
          $lookup_reverse_hash[$one_new_gene] = $old_g;
        } else {
          $lookup_reverse_hash[$one_new_gene] = $lookup_reverse_hash[$one_new_gene].";$old_g";
        }
      }
    } else {
      // save new gene correspondence to old genes, using ; if more than one found
      if (!$lookup_reverse_hash[$new_g]) {
        $lookup_reverse_hash[$new_g] = $old_g;
      } else {
        $lookup_reverse_hash[$new_g] = $lookup_reverse_hash[$new_g].";$old_g";
      }
    }

  }

}

// function to retrieve multiple lookup genes and add them to the input gene list $gids
function process_multiple_genes($gene_string, $other_gene, $gids, $lookup_reverse_hash) {

  $genes_array = explode(";",$gene_string);

  foreach ($genes_array as $one_gene) {
    if (!in_array(strtolower($one_gene),array_map('strtolower', $gids))) {
      array_push($gids,$one_gene);
    }

    $old_gene = $lookup_reverse_hash[$one_gene];

    if ($old_gene) {

      //---------------------------------------------------- multiple gene lookup
      if ( preg_match('/\;/', $old_gene) ) {
        echo "--Multiple older versions found for <b>$one_gene</b> -> $old_gene<br>";

        $genes_array = explode(";",$old_gene);

        foreach ($genes_array as $one_gene_multi2) {
          if (!in_array($one_gene_multi2,$gids)) {
            array_push($gids,$one_gene_multi2);
          }
        }
      }
      //-----------------------------------------------------
      else {
        echo "--Older version found for <b>$one_gene</b> -> $old_gene<br>";

        array_push($gids,$old_gene);
      }
    }// old gene

  }// foreach
  
  return $gids;
}

//#### Resumen search function to get sample names and their dataset file from the input form #######
function display_categories_info($categories_info)
{
    echo '<div class="alert alert-dismissible show mb-3" style="padding-top:20px; background-color:#f0f0f0">';
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';

    echo '<div class="row" style="margin-left:1rem;">';
    
    // calculate grid
    $total_categories = count($categories_info); // total number of categories
    $grid_count = $total_categories > 0 ? max(3, intval(12 / $total_categories)) : 12; 
    $grid = "col-md-" . $grid_count; // grid for displays >=768px. 

    // 1. categories boxes

    $expr_file_count = 0;
    $expr_file_sufix = "";

    foreach ($categories_info as $category => $datasets) {

        $category_header = str_replace("_", " ", $category); 
        
        echo '<div class="' . $grid . ' mt-0 mb-3 filter_card">';
        echo '<div class="card" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
        
        // category header
        echo '<div class="card-header text-black text-center alert-secondary font-weight-bold">' . $category_header . '</div>';
        
        // category body with datasets and samples
        echo '<div class="card-body" style="max-height: 350px; overflow-y: auto; background-color: #f0f0f0;">';
        
        if (!empty($datasets)) {
            // 2. datasets boxes loop
            foreach ($datasets as $dataset_name => $samples) {
                $expr_file_count ++;
                $expr_file_sufix = "D".$expr_file_count;

                $formatted_dataset = str_replace("_", " ", $dataset_name);
                $formatted_dataset = str_replace(".txt", " ", $formatted_dataset);
                
                // dataset box
                echo '<div class="card mb-2 border shadow-sm">';
                
                // dataset header
                // echo '<div class="card-header py-1 text-center alert-secondary text-dark font-weight-bold border-bottom" style="font-size: 0.9rem;">';
                // echo $formatted_dataset;
                // echo '</div>';
                
                // dataset body with samples
                echo '<div class="card-body p-2 " style="background-color: #f0f0f0;">';
                echo '<span class="badge  mr-1 mb-1 alert-primary" style="font-size: 0.9rem;">' . $expr_file_sufix. '</span>';
                echo '<span class="badge mr-1 mb-1 alert-secondary" style="font-size:0.9rem; max-width:100%; white-space:normal;text-align: left">'. $formatted_dataset . ' </span><br>';

                foreach ($samples as $sample) {
                  echo '<span class="badge badge-info mr-1 mb-1" style="max-width:100%; white-space:normal;text-align: left">' . $sample . '</span>';
                }
                echo '</div>'; // .card-body (dataset)
                echo '</div>'; // .card (dataset)
            }
        } else {
            echo '<em class="text-muted small">No hay datasets disponibles.</em>';
        }

        echo '</div>'; // .card-body (categoría)
        echo '</div>'; // .card (categoría)
        echo '</div>'; // .grid-col
    }

    echo '</div></div>';
}
######## end of function display_categories_info ########


//-----------------------------------------------------------------
// get each sample and their dataset and save it in a hash key=dataset-file value=array of experiments from that dataset
  
  $sample_hash = [];
  $found_categories = [];
  $newer_found = 0;

  $categories_info = [];

// if (isset($_POST['sample_names']) && is_array($_POST['sample_names'])) {
//     foreach ($_POST['sample_names'] as $sample) {
//         list($file, $exp) = explode("@", $sample); // file is the dataset path, exp is the sample name

//         $path_array = explode("/", rtrim($file, "/"));
            
//         if (count($path_array) >= 2) {
//             $category = $path_array[count($path_array) - 2];
//             $dataset_name = $path_array[count($path_array) - 1];

//           if (!in_array($exp, $categories_info[$category][$dataset_name] ?? [])) {
//                   $categories_info[$category][$dataset_name][] = $exp;
//           }

//         }
//     }
// }
//-----------------------------------------------------------------

  foreach ($_POST['sample_names'] as $sample) {
    list($file,$exp) = explode("@", $sample);

    if ($_POST['categories']) {
      //echo "Categories $file <br>";
      $path_array = explode("/", rtrim($file));
      $category = $path_array[count($path_array)-2];
      $dataset_name = $path_array[count($path_array) - 1];
      $found_categories[$category]=1;

      // save categories dataset_name and samples(expr) for display_categories_info function
      if (!in_array($exp, $categories_info[$category][$dataset_name] ?? [])) {
        $categories_info[$category][$dataset_name][] = $exp; 
      }
      //echo "Category $category <br>";
    }

    if (isset($sample_hash[$file])) {
      array_push($sample_hash[$file],$exp);
    } else {
      $sample_hash[$file] = [];
      array_push($sample_hash[$file],$exp);
    }
  }

  // check if more than one category was selected
  $category_number = count(array_keys($found_categories));

  if ($category_number > 1) {
    echo "<p class =\"text-center\" style=\"color:red\"><b>WARNING!</b> samples from different categories were selected (".join(", ",array_keys($found_categories) )."). Consider if their comparison make sense</p>";
  }

  display_categories_info($categories_info);

  // get input genes
  $gene_list = $_POST["gids"];
  $gids = [];
  $one_gene2 ="";

  if(isset($gene_list)) {


    //iterate by each gene and add genes with and without isoform version (.1) to the list $gids (e.g. if gids contains gene1.1 add gene1 to the list)
    foreach (explode("\n",$gene_list) as $one_gene) {
      $one_gene = rtrim($one_gene);

      if ($one_gene) {
        array_push($gids,$one_gene);

        if (preg_match('/\.\d+$/',$one_gene)) {
          $one_gene2 = preg_replace('/\.\d+$/',"",$one_gene);
          if (!in_array(strtolower($one_gene2),array_map('strtolower', $gids))) {
            array_push($gids,$one_gene2);
          }
        }
        // if ($one_gene2 && !preg_match('/\.\d+$/',$one_gene2)) {
        //   $one_gene3 = $one_gene2.".1";
        //   if (!in_array($one_gene3,$gids)) {
        //     array_push($gids,$one_gene3);
        //   }
        // }
        if (!preg_match('/\.\d+$/',$one_gene)) {
          $one_gene2 = $one_gene.".1";
          if (!in_array(strtolower($one_gene2),array_map('strtolower', $gids))) {
            array_push($gids,$one_gene2);
          }
        }

      }
    }

    //iterate each gene and look for newer versions 
    foreach ($gids as $one_gene) {

      if ($one_gene) {

        // ############################ Lookup code
        // add newest gene versions to gids
        if ($to_newest_v && $lookup_hash) {

          //Add the newest version of the genes
          if ($lookup_hash[$one_gene]) {

            //---------------------------------------------------- multiple gene lookup

            if ( preg_match('/\;/', $lookup_hash[$one_gene]) ) {

              $genes_string = $lookup_hash[$one_gene];

              // multiple newer versions found
              echo "<b>$one_gene</b>: Multiple newer versions found -> $genes_string <br>";
              $newer_found = 1;

              $gids = process_multiple_genes($genes_string, $one_gene, $gids, $lookup_reverse_hash);
            }
            //-----------------------------------------------------
            else {
              $one_gene2 = $lookup_hash[$one_gene];
              // Newer version found
            // echo'<div class="alert alert-info" role="alert" style="padding-top:10px;padding-bottom:0px;">';
            echo "<b>$one_gene</b>: Newer version found -> $one_gene2 <br>";

              $newer_found = 1;

              if (!in_array($one_gene2,$gids)) {
                array_push($gids,$one_gene2);
              }

              $old_gene = $lookup_reverse_hash[$one_gene2];

              if ($old_gene) {
                //---------------------------------------------------- multiple gene lookup
                if ( preg_match('/\;/', $old_gene) ) {

                  $gids = process_multiple_genes($old_gene, $one_gene2, $gids,$lookup_reverse_hash);

                  // Multiple older versions found
                  echo "-Multiple older versions found for <b>$one_gene2</b> -> $old_gene <br>";
                  $newer_found = 1;
                }
              }// old gene if

            } // end else ;

            //separate each gene
            echo "<br>";

          } else {
            // not found in lookup hash
            //echo "No newer version found for <b>$one_gene</b><br>";

            if ($lookup_reverse_hash[$one_gene]) {
              $old_gene = $lookup_reverse_hash[$one_gene];


              if ( preg_match('/\;/', $old_gene) ) {

                $gids = process_multiple_genes($old_gene, $one_gene, $gids,$lookup_reverse_hash);

                //NEW genes as input, find older versions for old datasets
                if ($category_number > 1) {
                  echo "<b>$one_gene</b>: Multiple older versions found -> $old_gene<br>";
                }
              }
              //-----------------------------------------------------
              else {
                //NEW genes as input, find older versions for old datasets
                if ($category_number > 1) {
                  echo "<b>$one_gene</b>: Older version found -> $old_gene<br>";
                }
                array_push($gids,$old_gene);
              }

            }
          }


        }//end newest and lookup hash


      }// end if gene
    }// end foreach

    // print("<pre>".print_r($gids,true)."</pre>");
    echo "</div>"; //end alert-info

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
$expr_file_counter=0; // expression file counter for sample prefix

// iterate each dataset selected in the comparator input
foreach($sample_hash as $expr_file => $comparator_samples_array) {

  $expr_file_counter++;
  $sample_prefix="D".$expr_file_counter; 

  // check dataset file exists and open it. Get header line and save sample names in header
  if ( file_exists("$expr_file") ) {

    $tab_file = file("$expr_file");

    $first_line = array_shift($tab_file);

    $header = explode("\t", rtrim($first_line));

    foreach ($header as $one_exp) {
    // if header sample is in comparator samples array seleted in the input, save it in full_header array for cre
      if (in_array($one_exp,$comparator_samples_array)) {

        // array_push($full_header_aux,$one_exp); // array with only comparator samples selected   
        array_push($full_header,$sample_prefix."_".$one_exp); // array with only comparator samples selected   
      }
    }


    //gets each replicate value for each gene
    foreach ($tab_file as $line) {
      $columns = explode("\t", rtrim($line));

      $col_count = 0;
      $gene_name = $columns[0]; //the first column of each line is the gene

      // if gene found in input list save it in found_genes hash
      // when datasets have different gene IDs it is possible that genes are not found in some of the selected datasets
      if ( in_array(strtolower($gene_name),array_map('strtolower', $gids)) || ($hk_genes && in_array(strtolower($gene_name),array_map('strtolower', $hk_genes))) ) {

        if ( in_array(strtolower($gene_name),array_map('strtolower', $gids))) {
          $found_genes[$gene_name] = 1; // gene found, save it in found_genes dictionary
        }
        //echo "1 replicates -> $gene_name $sample_name $col <br>";

        // create object with replicates of each sample and gene
        foreach ($columns as $col_count =>$col) {
          // echo $col_count." <br>";

          $sample_name2 = $header[$col_count]; // select sample name
          $sample_name= $sample_prefix."_".$sample_name2;

          //echo "2 replicates -> $gene_name $sample_name $col <br>";

          // if gene found in input list and sample is in comparator samples array and it is not the first column
          // if (in_array(strtolower($gene_name),array_map('strtolower', $gids)) && in_array($sample_name, $comparator_samples_array) && $col_count != 0 ) {
          if (in_array(strtolower($gene_name),array_map('strtolower', $gids)) && in_array($sample_name2, $comparator_samples_array) && $col_count != 0 ) {

              

            //########################################### Lookup code ######################################################
            if ($to_newest_v) {

              // convert old versions to new ones
              if ($lookup_hash[$gene_name] && !preg_match('/\;/', $lookup_hash[$gene_name]) ) {
                $new_gene_v = $lookup_hash[$gene_name];

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
          //########################################### end lookup code ################################


          // ############################### Housekeeping normalization ##############################

          if ($hk_genes) {

            // if ( in_array($gene_name,$hk_genes) && in_array($sample_name, $comparator_samples_array) && $col_count != 0) {
            if ( in_array($gene_name,$hk_genes) && in_array($sample_name2, $comparator_samples_array) && $col_count != 0) {
              
              //echo "hk true1 -> $gene_name";

              if ($hk_replicates[$sample_name][$gene_name]) {
               array_push($hk_replicates[$sample_name][$gene_name], $col);
              } else {
               $hk_replicates[$sample_name][$gene_name] = [];
               array_push($hk_replicates[$sample_name][$gene_name], $col);
              }
            }
            else {

              //########################################### Lookup code #################################
              if ($to_newest_v) {

                // convert old versions to new ones
                $newest_gene = $lookup_hash[$gene_name];

                // echo "comparator samples array<br>";
                // print_r($comparator_samples_array);
                // echo "<br>";

                // get data from old genes if new ones are used as input
                $old_gene = array_search($gene_name, $lookup_hash);

                // echo "hk -> ori: $gene_name new: $newest_gene old:$old_gene sample: $sample_name <br>";

                if ( in_array($newest_gene,$hk_genes) && in_array($sample_name2, $comparator_samples_array) && $col_count != 0) {
                  // echo "hk true new -> $newest_gene $sample_name <br>";

                  if ($hk_replicates[$sample_name][$newest_gene]) {
                   array_push($hk_replicates[$sample_name][$newest_gene], $col);
                  } else  {
                   $hk_replicates[$sample_name][$newest_gene] = [];
                   array_push($hk_replicates[$sample_name][$newest_gene], $col);
                  }
                }
                else if ( in_array($old_gene,$hk_genes) && in_array($sample_name2, $comparator_samples_array) && $col_count != 0) {
                  // echo "hk true old -> $old_gene $sample_name <br>";

                  if ($hk_replicates[$sample_name][$old_gene]) {
                   array_push($hk_replicates[$sample_name][$old_gene], $col);
                  } else {
                   $hk_replicates[$sample_name][$old_gene] = [];
                   array_push($hk_replicates[$sample_name][$old_gene], $col);
                  }
                }


              }
              //################## End Lookup code ##################

            } // end else

            // echo "<br>";
          }
          // ############################### End Housekeeping normalization ##############################

          // $col_count++;
        } // end column foreach
      } // end if in_array


    } //end foreach line
  } // end if expression file exists

} // end foreach sample_hash



if ($hk_genes && $to_newest_v && $newer_found) {

  // echo "<p><b>WARNING!</b> only genes in the newest version are normalized. Genes from older annotation versions with multiple gene matches in the newest version and viceversa should not be considered. In those cases consider to use the newest gene version as input</p>";
  echo '<div class="alert alert-warning" role="alert" style="padding-top:10px;padding-bottom:0px;">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
  <span aria-hidden="true">&times;</span>
  </button>
   <p><b>WARNING!</b> only genes in the newest version are normalized. Genes from older annotation versions with multiple gene matches in the newest version and viceversa should not be considered. In those cases consider to use the newest gene version as input</p></div>';
}


// echo "REPLICATES<br>";
// print_r($replicates);
// echo "<br>";

// echo "HK REPLICATES<br>";
// print_r($hk_replicates);
// echo "<br>";




$full_header = array_unique($full_header); 
$sample_names = array_values($full_header); // reset array indexes


// create average table and its header
array_push($table_code_array,"<table class=\"tblAnnotations table table-striped table-bordered\" id=\"tblResults\" style=\"display:none\">");
array_push($table_code_array,"<thead><tr><th>ID</th>");

foreach ($full_header as $exp_name) {
  // $exp_name = preg_replace('/D\d+_/',"",$exp_name);
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
    if ($lookup_hash[$gene_name] && $found_genes[$gene_name] && !preg_match('/\;/', $lookup_hash[$gene_name]) ) {
      unset($found_genes[$gene_name]);
      $newest_gene = $lookup_hash[$gene_name];
      $found_genes[$newest_gene] = 1;
      continue;
      //$gene_name = $lookup_hash{$gene_name};
    }

  }
}
//###########################################

// echo "Found genes2<br>";
// print_r($found_genes);
// echo "<br>";




$q_link = "";
if ( file_exists("$json_files_path/tools/comparator_link.json") ) {
  $link_json_file = file_get_contents("$json_files_path/tools/comparator_link.json");
  $link_hash = json_decode($link_json_file, true);

  if ($link_hash["link"]) {
    $q_link = $link_hash["link"];
  }
}


// echo "<p>link: $q_link</p>";


$warning_switch = 0;


foreach ($found_genes as $gene_name => $kk) {

  if ($q_link) {
    if ($q_link == "#") {
      array_push($table_code_array,"<tr><td><i><b>$gene_name</b></i></td>");
    }
    else {
      $q_link2 = preg_replace('/query_id/',$gene_name,$q_link);
      array_push($table_code_array,"<tr><td><a href=\"$q_link2\" target=\"_blank\">$gene_name</a></td>");
    }
  }
  else {
   array_push($table_code_array,"<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>");
  }


  $scatter_pos = 1;

  foreach ($sample_names as $sample_name) {

    $gene_reps_array = $replicates[$sample_name]; // get replicates of each sample

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
          $hk_newest_gene = $lookup_hash[$gene_name];


          if ($hk_newest_gene && $hk_replicates[$sample_name][$hk_newest_gene]) {
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_newest_gene]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_newest_gene]);

            //echo "HK genes new: $hk_newest_gene: $sample_name , $hk_sum , $hk_reps <br>";
          } else if ($hk_old_gene && $hk_replicates[$sample_name][$hk_old_gene]) {
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_old_gene]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_old_gene]);

            //echo "HK genes old: $hk_old_gene: $hk_sum , $hk_reps <br>";

          } else if ($hk_replicates[$sample_name][$hk_genename]) {
            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_genename]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_genename]);

            //echo "HK genes as it comes: $hk_genename: $sample_name , $hk_sum , $hk_reps <br>";
          }
          else if ($hk_replicates[$sample_name][$hk_genename]) {
            //echo "HK genes :\ $hk_genename: $sample_name , $hk_old_gene <br>";


            $hk_sum = array_sum($hk_replicates[$sample_name][$hk_newest_gene]);
            $hk_reps = count($hk_replicates[$sample_name][$hk_newest_gene]);

            //echo "HK genes new: $hk_newest_gene: $sample_name , $hk_sum , $hk_reps <br>";

          } else {

            if (!$hk_replicates[$sample_name][$hk_genename]) {
              // echo "------------> $sample_name <br>";
              $warning_switch = 1;
            }

          }


          $hk_total_sum = $hk_total_sum + $hk_sum;
          $hk_total_reps = $hk_total_reps + $hk_reps;

        } else {

          $hk_sum = array_sum($hk_replicates[$sample_name][$hk_genename]);
          $hk_reps = count($hk_replicates[$sample_name][$hk_genename]);

          $hk_total_sum = $hk_total_sum + $hk_sum;
          $hk_total_reps = $hk_total_reps + $hk_reps;

          //echo "HK genes3: $hk_genename: $hk_sum , $hk_reps <br>";
        }
      }

      $hk_ave = 0;

      if ($hk_total_reps >0) {
        $hk_ave = sprintf("%1\$.2f",$hk_total_sum/$hk_total_reps);
      }

      if ($hk_ave != $average && $hk_ave == 0) {
        $hk_ave = 0.001;
        $average = sprintf("%1\$.2f",$average/$hk_ave);
      } else if ($hk_ave == $average) {
        $average = sprintf("%1\$.2f",1);
      } else {
        $average = sprintf("%1\$.2f",$average/$hk_ave);
      }

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
    else 
    // If no replicates are available for a given sample and gene, the corresponding data will be null. (This is necessary for the scatter plot to function correctly for each sample)
    {
      $scatter_one_sample["name"] = $sample_name;
      $scatter_one_sample["data"][] = [$scatter_pos, null];
    }

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
array_push($table_code_array,"</table>");

$samples_found = array_keys($replicates);

  if ($warning_switch) {
    //  echo "<p style=\"color:red\"><b>WARNING!</b> The selected gene for relative normalization did not work in cases were matched multiple genes.</p>";
  echo '<div class="alert alert-danger" role="alert" style="padding-top:10px;padding-bottom:0px;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
      <span aria-hidden="true">&times;</span>
    </button>
        <p style="text-align: center;"><b>WARNING!</b> The selected gene for relative normalization did not work in cases were matched multiple genes.</p></div>';
  }


  include realpath('01_expr_colors_range.php');

if(count($found_genes)!=0)
{
  echo "<center>";
 #####################             Lines             ################################
  include realpath("03_expr_load_lines_html.php");


 #####################             Heatmap             ################################

    include realpath("03_expr_load_heatmap_html.php");



 #####################             Replicates           ################################
?>

    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#replicates_graph" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Replicates
    </div>

    <div id="replicates_graph" class="collapse hide">

      <div id="chart2_frame" class=" expression-container" style="width:100%; padding-top:7px;">
        <div class="form-group d-inline-flex" style="width: 450px;">
          <label for="sel1" style="width: 150px; margin-top:7px">Select gene:</label>
          <select class="form-control" id="sel1">';
              <?php foreach ($found_genes as $gene => $kk) {
                echo "<option value=\"$gene\">$gene</option>";
              }?>
          </select>
         </div>
            <div id="chart2" style="min-height: 365px;"></div>
          </div>
    </div>

</center>

<?php


  // echo implode("\n", $table_code_array);
  $found_genes = array_keys($found_genes);
  // print_r($found_genes);
  // echo "replicates_all_genes<br>";
  // print_r($replicates_all_genes[$found_genes[3]]);
  // echo "<br>";


// ------------------- create dataset header for the datatable and annotations for the lines/bars graph---------------
  $dataset_header=[];
  $dataset_group=[]; // array to group samples by dataset
  $dataset_groups=[]; // array to group all dataset_group

  $header_colors=["#f0f0f0","#ffffff"];
  $color_index = 0;
  // $sample_index = 0;
  $datasets_count = 0;
  $sample_prefix="";

  foreach ($categories_info as $category => $datasets) {
    foreach ($datasets as $dataset_name => $samples) {

    // #### Dataset header for the datatable ####
      $dataset_name = str_replace("_", " ", $dataset_name);
      $dataset_name = str_replace(".txt", " ", $dataset_name);
      
      array_push($dataset_header,'<th title="' . $dataset_name . '" colspan=' . count($samples) . ' style="background-color:' . $header_colors[$color_index%2] . '; text-align: center; border-left: 1px solid #ccc;border-right: 1px solid #ccc;">' .$dataset_name . '</th>');
      $color_index++;

      // #### Datasets labels (annotations) for linea/bars graph ####
      $datasets_count ++;
      $sample_prefix = "D".$datasets_count;
      $dataset_group["x"] = $sample_prefix . "_" . $samples[0]; // first sample of the dataset label
      $dataset_group["x2"] = $sample_prefix . "_" . $samples[count($samples) - 1]; // last sample of the dataset label
      $dataset_group["label"]["style"]["background"] = "rgba(255, 255, 255, 0.6)"; // color and opacity of the dataset label
      $dataset_group["label"]["text"] = $sample_prefix . " ➜ " . $dataset_name; // dataset label
      // $dataset_group["opacity"] = 0.1;
      // $dataset_group["label"]["position"] = 'middle';
      // $sample_index += count($samples);
      array_push($dataset_groups,$dataset_group);
    }

   } // end foreach $categories_info

   
   #####################             datatable           ################################

   $comparator_table=1; // flag to indicate that the table is a comparator table and not a regular expression table
   include realpath("03_expr_load_avg_table_html.php"); 
   
}else
{
  echo '<br><div class="alert alert-danger" role="alert" style="text-align:center">
          No gene was found in the selected dataset
          </div>';
    echo "<script> $('.alert-info').remove() </script>";
}
?>
<br>

<?php

include realpath('../../footer.php');

?>


<script type="text/javascript">

  var sample_array = <?php echo json_encode($sample_names) ?>;
  var samples_found = <?php echo json_encode($samples_found) ?>;
  // reverse heatmap_series to match the order of genes in the table
  var heatmap_series = <?php echo json_encode(array_reverse($heatmap_series)) ?>;

  var dataset_groups = <?php echo json_encode($dataset_groups) ?>;
  // alert(JSON.stringify(dataset_groups));

  var gene_list = <?php echo json_encode($found_genes) ?>;
  var replicates_one_gene = <?php echo json_encode($replicates_all_genes[$found_genes[0]]) ?>;
  var scatter_one_gene = <?php echo json_encode($scatter_all_genes[$found_genes[0]]) ?>;
  var scatter_all_genes = <?php echo json_encode($scatter_all_genes) ?>;
  var replicates_all_genes = <?php echo json_encode($replicates_all_genes) ?>;
  // alert(JSON.stringify(replicates_one_gene));

</script>

<script src="expression_graphs.js"></script>
<script> $('#line_chart_frame').collapse('show')</script>
<style>
  #range_color_btn{
    border-color: #b71005;
    background: -moz-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
    background: -webkit-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
    background: linear-gradient(90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0c320', endColorstr='#ff0000',GradientType=1 );
  }
</style>
