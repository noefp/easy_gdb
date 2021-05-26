<?php include realpath('../header.php'); ?>

<br>

<div id="dlgDownload">
  <h1 class="text-center">Gene Version Lookup</h1>
  <div class="form margin-20">

    <form id="gene_version_lookup">
      <label for="txtGenes">Paste a list of gene IDs</label>
      <textarea name="txtGenes" id="txtGenes" class="form-control" rows="10">
<?php echo "$input_gene_list" ?>
      </textarea>
<br>


<?php
// include_once 'common_functions.php';
include_once realpath("$easy_gdb_path/tools/common_functions.php");

$sps_found = get_dir_and_files($lookup_path); // call the function

echo "<div class=\"form-group\">";
echo  "<label for=\"sel1\">Select Data set</label>";
echo  "<select class=\"form-control\" id=\"sel1\" name=\"lookup_db\">";


foreach ($sps_found as $bdb) {
  if (preg_match('/\.txt$/', $bdb, $match)) {
    $blast_db = str_replace(".txt","",$bdb);
    $blast_db = str_replace("_"," ",$blast_db);
    echo "<option dbtype=\"$match[0]\" value=\"$lookup_path/$bdb\">$blast_db</option>";
  }
}

echo   "</select>";
echo   "</div>";

?>


      <button type="submit" class="btn btn-success float-right" form="gene_version_lookup" formaction="gene_lookup_output.php" formmethod="post">search</button>
    </form>

  </div>
</div>
<br>
<br>
<?php include realpath('../footer.php'); ?>


<style>
  .margin-20 {
    margin: 20px;
  }
</style>


<script>
  $(document).ready(function () {

    $('#gene_version_lookup').submit(function () {

      var gene_lookup_input = $('#txtGenes').val();
      filtered_input = gene_lookup_input.replace(/\n+/g, '\n');
      $('#txtGenes').val(filtered_input);

      var gene_count = (filtered_input.match(/\n/g)||[]).length;
      // alert("gene_lookup_input: "+gene_lookup_input+", gene_count: "+gene_count);

      //check input genes from gene lookup before sending form
      var max_input = "<?php echo $max_lookup_input ?>";
      if (!max_input) {
        max_input = 100000;
      }
      if (gene_count > max_input) {
          alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+gene_count);
          return false;
      }

      return true;
    });

  });
</script>
