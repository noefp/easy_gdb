<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include realpath("../modal.html"); ?>

<!-- INFO -->
<?php include_once realpath("coex_modal.php");?>

<!-- JQUERY -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<!-- HELP -->
<?php 
if (!file_exists("$custom_text_path/tools/coexpression.php")) {
  echo '
  <div class="margin-20">
    <a class="float-right" href="/easy_gdb/help/10_coexpression.php" target="_blank">
      <i class="fa fa-info" style="font-size:20px;color:#229dff"></i> Help
    </a>
  </div>
  <br><br>
  ';
}
else {
  echo '<br><br>';
}
?>


<!-- HTML -->
<div>
  <h1 class="text-center">Coexpression Search <i class="fas fa-network-wired" style="color:#555"></i></h1>
  <br>

  <div id="tool-container" class="form margin-20" style="margin:auto; max-width:900px">

<!-- OPTIONAL TEXT -->
<?php 
if (file_exists("$custom_text_path/tools/coexpression.php")) {
  include_once realpath("$custom_text_path/tools/coexpression.php");
}
?>

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
          $all_datasets = get_dir_and_files($coexpression_path);
          asort($all_datasets);
          $dir_hash = array();
          $datasets_array = array();

          echo "<label for=\"cat1\">Select Dataset</label>";
          echo "<select class=\"form-control\" id=\"cat1\" name=\"cat1\">";

          $dir_counter = 0;
          $first_category = "";

          foreach ($all_datasets as $item) {
            if (is_dir("$coexpression_path/$item")) {
              $dir_name = str_replace("_", " ", $item);
              echo "<option>$dir_name</option>";

              if ($dir_counter == 0) {
                $first_category = $dir_name;
              }

              $files_in_dir = get_dir_and_files("$coexpression_path/$item");
              foreach ($files_in_dir as $dataset_file) {
                if (!preg_match('/\.php$/i', $dataset_file) && !preg_match('/\.json$/i', $dataset_file) && file_exists("$coexpression_path/$item/$dataset_file")) {
                  $dataset_name = preg_replace('/\.[a-z]{3}$/', '', $dataset_file);
                  $dataset_name = str_replace("_", " ", $dataset_name);
                  array_push($datasets_array, "<option value=\"$coexpression_path/$item/$dataset_file\">$dataset_name</option>");
                }
              }

              sort($datasets_array);
              $dir_hash[$dir_name] = $datasets_array;
              $datasets_array = array();
              $dir_counter++;
            }
          }

          echo "</select><br>";
          echo "<select class=\"form-control\" id=\"sel1\" name=\"get_dataset\">";

          foreach ($dir_hash[$first_category] as $option) {
            echo $option;
          }

          echo "</select><br>";
          ?>

          <script>
            var coex_categories = <?php echo json_encode($dir_hash); ?>;
          </script>

          <?php
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

  function update_dataset_dropdown(category) {
    let options = coex_categories[category];
    $('#sel1').html(options);
    let first_dataset = $('#sel1').val();
    ajax_call(first_dataset);
  }

  let initial_category = $('#cat1').val();
  update_dataset_dropdown(initial_category);

  $('#cat1').change(function () {
    let new_category = $(this).val();
    update_dataset_dropdown(new_category);
  });

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
