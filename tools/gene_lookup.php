<?php
  if (isset($_POST['ajax_gene_lookup']) && $_POST['ajax_gene_lookup'] == '1') {
    $file = $_POST['file'];
    $second_line = get_second_line($file);
    $data_set_name = preg_replace('/\.[a-z]{3}$/', "", basename($file));
    $data_set_name = str_replace("_", " ", $data_set_name);

    echo "<div id=\"color_default\" class=\"alert alert-primary\" role=\"alert\" style=\"display: block;\">";
    echo "<div class=\"card-body\" style=\"padding:0px;text-align: center\">";
    echo "Example genes for <strong>$data_set_name</strong>:<br>$second_line";
    echo "</div></div>";
    exit;
  }
?>

<?php include realpath('../header.php'); ?>
<?php include realpath('modal.html'); ?>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/06_gene_lookup.php" target="_blank">
    <i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help
  </a>
</div>
<br>

<div>
  <h1 class="text-center">Gene Version Lookup <i class="fas fa-search" style="color:#555"></i></h1>
  <br>

  <div id="gene-version-container" class="form margin-20" style="margin:auto; max-width:900px">

    <?php
      function get_second_line($file_path) {
        $lines = file($file_path);
        return $lines[1] ?? 'No second line available';
      }
    ?>

    <?php 
      include_once realpath("$easy_gdb_path/tools/common_functions.php");
      $sps_found = get_dir_and_files($lookup_path);
      $num_files = count($sps_found);
      echo "<div id=\"color_error\"></div>";
    ?>

    <form id="gene_version_lookup">
    <label for="txtGenes">Paste a list of gene IDs</label>
    <textarea name="txtGenes" id="txtGenes" class="form-control" rows="10"><?php echo "$input_gene_list" ?></textarea>
    <br>


    <?php
      include_once realpath("$easy_gdb_path/tools/common_functions.php");

      $sps_found = get_dir_and_files($lookup_path); // call the function
      echo "<div class=\"form-group\">";
      echo  "<label for=\"sel1\">Select Dataset</label>";
      echo "<select class=\"form-control\" id=\"sel1\" name=\"lookup_db\" onchange=\"loadExampleGenes(this.value)\">";

      foreach ($sps_found as $bdb) {
        if (preg_match('/\.txt$/', $bdb, $match)) {
          $blast_db = str_replace(".txt","",$bdb);
          $blast_db = str_replace("_"," ",$blast_db);
          echo "<option dbtype=\"$match[0]\" value=\"$lookup_path/$bdb\">$blast_db</option>";
        }
      }

      echo "</select>";
      echo "</div>";

    ?>


      <button type="submit" class="btn btn-info float-right" form="gene_version_lookup" formaction="gene_lookup_output.php" formmethod="post">Search</button>
    </form>
<br>
  </div>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script>
$(document).ready(function () {
  $('#gene_version_lookup').submit(function () {
    var gene_lookup_input = $('#txtGenes').val().trim();
    
    var splitted = gene_lookup_input.split(/[\s;,:]+/);
    splitted = splitted.filter(function(el){ return el !== ""; });
    
    var gene_count = splitted.length;

    var max_input = "<?php echo $max_lookup_input ?>";
    if (!max_input) max_input = 10000;

    if (!gene_lookup_input) {
      $("#search_input_modal2").html("The gene list is empty.");
      $('#no_gene_modal2').modal();
      return false;
    }
    else if (gene_lookup_input.length <= 3) {
      $("#search_input_modal2").html("Input is too short, please provide a longer term to search.");
      $('#no_gene_modal2').modal();
      return false;
    }

    if (gene_count > max_input) {
      $("#search_input_modal2").html(
        "A maximum of " + max_input + " sequences can be provided as input, your input has <strong>" + gene_count + "</strong>\."
      );
      $('#no_gene_modal2').modal();
      return false;
    }

    return true;
  });

  var defaultDataset = $('#sel1').val();
  if (defaultDataset) {
    loadExampleGenes(defaultDataset);
  }
});
</script>

<script>
  function loadExampleGenes(filePath) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "", true); // se manda al mismo fichero PHP
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      document.getElementById("color_error").innerHTML = xhr.responseText;
    }
  };
  xhr.send("ajax_gene_lookup=1&file=" + encodeURIComponent(filePath));
}
</script>
