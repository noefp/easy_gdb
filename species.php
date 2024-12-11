<?php include_once realpath("header.php");?>


  <br>
  <h1>Species</h1>
  
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

  <?php 
    // if ( file_exists("$species_path/species_list.json") ) {
      //$logos_json = file_get_contents($json_files_path."/customization/logos.json");
      
    if ( file_exists($json_files_path."/customization/species_list.json") ) {
        
        // $sps_json_file = file_get_contents("$species_path/species_list.json");
        $sps_json_file = file_get_contents("$json_files_path/customization/species_list.json");
        // var_dump($sps_json_file);
        $species_hash = json_decode($sps_json_file, true);
        // var_dump($$species_hash);
        
        foreach($species_hash as $key => $value) {
          
          if ($species_hash[$key]["public"]) {
            echo '<a href="species_view.php?sps_name='.$key.'&common_name='.$species_hash[$key]["common_name"].'&sps_img='.$species_hash[$key]["image"].'" class="float-left card egdb_person_card" style="color:#333">';
            echo '<img class="egdb_person_img" src="'.$images_path.'/species/'.$species_hash[$key]["image"].'" alt="'.$species_hash[$key]["sps_name"].'">';
            echo '<div class="card-body" style="white-space: nowrap; padding: 5px;">';
            echo '<h4 style="margin-bottom: 5px"><i>'.$species_hash[$key]["sps_name"].'</i></h4>';
            echo '<p class="card-text">'.$species_hash[$key]["common_name"].'</p>';
            echo '</div>';
            echo '</a>';
          }
          
        }
        
      }
  ?>      

    </div>
  </div>

  <br>
<style>
  
  
  .egdb_person_card {
    min-height:150px;
    margin-right: 5px;
    margin-bottom: 5px;
    border: 1px solid #ddd;
    padding: 10px 10px 0px;
  }
  
  .person-card-text {
    margin-bottom:2px;
  }
  .egdb_person_img {
    height:140px;
    object-fit: scale-down;
/*    object-fit: none;*/
  }

  .egdb_person_card a:link {
    color:#333;
  }
  .egdb_person_card:hover {
    color:#333;
    border-color: #000;
    cursor:pointer;
    text-decoration:none;
  }
</style>



<?php include_once realpath("$easy_gdb_path/footer.php");?>