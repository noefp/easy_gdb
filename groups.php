  <br>
  <h1>People</h1>

      <?php 
      if ($dh = opendir($lab_path)){
        // echo "<ul>";
        while (($file_name = readdir($dh)) !== false){ //iterate all files in dir
          
          if (!preg_match('/^\./', $file_name)) { //discard hidden files
            if (!is_dir($lab_path."/".$file_name) && preg_match('/\.json/', $file_name) ){
              
              $labs_json = file_get_contents($lab_path."/".$file_name);
              // var_dump($labs_json);
              $lab = json_decode($labs_json, true);
              // var_dump($lab);
              echo '<h2>'.$lab["group_name"].'</h2><br>';
              echo '<div class="row">';
              echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
              
              foreach($lab["people"] as $person) {
                if ($person["link"]) {
                  if (preg_match('/www|http/', $person["link"])) { //external link
                  
                    echo '<a href="'.$person["link"].'" target="blank" class="float-left card egdb_person_card" style="color:#333">';
                  } else {
                    echo '<a href="person_view.php?person_file='.$person["link"].'" class="float-left card egdb_person_card" style="color:#333">';
                  }
                } else {
                  echo '<a href="" class="float-left card egdb_person_card" style="color:#333">';
                }
                echo '<img class="card-img-top egdb_person_img" src="'.$images_path.'/people/'.$person["picture"].'" alt="Lab member">';
                echo '<div class="card-body" style="white-space: nowrap; padding: 5px;">';
                echo '<h4 style="margin-bottom: 5px">'.$person["person_name"].'</h4>';
                echo '<p class="person-card-text">'.$person["position"].'</p>';
                if ($person["more_info"]) {
                  foreach($person["more_info"] as $person_item) {
                    echo '<p class="person-card-text">'.$person_item.'</p>';
                  }
                }
                echo '</div>';
                echo '</a>';
                
              }
              
              echo '</div></div><br>';
              
            }
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
    height:200px;
    object-fit: scale-down;
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


