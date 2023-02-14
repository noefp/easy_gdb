<?php include_once realpath("header.php");?>

<div id="index_container">
  <br>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php include_once realpath("$custom_text_path/welcome_text.php");?>
    </div>
  </div>
  <br>
  <?php 
    if (!$rm_citation) {
      if (file_exists(realpath("$custom_text_path/db_citation.php")) && filesize(realpath("$custom_text_path/db_citation.php")) >0) {
        include_once realpath("$custom_text_path/db_citation.php");
      }
    }
  ?>

  <br>

</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>

<style>
  
  #index_container {
    max-width:900px; 
    margin:auto;
  }
  
</style>
