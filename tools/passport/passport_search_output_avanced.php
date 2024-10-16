
<!-- HEADER -->
<?php include_once realpath("../../header.php"); 
include_once realpath("$easy_gdb_path/tools/common_functions.php");?>

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a href="passport_search_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>

<!-- HTML -->
<div class="page_container">


<!-- GET INPUT -->
<?php
  $filters =explode("\n",rtrim($_POST['filters']));
  $file =$_POST['file'];
  
  $filters_dict=[];

  
// create a dictionary with the selected filters where the categories are keys and the selected names are the values
  foreach($filters as $filter){
    if ($filters_dict[explode(" -> ", $filter)[0]]) {
        array_push($filters_dict[explode(" -> ", $filter)[0]], rtrim(explode(" -> ", $filter)[1]));
        } else {
        $filters_dict[explode(" -> ", $filter)[0]] = [];
        array_push($filters_dict[explode(" -> ", $filter)[0]], rtrim(explode(" -> ", $filter)[1]));
    }
  }
?>


<!-- IS BETTER TO SET IN ANOTHER FILE -->
<?php

function search_no_numeric_table($tab_file,$filters,$category){

  $sample_file=$tab_file;

  foreach(array_keys($filters) as $filters_select){
    foreach($category as $index => $cat)
    {
      if($filters_select == $cat)
      {
        // $search= "awk -F \"\\t\" '$$index == \"$cat\" {print}' .".$GLOBALS['passport_path']."/".$GLOBALS['dir_or_file']."/".$GLOBALS['file'].".txt ";
        // $shell_res = shell_exec($search);
        // echo $shell_res;

        $sample_found=[];
        foreach($sample_file as $n_sample)
        {
          $sample=strip_tags(explode("\t",rtrim($n_sample))[$index]); // delete all html tags 

          if(in_array($sample,$filters[$filters_select]))
          {
              array_push($sample_found,$n_sample);
          }
        }
        $sample_file=$sample_found;
      }
    }
  }

  
  return($sample_file);
}

function search_numeric_table($tab_file,$filters,$category){

  $sample_file=$tab_file;

  foreach(array_keys($filters) as $filters_select){
    foreach($category as $index => $cat)
    {
      if($filters_select == $cat)
      {
        foreach($filters[$filters_select] as $value){
          $symbol_value=explode(" ",$value);
          $sample_found=[];

          switch($symbol_value[0])
          {
            case ">":
              foreach($sample_file as $sample)
                {
                  if(explode("\t",rtrim($sample))[$index] > $symbol_value[1])
                  {
                    array_push($sample_found,$sample);
                  }
                }
              break;

            case ">=":
              foreach($sample_file as $sample)
                {
                  if(explode("\t",rtrim($sample))[$index] >= $symbol_value[1])
                  {
                    array_push($sample_found,$sample);
                  }
                }             
              break;

            case "<":
              foreach($sample_file as $sample)
                {
                  if(explode("\t",rtrim($sample))[$index] < $symbol_value[1]  && !is_null(explode("\t",rtrim($sample))[$index]))
                  {
                    array_push($sample_found,$sample);
                  }
                }              
              break;
              
            case "<=":
              foreach($sample_file as $sample)
                {
                  if((explode("\t",rtrim($sample))[$index] <= $symbol_value[1]) && (!is_null(explode("\t",rtrim($sample))[$index])))
                  {
                    array_push($sample_found,$sample);
                  }
                }              
              break;
              
            case "=":
              foreach($sample_file as $sample)
                {
                  if(explode("\t",rtrim($sample))[$index] == $symbol_value[1])
                  {
                    array_push($sample_found,$sample);
                  }
                }
              break;    
          }
          $sample_file=$sample_found;
        }
      }
    }
  }
  return($sample_file);
}
 
