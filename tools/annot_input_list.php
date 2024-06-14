<!-- HEADER -->
<?php include_once realpath("../header.php");?>

<!-- HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/05_annotation_extraction.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<br>
<h3 class="text-center">Annotation Extraction</h3>

<!-- INPUT FORM -->
<div class="form margin-20">
  <div style="margin:auto; max-width:900px">

<?php
  if ($file_database) {
    echo "<form id=\"egdb_annot_file_form\" action=\"annot_output_file.php\" method=\"post\">";
  } else {
    echo "<form id=\"egdb_annot_file_form\" action=\"annot_output_db.php\" method=\"post\">";
  }
?>

    <!-- FORM OPPENED -->
    <div class="form-group">
      <label for="search_box" style="font-size:16px">Paste a list of gene IDs</label>
      <textarea type="search_box" class="form-control" id="annot_file_box" name="txtGenes" rows="5" style="border-color: #666"><?php echo "$input_gene_list"; ?></textarea>
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
          echo  "<div class=\"form-group\"><label for=\"search_box\" style=\"font-size:16px\">Select your annotation/s file</label>";

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
                if ( !preg_match('/\.php$/i', $annot_dataset) && !is_dir($annotations_path.'/'.$dirs_and_files.'/'.$annot_dataset) &&  !preg_match('/\.json$/i', $annot_dataset) && file_exists($annotations_path.'/'.$dirs_and_files.'/'.$annot_dataset)   ) {
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
          echo "<div class=\"form-group\" style=\"display:none\">";
          // $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$all_datasets[0]);
          // $dataset_name = str_replace("_"," ",$dataset_name);
          // //echo "<option value=\"$annotations_path/$dataset@$dataset_name\">$dataset_name</option>";
          //
          // echo "<input type=\"checkbox\" class=\"form-check-input sample_checkbox\" name=\"sample_names[]\" value=\"$annotations_path/$dataset@$dataset_name\">$dataset_name";
          
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
    <button type="submit" class="btn btn-info float-right" form="egdb_annot_file_form" style="margin-top: -5px" formmethod="post">Search</button>
    <br>
    <br>
    <br>
    </form>
  </div>
</div>


<!-- ERROR BANNER -->
<div class="modal fade" id="no_gene_modal" role="dialog">
  <div class="modal-dialog modal-sm">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="text-align: center;">ERROR</h4>
      </div>
      <div class="modal-body">
        <div style="text-align: center;">
          <p id="annot_input_modal"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

<!-- JAVASCRIPT -->
<script> 
$(document).ready(function () {
  $('#egdb_annot_file_form').submit(function() {
    var gene_id = $('#annot_file_box').val();
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
      $("#annot_input_modal").html( "No input provided in the search box" );
      $('#no_gene_modal').modal();
      return false;
    }
    else if (gene_id.length < 3) {
      $("#annot_input_modal").html( "Input is too short, please provide a longer term to search" );
      $('#no_gene_modal').modal();
      return false;
    }
    else if (file_database === '1' && !data_set_selected && select_field) {
      $("#annot_input_modal").html( "No annotation file/s selected" );
      $('#no_gene_modal').modal();
      return false;
    }
    else {
      return true;
    };
  });
});

</script>