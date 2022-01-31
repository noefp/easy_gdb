<?php include realpath('../header.php'); ?>

<div id="dlgDownload">
  <br>
  <h3 class="text-center">Gene Expression Atlas</h3>
  <div class="form margin-20">
    <form id="get_expression_form" action="expression_output.php" method="post">
      <label for="InputGenes">Paste a list of gene IDs</label>
      <textarea class="form-control" id="InputGenes" rows="8" name="gids">
<?php echo "$expr_input_gene_list" ?>
      </textarea>
      <br>

      <div class="form-group">
<?php

include_once realpath("$easy_gdb_path/tools/common_functions.php");
// include_once '../common_functions.php';

$all_datasets = get_dir_and_files($expression_path); // call the function

echo "<div class=\"form-group\">";
echo  "<label for=\"sel1\">Select Data set</label>";
echo  "<select class=\"form-control\" id=\"sel1\" name=\"expr_file\">";

asort($all_datasets);

foreach ($all_datasets as $expr_dataset) {
  $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$expr_dataset);
  $data_set_name = str_replace("_"," ",$data_set_name);
  echo "<option value=\"$expression_path/$expr_dataset\">$data_set_name</option>";
}

echo   "</select>";
echo   "</div>";

?>
      </div>

      <button class="button btn btn-info float-right" id="btnSend" type="submit" form="get_expression_form" formmethod="post">Get Expression</button>
      </form>
      <br>
      <br>
  </div>

</div>

<?php include realpath('../footer.php'); ?>


<style>
  .margin-20 {
    margin: 20px;
  }
</style>


<script>
  $(document).ready(function () {

    $('#get_expression_form').submit(function () {
      var gene_lookup_input = $('#InputGenes').val();
      var gene_count = (gene_lookup_input.match(/\n/g)||[]).length

      // alert("gene_lookup_input: "+gene_lookup_input+", gene_count: "+gene_count);

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
