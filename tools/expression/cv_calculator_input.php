<?php include realpath('../../header.php'); ?>
<?php include_once realpath("../modal.html");?>

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<div id="dlgDownload">
  <div class="margin-20">
    <a class="float-right" href="/easy_gdb/help/09_expression_comparator.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
  </div>
  <br>
  
  <h3 class="text-center">Coefficient of Variation Calculator</h3><br>
  <div class="form margin-20">
  
  <!-- FORM -->
    <form id="comparator_form" action="cv_calculator_output.php" method="post">
    <br>

    <?php

    include_once realpath("$easy_gdb_path/tools/common_functions.php");
    // include_once '../common_functions.php';

    $all_datasets = get_dir_and_files($expression_path); // call the function


    if ($expr_menu && file_exists("expression_menu.php") ) {
      echo "<a href=\"expression_menu.php\" class=\"float-right\" style=\"text-decoration: underline;\" target=\"_blank\">Dataset Information</a><br>";
    }

    echo "<div class=\"form-group\">";

    asort($all_datasets);


    $dir_counter = 0;

    foreach ($all_datasets as $expr_dataset) {
      
      if (is_dir($expression_path."/".$expr_dataset)){ // get dirs and print categories
        $dir_counter++;
      }
    }

  //category organization
  if ($dir_counter) {
  
  echo "<input style=\"display:none\" name=\"categories\" value=1>"; // if there are categories, set the value to 1
  
  foreach ($all_datasets as $dirs_and_files) {

    if (is_dir($expression_path."/".$dirs_and_files)){ // get dirs and print categories
      $all_dir_datasets = get_dir_and_files($expression_path."/".$dirs_and_files); // call the function

      $dir_name = str_replace("_"," ",$dirs_and_files);
      echo "<div class=\"card\">";
      echo "<div class=\"card-body\" style=\"widht: 100%\">";
      echo "<div class=\"row\"><h4>$dir_name</h4></div>";
      
      echo "<div class=\"row\">";
  
      sort($all_dir_datasets);
  
      foreach ($all_dir_datasets as $expr_dataset) {
        if ( !preg_match('/\.php$/i', $expr_dataset) && !is_dir($expression_path.'/'.$dirs_and_files.'/'.$expr_dataset) && ($expr_dataset != "comparator_gene_list.txt") && ($expr_dataset != "comparator_lookup.txt") && !preg_match('/\.json$/i', $expr_dataset) && file_exists($expression_path.'/'.$dirs_and_files.'/'.$expr_dataset) ) {
          
          $f = fopen("$expression_path/$dirs_and_files/$expr_dataset", 'r');
          $first_line = fgets($f);
          $header = explode("\t", rtrim($first_line));
          array_shift($header);
          $header = array_unique($header);
          fclose($f);
    
          $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$expr_dataset);
          $data_set_name = str_replace("_"," ",$data_set_name);

          $link_name = preg_replace('/\s|\.|\d/', '', $data_set_name);
    
          echo "<div class=\"col-sm-6 col-md-4 col-lg-3\">";
          echo "<input type=\"checkbox\" class=\"form-check-input selectall\" style=\"margin-left:0px\" name=\"$link_name\">";
          echo "<a class=\"collapsed\" href=\"#$link_name\" data-toggle=\"collapse\" aria-expanded=\"false\" style=\"margin-left:15px\" >";
          echo "<i class=\"fa fa-chevron-circle-right\"></i><i class=\"fa fa-chevron-circle-down\"></i> $data_set_name";
          echo "</a>";
          echo "<div id=\"$link_name\" class=\"form-check collapse\">";
          // echo "<div id=\"$link_name\" class=\"form-check collapse show\">";
    
          foreach ($header as $sample) {
              echo "<label class=\"form-check-label\">";
              echo "<input type=\"checkbox\" class=\"form-check-input sample_checkbox\" name=\"sample_names[]\" value=\"$expression_path/$dirs_and_files/$expr_dataset@$sample\">$sample";
              echo "</label>";
              echo "<br>";
          }
          echo "<br>";
          
          echo "</div>";
          echo "</div>";
          echo "<br>";
    
        } // close if
      } //end foreach expr_file
      
      echo "</div>";
      echo "</div>";
      echo "</div>";
      echo "<br>";
      
    }//close if is_dir
    
    
    
  }//end foreach dir 
  
} else {

  echo "<div class=\"card\">";
  echo "<div class=\"card-body\" style=\"widht: 100%\">";
  echo "<div class=\"row\">";
  echo "<input style=\"display:none\" name=\"categories\" value=0>";


  foreach ($all_datasets as $expr_dataset) {
    $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$expr_dataset);
    $data_set_name = str_replace("_"," ",$data_set_name);
    if ( !preg_match('/\.php$/i', $expr_dataset) && !is_dir($expression_path.'/'.$expr_dataset) && ($expr_dataset != "comparator_gene_list.txt") && ($expr_dataset != "comparator_lookup.txt") && !preg_match('/\.json$/i', $expr_dataset) && file_exists("$expression_path/$expr_dataset") ) {
    
      $f = fopen("$expression_path/$expr_dataset", 'r');
      $first_line = fgets($f); // get header
      $header = explode("\t",trim($first_line));
      array_shift($header);
      $header = array_unique($header);
      fclose($f);
    
      $link_name = preg_replace('/\s|\.|\d/', '', $data_set_name);
    
      echo "<div class=\"col-sm-6 col-md-4 col-lg-3\">";
      echo "<input type=\"checkbox\" class=\"form-check-input selectall\" style=\"margin-left:0px\" name=\"$link_name\">";
      echo "<a class=\"collapsed\" href=\"#$link_name\" data-toggle=\"collapse\" aria-expanded=\"false\" style=\"margin-left:15px\" >";
      echo "<i class=\"fa fa-chevron-circle-right\"></i><i class=\"fa fa-chevron-circle-down\"></i> $data_set_name";
      echo "</a>";
      echo "<div id=\"$link_name\" class=\"form-check collapse\">";
      // echo "<div id=\"$link_name\" class=\"form-check collapse show\">";
    
      foreach ($header as $sample) {
          echo "<label class=\"form-check-label\">";
          echo "<input type=\"checkbox\" class=\"form-check-input sample_checkbox\" name=\"sample_names[]\" value=\"$expression_path/$expr_dataset@$sample\">$sample";
          echo "</label>";
          echo "<br>";
      }
      echo "<br>";
      echo "</div>";
      echo "</div>";
      echo "<br>";
      echo "<br>";
    
    }
  } //end foreach
  
  
  echo "</div>";
  echo "</div>";
  echo "</div>";
  echo "<br>";
  
  
} // close else

