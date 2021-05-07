<?php include_once realpath("header.php");?>
<?php include_once 'common_functions.php';?>

<div style="max-width:900px; margin:auto">
  <br>
  <?php
    
  $sps_name = test_input($_GET["sps_name"]);
  // echo "<h1>$sps_name</h1>"
    
  ?>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php 
        if ( file_exists("$species_path/species_list.json") ) {
        
            $sps_json_file = file_get_contents("$species_path/species_list.json");
            // var_dump($sps_json_file);
            $species_hash = json_decode($sps_json_file, true);
            // var_dump($species_hash);
        
            // foreach($species_hash[$sps_name] as $one_sps) {

              // echo '<a href="species_view.php?sps_name='.$one_sps["sps_name"].'" target="_blank" class="float-left egdb_person_card rounded" style="color:#333">';
              // echo '<img class="float-left egdb_person_img rounded" src="'.$images_path.'/species/'.$one_sps["image"].'" alt="'.$one_sps["sps_name"].'">';
              // echo '<div style="margin:5px; margin-left:160px; white-space: nowrap;">';
              // echo '<h4 class="card-title"><i>'.$species_hash[$sps_name]["link"].'</i></h4>';
              // echo '<p class="person-card-text">'.$one_sps["common_name"].'</p>';
              // echo '</div>';
              // echo '</a>';

            // }
        
            // echo '</div></div><br>';
        
            //print downloadable files
            // echo "<li>$file_name</li>";
          }
      ?>      
      
      <?php include_once realpath("$species_path/".$species_hash[$sps_name]["link"]);?>
    </div>
  </div>


  <br>

</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>