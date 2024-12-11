<?php include realpath('../../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript" src="/easy_gdb/js/kinetic-v5.1.0.min.js"></script>


<?php
// Get variables: expression file, and input genes
  $expr_file = $_POST["expr_file"];
  $gene_list = $_POST["gids"];
  $dataset_name_ori = preg_replace('/.+\//',"",$expr_file);
  $dataset_name = preg_replace('/_/'," ",$dataset_name_ori);
  $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
  
  $annot_hash=[];
  //load expression_info.json
  if ( file_exists("$json_files_path/tools/expression_info.json") ) {
    $annot_json_file = file_get_contents("$json_files_path/tools/expression_info.json");
    $annot_hash = json_decode($annot_json_file, true);
  }
?>

  <div class="margin-20">
    <a class="float-right" href="/easy_gdb/help/08_gene_expression.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
  </div>

<a href="/easy_gdb/tools/expression/expression_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<div class="page_container" style="margin-top:20px">
  <br>
<?php include realpath('01_expr_check_dataset_description.php'); ?>

</div> <!-- end of page_container -->
  
  
<?php


$gids = [];
$sample_names = [];
$heatmap_one_gene = [];
$heatmap_series = [];
$scatter_one_sample = [];
$scatter_all_genes = [];
$cartoons_all_genes = [];
$replicates_all_genes = [];

$found_genes = [];
$not_found_genes = [];

$table_code_array = [];


foreach (explode("\n",$gene_list) as $one_gene) {
  $one_gene = rtrim($one_gene);
  
  if (!in_array($one_gene,$gids)) {
    array_push($gids,$one_gene);
  }
}

if ( file_exists("$expr_file") && isset($gids) ) {
  $tab_file = file("$expr_file");
  
  // load annotations
  include realpath('02_expr_load_annotations.php');
  // include realpath('02_expr_load_db_annotations.php');
  
  // array_push($table_code_array,"<div style=\"margin: auto; overflow: scroll;\"><table class=\"table table-striped\" id=\"tblResults\">");
  array_push($table_code_array,"<table class=\"tblAnnotations table table-striped table-bordered\" id=\"tblResults\">");
  
  
  
  $columns = [];
  $replicates = [];
  $average = [];
  $header_printed = 0;
  
  $first_line = array_shift($tab_file);
  $header = explode("\t", rtrim($first_line));
  
  $gene_name_found = 0;
  
  //gets each replicate value for each gene
  foreach ($tab_file as $line) {
    $columns = explode("\t", rtrim($line));
    
    $col_count = 0;
    $gene_name = $columns[0];
    
    
    // gene found in input list
    if ( in_array(strtolower($gene_name), array_map("strtolower", $gids)) ) {
      if (!in_array($gene_name,$found_genes)) {
        array_push($found_genes,$gene_name);
        $gene_name_found = 1;
      }
    }
    
    // gene found in input list but expression matrix gene names has transcript version (.1) and input gene not
    // remevo transcript vesrsion from matrix gene
    $gene_name2 = preg_replace('/\.\d+$/',"",$gene_name);
    if ( in_array(strtolower($gene_name2), array_map("strtolower", $gids)) ) {
      if (!in_array($gene_name,$found_genes)) {
        array_push($found_genes,$gene_name);
        $gene_name_found = 1;
      }
    }
    
    // gene found in input list but expression matrix gene names have not transcript version (.1)
    // add transcript version to matrix gene
    if ( in_array(strtolower("$gene_name.1"), array_map("strtolower", $gids)) ) {
      if (!in_array($gene_name,$found_genes)) {
        array_push($found_genes,$gene_name);
        $gene_name_found = 1;
      }
    }
    
    // if the gene was found
    if ($gene_name_found) {
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
      
      //################################################################################################## ADD header
      //print header with sample names
      if (!$header_printed) {
        
        
        if ($file_database) {
          array_push($table_code_array, "<thead><tr><th>Gene ID</th>");
        }
        else {
          array_push($table_code_array, "<thead><tr><th>".$header[0]."</th>");
        }

        foreach ($replicates as $r_key => $r_value) {
          array_push($table_code_array,"<th>$r_key</th>");
        }

        //------------------------------------- using annotation file
        if ($file_database) {
          $columns = explode("\t", $output_head);
          array_shift($columns);

          foreach ($columns as $col) {
            array_push($table_code_array,"<th>$col</th>");
          }
        }
        
        
        //------------------------------------- using database
        if ($dbconn) {
          
          foreach ($annotTypes as $type) {
            array_push($table_code_array, "<th style=\"min-width:100px\">".$all_annotation_types[$type]." ID</th>");
            array_push($table_code_array, "<th style=\"min-width:200px\">".$all_annotation_types[$type]." Description</th>");
          }
        }
        
        array_push($table_code_array,"</tr></thead><tbody>");
        
        $header_printed = 1;
        $sample_names = array_keys($replicates);
      }
      //##################################################################################################
      
      
      $q_link = "";
      if ($annot_hash[$dataset_name_ori]) {
        if ($annot_hash[$dataset_name_ori]["link"]) {
          if ($annot_hash[$dataset_name_ori]["link"] == "#") {
            array_push($table_code_array,"<tr><td>$gene_name</td>");
          }
          else {
            $q_link = $annot_hash[$dataset_name_ori]["link"];
            $q_link = preg_replace('/query_id/',$gene_name,$q_link);
            array_push($table_code_array,"<tr><td><a href=\"$q_link\" target=\"_blank\">$gene_name</a></td>");
          }
        }
        else {
          if ($file_database){
            $annot_encode = str_replace($annotations_path."/", "", $annot_file);
            array_push($table_code_array,"<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name&annot=$annot_encode\" target=\"_blank\">$gene_name</a></td>");
          }
          else {
            array_push($table_code_array,"<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>");
          }
        }
      }
      else {
        array_push($table_code_array,"<tr><td>$gene_name</td>");
      }
      
      
      
      $scatter_pos = 1;
      
      // print expression average values $r_key is like "Sample1" and $r_value is like [4.4,2.3,8.1]
      foreach ($replicates as $r_key => $r_value) {
        
        $average = 0;
        $zero_values = array_count_values($r_value)['0'];
        $empty_values = count($r_value) - (count(array_filter($r_value)) + $zero_values);
        
        if (count($r_value) == $zero_values) {
          $average = 0;
        }
        else if(count($r_value) == $empty_values-$zero_values) {
          $average = null;
        }
        else if($empty_values) {
          $a_sum = array_sum($r_value);
          $a_reps = count($r_value) - $empty_values;
        
          $average = sprintf("%1\$.2f",$a_sum/$a_reps);
        }
        else {
          
          $a_sum = array_sum($r_value);
          $a_reps = count($r_value);
        
          $average = sprintf("%1\$.2f",$a_sum/$a_reps);
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
        
        
        //save cartoons data
        // echo $positions['cartoons']
        if (($positions['cartoons'] != 0) && $annot_hash) {
        // if ($annot_hash) {
          
          if ($annot_hash[$dataset_name_ori]["cartoons"]) {
            
            if ($cartoons_all_genes[$gene_name] ) {
                $cartoons_all_genes[$gene_name][$r_key] = $average;
            } else {
              $cartoons_all_genes[$gene_name] = [];
              $cartoons_all_genes[$gene_name][$r_key] = $average;
            }
              
          }
        }
        
        
      } //end foreach
      
      
      //################################################################################################## ADD ANNOTATIONS
      if ($file_database) {
        array_push($table_code_array,$annotations_hash_file[$gene_name]);
      }
      else {
        array_push($table_code_array,$annotations_hash2[$gene_name]);
      }
      //##################################################################################################
      
      
      array_push($table_code_array,"</tr>");
      
      array_push($heatmap_series, $heatmap_one_gene);
      
      
      
      $replicates = [];
      $heatmap_one_gene = [];
      $scatter_one_sample = [];
      
      $gene_name_found = 0;
    } // end if gene in input list
    
    
  } // each line, each gene foreach
  array_push($table_code_array,"</tbody></table>");
  // array_push($table_code_array,"</tbody></table></div>");
  
  
  //################################################# ADD ANNOTATIONS
	// Freeing result and closing connection.
  if ($dbconn) {
    pg_free_result($dbRes);
    pg_close($dbconn);
  }
  
  
  
  // if gene not found in input list
  $not_found_genes = implode("\n",array_diff($gids,$found_genes)); 
  
  
} // if expr file exists
?>

  <!-- This message would be displayed when the information in the Json "expression_colors" arrays does not match the size -->
  <div id="color_default" class="alert alert-info" style="display:none"><strong>Info:</strong> The default palette has been selected because the size of the attributes
"expression_colors" in <i>"expression_info.json"</i> do not match !!!</div>



<?php include realpath('01_expr_colors_range.php');

//--------- sets the position of the elements to be displayed-----------
asort($positions);

$first_info=true;
$frame="";

foreach($positions as $key => $value){
  if($value!=0)
  {
    switch($key){
      case 'cards':
        include realpath('03_expr_load_cards_html.php');
        $frame="cards_frame"; 
        break;

      case 'table' :
        include realpath('03_expr_load_avg_table_html.php');
        $frame="avg_table";
        break;

      case 'replicates':
        include realpath('03_expr_load_replicates_html.php');
        $frame="replicates_graph";
        break;

      case 'cartoons':
        include realpath('03_expr_load_cartoons_html.php');
        if($cartoons_files_found) // This variable is located in 03_expr_load_cartoons_html
        {$frame="cartoons_frame";} 
        break;

      case 'heatmap':
        include realpath('03_expr_load_heatmap_html.php');
        $frame="heatmap_graph"; 
        break;

      case 'lines':
        include realpath('03_expr_load_lines_html.php');
        $frame="line_chart_frame";
        break;

      case 'description':
        include realpath('01_expr_load_description.php');
        if($description_files_found) // This variable is located in 01_expr_load_description
        {$frame="description_frame";}
        break;
    }
    if($first_info && $frame!="") // first position turn from hide to show
    {
      $first_info=false;
      echo "<script> $('#$frame').collapse('show')</script>";
    }
  }
} // en sets elements
?>

<!-- </div> old end of page_container-->

<br>

<?php include realpath('../../footer.php'); ?>



<script type="text/javascript">
  
  var sample_array = <?php echo json_encode($sample_names) ?>;
  var samples_found = <?php echo json_encode($sample_names) ?>;
  var heatmap_series = <?php echo json_encode(array_reverse($heatmap_series)) ?>;
  
  var gene_list = <?php echo json_encode($found_genes) ?>;
  var scatter_one_gene = <?php echo json_encode($scatter_all_genes[$found_genes[0]]) ?>;
  var scatter_all_genes = <?php echo json_encode($scatter_all_genes) ?>;
  var cartoons_all_genes = <?php echo json_encode($cartoons_all_genes) ?>;
  // var replicates_one_gene = <?php //echo json_encode($replicates_all_genes[$found_genes[0]]) ?>;
  // var replicates_all_genes = <?php //echo json_encode($replicates_all_genes) ?>;
  var replicates_one_gene = scatter_one_gene;
  var replicates_all_genes = scatter_all_genes;
  
  var db_title = <?php echo json_encode($dbTitle) ?>;
  var db_logo = <?php echo json_encode("$images_path/$db_logo") ?>;
  var img_path = <?php echo json_encode($images_path) ?>;
  var expr_img_array = <?php echo json_encode($expr_img_array) ?>;
  
  var cartoons = <?php echo json_encode($positions['cartoons']) ?>;

  if (cartoons!=0) {
    var imgObj = <?php echo json_encode($jcartoons) ?>;
    var canvas_h = <?php echo json_encode($canvas_h) ?>;
    var canvas_w = <?php echo json_encode($canvas_w) ?>;
    //alert("cartoons_all_genes: "+JSON.stringify(cartoons_all_genes) );
  }
    
  if (gene_list.length == 0) {
    $( "#chart1" ).css("display","none");
    $( "#chart2" ).css("display","none");
    //$( "#dataset_title" ).html("No gene was found in the selected dataset. Please, check gene names.");
    alert("No gene was found in the selected dataset. Please, check gene names.")
  }
  
  var genes_not_found = <?php echo json_encode($not_found_genes) ?>;
  
  if (genes_not_found) {
    alert( "These input genes were not found in the selected dataset:\n\n"+genes_not_found );
  }

</script>


  
<script type="text/javascript" src="cartoons_kinetic.js"></script>
<script src="expression_graphs.js"></script>

<script type="text/javascript">
  
  if (cartoons) {
    canvas = create_canvas(canvas_h,canvas_w);
    draw_gene_cartoons(canvas,imgObj,cartoons_all_genes,gene_list[0],ranges,colors);

     var gene_expr_values = cartoons_all_genes[gene_list[0]];

    for (var sample in gene_expr_values){
      expr_value = gene_expr_values[sample];
       sample_id=sample+"_kj_image";    
       color_rgb=get_expr_color(expr_value,ranges,colors);
    //    $(document.getElementById(sample_id)).html(sample+": "+expr_value).css('color','rgb('+color_rgb+')');
       $(document.getElementById(sample_id)).html(sample+": "+expr_value).css('text-decoration',' double underline').css('text-decoration-color','rgb('+color_rgb+')');
      }
  }
  
</script>

<style>
  #range_color_btn{
/*  height: 50px;*/
  border-color: #b71005;
  background: -moz-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  background: -webkit-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  background: linear-gradient(90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0c320', endColorstr='#ff0000',GradientType=1 );
  }

#tblResults th, td{
  text-align:center;
}  
  
  
</style>
