<?php include realpath('../../header.php'); ?>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<div id="dlgDownload">
  <br>
  <h3 class="text-center">Expression Comparator</h3>
  <div class="form margin-20">
    <form id="comparator_form" action="comparator_output.php" method="post">
    <br>
      
    <div class="row">
      <div class="col-sm-6 col-md-6 col-lg-6">

        <div class="form-group">
          <label for="usr">Find your gene/metabolite by name:</label>

          <div class="input-group mb-3">
            <div class="input-group-prepend" style="width:48px">
              <button id="add_housekeeping_btn" class="btn btn-success" style="width:48px"><i class="fas fa-angle-double-down" style="font-size:28px;color:white;"></i></button>
            </div>
            <input id="autocomplete_gene" type="text" class="form-control form-control-lg" placeholder="gene/metabolite name">
            <div class="input-group-append">
              <button id="add_gene_btn" class="btn btn-success"><i class="fas fa-angle-double-right" style="font-size:28px;color:white"></i></button>
            </div>
          </div>

          <label for="InputGenes" style="width:100%">
            Paste a gene ID or list of gene IDs to be used for fold change calculation.
            <!-- <span class="form-check-label float-right">
              <input type="checkbox" class="form-check-input" name="fc_log2" value=1> Apply log2
            </span> -->
          </label>

<textarea class="form-control" id="housekeeping_input" rows="3" name="denominator_genes">
</textarea>
            <span class="form-check-label float-right">
              <input type="checkbox" class="form-check-input" name="fc_log2" value=1> Apply log2
            </span>
          
        </div>

      </div>
      <div class="col-sm-6 col-md-6 col-lg-6">

        <label for="InputGenes">Paste a list of query gene IDs</label>
<textarea class="form-control" id="InputGenes" rows="8" name="gids">
</textarea>
        <br>

      </div>
    </div>
    
<?php

include_once realpath("$easy_gdb_path/tools/common_functions.php");
// include_once '../common_functions.php';

$all_datasets = get_dir_and_files($expression_path); // call the function


if ($expr_menu && file_exists("expression_menu.php") ) {
  echo "<a href=\"expression_menu.php\" class=\"float-right\" style=\"text-decoration: underline;\" target=\"_blank\">Dataset Information</a>";
}

echo "<div class=\"form-group\">";
echo "<h4>Select samples</h4>";

echo "<div class=\"card\">";
  // echo "<div class=\"card-body\" style=\"column-count: 4\">";
echo "<div class=\"card-body\" style=\"widht: 100%\">";
echo "<div class=\"row\">";

asort($all_datasets);

foreach ($all_datasets as $expr_dataset) {
  $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$expr_dataset);
  $data_set_name = str_replace("_"," ",$data_set_name);
  if ( !preg_match('/\.php$/i', $expr_dataset) && !is_dir($expression_path.'/'.$expr_dataset) && ($expr_dataset != "comparator_gene_list.txt") && !preg_match('/\.json$/i', $expr_dataset) && file_exists("$expression_path/$expr_dataset") ) {
    
    $f = fopen("$expression_path/$expr_dataset", 'r');
    $first_line = fgets($f);
    $header = explode("\t", rtrim($first_line));
    array_shift($header);
    $header = array_unique($header);
    fclose($f);
    
    $link_name = preg_replace('/\s|\.|\d/', '', $data_set_name);
    
    echo "<div class=\"col-sm-6 col-md-4 col-lg-3\">";
    echo "<a class=\"collapsed\" href=\"#$link_name\" data-toggle=\"collapse\" aria-expanded=\"false\">";
    echo "<i class=\"fa fa-chevron-circle-right\"></i><i class=\"fa fa-chevron-circle-down\"></i> $data_set_name";
    echo "</a>";
    echo "<div id=\"$link_name\" class=\"form-check collapse\">";
    // echo "<div id=\"$link_name\" class=\"form-check collapse show\">";
    
    foreach ($header as $sample) {
        echo "<label class=\"form-check-label\">";
        echo "<input type=\"checkbox\" class=\"form-check-input\" name=\"sample_names[]\" value=\"$expression_path/$expr_dataset@$sample\">$sample";
        echo "</label>";
        echo "<br>";
    }
    echo "</div>";
    echo "</div>";
    echo "<br>";
    
  }
}

echo   "</div>";
echo   "</div>";
echo   "</div>";
echo   "</div>";


// read gene list file for autocompletion function
$file_array = array();

if ( file_exists($expression_path."/comparator_gene_list.txt") ) {
  $file_array2 = file($expression_path."/comparator_gene_list.txt");
  // $first_line = array_shift($file_array2);
  
  foreach ($file_array2 as $line) {
    $gene_name = rtrim($line);

    array_push($file_array,$gene_name);
  }
  
  
  
}



?>

      

      <button class="button btn btn-info float-right" id="btnSend" type="submit" form="comparator_form" formmethod="post">Compare</button>
      </form>
      <br>
      <br>
  </div>

</div>

<?php include realpath('../../footer.php'); ?>


<style>
  
  .margin-20 {
    margin: 20px;
  }
  
  .ui-autocomplete {
    max-height: 160px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }

  * html .ui-autocomplete {
    height: 160px;
  }
  
  [aria-expanded="true"] .fa-chevron-circle-right, 
  [aria-expanded="false"] .fa-chevron-circle-down {
      display:none;
  }
  
  </style>

<script>
  $(document).ready(function () {
    
    var names = <?php echo json_encode($file_array) ?>;
      
      $("#InputGenes").val(names.slice(0, 5).join("\n"));
      $( "#autocomplete_gene" ).autocomplete({
        source: function(request, response) {
          var results = $.ui.autocomplete.filter(names, request.term);
          response(results.slice(0, 15));
        }
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
    
    $('#add_housekeeping_btn').click(function () {
      var selected_gene = $('#autocomplete_gene').val();
      // alert("selected_gene: "+selected_gene);
      event.preventDefault(); // cancel submission default behavior
      
      var box_val = $('#housekeeping_input').val();
      
      if ( box_val ) {
        $('#housekeeping_input').val(box_val+"\n"+selected_gene);
        //alert("full");
      }
      else {
        $('#housekeeping_input').val(selected_gene);
        //alert("empty");
      }
      
    });
    
    
    $('#comparator_form').submit(function () {
      var gene_lookup_input = $('#InputGenes').val();
      var gene_count = (gene_lookup_input.match(/\w+\n*/g)||[]).length

      //alert("gene_lookup_input: "+gene_lookup_input+", gene_count: "+gene_count);

      //check input genes from gene lookup before sending form
      var max_input = "<?php echo $max_expression_input ?>";
      
      if (!max_input) {
        max_input = 15;
      }
      
      if (gene_count > max_input) {
          alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+gene_count);
          return false;
      }

      if (gene_count == 0) {
          alert("No genes were included in the analysis. Gene count: "+gene_count+". Please, add some gene IDs to the input list box.");
          return false;
      }

      return true;
    });

  });
</script>