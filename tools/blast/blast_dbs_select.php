<?php

$counter = 0;
$first_category;

if ($dh = opendir($blast_dbs_path)){
  
  echo "<div class=\"form-group\">";
  echo  "<label for=\"blast_category\">Select Dataset</label>";
  echo  "<select class=\"form-control\" id=\"blast_category\" style=\"display:none; margin-bottom:10px\">";
  
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
  
  echo  "<select class=\"form-control\" id=\"sel1\" name=\"blast_db\">";
  echo  "</select>";

  echo   "</div>";
}

?>

<script>
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
</script>