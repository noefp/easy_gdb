<?php include realpath('../../header.php'); ?>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<div>
  <div class="margin-20">
    <a class="float-right" href="/easy_gdb/help/08_gene_expression.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
  </div>
  <br>
  <h3 class="text-center">Gene Expression Viewer</h3>
  
  
  <div class="form margin-20">
    
    <form id="get_expression_form" action="expression_output.php" method="post">
    
    
      <div class="form-group">
<?php

include_once realpath("$easy_gdb_path/tools/common_functions.php");
// include_once '../common_functions.php';

$all_datasets = get_dir_and_files($private_expression_path); // call the function

echo "<label for=\"sel1\">Select Dataset</label>";

if ($expr_menu && file_exists("expression_menu.php") ) {
  echo "<a href=\"expression_menu.php\" class=\"float-right\" style=\"text-decoration: underline;\" target=\"_blank\">Dataset Information</a>";
}

asort($all_datasets);

$dir_counter = 0;

foreach ($all_datasets as $expr_dataset) {
  
  if (is_dir($private_expression_path."/".$expr_dataset)){ // get dirs and print categories
    $dir_counter++;
  }
}

//category organization
if ($dir_counter) {
  
  $dir_hash = array();
  $datasets_array = array();
  
  echo "<select class=\"form-control\" id=\"cat1\" name=\"expr_dir\">";
  
  $dir_counter2 = 0;
  $first_category = "";
    
  //check each dir and file
  foreach ($all_datasets as $dirs_and_files) {
  
    if (is_dir($private_expression_path."/".$dirs_and_files)){ // get dirs and print categories
      $all_dir_datasets = get_dir_and_files($private_expression_path."/".$dirs_and_files); // call the function
      
      $dir_name = str_replace("_"," ",$dirs_and_files);
      echo "<option>$dir_name</option>";
      
      if ($dir_counter2 == 0) {
        $first_category = $dir_name;
      }
      
      //get expression datasets from each dir
      foreach ($all_dir_datasets as $one_dataset) {
        $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$one_dataset);
        $data_set_name = str_replace("_"," ",$data_set_name);
        if ( !preg_match('/\.php$/i', $one_dataset) && !is_dir("$private_expression_path/$dirs_and_files/$one_dataset") && ($one_dataset != "comparator_gene_list.txt") && ($one_dataset != "comparator_lookup.txt") && !preg_match('/\.json$/i', $one_dataset) && file_exists("$private_expression_path/$dirs_and_files/$one_dataset") ) {
          array_push($datasets_array, "<option value=\"$private_expression_path/$dirs_and_files/$one_dataset\">$data_set_name</option>");
        }
      }
      
      sort($datasets_array);
      $dir_hash[$dir_name]=$datasets_array;
      $datasets_array = array();
      
      $dir_counter2++;
    } // end if dir
    
  } //end foreach dir and file
  
  echo   "</select><br>";
  
  // print datasets of first category
  echo "<select class=\"form-control\" id=\"sel1\" name=\"expr_file\">";
  foreach ($dir_hash[$first_category] as $html_option) {
    echo $html_option;
  }
  echo   "</select>";
  
  
} else {
  // no dir organization
  echo "<select class=\"form-control\" id=\"sel1\" name=\"expr_file\">";

  foreach ($all_datasets as $expr_dataset) {
    $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$expr_dataset);
    $data_set_name = str_replace("_"," ",$data_set_name);
  
    if ( !preg_match('/\.php$/i', $expr_dataset) && !is_dir($private_expression_path.'/'.$expr_dataset) && ($expr_dataset != "comparator_gene_list.txt") && ($expr_dataset != "comparator_lookup.txt") && !preg_match('/\.json$/i', $expr_dataset) && file_exists("$private_expression_path/$expr_dataset") ) {
      echo "<option value=\"$private_expression_path/$expr_dataset\">$data_set_name</option>";
    }
  }

  echo   "</select>";
  
}
?>
      </div>
      
      
      <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
    
          <div class="form-group">
            <label for="usr">Find your gene/metabolite by name:</label>
      
            <div class="input-group mb-3">
              <input id="autocomplete_gene" type="text" class="form-control form-control-lg" placeholder="gene/metabolite name">
              <div class="input-group-append">
                <button id="add_gene_btn" class="btn btn-success"><i class="fas fa-angle-double-right" style="font-size:28px;color:white"></i></button>
              </div>
            </div>
      
          </div>
    
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
    
          <label for="InputGenes">Paste a list of gene IDs</label>
<textarea class="form-control" id="InputGenes" rows="8" name="gids">
</textarea>
          <br>
    
        </div>
      </div>
      

      <button class="button btn btn-info float-right" id="btnSend" type="submit" form="get_expression_form" formmethod="post">Get Expression</button>
      
    </form>
    <br>
    <br>
  </div>

</div>

<?php include realpath('../../footer.php'); ?>


<style>
  
  .ui-autocomplete {
    max-height: 160px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }

  * html .ui-autocomplete {
    height: 160px;
  }
  </style>

<script>
  $(document).ready(function () {
    
    
    //call PHP file ajax_get_names_array.php to get the gene list to autocomplete from the selected dataset file
    function ajax_call(expr_file) {
      
      jQuery.ajax({
        type: "POST",
        url: 'ajax_get_names_array.php',
        data: {'expr_file': expr_file},

        success: function (names_array) {
          
          var names = JSON.parse(names_array);
          
          $("#InputGenes").val(names.slice(0, 5).join("\n"));
          $( "#autocomplete_gene" ).autocomplete({
            source: function(request, response) {
              var results = $.ui.autocomplete.filter(names, request.term);
              response(results.slice(0, 15));
            }
          });
        }
      });
      
    }; // end ajax_call
    
    //get first gene list to autocomplete
    first_dataset = $('#sel1').val();
    ajax_call(first_dataset);
    
    // Get dataset genes when changing dataset
    $('#sel1').change(function () {
      selected_dataset = $('#sel1').val();
      
      ajax_call(selected_dataset);
    });

    // Get datasets when changing category
    
    var categories = <?php echo json_encode($dir_hash) ?>;
    
    $('#cat1').change(function () {
      
      selected_category = $('#cat1').val();
      dataset_options = categories[selected_category];
      // console.dir("selected_dataset: "+selected_dataset);
      // console.dir("dataset_options: "+dataset_options);
      
      jQuery('#sel1').html(dataset_options);
      selected_dataset = $('#sel1').val();
      ajax_call(selected_dataset);
      
    });
    
    
    
    $('#add_gene_btn').click(function () {
      var selected_gene = $('#autocomplete_gene').val();
      // alert("selected_gene: "+selected_gene);
      event.preventDefault(); // cancel submission default behavior
      
      var box_val = $('#InputGenes').val();
      
      if ( box_val ) {
        $('#InputGenes').val(box_val+"\n"+selected_gene);
        //alert("full");
      }
      else {
        $('#InputGenes').val(selected_gene);
        //alert("empty");
      }
      
    });
    
    
    $('#get_expression_form').submit(function () {
      var gene_lookup_input = $('#InputGenes').val();
      var gene_count = (gene_lookup_input.match(/.+\n?/g)||[]).length
      
      if (gene_count == 0) {
          alert("No genes were included in the analysis. Gene count: "+gene_count+". Please, add some gene IDs to the input list box.");
          return false;
      }
      //check input genes from gene lookup before sending form
      var max_input = "<?php echo $max_expression_input ?>";
      
      if (!max_input) {
        max_input = 15;
      }
      
      if (gene_count > max_input) {
          alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+gene_count);
          return false;
      }

      return true;
    });

  });
</script>
