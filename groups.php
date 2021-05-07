<!-- <?php //include_once realpath("header.php");?> -->


<!-- <div style="max-width:900px; margin:auto"> -->
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
                
                echo '<a href="'.$person["link"].'" target="_blank" class="float-left egdb_person_card rounded" style="color:#333">';
                echo '<img class="float-left egdb_person_img rounded" src="'.$images_path.'/people/'.$person["picture"].'" alt="Lab member">';
                echo '<div style="margin:5px; margin-left:160px; white-space: nowrap;">';
                echo '<h4 class="card-title">'.$person["person_name"].'</h4>';
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
              
              //print downloadable files
              // echo "<li>$file_name</li>";
            }
          }
          
        }
        // echo "</ul>";
      }
      ?>      

    </div>
  </div>

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

<!-- <?php// include_once realpath("$easy_gdb_path/footer.php");?> -->