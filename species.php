<?php include_once realpath("header.php");?>


<!-- <div style="max-width:900px; margin:auto"> -->
  <br>
  <h1>Species</h1>
  
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

  <?php 
    if ( file_exists("$species_path/species_list.json") ) {
        
        $sps_json_file = file_get_contents("$species_path/species_list.json");
        // var_dump($sps_json_file);
        $species_hash = json_decode($sps_json_file, true);
        // var_dump($$species_hash);
        
        foreach($species_hash as $key => $value) {
          
          if ($species_hash[$key]["public"]) {
            echo '<a href="species_view.php?sps_name='.$key.'" target="_blank" class="float-left egdb_person_card rounded" style="color:#333">';
            echo '<img class="float-left egdb_person_img rounded" src="'.$images_path.'/species/'.$species_hash[$key]["image"].'" alt="'.$species_hash[$key]["sps_name"].'">';
            echo '<div style="margin:5px; margin-left:160px; white-space: nowrap;">';
            echo '<h4 class="card-title"><i>'.$species_hash[$key]["sps_name"].'</i></h4>';
            echo '<p class="person-card-text">'.$species_hash[$key]["common_name"].'</p>';
            echo '</div>';
            echo '</a>';
          }
          
        }
        
        // foreach($species_hash as $one_sps) {
        //   if ($one_sps["public"]) {
        //     echo '<a href="species_view.php?sps_name='.$one_sps["sps_name"].'" target="_blank" class="float-left egdb_person_card rounded" style="color:#333">';
        //     echo '<img class="float-left egdb_person_img rounded" src="'.$images_path.'/species/'.$one_sps["image"].'" alt="'.$one_sps["sps_name"].'">';
        //     echo '<div style="margin:5px; margin-left:160px; white-space: nowrap;">';
        //     echo '<h4 class="card-title"><i>'.$one_sps["sps_name"].'</i></h4>';
        //     echo '<p class="person-card-text">'.$one_sps["common_name"].'</p>';
        //     echo '</div>';
        //     echo '</a>';
        //   }
        // }
        
        // echo '</div></div><br>';
        
        //print downloadable files
        // echo "<li>$file_name</li>";
      }
  ?>      

    </div>
  </div>

  <!-- <div style="height:500px"></div> -->
  <br>
<style>
  .egdb_person_card {
/*    width:350px;*/
    min-height:150px;
    margin-right: 5px;
    margin-bottom: 5px;
    border: 1px solid #ddd;
  }
  
  .person-card-text {
    margin-bottom:2px;
  }
  .egdb_person_img {
    height:140px;
    width: 150px;
    margin: 5px;
    position:absolute;
    object-fit: cover;
/*    left:0px;*/
  }
  .card-body{
    position: absolute;
    margin-left:160px;
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

<!-- </div> -->

<?php include_once realpath("$easy_gdb_path/footer.php");?>