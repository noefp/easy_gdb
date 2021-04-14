<?php include_once realpath("header.php");?>


<div style="max-width:900px; margin:auto">
  <br>
  <h1>About Us</h1>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php include_once realpath("$custom_text_path/welcome_text.php");?>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

      <?php 
      if (file_exists(realpath("$custom_text_path/db_citation.php")) && filesize(realpath("$custom_text_path/db_citation.php")) >0) {
        echo "<h4>Please, when using this web site or its data, cite us using the reference:</h4>";
        
        include_once realpath("$custom_text_path/db_citation.php");
      }
      ?>      

    </div>
  </div>

  <br>
  <!-- <img class="img-fluid" src=<?php echo "$images_path/header_img.png";?> /> -->

</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>