function print_search_table($grep_input, $annot_file, $file_name) {
       
    $annot_file = str_replace(" ", "\\ ", $annot_file);
    
    $tab_file = file("$annot_file");
    $first_line = array_shift($tab_file);
    $columns = explode("\t",rtrim($first_line));

    $filters_values=search_numeric_values($grep_input);
    $results_no_numerics=search_no_numeric_table($tab_file,$filters_values['not_numeric'],$columns);
    $results=search_numeric_table($results_no_numerics,$filters_values['is_numeric'],$columns);


      // echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#Annot_file\" aria-expanded=\"true\><i class=\"fas fa-sort\" style=\"color:#229dff\"></i>$file_name</div>";
      
    //   // TABLE BEGIN
    echo"<p id=\"load\" style=\"text-align: center\"><b>Table Loading...</b></p>";

      echo "<div style=\"display:none\" id=\"body\"><table id=\"tblAnnotations\" class=\"tblAnnotations table table-striped table-bordered\">";

      echo "\n<br><h3>Search</h3>\n<div class=\"card bg-light\"><div class=\"card-body\">";
      if(!array_keys($GLOBALS['filters_dict'])[0]==""){ // if search isn't empty
          foreach($GLOBALS['filters_dict'] as $key=>$values){
          echo"<label>".$key." &#10132 "."</label>";
          foreach($values as $value)
          {
            echo "<label>"."  ".$value." ,"."</label>";
          }
          echo"<br>";
        } 
      }
      echo"</div></div><br>\n";

      
      $title = str_replace("_"," ",$file_name);  
      echo "<h1 style=\"text-align:center\">$title</h1><br>";
    


    // //   TABLE HEADER
      echo "<thead><tr>\n";

      foreach ($columns as $index => $col) {
        echo "<th>$col</th>\n";
      }
      echo "</tr></thead>\n";

//       // TABLE BODY
      echo "<tbody>\n";
      foreach($results as $sample_select){
        echo "<tr>";
        foreach(explode("\t",$sample_select) as $n => $data)
        {
          if($n==0)
          {echo "<td><a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$GLOBALS[dir_or_file]&acc_id=$data\" target=\"_blank\">$data</a></td>\n";}
          else
          {echo "<td>$data</td>\n";}
        }
        echo "</tr>\n"; 
      }
      echo "</tbody></table></div><br>\n";      
  } // End of function
  

  
function search_numeric_values($filters_selection)
{
    $is_numeric=[];
    $not_numeric=[];
    $numeric=[];
    
    //separates numerical values and categorical values
    foreach ($filters_selection as $key=>$values) {

      if (preg_match("/^[<>=]/", $values[0])) {
        if(!$is_numeric[$key]){
          $is_numeric[$key]=[];
          foreach($values as $value)
            {array_push($is_numeric[$key],$value);}
        }
      }
      else
      {
        if(!$not_numeric[$key]){
          $not_numeric[$key]=[];
          foreach($values as $value)
            {array_push($not_numeric[$key],$value);}
          }
      }
  }
  $numeric["is_numeric"]=$is_numeric;
  $numeric["not_numeric"]=$not_numeric;
return $numeric;
}
?>



<!-- INCLUDE TABLE  -->
<?php

    include_once realpath("$easy_gdb_path/tools/common_functions.php");

    $file_found=0;
    $all_datasets = get_dir_and_files($passport_path); // find dirs in passport path
    asort($all_datasets);
      
    foreach ($all_datasets as $dir_or_file) {
      if (is_dir($passport_path."/".$dir_or_file)){ // get dirs and print categories  
        // get info from passport.json
        if ( file_exists("$passport_path/$dir_or_file/passport.json") ) {
          $pass_json_file = file_get_contents("$passport_path/$dir_or_file/passport.json");
          $pass_hash = json_decode($pass_json_file, true);

          if($file.".txt" == $pass_hash['passport_file'])
          {
            $file_found=true;
            print_search_table($filters_dict, $passport_path."/".$dir_or_file."/".$file.".txt", $file);
          }
          else
          {
            foreach($pass_hash['phenotype_files'] as $phenotipe){
                if($file.".txt" == $phenotipe)
                {   
                    $file_found=true;
                    print_search_table($filters_dict, $passport_path."/".$dir_or_file."/".$file.".txt", $file);
                }
            }
          }
          if(!$file_found){echo("<i><h2>".$file.".txt"." File not found</h2></i>");}
        }        
      }// close if is_dir         
    }//foreach all_dir
?>
<!-- END TABLE  -->


<br>
<br>
</div>
<!-- END HTML -->

 <!-- Cs -->
<style>
  table.dataTable td,th  {
    max-width: 500px;
    white-space: nowrap;
    overflow: hidden;
    text-align: center;
  }
  
  .td-tooltip {
    cursor: pointer;
  }
  
</style>

<!-- JS DATATABLE -->
<script type="text/javascript">
$(document).ready(function(){
  // //when data table is ready -> show the data table
  $('#body').css("display","block");
  $('#load').remove();

  $(".tblAnnotations").dataTable({
    dom:'Bfrtlpi',
    "oLanguage": {
      "sSearch": "Filter by:"
      },
    buttons: [
      'copy', 'csv', 'excel',
        {
          extend: 'pdf',
          orientation: 'landscape',
          pageSize: 'LEGAL'
        },
      'print', 'colvis'
      ],
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    "drawCallback": function( settings ) {
      $('#body').css("display","inline");
  },
    });

$(".dataTables_filter").addClass("float-right");
$(".dataTables_info").addClass("float-left");
$(".dataTables_paginate").addClass("float-right");

  });
</script>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

