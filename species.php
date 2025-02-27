<?php include_once realpath("header.php");?>

<?php
if ( file_exists("$species_path/custom_species_title.php") ) {
  include_once realpath("$species_path/custom_species_title.php");
} else {
  echo '<br>';
  echo '<div style="text-align:center"> <h1 style="font-size:40px;text-align:center">Species</h1> </div>';
}
?>
  
<div id="species_menu_container" class="row">

<?php
  if ( file_exists($json_files_path."/customization/species_list.json") ) {
        
    $sps_json_file = file_get_contents("$json_files_path/customization/species_list.json");
    $species_hash = json_decode($sps_json_file, true);
    
    foreach($species_hash as $key => $value) {
      
      if ($species_hash[$key]["public"]) {
        echo '<a href="species_view.php?sps_key='.$key.'" class="float-left card species_card h-100">';
        // echo '<a href="species_view.php?sps_name='.$key.'&common_name='.$species_hash[$key]["common_name"].'&sps_img='.$species_hash[$key]["image"].'" class="float-left card egdb_person_card" style="color:#333">';
        echo '<img class="species_img" src="'.$images_path.'/species/'.$species_hash[$key]["image"].'" alt="'.$species_hash[$key]["sps_name"].'">';
        echo '<div class="species_card-body">';
        echo '<div class="sps_card_title">'.$species_hash[$key]["card_title"].'</div>';
        echo '<div class="sps_card_subtitle">'.$species_hash[$key]["card_subtitle"].'</div>';
        echo '</div>';
        echo '</a>';
      }
      
    }
    
  }
?>

</div>
<br>

<?php include_once realpath("$easy_gdb_path/footer.php");?>