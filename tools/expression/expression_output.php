<?php include realpath('../../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript" src="/easy_gdb/js/kinetic-v5.1.0.min.js"></script>


<?php 
  $expr_file = $_POST["expr_file"];
  $gene_list = $_POST["gids"];
  $dataset_name_ori = preg_replace('/.+\//',"",$expr_file);
  $dataset_name = preg_replace('/_/'," ",$dataset_name_ori);
  $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
  
  $gids = [];
  $one_gene2;
  
  if(isset($gene_list)) {
    
    //$time_start = microtime(true); 
    
    foreach (explode("\n",$gene_list) as $one_gene) {
      $one_gene = rtrim($one_gene);
      
      if (preg_match('/\.\d+$/',$one_gene)) {
        $one_gene2 = preg_replace('/\.\d+$/',"",$one_gene);
        if (!in_array($one_gene2,$gids)) {
          array_push($gids,$one_gene2);
        }
      }
      if ($one_gene2 && !preg_match('/\.\d+$/',$one_gene2)) {
        $one_gene3 = $one_gene2.".1";
        if (!in_array($one_gene3,$gids)) {
          array_push($gids,$one_gene3);
        }
      }
      if (!preg_match('/\.\d+$/',$one_gene)) {
        $one_gene2 = $one_gene.".1";
        if (!in_array($one_gene2,$gids)) {
          array_push($gids,$one_gene2);
        }
      }
      if (!in_array($one_gene,$gids)) {
        array_push($gids,$one_gene);
      }
    }
    
    //print("<pre>".print_r($gids,true)."</pre>");
    
    // $time_end = microtime(true);
    // $execution_time = ($time_end - $time_start);
    // echo '<p><b>Total Execution Time:</b> '.$execution_time.'</p>';
    
    
    //$gids = explode("\n", rtrim($gene_list));
    
    // $gids=array_map(function($row) {
    //   return rtrim($row);
    // },explode("\n",$gene_list));
    
  }
  
?>

  <div class="margin-20">
    <a class="float-right" href="/easy_gdb/help/08_gene_expression.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
  </div>

<a href="/easy_gdb/tools/expression/expression_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<div class="page_container" style="margin-top:20px">
  <br>
<?php
  // ############################################################### DATASET TITLE AND DESCRIPTION
  
  $expr_img_array = [];
  
  if ($dataset_name) {
    echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
  }
  
  if ( file_exists("$expression_path/expression_info.json") ) {
    $annot_json_file = file_get_contents("$expression_path/expression_info.json");
    $annot_hash = json_decode($annot_json_file, true);
    
    if ($annot_hash[$dataset_name_ori]["description"]) {
    
      $desc_file = $annot_hash[$dataset_name_ori]["description"];

      if ( file_exists("$custom_text_path/expr_datasets/$desc_file") ) {
        
        // echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
        
        echo "<h2 style=\"font-size:20px\">$r_key</h2>";
        include("$custom_text_path/expr_datasets/$desc_file");
        echo"<br>";
      }
      // else {
      //   echo "<h1 id=\"dataset_title\" class=\"text-center\">$dataset_name</h1>";
      // }
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
$cartoons_all_genes = [];
$replicates_all_genes = [];

$found_genes = [];

$table_code_array = [];

if ( file_exists("$expr_file") && isset($gids) ) {
  $tab_file = file("$expr_file");
  
  
  //################################################################################################## ADD ANNOTATIONS
  
  // Get annotation types
  include_once realpath ("$conf_path/database_access.php");
  
  $dbconn = 0;
  
  if (getConnectionString()) {
    $dbconn = pg_connect(getConnectionString());
  }
  
  if ($dbconn) {
    include_once("../get_annotation_types.php");
    
  	// load annotation links in hash
  	$external_db_annot_hash;

  	if ( file_exists("$annotation_links_path/annotation_links.json") ) {
  	    $annot_json_file = file_get_contents("$annotation_links_path/annotation_links.json");
  	    $external_db_annot_hash = json_decode($annot_json_file, true);
  	}
    
    
    // Getting all annotation types.
    $query="SELECT annotation_type_id,annotation_type from annotation_type"; // array with annotation type ids

    $res=pg_query($query) or die("Couldn't query database.");

    $annotTypes=pg_fetch_all_columns($res);
  
    $gNamesArr=array_filter(explode("\n",trim($_POST["gids"])),function($gName) {return ! empty($gName);});
  
    $gNameValues=implode(",",array_map(function($input) {if(empty(trim($input))) return ""; else  return "'" . trim(pg_escape_string($input))."'" ;},$gNamesArr));
  
    $query="SELECT searchValues.search_name as \"input\", array_agg( distinct (g.gene_name)) as \"genes\", array_agg(distinct (annotation.annotation_term, annotation.annotation_desc, annotation.annotation_type_id)) \"annot\"
    FROM
    gene g inner join gene_annotation on gene_annotation.gene_id=g.gene_id
    inner join annotation on annotation.annotation_id=gene_annotation.annotation_id
    inner join annotation_type on annotation_type.annotation_type_id=annotation.annotation_type_id
    right join unnest(array[{$gNameValues}]) WITH ORDINALITY AS searchValues(search_name,ord) on search_name=g.gene_name
    group by searchValues.search_name, searchValues.ord
    order by searchValues.ord asc";
  
    $dbRes=pg_query($query) or die('Query failed: ' . pg_last_error());
  
  
    $annotations_hash2;
  
    while($row=pg_fetch_array($dbRes,null, PGSQL_ASSOC)) {
      // Parse gene array returned by database - removing 3 characters in the end and at the beginning.
      $geneEntries=array_map(function($geneCol) { return explode(",",$geneCol);},explode(")\",\"(",substr($row["genes"],3,-3)));

      // Removing \" enclosing the the multi word gene names.
      array_walk($geneEntries,function(&$entry) {$entry[0]=str_replace("\\\"","",$entry[0]);});

      // Get all anotations for this row and create the annotation columns.
      $annotStr="";
      // Parse annotation array returned by database, removed 3 characters in the end and at the beginning. Saved terms, description and annotation type in $annotEntries
      $annotEntries=array_map(function($annotRow) {
        preg_match("/([^,]*),(.+),(\d+)/",$annotRow,$matches);
        return array(0=>$matches[1],1=>$matches[2],2=>$matches[3]);
      },explode(")\",\"(",substr($row["annot"],3,-3)));

      foreach ($annotTypes as $type) {
      $terms_array = [];
      $annots_array = [];
        foreach ($annotEntries as $annot_row) {
          if ($annot_row[2] == $type) {
            // echo "{$annot_row[0]}";
            $q_link = "#";
            $annot_type = $all_annotation_types[$type];
            if ($annot_type == "TAIR10" || $annot_type == "Araport11") {
              $annot_row[0] = preg_replace('/\.\d$/','',$annot_row[0]);
            }
            if ($external_db_annot_hash[$annot_type]) {
              $q_link = $external_db_annot_hash[$annot_type];
              $q_link = preg_replace('/query_id/',$annot_row[0],$q_link);
            }

            array_push($terms_array,"<a href=\"$q_link\" target=\"_blank\">$annot_row[0]</a>");
          } //close if
        } // close foreach annot_row
      
      
        $gene_name = $row["input"];
      
        $annotations_hash2[$gene_name] .= "<td>".implode($terms_array,"; <br>")."</td><td>";
      
        foreach ($annotEntries as $annot_row) {
          if ($annot_row[2] == $type) {
            array_push( $annots_array, str_replace("\\\"","",$annot_row[1]) );
          }
        }
        $annotations_hash2[$gene_name] .= implode($annots_array,"; <br>")."</td>";

      } // close foreach type
    } // end while
  
  } // close if dbconnect
  
  //##################################################################################################
  
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
          
          
          
          //################################################################################################## ADD ANNOTATIONS
          
          if ($dbconn) {
            
            foreach ($annotTypes as $type) {
              array_push($table_code_array, "<th style=\"min-width:100px\">".$all_annotation_types[$type]." ID</th>");
              array_push($table_code_array, "<th style=\"min-width:200px\">".$all_annotation_types[$type]." Description</th>");
            }
          }
          //##################################################################################################
          
          
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
        else {
           //echo "<tr><td>$gene_name</td>";
          array_push($table_code_array,"<tr><td>$gene_name</td>");
        }
        
        
        
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
          
          
          //save cartoons data
          if ($expr_cartoons && $annot_hash) {
            
            if ($annot_hash[$dataset_name_ori]["cartoons"]) {
              //$cartoons_json = $annot_hash[$dataset_name_ori]["cartoons"];
              //cartoons_sk1.json
              
              //echo "<p>gene_name: ".$gene_name." r_key: ".$r_key." average: ".$average."</p>";
              
              if ($cartoons_all_genes[$gene_name] ) {
                  $cartoons_all_genes[$gene_name][$r_key] = $average;
              } else {
                $cartoons_all_genes[$gene_name] = [];
                $cartoons_all_genes[$gene_name][$r_key] = $average;
              }
                
                
                
            }
          
          }
          
          
          
        } //end foreach
        // echo "</tr>";
        
        
        //################################################################################################## ADD ANNOTATIONS
        if ($dbconn) {
        
          array_push($table_code_array,$annotations_hash2[$gene_name]);
        }
        //##################################################################################################
        
        
        array_push($table_code_array,"</tr>");
        
        array_push($heatmap_series, $heatmap_one_gene);
        
        
        
        $replicates = [];
        $heatmap_one_gene = [];
        // $scatter_one_gene = [];
        $scatter_one_sample = [];
      } // end if gene in input list
      
      
    } // each line, each gene foreach
    array_push($table_code_array,"</table></div>");
    
  
  //################################################# ADD ANNOTATIONS
	// Freeing result and closing connection.
  if ($dbconn) {
    pg_free_result($dbRes);
    pg_close($dbconn);
  }
  
} // if expr file exists

