<!-- HEADER -->
<?php include_once realpath("../../header.php");?>

<?php 
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
  // $pass_dir = test_input($_GET["dir_name"]); // get passport directory with files to list
?>

<div class="page_container" style="margin-top:20px">

<?php  
  if (file_exists("$custom_text_path/custom_titles/custom_germplasm_title.php")) {
    include_once realpath("$custom_text_path/custom_titles/custom_germplasm_title.php");
  }else{
    echo "<h1 class=\"text-center\"> Germplasm's species of $dbTitle </h1>";
  }
?>
  <br>
  <!-- Container for cards, printed later by JavaScript -->
   <div id="cards_container" class="row"></div>

<?php
  // Get subdirectories of passport directory
  $subdir_name = [];  
  if (is_dir($passport_path) && $sub_dh = opendir($passport_path) ) {   
    while ( $species = readdir($sub_dh) ) { //iterate all subdirs as $species      
      if (!preg_match('/^\./', $species) && is_dir($passport_path."/".$species) ) {
        array_push($subdir_name,$species);
      }     
    } //end while
    closedir($sub_dh);
  } 

// If there are subdirectories, check if germplasm_list.json file exist to get germplasm data, if not, redirect to datatable of the only species
if(!empty($subdir_name)){
  if (file_exists("$passport_path/germplasm_list.json")) {
    $germplasm_json_file = file_get_contents("$passport_path/germplasm_list.json");
    $germplasm_hash = json_decode($germplasm_json_file, true);
  } elseif (count($subdir_name) === 1) 
    // if there is only one species, redirect to 02_pass_file_to_datatable of the only species
  {   echo '<script>location.href = "02_pass_file_to_datatable.php?dir_name=' . $subdir_name[0] . '";</script>';
      exit;
  }else 
  // if there are more than one species, print a list of species with links to 02_pass_file_to_datatable of each species
  {  foreach ($subdir_name as $species) {
      echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=/$species\">$species</a></li>";
      }
      // include_once realpath("$easy_gdb_path/footer.php");
      // exit;
  } 
}
else{ // If there are no subdirectories, print message and exit
  echo "<p><i>No subdirectories found</i>.</p>";
  include_once realpath("$easy_gdb_path/footer.php");
  exit;
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
  foreach (array_keys($species_count) as $species_key) {
    // Verify if specie exists in germplasm_hash
    if (array_key_exists($species_key, $germplasm_hash) ) {
      $species_data = $germplasm_hash[$species_key];
        
      // Loop through data of specie and generate cards
        if ($species_data["public"]) {
          $cards_data[] = [
            // "link" => $species_data["link"],
            "link" => "02_pass_file_to_datatable.php?dir_name=$species_key",
            "image" => $images_path .'/species/'. $species_data["image"],
            "sps_name" => $species_data["sps_name"],
            "common_name" => $species_data["common_name"], 
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

  // Convert data to JSON
  $json_cards_data = json_encode($cards_data);

?>


</div>
<br>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php"); ?>


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

<style>
  
/*  .egdb_person_card {
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: transform 0.2s, border-color 0.2s;
  }

  .egdb_person_card:hover {
    transform: translateY(-5px);
    border-color: rgb(53,58,64);
    text-decoration: none;
  }

  .egdb_person_img {
    height: 200px;
    width: 100%;
    object-fit: cover;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }
*/  
  .egdb_person_card {
    min-height:150px;
    margin-right: 5px;
    margin-bottom: 5px;
    border: 1px solid #ddd;
    padding: 10px 10px 0px;
    min-width:200px;
    transition: transform 0.2s, border-color 0.2s;
  }
  
  .egdb_person_card:hover {
    transform: translateY(-5px);
    border-color: rgb(53,58,64);
    text-decoration: none;
  }
  
  .person-card-text {
    margin-bottom:2px;
  }
  .egdb_person_img {
    height:140px;
/*    object-fit: scale-down;*/
    
    width: 100%;
    object-fit:contain;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    
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

  #cards_container{
    justify-content: center;
  }

  .page_container {
    width: 100%;
  }


 @media (min-width: 1500px) {
  .page_container {
    width: 90%;
  }
}  
 
</style>
