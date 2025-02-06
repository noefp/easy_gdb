<!-- HEADER -->
<?php include_once realpath("../../header.php"); 
include_once realpath("$easy_gdb_path/tools/common_functions.php");?>


<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a class="float-left pointer_cursor " style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<!-- <a href="passport_search_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a> -->

<br>


<!-- GET INPUT -->
<?php

$file=array();
$filters_dict=array();
$all_filters=array();
$files_counts=$_POST['counts'];


$passport_dir_name="";

$passport_path_file=$_POST['passport'];

for($i=1;$i<=$files_counts;$i++) {
  $filters[$i] =explode("\t",rtrim($_POST['filters'.$i]));
  $file[$i] =$_POST['file'.$i];
 
  $filters_dict=[];
// create a dictionary with the selected filters where the categories are keys and the selected names are the values
  foreach($filters[$i] as $index => $filter){
    if ($filters_dict[$i][explode(" -> ", $filter)[0]]) {
        array_push($filters_dict[$i][explode(" -> ", $filter)[0]], rtrim(explode(" -> ", $filter)[1]));
        } else {
        $filters_dict[$i][explode(" -> ", $filter)[0]] = [];
        array_push($filters_dict[$i][explode(" -> ", $filter)[0]], rtrim(explode(" -> ", $filter)[1]));
    }
    $all_filters[$file[$i]]=$filters_dict[$i];
  }
} 
?>
<!-- End Get Input -->


<!------ MAIN FUNCTION ----------------->
<!-- <div class="page_container"></div> -->

<?php

  include_once realpath("$easy_gdb_path/tools/common_functions.php");

    $file_found=0;
    // $all_datasets = get_dir_and_files($passport_path); // find dirs in passport path
    // asort($all_datasets);
    // if ($all_datasets)  
    // foreach ($all_datasets as $dir_or_file) {
    //   if (is_dir($passport_path."/".$dir_or_file)){ // get dirs and print categories  
    //     // get info from passport.json
    if ( file_exists("$passport_path_file/passport.json") ) {
      $pass_json_file = file_get_contents("$passport_path_file/passport.json");
      $pass_hash = json_decode($pass_json_file, true);
      $unique_link=$pass_hash['acc_link'];
      $results=[];
      $read_file_all=[];
      $acc_link_array=[];
      $i=[];

      search_info($all_filters,$file_name);
      $pass_dir_name=str_replace($passport_path,"",$passport_path_file);
      $pass_dir_name=str_replace("/","",$pass_dir_name);


      foreach($all_filters as $file => $filters_dict){
        $file_found=false;
        foreach($pass_hash['phenotype_files'] as $phenotipe){
            if($file.".txt" == $phenotipe)
            {   
                $file_found=true;
                $file_content=read_files($passport_path_file."/".$file.".txt");
                $results[$file] = search_results($filters_dict,$file_content);
                $acc_link_array[$file] =acc_link_array($results[$file],$file_content,$unique_link);
                $read_file_all[$file]=$file_content;
            }
        }
        if(!$file_found){echo("<i><h2>".$file.".txt"." File not found</h2></i>");}
      }
      $common_search=acc_link_common_array($acc_link_array);
      natsort($common_search);


      if(!empty($common_search))
      {
        //--------------------results list: ------------------------------------------------------------
        echo '<div class="alert alert-primary" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
        <span aria-hidden="true">×</span>
        </button>
        <strong style="justify-content:center; display:flex">'.$unique_link.' Results: </strong>';
        echo '<body>
        <ul class="acc_link_list" style="justify-content:center;display:flex;flex-wrap:wrap;">';
        foreach($common_search as $index => $acc_name)
        {echo "<li style=\"display:inline; margin-right:20px;\"><a class=\"pointer_cursor\" href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$pass_dir_name&acc_id=$acc_name\" target=\"_blank\">$acc_name</a></li>";}
        echo"</lu></body>";
        echo "</div>";
        //---------------------------------------------------------------------------------------

        print_search_passport_table($common_search,$passport_path_file."/".$pass_hash['passport_file'],$pass_hash['passport_file']);
        print_search_phenotype_table($common_search,$read_file_all,$results);
        // print_r($common_search);
      }
      else{
        echo '<div class="alert alert-warning" style="text-align:center">Results not found</div>';
      }
}
?>
<!-- END MAIN  -->