?>


  
  
  
  
  
  

<!-- #####################             Lines             ################################ -->
  <center>
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#line_chart_frame" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Lines
    </div>

    <div id="line_chart_frame" class="collapse show" style="width:95%; border:2px solid #666; padding-top:7px">
      

      <div id="lines_frame">
          <button id="lines_btn" type="button" class="btn btn-danger">Lines</button>
          <button id="bars_btn" type="button" class="btn btn-primary">Bars</button>

        <div id="chart_lines" style="min-height: 550px;"></div>
        
      </div>
        
      
      
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
          
            foreach ($found_genes as $gene) {
              echo "<option value=\"$gene\">$gene</option>";
            }
        
        echo '</select>';
      echo '</div>';


      echo '<div class="d-inline-flex" style="margin:10px">';
        echo '<span class="circle" style="background-color:#000000"></span> Lowest <2';
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
    
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
<!-- #####################             Cartoons             ################################ -->

<center>

<?php
  
if ( file_exists("$expression_path/expression_info.json") ) {
  
  if ($annot_hash[$dataset_name_ori]["cartoons"]) {
  
    $cartoon_conf = $annot_hash[$dataset_name_ori]["cartoons"];

    //echo "<p>annot_hash cartoons exists and was found!</p>";

    if ($expr_cartoons && file_exists($expression_path."/$cartoon_conf") ) {
      
      $cartoons_json = file_get_contents($expression_path."/$cartoon_conf");
      
      // echo "<p>annot_hash cartoons_json exists and was found!</p>";
      //var_dump($cartoons_json);

      $jcartoons = json_decode($cartoons_json, true);
  
      $max_w = 100;
      $max_h = 100;
      $max_x = 10;
      $max_y = 10;
      
      foreach($jcartoons["cartoons"] as $img) {
        echo "<img id='".$img["img_id"]."' src='".$images_path."/expr/cartoons/".$img["image"]."' style=\"display:none\">";
    
        if ($img["width"] > $max_w) {
          $max_w = $img["width"];
        }
        if ($img["height"] > $max_h) {
          $max_h = $img["height"];
        }
        if ($img["x"] > $max_x) {
          $max_x = $img["x"];
        }
        if ($img["y"] > $max_y) {
          $max_y = $img["y"];
        }
      } //end foreach
  
      $canvas_w = $max_w + $max_x;
      $canvas_h = $max_h + $max_y;

      echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#cartoons_frame" aria-expanded="true">';
      echo '<i class="fas fa-sort" style="color:#229dff"></i> Expression images';
      echo '</div>';

      echo '<div id="cartoons_frame" class="row collapse hide" style="width:95%; border:2px solid #666; padding-top:7px">';
    
      echo "<div class=\"form-group d-inline-flex\" style=\"width: 450px;\">";
      echo "<label for=\"sel_cartoons\" style=\"width: 150px; margin-top:7px\">Select gene:</label>";
      echo "<select class=\"form-control\" id=\"sel_cartoons\">";
      foreach ($found_genes as $gene) {
        echo "<option value=\"$gene\">$gene</option>";
      }
      echo "</select>";
      echo "</div>";

      echo '<div class="d-inline-flex" style="margin:10px">';
      echo '<span class="circle" style="background-color:#C7FFED"></span> Lowest <1';
      echo '<span class="circle" style="background-color:#CCFFBD"></span> >=1';
      echo '<span class="circle" style="background-color:#FFFF5C"></span> >=2';
      echo '<span class="circle" style="background-color:#FFC300"></span> >=10';
      echo '<span class="circle" style="background-color:#FF5733"></span> >=50';
      echo '<span class="circle" style="background-color:#C70039"></span> >=100';
      echo '<span class="circle" style="background-color:#900C3F"></span> >=200';
      echo '<span class="circle" style="background-color:#581845"></span> >=5000';
      echo '</div>';

      echo "<div class=\"row\">";
    
      echo "<div class=\"col-xs-12 col-sm-12 col-md-8 col-lg-8\">";
        echo "<div class=\"cartoons_canvas_frame\">";
          echo "<div id=\"canvas_div\">";
            echo '<div id=myCanvas>';
              echo "Your browser does not support the HTML5 canvas";
            echo "</div>";
          echo "</div>";
          echo "<br>";
        echo "</div>";
      echo "</div>";
    
        echo "<div class=\"col-xs-12 col-sm-12 col-md-4 col-lg-4\">";
      
        echo "<ul id=\"cartoon_labels\" style=\"text-align:left\">";
        foreach ($cartoons_all_genes[$found_genes[0]] as $sample_name => $ave_value) {
        
          echo "<li class=\"cartoon_values pointer_cursor\" id=\"$sample_name"."_kj_image\">".$sample_name.": ".$ave_value."</li>";
        }
        
        echo "</ul>";
      
        echo "</div>";
    
      echo "</div>";

      echo '</div>';

    }//end cartoons conf
    // else {
    //   echo "<p>cartoons.json file was not found!</p>";
    // }
    
  }//end cartoons hash
  // else {
  //   echo "<p>cartoons hash was not found!</p>";
  // }
  
} //end expression_info.json
// else {
//   echo "<p>expression_info.json was not found!</p>";
// }
?>
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
              foreach ($found_genes as $gene) {
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
  var samples_found = <?php echo json_encode($sample_names) ?>;
  var heatmap_series = <?php echo json_encode(array_reverse($heatmap_series)) ?>;
  
  var gene_list = <?php echo json_encode($found_genes) ?>;
  var scatter_one_gene = <?php echo json_encode($scatter_all_genes[$found_genes[0]]) ?>;
  var scatter_all_genes = <?php echo json_encode($scatter_all_genes) ?>;
  var cartoons_all_genes = <?php echo json_encode($cartoons_all_genes) ?>;
  // var replicates_one_gene = <?php echo json_encode($replicates_all_genes[$found_genes[0]]) ?>;
  // var replicates_all_genes = <?php echo json_encode($replicates_all_genes) ?>;
  var replicates_one_gene = scatter_one_gene;
  var replicates_all_genes = scatter_all_genes;
  
  var db_title = <?php echo json_encode($dbTitle) ?>;
  var db_logo = <?php echo json_encode("$images_path/$db_logo") ?>;
  var img_path = <?php echo json_encode($images_path) ?>;
  var expr_img_array = <?php echo json_encode($expr_img_array) ?>;
  
  var cartoons = <?php echo json_encode($expr_cartoons) ?>;
  
  if (cartoons) {
    var imgObj = <?php echo json_encode($jcartoons) ?>;
    var canvas_h = <?php echo json_encode($canvas_h) ?>;
    var canvas_w = <?php echo json_encode($canvas_w) ?>;
    //alert("cartoons_all_genes: "+JSON.stringify(cartoons_all_genes) );
  }
    
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
  
<script type="text/javascript" src="cartoons_kinetic.js"></script>
<script src="expression_graphs.js"></script>

<script type="text/javascript">
  
  if (cartoons) {
  
    canvas = create_canvas(canvas_h,canvas_w);
    draw_gene_cartoons(canvas,imgObj,cartoons_all_genes,gene_list[0]);
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
  
  .cartoon_values:hover {
    color: red;
  }
  
</style>
