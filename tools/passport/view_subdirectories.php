<!-- HEADER -->
<?php include_once realpath("../../header.php");?>

<?php 
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
  $pass_dir = test_input($_GET["dir_name"]); // get passport directory with files to list
  $pass_dir_title = str_replace("_", " ", $pass_dir);
?>

<div class="page_container" style="margin-top:20px">
  <h1 class="text-center">Germplasm's species of <?php echo $pass_dir_title; ?></h1>
  <br>

  <!-- Container for cards, printed by JavaScript -->
   <div id="cards_container" class="row"></div>

<?php
  
  $dir_name = $_GET['dir_name'];
  $sub_path = "$passport_path/$dir_name";

  if (file_exists("$sub_path/germplasm_list.json") ) {
    $germplasm_json_file = file_get_contents("$sub_path/germplasm_list.json");
    //var_dump($germplasm_json_file);
    $germplasm_hash = json_decode($germplasm_json_file, true);
    //var_dump($germplasm_hash);

    $species = []; // array to keep subdir names
    if (is_dir($sub_path) && $sub_dh = opendir($sub_path) ) {
      
      while (($species = readdir($sub_dh)) !== false) { //iterate all subdirs
  
        if (!preg_match('/^\./', $species) && is_dir($sub_path."/".$species) ) {
          $subdir_name[] = $species;
        }
          
      } //end while
      closedir($sub_dh);
    } else {
      echo "<p><i>No subdirectories found</i>.</p>";
    }

  } elseif ($sub_path) {// close if file_exists germplasm_list.json

    $species = []; // array to keep subdir names
    if (is_dir($sub_path) && $sub_dh = opendir($sub_path) ) {
      
      while (($species = readdir($sub_dh)) !== false) { //iterate all subdirs
  
        if (!preg_match('/^\./', $species) && is_dir($sub_path."/".$species) ) {
          $subdir_name[] = $species;
          echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=$dir_name/$species\">$species</a></li>"; // simple list
        }
          
      } //end while
      closedir($sub_dh);
    } else {
      echo "<p><i>No subdirectories found</i>.</p>";
    }
  }
  
?>

  <br>
  <br>

  <!-- MAP-->
<?php 
  //$species_count = "";
  include_once realpath("$easy_gdb_path/tools/passport/multi_map.php"); 
  // $species_count está accessible
  $json_species_count = json_encode($species_count);
  $cards_data = [];

  // Sort species according to $species_count
  arsort($species_count); // Ya estaba ordenado en multi_map.php, pero por si acaso

  // loop through $species in the order of $species_count
  foreach ($species_count as $species_key => $count) {
    // Verify if specie exists in germplasm_hash
    if (array_key_exists($species_key, $germplasm_hash) ) {
      $species_data = $germplasm_hash[$species_key];
        
      // Loop through data of specie and generate cards
      foreach ($species_data as $key => $value) {
        if ($value["public"]) {
          $cards_data[] = [
            "link" => $value["link"],
            "image" => $images_path .'/species/'. $value["image"],
            "sps_name" => $value["sps_name"],
            "common_name" => $value["common_name"], 
            "total_acc" => $species_count[$species_key] // Add total acc
          ];  
//            echo '<a href="'.$value["link"].'" class="float-left card egdb_person_card" style="color:#333">';
//            echo '<img class="card-img-top egdb_person_img" src="'.$images_path.'/species/'.$value["image"].'" alt="'.$value["sps_name"].'">';
//            echo '<div class="card-body" style="white-space: nowrap; padding: 5px;">';
//            echo '<h4 style="margin-bottom: 5px"><i>'.$value["sps_name"].'</i></h4>';
//            echo '<p class="card-text">'.$value["common_name"].'</p>';
//            echo '</div>';
//            echo '</a>';

        }
      }
    }
  }

  // Convert data to JSON
  $json_cards_data = json_encode($cards_data);

?>


</div>
<br>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php"); ?>


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


<script>
  const cardsData = <?php echo $json_cards_data; ?>;

  // Generate cards
  function generateCards(cardsData) {
    const cardsContainer = document.getElementById('cards_container');
    let cardsHTML = '';

    cardsData.forEach(card => {
      cardsHTML += `
        <a href="${card.link}" class="float-left card egdb_person_card" style="color:#333">
          <img class="card-img-top egdb_person_img" src="${card.image}" alt="${card.sps_name}">
          <div class="card-body" style="white-space: nowrap; padding: 5px;">
            <h4 style="margin-bottom: 5px"><i>${card.sps_name}</i></h4>
            <p class="card-text">${card.common_name}</p>
            <p class="card-text" style="color: grey">Total accessions: <b>${card.total_acc}</b></p>
          </div>
        </a>
      `;
    });

    // Insert  HTML generated in the container
    cardsContainer.innerHTML = cardsHTML;
  }

  // Call function to generate cards
  generateCards(cardsData);
</script>