<!-- IS BETTER TO SET IN ANOTHER FILE -->
<!--  ---FUNCTIONS DECLARATION --- --------------------->
<?php

function read_files($annot_file){

  $tab_file = file("$annot_file");
  $first_line = array_shift($tab_file);
  $columns = explode("\t",rtrim($first_line));

  $file_result=[];
  $file_result['tab_file']=$tab_file;
  $file_result['columns']=$columns;

  return $file_result;  
}


function search_results($grep_input, $file) {
  //     $annot_file = str_replace(" ", "\\ ", $annot_file);
      $tab_file=$file['tab_file'];
      $columns=$file['columns'];
  
      $filters_values=search_numeric_values($grep_input);
      $results_no_numerics=search_no_numeric_table($tab_file,$filters_values['not_numeric'],$columns);
      $results=search_numeric_table($results_no_numerics,$filters_values['is_numeric'],$columns);
      return $results;
}

 
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



function search_info($filters_dict)
{
  $info="Here are the filters selected for the search.\nThis search uses the AND method for filtering.\nThe tables shown below meet all the selected characteristics.";

  echo '<br><div class="alert alert-dismissible show" style="background-color:#f0f0f0">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
        <span aria-hidden="true">&times;</span>
      </button>';

    echo"<lable style=\"margin:5px\" class=\"info_icon td-tooltip\" title=\"$info\">i</lable>";
    echo "<h3 style=\"display:inline\"><i>Search</i></h3>";

  echo '<table class="table table-bordered table-sm" style="width:100%;">';

  echo "<thead><tr>";
  foreach($filters_dict as $file => $file_filters){
    $file_column=str_replace("_"," ",$file);
      echo "<th><i>$file_column</i></th>";
  }
  echo "</tr></thead>";
  echo "<tbody><tr>";
  foreach($filters_dict as $file => $file_filters){
      echo "<td>";
      if (!empty($file_filters)) {
          foreach($file_filters as $key => $values){
            if ($key != "") {
              echo "<b>".$key." &#10132; "."</b>";
              foreach($values as $value){
                  echo $value.", ";
              }
            }
              echo "<br>";
          }
      }
      echo "</td>";
  }
  echo "</tr></tbody>";
  echo "</table></div>";
  echo "<br>\n";
}


function acc_link_array($results,$file,$unique_link)
{
  $columns = $file['columns'];

  $acc_link=[];

    foreach ($columns as $index => $col) {
      // find column index for unique identifier that will link to accession info
      if ($unique_link == $col) {
        $acc_linck_col= $index;
        break;  
      }
  } //close foreach


  foreach($results as $sample_select){
      $data_array=(explode("\t",$sample_select));
      array_push($acc_link,$data_array[$acc_linck_col]);
  }

 //array_unique Note that keys are preserved
  return array_unique($acc_link);
}


function acc_link_common_array($acc_links_array){

  $file_names=array_keys($acc_links_array);
  $files_count=count($file_names);

  $first_array=$acc_links_array[$file_names[0]];
  foreach($first_array as $index => $acc_array ){
    for($i=1;$i<$files_count;$i++)
    {
      if (!in_array($acc_array,$acc_links_array[$file_names[$i]]))
      {
        unset($first_array[$index]);
        break;

      }
    }
  }
  return($first_array);
}



