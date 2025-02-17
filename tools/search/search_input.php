<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include_once realpath("../modal.html");?>
<!-- INFO -->
<?php include_once realpath("search_info_modal.php");?>

<!-- HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php" target="blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<br>
<h3 class="text-center">Gene / Annotation Search</h3>

<!-- INPUT FORM -->
<div class="form margin-20">
  <div style="margin:auto; max-width:900px">

<?php
  if ($file_database) {
    echo "<form id=\"egdb_search_file_form\" action=\"search_output_file.php\" method=\"get\">";
  } else {
    echo "<form id=\"egdb_search_file_form\" action=\"search_output.php\" method=\"get\">";
  }
?>


    <!-- FORM OPPENED -->
    <div class="form-group">
      <label for="search_file_box" style="font-size:16px">Insert a gene ID or annotation keywords</label>
      <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button>
      <input type="search_box" class="form-control" id="search_file_box" name="search_keywords" style="border-color: #666">
    </div>


    <!-- IS BETTER TO SET IN ANOTHER FILE -->
    <?php
      if ($file_database) {
        include_once realpath("$easy_gdb_path/tools/common_functions.php");

        $all_datasets = get_dir_and_files($annotations_path); // call the function
        asort($all_datasets);

        $dir_counter = 0;
        $data_counter = count($all_datasets);

        foreach ($all_datasets as $annot_dataset) {
          if (is_dir($annotations_path."/".$annot_dataset)){ // get dirs and print categories
            $dir_counter++;
          }
        }

        // CHECK ANNOTATION FILES
        if ($dir_counter) {
          echo  "<div class=\"form-group\"><span style=\"font-size:16px\">Select your annotation/s file</span>";

          foreach ($all_datasets as $dirs_and_files) {
            if (is_dir($annotations_path."/".$dirs_and_files)){ // get dirs and print categories
              $all_dir_datasets = get_dir_and_files($annotations_path."/".$dirs_and_files); // call the function
              $dir_name = str_replace("_"," ",$dirs_and_files);
              echo "<div class=\"card\" style=\"margin-bottom: 5px;\">";
              echo "<div class=\"card-body\" style=\"widht: 100%\">";
              echo "<h4>$dir_name</h4>";
              echo "<div class=\"row\" style=\"margin:0px\">";
              sort($all_dir_datasets);

              foreach ($all_dir_datasets as $annot_dataset) {
                if ( !preg_match('/\.php$/i', $annot_dataset) && !is_dir($annotations_path.'/'.$dirs_and_files.'/'.$annot_dataset) &&  !preg_match('/\.json$/i', $annot_dataset) && file_exists($annotations_path.'/'.$dirs_and_files.'/'.$annot_dataset)) {
                  $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$annot_dataset);
                  $data_set_name = str_replace("_"," ",$data_set_name);
                  echo "<div class=\"col-sm-6 col-md-4 col-lg-4\">";
                  echo "<label class=\"form-check-label\" style=\"cursor:pointer\">";
                  echo "<input type=\"checkbox\" class=\"form-check-input sample_checkbox\" name=\"sample_names[]\" value=\"$annotations_path/$dirs_and_files/$annot_dataset@$data_set_name\">$data_set_name";
                  echo "</label>";
                  echo "<br>";
                  echo "</div>";
                  echo "<br>";
                }//if preg_match
              }//foreach all_dir
              echo "</div>";
              echo "</div>";
              echo "</div>";
            }//if is_dir
          }// foreach dir
        }//if dir_counter

        elseif ($dir_counter === 0 && $data_counter === 1) {
          echo "<div class=\"form-group\">";
          echo "</div>";
        }

        else {
          echo "<div class=\"form-group\">";
          echo "<label for=\"search_box\" style=\"font-size:16px\">Select your annotation file</label>";
          echo "<select class=\"form-control\" name=\"sample_names[]\">";
          foreach ($all_datasets as $dataset) {
            $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset);
            $dataset_name = str_replace("_"," ",$dataset_name);
            echo "<option value=\"$annotations_path/$dataset@$dataset_name\">$dataset_name</option>";
          }
          echo "</select>";
          echo "</div>";
        }

      }//if file_database
    ?>

    <br>
    <button type="submit" class="btn btn-info float-right" style="margin-top: -5px">Search</button>
    <br>
    <br>
    <br>
    </form>
  </div>
</div>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>


<!-- IS BETTER TO ADD TO THE GENERAL CSS -->
<style>  
  .info_icon {
    background-color:#4387FD;
    border-radius:20px;
    vertical-align: top;
    border:0px;
    display:inline-block;
    color:#ffffff;
    font-family:"Georgia",Georgia,Serif;
    font-size:12px;
    font-weight:bold;
    font-style:normal;
    width:18px;
    height:18px;
    line-height:0px;
    text-align:center;
  }
  .info_icon:hover {
    background-color:#5EA1FF;
    color:#0000CC;
  }

  .info_icon:active {
    position:relative;
    top:1px;
  }
</style>


<!-- JAVASCRIPT -->
<script> 
$(document).ready(function () {
  $('#egdb_search_file_form').submit(function() {
    var gene_id = $('#search_file_box').val();
    var data_set_selected = false;
    var file_database = "<?php echo $file_database; ?>";
    var select_field = $('.sample_checkbox').length > 0;

    if (select_field) {
      $('.sample_checkbox').each(function() {
        if ($(this).is(':checked')) {
          data_set_selected = true;
          return false;
        }
      });
    }

    // Forms
    if (!gene_id) {
      $("#search_input_modal").html( "No input provided in the search box" );
      $('#no_gene_modal').modal();
      return false;
    }
    else if (gene_id.length < 3) {
      $("#search_input_modal").html( "Input is too short, please provide a longer term to search" );
      $('#no_gene_modal').modal();
      return false;
    }
    else if (file_database === '1' && !data_set_selected && select_field) {
      $("#search_input_modal").html( "No annotation file/s selected" );
      $('#no_gene_modal').modal();
      return false;
    }
    else {
      return true;
    };
  });
});

</script>