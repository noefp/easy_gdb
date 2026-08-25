<?php include_once realpath("header.php");?>

<div class="page_container">

<?php if (file_exists("$custom_text_path/about.php"))
   { 
      echo'<div class="row custom-container">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
               include_once realpath("$custom_text_path/about.php");
      echo '</div>
      </div>';
   }
 
   if ( $ab_citation && file_exists(realpath("$custom_text_path/db_citation.php")) && filesize(realpath("$custom_text_path/db_citation.php")) >0) {
         include_once realpath("$custom_text_path/db_citation.php");
      }
?>
         

<?php 
      if ( $ab_labs && file_exists(realpath("groups.php")) ) {
      echo '<div class="row custom-container">
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
                  include_once realpath("groups.php");
      echo '</div>
      </div>';

      }
?>

<br>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
