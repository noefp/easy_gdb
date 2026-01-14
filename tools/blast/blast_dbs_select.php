<?php

if (!$multiple_blast_db) {
  $counter = 0;
  $first_category;

  if ($dh = opendir($blast_dbs_path)){
    
    echo "<div class=\"blast_attr form-group\">";
    echo  "<label for=\"blast_category\">Select Dataset</label>";
    echo  "<select class=\"form-control blast_box\" id=\"blast_category\" style=\"display:none; margin-bottom:10px\">";
    
    //iterate all files in dir
    while ( ($file_name = readdir($dh)) !== false ){ 
      
      if (!preg_match('/^\./', $file_name) && !preg_match('/\.json$/', $file_name) ) { //discard hidden and json files
        
        if (is_dir($blast_dbs_path."/".$file_name)){ // get dirs and print categories
          $file_name = str_replace("_"," ",$file_name);
          
          echo "<option>$file_name</option>";
          
          if ($counter == 0) {
            $first_category = $file_name;
          }
          $counter++;
        }
        
      }
    }
    
    echo  "</select>";
    
    echo  "<select class=\"form-control blast_box\" id=\"sel1\" name=\"blast_db\">";
    echo  "</select>";

    echo   "</div>";
  }
}

?>

<script>
  var multiple_blast_db = <?php echo isset($multiple_blast_db) ? $multiple_blast_db : 0; ?>;

  if (!multiple_blast_db) {
    $(document).ready(function () {
    
      var counter = '<?php echo $counter ?>';
      var blast_dbs_path = '<?php echo $blast_dbs_path ?>';
      
      // display categories select if multiple categories
      if (counter > 1) {
        jQuery('#blast_category').css("display", "block");
      }
      
      //call PHP file get_dbs.php to get blast dbs in the selected category
      function ajax_call(category) {
        
        jQuery.ajax({
          type: "POST",
          // url: 'get_dbs.php',
          url: '/easy_gdb/tools/blast/get_dbs.php',
          data: {'category': blast_dbs_path+'/'+category},

          success: function (datasets) {
            jQuery('#sel1').html(datasets);
            // alert(datasets);
          }
        });
        
      }
      
      //display first category
      blast_category = $('#blast_category').val();
      blast_category = blast_category.replace(/ /g,"_");
      
      ajax_call(blast_category);
      
      // Change available blast dbs in selected category
      $('#blast_category').change(function () {
        blast_category = $('#blast_category').val();
        blast_category = blast_category.replace(/ /g,"_");
        
        ajax_call(blast_category);
        
      });
      
    });
  }
</script>


<?php

if ($multiple_blast_db) {

  // Arrays to store nucleotide and protein database entries
  $nucleotide_dbs = [];
  $protein_dbs = [];



  function get_databases($directory_path, &$database_list, $allowed_extensions) {
    // Check if the directory exists
    if (is_dir($directory_path)) {
        // Open the directory
        $dir = opendir($directory_path);
        // Loop through files in the directory
        while (($file = readdir($dir)) !== false) {
            // Skip "." and ".."
            if ($file === "." || $file === "..") {
                continue;
            }

            $full_path = $directory_path . "/" . $file;

            // If it's a directory, call this function recursively
            if (is_dir($full_path)) {
                get_databases($full_path, $database_list, $allowed_extensions);
            } else {
                // Get the file extension
                $file_extension = pathinfo($file, PATHINFO_EXTENSION);
                // Check if the file extension is in the allowed list
                if (in_array($file_extension, $allowed_extensions)) {
                    // $database_list[] = str_replace("_", " ", $file); // Add file to list
                    $database_list[] = $file; // Add file to list
                }
            }
        }
        // Close the directory
        closedir($dir);
    } else {
        echo "Directory not found: $directory_path";
    }
  }


  // Define paths to nucleotide and protein directories
  // $nucleotide_path = $blast_dbs_path . "/nucleotides";
  // $protein_path = $blast_dbs_path . "/proteins";


  // $end_ls = ['fasta','fa','fna','fas','ffn','faa','mpfa','frn'];

  // Retrieve files in nucleotide and protein directories
  // get_databases($nucleotide_path, $nucleotide_dbs, $end_ls);
  // get_databases($protein_path, $protein_dbs, $end_ls);
  get_databases($blast_dbs_path, $nucleotide_dbs, ['nhr']);
  get_databases($blast_dbs_path, $protein_dbs, ['phr']);


  // Display Nucleotide databases
  echo "<div class=\"form-group blast_attr\" id='nucleotide_db_list'>";
  echo "<label for=\"blast_sequence\">Nucleotide databases <a href='#' class='select-toggle' data-type='nucleotide'>[Select all]</a></label>";
  echo "<ul style='list-style-type: none; padding: 0;'>";

  foreach ($nucleotide_dbs as $db) {
    $db_name = preg_replace('/\.[a-z]+\.nhr$/i', '', $db);
    // $db_name = str_replace([".phr", ".nhr"], "", $db);
    // $db_name = str_replace(".fasta", "", $db);
    $db_name = str_replace("_", " ", $db_name);
    echo "<li><input type='checkbox' name='blast_db[]' value='$db' class='nucleotide-checkbox' data-type='nucleotide'> $db_name</li>";
  }

  echo "</ul>";
  echo "</div>";

  // Display Protein databases
  echo "<div class=\"form-group blast_attr\" id='protein_db_list'>";
  echo "<label for=\"blast_sequence\">Protein databases <a href='#' class='select-toggle' data-type='protein'>[Select all]</a></label>";
  echo "<ul style='list-style-type: none; padding: 0;'>";

  foreach ($protein_dbs as $db) {
    $db_name = preg_replace('/\.[a-z]+\.phr$/i', '', $db);
    // $db = str_replace(".phr", "", $db);
    // $db_name = str_replace(".fasta", "", $db);
    $db_name = str_replace("_", " ", $db_name);
    echo "<li><input type='checkbox' name='blast_db[]' value='$db' class='protein-checkbox' data-type='protein'> $db_name</li>";
  }

  echo "</ul>";
  echo "</div>";
  //echo "<div id='blast_db_list'></div>";
}