echo   "</div>";



?>
      <button class="button btn btn-info float-right" id="btnSend" type="submit" form="comparator_form" formmethod="post">Calculate</button>
</form>
 <!-- Fin FORM  -->
      <br>
      <br>
  </div>

</div>

<?php include realpath('../../footer.php'); ?>


<style>
  
  .margin-20 {
    margin: 20px;
  }
  
  [aria-expanded="true"] .fa-chevron-circle-right, 
  [aria-expanded="false"] .fa-chevron-circle-down {
      display:none;
  }
  
  </style>

<script>
  $(document).ready(function () {
    
    // var names = <?php echo json_encode($file_array) ?>;
        
    
    $('#comparator_form').submit(function () {

      // //check input genes from gene lookup before sending form
      // var max_input = "<?php echo $max_expression_input ?>";
      
      //var numberOfChecked = $('input:sample_checkbox:checked').length;
      var numberOfChecked = $('input.sample_checkbox:checked').length;
      
      if (numberOfChecked <= 1 ) {
        // alert("Please, select some samples.");
        $("#search_input_modal").html("Please select two or more samples.");
        $('#no_gene_modal').modal()
        return false;
      }
      
      return true;
    });
    
    
    //select and unselect all checkbox on comparator datasets
    $('.selectall').click(function () {
      //alert(this.name);
      var dataset = this.name;
      //alert( $("#"+dataset+" input").val() );
      if (this.checked) {
        $("#"+dataset+" input").prop('checked', true)
      } else {
        $("#"+dataset+" input").prop('checked', false)
      }
      
    });    
  });
</script>