function print_search_phenotype_table($acc_link_common_array,$annot_files,$search_result) {

  echo "<h2 style=\"text-align:center\">Phenotypes Results </h2>";

  // $pass_dir_name=str_replace($GLOBALS['passport_path'],"",$GLOBALS['passport_path_file']);



  foreach($annot_files as $file_name => $file_data)
  {
    $title=str_replace("_"," ",$file_name);
    echo "<div id=\"$file_name\" class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#body_$file_name\" aria-expanded=\"true\"><i class=\"fas fa-table\" style=\"color:#229dff\"></i> $title table <i class=\" fas fa-sort\" style=\"color:#229dff\"></i></div>";

      // TABLE BEGIN

    echo "<div class=\"body collapse\" style=\"display:hide\" id=\"body_$file_name\"><table id=\"table_$file_name\" class=\"tblAnnotations table table-striped table-bordered\" style=\"display:none\">";

    echo "<div id=\"load_$file_name\" class=\"loader\"></div>"; // loading icon

// //     // //   TABLE HEADER
      echo "<thead><tr>\n";
      $field_number = -1;
      foreach ($file_data['columns'] as $index => $col) {
        echo "<th>$col</th>";

        // find column index for unique identifier that will link to accession info
        if ($GLOBALS['unique_link'] == $col) {
          $field_number = $index;
        }
      } //close foreach
      echo "</tr></thead>";

// //       // TABLE BODY
      echo "<tbody>\n";
      foreach($search_result[$file_name] as $sample_select){
      if(in_array(explode("\t",$sample_select)[$field_number],$acc_link_common_array))
      {
        echo "<tr>";
        foreach(explode("\t",$sample_select) as $index => $data)
        {
          if ($index == $field_number) 
          {echo "<td><a class=\"pointer_cursor\" href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$GLOBALS[pass_dir_name]&acc_id=$data\" target=\"_blank\">$data</a></td>\n";
        //  {echo "<td>$data</td>"; 
          }else{echo "<td>$data</td>\n";}
        }
        echo "</tr>\n"; 
      }
    }
      echo "</tbody></table><br><br></div>";      
  }  
} // End of function

function print_search_passport_table($common_search,$root_passport_file,$passport_file_name)
{
  if (!file_exists($root_passport_file))
  {
    return -1;
  }
  echo "<br><h2 style=\"text-align:center\">Passport Results</h2>";

  $file_name=str_replace(".txt","",$passport_file_name);
  $title=str_replace("_"," ",$file_name);

  $read_passport_file=read_files($root_passport_file);

  // $pass_dir_name=str_replace($GLOBALS['passport_path'],"",$GLOBALS['passport_path_file']);

  echo "<div id=\"$file_name\" class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#body_$file_name\" aria-expanded=\"true\"><i class=\"fas fa-table\" style=\"color:#229dff\"></i> $title table <i class=\" fas fa-sort\" style=\"color:#229dff\"></i></div>";

    // TABLE BEGIN
  echo "<div class=\"body collapse\" style=\"display:hide\" id=\"body_$file_name\"><table id=\"table_$file_name\" class=\"tblAnnotations table table-striped table-bordered\" style=\"display:none\">";

    echo "<div id=\"load_$file_name\" class=\"loader\"></div>"; // loading icon
    echo "<thead><tr>\n";
    $field_number = -1;
    foreach ($read_passport_file['columns'] as $index => $col) {
      echo "<th>$col</th>";

      // find column index for unique identifier that will link to accession info
      if ($GLOBALS['unique_link'] == $col) {
        $field_number = $index;
      }
    } //close foreach
    echo "</tr></thead>";

// //       // TABLE BODY
    echo "<tbody>\n";
    foreach($read_passport_file['tab_file'] as $sample_select){
    if(in_array(explode("\t",$sample_select)[$field_number],$common_search))
    {
      echo "<tr>";
      foreach(explode("\t",$sample_select) as $index => $data)
      {
        if ($index == $field_number) 
        {echo "<td><a class=\"pointer_cursor\" href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$GLOBALS[pass_dir_name]&acc_id=$data\" target=\"_blank\">$data</a></td>\n";
      //  {echo "<td>$data</td>"; 
        }else{echo "<td>$data</td>\n";}
      }
      echo "</tr>\n"; 
    }
  }
    echo "</tbody></table><br><br></div><br>";      
}  

?>

<!-- End functions -->


<!--  CSS -->
<style>

  table.dataTable td,th  {
    
    /* max-width: 500px; */
    overflow: hidden;
    white-space: nowrap;
    text-align: center;

  }
 
  .td-tooltip {
    cursor: pointer;
  }

  .btn:hover{
    background-color:dodgerblue;
  }
  
</style>
<!-- End CSS -->


<!------------------- JS DATATABLE -------------------------------------------------------------------------------------->
<script src="../../js/datatable.js"></script>
<script type="text/javascript">

$(document).ready(function(){

    $(".collapse").on('shown.bs.collapse', function(){
          var id=$(this).attr("id");
          id=id.replace("body_","");
          // alert(id);


      // //when data table is ready -> show the data table
      $('#table_'+id).css("display","table");
      $('#load_'+id).remove();

       datatable('#table_'+id,id);

  });

});

</script>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>
