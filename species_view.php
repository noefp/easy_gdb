<?php include_once realpath("header.php");?>
<?php include_once 'tools/common_functions.php';?>

<?php
    
  $sps_key = test_input($_GET["sps_key"]);
  
  if ( file_exists("$json_files_path/customization/species_list.json") ) {
    $sps_json_file = file_get_contents("$json_files_path/customization/species_list.json");
    $species_hash = json_decode($sps_json_file, true);
  }
  
  $sps_title = $species_hash[$sps_key]["card_title"];
  $sps_subtitle = $species_hash[$sps_key]["card_subtitle"];
  $sps_img = $species_hash[$sps_key]["image"];
  $sps_link = $species_hash[$sps_key]["link"];
?>

<div id="species_container">
  <?php include_once realpath("$species_path/".$sps_link);?>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>