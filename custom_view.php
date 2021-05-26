<?php include_once realpath("header.php");?>
<?php include_once 'tools/common_functions.php';?>

  <br>
  <?php $custom_file = test_input($_GET["file_name"]); ?>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php 
        if ( file_exists("$custom_text_path/custom_pages/$custom_file") ) {
          include_once realpath("$custom_text_path/custom_pages/$custom_file");
        }
      ?>
    </div>
  </div>

  <br>

<?php include_once realpath("$easy_gdb_path/footer.php");?>