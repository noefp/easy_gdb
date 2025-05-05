<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include realpath("../modal.html"); ?>

<!-- INFO -->
<?php include_once realpath("coex_modal.php");?>

<!-- JQUERY -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<!-- RETURN AND HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php" target="_blank">
    <i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help
  </a>
</div>
<br>


<!-- HTML -->
<div>
  <h1 class="text-center">Coexpression Search <i class="fas fa-network-wired" style="color:#555"></i></h1>
  <br>

  <div id="gene-correlation-container" class="form margin-20" style="margin:auto; max-width:900px">

    <!-- INPUT FORM -->
    <div class="form">
      <div style="margin:auto; max-width:900px">
        <form id="search_cor" action="coex_output.php" method="get">
          <div class="form-group">
            <label for="txtGenes" style="font-size:16px">Insert a gene ID</label> <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button>
            <input type="search" class="form-control" id="txtGenes" name="txtGenes">
          </div>

          <?php
          include_once realpath("$easy_gdb_path/tools/common_functions.php");
          $fold_found = get_dir_and_files($coexpression_path);

          echo "<div class=\"form-group\">";
          echo "<label for=\"sel1\">Select Dataset</label>";
          echo "<select class=\"form-control\" id=\"sel1\" name=\"get_dataset\">";

          asort($fold_found);
          foreach ($fold_found as $folder) {
            $full_path = $coexpression_path . '/' . $folder;
            if (is_dir($full_path)) {
              $res = str_replace("_", " ", $folder);
              $res = ucfirst($res);
              echo "<option value=\"$full_path\">$res</option>";
            }
          }

          echo "</select>";
          echo "<br>";
          echo "<button type=\"submit\" class=\"btn btn-info float-right\" form=\"search_cor\">Search</button>";
          echo "</div>";
          ?>

        </form>
        <br>
      </div>
    </div>
  </div>
  <br>
</div>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>


<!-- CSS -->
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


<!-- JAVASCRIPT -->
<script>
$(document).ready(function () {
  function ajax_call(expr_file) {
    jQuery.ajax({
      type: "POST",
      url: 'ajax_get_names_array.php',
      data: {'expr_file': expr_file},
      success: function (names_array) {
        var names = JSON.parse(names_array);
        $("#txtGenes").autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(names, request.term);
            response(results.slice(0, 15));
          }
        });
      }
    });
  }

  // First dataset for autocomplete
  let first_dataset = $('#sel1').val();
  ajax_call(first_dataset);

  // Change dataset
  $('#sel1').change(function () {
    let selected_dataset = $('#sel1').val();
    ajax_call(selected_dataset);
  });
});
</script>

<script>
$(document).ready(function () {
  $('#search_cor').submit(function (e) {
    var gene_lookup_input = $('#txtGenes').val().trim();
    var splitted = gene_lookup_input.split(/[\s;,:]+/);
    splitted = splitted.filter(function(el){ return el !== ""; });
    var gene_count = splitted.length;
    var max_input = 1;

    if (!gene_lookup_input) {
      $("#search_input_modal2").html("The gene ID field is empty.");
      $('#no_gene_modal2').modal();
      return false;
    }
    else if (gene_lookup_input.length <= 2) {
      $("#search_input_modal2").html("Input is too short, please provide a longer gene ID.");
      $('#no_gene_modal2').modal();
      return false;
    }
    if (gene_count > max_input) {
      $("#search_input_modal2").html(
        "Please provide only one gene ID."
      );
      $('#no_gene_modal2').modal();
      return false;
    }

    return true;
  });
});
</script>
