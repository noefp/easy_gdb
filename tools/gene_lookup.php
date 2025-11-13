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

  <div id="tool-container" class="form margin-20" style="margin:auto; max-width:900px">

     <?php 
      echo "<div id=\"color_default\" class=\"alert alert-primary\" role=\"alert\" style=\"display:block;margin:0px\">";
      echo "<div  class=\"card-body\" style=\"padding:0px;text-align:center\">";
      // echo "Example genes for <strong>$data_set_name</strong>:<br>$second_line";
      echo "</div></div>";
    ?> 
    <br>

    <form id="gene_version_lookup">
    <label for="txtGenes">Paste a list of gene IDs</label>
    <textarea name="txtGenes" id="txtGenes" class="form-control" rows="10"></textarea>

    <br>


    <?php
      include_once realpath("$easy_gdb_path/tools/common_functions.php");

      $sps_found = get_dir_and_files($lookup_path);
      echo "<div class=\"form-group\">";
      echo "<label for=\"sel1\">Select Dataset</label>";
      echo "<select class=\"form-control\" id=\"sel1\" name=\"lookup_db\">";

      foreach ($sps_found as $bdb) {
        if (preg_match('/\.txt$/', $bdb)) {
          $blast_db = str_replace(".txt","",$bdb);
          $blast_db = str_replace("_"," ",$blast_db);
          echo "<option value=\"$lookup_path/$bdb\">$blast_db</option>";
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
<br>

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
      $("#search_input_modal").html("The gene list is empty.");
      $('#no_gene_modal').modal();
      return false;
    }
    else if (gene_lookup_input.length <= 3) {
      $("#search_input_modal").html("Input is too short, please provide a longer term to search.");
      $('#no_gene_modal').modal();
      return false;
    }

    if (gene_count > max_input) {
      $("#search_input_modal").html(
        "A maximum of " + max_input + " sequences can be provided as input, your input has <strong>" + gene_count + "</strong>\."
      );
      $('#no_gene_modal').modal();
      return false;
    }

    return true;
  });

  function ajax_gene_lookup(selectedDataset) {
    var selectedDataset = $('#sel1').val();
    $.ajax({
      type: "POST",
      url: "ajax_gene_lookup.php",
      data: {'file': selectedDataset},
      success: function (response) {
        var data =JSON.parse(response);
        $("#color_default .card-body").html("Example genes for <strong>"+data.data_set_name+"</strong>:<br>"+data.second_line.join(" "));
        $('#txtGenes').val(data.input_list.join("\n")); 
        // $('#txtGenes').attr("placeholder", data.input_list.join("\n"));
      }
    });
  }

  ajax_gene_lookup();

  $('#sel1').change(function() {
    ajax_gene_lookup();
  });

});
</script>

