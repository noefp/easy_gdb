<?php include_once realpath("header.php");?>
<?php include_once 'tools/common_functions.php';?>

<div style="max-width:900px; margin:auto; text-align: justify;">
  <br>
  <?php
    
  $sps_name = test_input($_GET["sps_name"]);
  $common_name = test_input($_GET["common_name"]);
  $sps_img = test_input($_GET["sps_img"]);
    
  ?>

  <div class="row" style="margin-bottom: 10px;">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php 
        if ( file_exists("$species_path/species_list.json") ) {
        
            $sps_json_file = file_get_contents("$species_path/species_list.json");
            // var_dump($sps_json_file);
            $species_hash = json_decode($sps_json_file, true);
            // var_dump($species_hash);
          }
      ?>
      
      <img class="float-right" style="z-index:0;height:150px" src="<?php echo $images_path.'/species/'.$sps_img ?>" >

      
      <h1><?php echo $common_name ?></h1>
      <h3 style="color:#666"><i><?php echo $sps_name ?></i></h3>

      <?php include_once realpath("$species_path/".$species_hash[$sps_name]["link"]);?>
    </div>
  </div>


</div>

<style>
  .sps-btn {
    float: left!important;
    margin-right: 10px;
    margin-bottom: 10px;
  }
</style>


<?php include_once realpath("$easy_gdb_path/footer.php");?>