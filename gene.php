<!-- HEADER -->
<?php include_once realpath("header.php");?>
<?php include_once realpath("$root_path/easy_gdb/tools/common_functions.php");?>


<!-- HELP -->
<div class="margin-20">
  <a class="float-right" href="help/00_help.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>


<div class="page_container">
<br><br>

<?php
if ($file_database){

  $gene_name = trim($_GET["name"]);
  $annot_file = $annotations_path."/".trim($_GET["annot"]);
  // list($gene_name, $annot_file) = explode("@", trim($_GET["name"]));
  
  if (file_exists("$annotation_links_path/annotation_links.json")) {
    $annot_json_file = file_get_contents("$annotation_links_path/annotation_links.json");
    $annotation_hash = json_decode($annot_json_file, true);
  }


  $quoted_search = 0;
  if (preg_match('/^".+"$/', $name)) {
    $quoted_search = 1;
  }

  $search_input = test_input2($gene_name);


  echo "<div class=\"card bg-light\"><div class=\"card-body\">$search_input</div></div><br>\n";

  include_once realpath("$easy_gdb_path/jb_frame_file.php");
  include_once realpath("$easy_gdb_path/annot_desc_file.php");
  include_once realpath("$easy_gdb_path/gene_seq.php");


  include_once realpath("$easy_gdb_path/tools/common_functions.php");

  $all_datasets = get_dir_and_files($annotations_path); // call the function
  asort($all_datasets);

  $dir_counter = 0;
  $data_counter = count($all_datasets);

  foreach ($all_datasets as $annot_dataset) {
    if (is_dir($annotations_path."/".$annot_dataset)){ // get dirs and print categories
      $dir_counter++;
    }
  }


   // CHECK ANNOTATION FILES
  if ($dir_counter) {
    foreach ($all_datasets as $dirs_and_files) {
      if (is_dir($annotations_path."/".$dirs_and_files)){ // get dirs and print categories
        $all_dir_datasets = get_dir_and_files($annotations_path."/".$dirs_and_files); // call the function
        $dir_name = str_replace("_"," ",$dirs_and_files);
        sort($all_dir_datasets);
        foreach ($all_dir_datasets as $annot_dataset) {
          if ( !preg_match('/\.php$/i', $annot_dataset) && !is_dir($annotations_path.'/'.$dirs_and_files.'/'.$annot_dataset) &&  !preg_match('/\.json$/i', $annot_dataset) && file_exists(  $annotations_path.'/'.$dirs_and_files.'/'.$annot_dataset)) {
            $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$annot_dataset);
            $data_set_name = str_replace("_"," ",$data_set_name);
          }//if preg_match
        }//foreach all_dir
      }//if is_dir
    }// foreach dir
  }//if dir_counter

  else {
    foreach ($all_datasets as $dataset) {
      $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset);
      $dataset_name = str_replace("_"," ",$dataset_name);
    }
  }
}

else {

  echo "<div class=\"card bg-light\">";
  echo "<div id=\"query_gene\" class=\"card-body\">".test_input($_GET["name"])."</div>";
  echo "</div>";

  $current_version = $max_version;

  // Connecting, selecting database
  include_once realpath ("$conf_path/database_access.php");
  $dbconn = pg_connect(getConnectionString())
      or die('Could not connect: ' . pg_last_error());

  $gene_name = test_input($_GET["name"]);
  // $search_input = $gene_name;
  // $gene_name_displayed = $gene_name;
  
  // echo "\n\n<br><br><h1>GENE NAME: $gene_name</h1><br><br>\n\n";
  
  
  // Performing SQL query
  
  $query = "SELECT * FROM gene FULL OUTER JOIN annotation_version USING(annotation_version_id) FULL OUTER JOIN species USING(species_id) WHERE gene_name='".pg_escape_string($gene_name)."'";
  // $query = "SELECT * FROM gene WHERE gene_name='".pg_escape_string($gene_name)."'";
  
  
  // $query = "SELECT gene_id,gene_version FROM gene join gene_version ON (gene.gene_version_id=gene_version.gene_version_id) WHERE gene_name='".pg_escape_string($gene_name)."'";
  $res = pg_query($query) or die("The gene $gene_name was not found in the database. Please, check the spelling carefully or try to find it in the search tool.");
  // $res = pg_query($query) or die('Query failed: ' . pg_last_error());
  
  $ori_gene_name = $gene_name;
  
  if (pg_num_rows($res) == 0) {
    $gene_name = preg_replace("/$/", ".1", $gene_name);
    $query = "SELECT * FROM gene FULL OUTER JOIN annotation_version USING(annotation_version_id) FULL OUTER JOIN species USING(species_id) WHERE gene_name='".pg_escape_string($gene_name)."'";
    $res = pg_query($query) or die("The gene $gene_name was not found in the database. Please, check the spelling carefully or try to find it in the search tool.");
    if (pg_num_rows($res) == 0) {
      echo "\n\n<br><br><h3>The gene $ori_gene_name was not found in the database. Please, check the spelling carefully or try to find it in the search tool.</h3><br><br>\n\n";
    }
  }
  
  if (pg_num_rows($res) > 0) {
    $gene_row = pg_fetch_array($res,0,PGSQL_ASSOC);
    if ($gene_row) {
      $gene_id = $gene_row["gene_id"];
      $species_id= $gene_row["species_id"];
  
      $species_name = $gene_row["species_name"];
      $annot_version = $gene_row["annotation_version"];
  
  
      include_once 'jb_frame.php';
      include_once 'annot_desc.php';
      include_once 'gene_seq.php';
    }
  }
  
    
  
  // Free resultset
  // pg_free_result($res);
  
  // Closing connection
  pg_close($dbconn);
}


?>

<br>
<br>
</div>


<?php include_once 'footer.php';?>

<script>
  var query_gene = "<?php echo $gene_name ?>";
  var sps_name = "<?php echo $species_name ?>";
  var annot_v = "<?php echo $annot_version ?>";
  document.getElementById('query_gene').innerHTML = query_gene+" &nbsp; <i>"+sps_name+"</i> &nbsp; v"+annot_v;
  
  // document.getElementById('query_gene').innerHTML = query_gene;
  // document.getElementById('query_gene').style.display = "block";

</script>
