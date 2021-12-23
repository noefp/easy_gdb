<?php include_once realpath("header.php");?>
<?php include_once 'tools/common_functions.php';?>

<div style="max-width:900px; margin:auto; text-align: justify;">
  <br>
  <?php $person_file = test_input($_GET["person_file"]); ?>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php include_once realpath("$lab_path/$person_file");?>
    </div>
  </div>


  <br>

</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>