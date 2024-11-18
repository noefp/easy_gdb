<?php
  // ############################################################### DATASET TITLE AND DESCRIPTION
  // if de dataset informatin files exit
  if($description_files_found)
  {
    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#description_frame" aria-expanded="true">';
    echo '<i class="fas fa-sort" style="color:#229dff"></i> Description dataset';
    echo '</div>';

  echo '<div id="description_frame" class="collapse hide" style="padding-top:7px">';
    include("$custom_text_path/expr_datasets/$desc_file");
    echo  "</div>";
  
  }
?>