?>

<script>
  var multiple_blast_db = <?php echo $multiple_blast_db; ?>;

  if (multiple_blast_db) {
    $(document).ready(function () {
      var blast_dbs_path = '<?php echo $blast_dbs_path; ?>';
      var blast_get_dbs_url = '<?php echo $blast_get_dbs; ?>';
      
      // Functionality for "Select all" and "Unselect all" links
      $('.select-toggle').click(function (e) {
        e.preventDefault();
        var type = $(this).data('type');
        var checkboxes = type === 'nucleotide' ? $('.nucleotide-checkbox') : $('.protein-checkbox');
        var otherCheckboxes = type === 'nucleotide' ? $('.protein-checkbox') : $('.nucleotide-checkbox');
        var otherToggle = type === 'nucleotide' ? $('.select-toggle[data-type="protein"]') : $('.select-toggle[data-type="nucleotide"]');
        
        // If all checkboxes in the group are unchecked, select all; otherwise, unselect all
        if (checkboxes.filter(':checked').length === 0) {
          checkboxes.prop('checked', true);
          $(this).text('[Unselect all]');
          otherCheckboxes.prop('checked', false).prop('disabled', true);
          otherToggle.addClass('disabled').attr('style', 'pointer-events: none; color: grey;');
        } else {
          checkboxes.prop('checked', false);
          $(this).text('[Select all]');
          otherCheckboxes.prop('disabled', false);
          otherToggle.removeClass('disabled').attr('style', '');
        }
      });

      // Functionality for individual checkbox selection
      $('input[type="checkbox"]').change(function () {
        var type = $(this).hasClass('nucleotide-checkbox') ? 'nucleotide' : 'protein';
        var otherCheckboxes = type === 'nucleotide' ? $('.protein-checkbox') : $('.nucleotide-checkbox');
        var otherToggle = type === 'nucleotide' ? $('.select-toggle[data-type="protein"]') : $('.select-toggle[data-type="nucleotide"]');

        if ($(this).is(':checked')) {
          otherCheckboxes.prop('checked', false).prop('disabled', true);
          otherToggle.addClass('disabled').attr('style', 'pointer-events: none; color: grey;');
        } else {
          if ($('.' + type + '-checkbox:checked').length === 0) {
            otherCheckboxes.prop('disabled', false);
            otherToggle.removeClass('disabled').attr('style', '');
          }
        }

        updateToggleLinks();
      });

      // Function to update "Select all" or "Unselect all" based on checkbox states
      function updateToggleLinks() {
        $('.select-toggle').each(function () {
          var type = $(this).data('type');
          var checkboxes = type === 'nucleotide' ? $('.nucleotide-checkbox') : $('.protein-checkbox');
          if (checkboxes.filter(':checked').length > 0) {
            $(this).text('[Unselect all]');
          } else {
            $(this).text('[Select all]');
            if (type === 'nucleotide') {
              $('.protein-checkbox').prop('disabled', false);
              $('.select-toggle[data-type="protein"]').removeClass('disabled').attr('style', '');
            } else {
              $('.nucleotide-checkbox').prop('disabled', false);
              $('.select-toggle[data-type="nucleotide"]').removeClass('disabled').attr('style', '');
            }
          }
        });
      }

      // Initial update of the toggle links based on default state
      updateToggleLinks();

      // Function to collect selected databases and make an AJAX call
      function ajax_call() {
          // Collect all selected databases
          var selected_dbs = [];
          $('#nucleotide_db_list .nucleotide-checkbox:checked, #protein_db_list .protein-checkbox:checked').each(function () {
              selected_dbs.push($(this).val());
          });

          var ls = {};

          // Add each selected database to the `ls` object
          for (var i = 0; i < selected_dbs.length; i++) {
              ls['db' + i] = blast_dbs_path + '/' + selected_dbs[i];
          }

          // Make AJAX call to the server with the selected databases
          jQuery.ajax({
              type: "POST",
              url: blast_get_dbs_url,
              data: JSON.stringify(ls), 
              contentType: "application/json",
              success: function (datasets) {
                // #blast_db_list id to display ajax results
                  $('#blast_db_list').html(datasets);
                  //$('#blast_db_list').html(JSON.stringify(selected_dbs));
                  
              },
              error: function (xhr, status, error) {
                  console.error("AJAX error: ", status, error);
                  $('#blast_db_list').html(error);
                  //$('#blast_db_list').html(JSON.stringify(selected_dbs));
              }
          });
      }

      // Call the AJAX function when checkboxes are changed
      $('input[type="checkbox"]').change(function () {
          ajax_call();
      });

      // Initial call when the page loads to show default databases
      ajax_call();

    });
  }

  </script>