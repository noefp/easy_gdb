
<div id ="seq_frame_section" class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#seq_section" aria-expanded="true">
  <i class="fas fa-sort" style="color:#229dff"></i> Sequences
</div>

<div id="seq_section" class="collapse show">

<?php
// Function to find gene sequence in blast dbs
function find_gene_in_blast_db($bdb_path, $dir_found, $blast_db_array, $gene_name) {

  $dbs_counter = 0;

  foreach ($dir_found as $bdb) {

      $bdb = str_replace([".phr",".nhr"],"",$bdb);
      $bdb_file = array_pop(explode("/",$bdb)); // get the last element of the path as blast db file name

      if ( isset($blast_db_array) and empty($blast_db_array)) {
        //echo "<h5>array defined empty</h5>"; // in this case do not search
        echo '<script>
                $("#seq_frame_section").hide();
              </script>'; 
        // $ret=null;
        break;
      } else if (isset($blast_db_array) and in_array($bdb_file, $blast_db_array) ) {
        // echo "<h5>array defined</h5>";
        exec("blastdbcmd -db {$bdb} -entry " . escapeshellarg($gene_name) ."| sed 's/lcl|//'" ,$ret);
      } else if (!isset($blast_db_array) ) { 
        // echo "<h5>NO array defined</h5>";
        exec("blastdbcmd -db {$bdb} -entry " . escapeshellarg($gene_name) ."| sed 's/lcl|//'" ,$ret);
      }

      if ($ret) {
        $blast_db = str_replace($bdb_path."/","",$bdb); // get blast db name without $blast_dbs_path path
        $blast_db = str_replace(".fasta","",$blast_db); // remove extension
        $blast_db = str_replace("_"," ",$blast_db); // replace _ with space
        $blast_db = str_replace("/"," / ",$blast_db); // replace / with space / space
          echo "<h5>$blast_db</h5>";
        
          echo "<div class=\"card bg-light\">";
          echo "<div class=\"card-body\" style=\"font-family:courier\">".implode("<br>",$ret)."</div>";
          echo "</div><br>";
      }else{
        // count blast dbs where gene sequence was not found
        $dbs_counter++;
      }
      $ret=null;

  }// close foreach
  // if no gene sequence found in any blast db
  if ($dbs_counter == count($dir_found)) {
    echo "<div class=\"alert alert-danger\" style=\"padding:10px\">";
    echo "<div class=\"alert-body\" style=\"text-align:center\"> Gene sequence not found in the selected BLAST databases </div>";
    echo "</div>";
  }
}// close function

$dir_found=[];
// Function to get files and directories 
function get_dir ($directory_path, &$database_list, $extensions) {

  // Check if the directory exists
  if (is_dir($directory_path)) {
      // Open the directory   and get its files and directories   
      $dir = get_dir_and_files($directory_path);

      // Loop through files in the directory
      foreach ($dir as $file) {

       $full_path = $directory_path . "/" . $file;
          // If it's a directory, call this function recursively
          if (is_dir($full_path)) {
              get_dir($full_path, $database_list, $extensions);
          } else {
              // Get the file extension
              $file_extension = pathinfo($file, PATHINFO_EXTENSION);
              // Check if the file extension is in the allowed list
              if (in_array($file_extension, $extensions)) {
                  $database_list[] = $full_path; // Add file to list
              }
          } 
      } // close foreach
  } // close if is_dir
}


// $bdb_path = $blast_dbs_path;
//$dir_found = (isset($blast_dbs_path)) ? get_dir_and_files($blast_dbs_path): null; // call the function

// blast_dbs_path variable not set or blast_dbs folder not found
if(!isset($blast_dbs_path) || !is_dir($blast_dbs_path)) { 
  echo "<div class=\"alert alert-danger\" style=\"padding:10px\">";
   echo "<div class=\"alert-body\" style=\"text-align:center\"> <b>blast_dbs_path</b> variable in the easyGDB.conf or <b>blast_dbs</b> folder not found </div>";
  echo "</div>";
  // exit;
}else{
  $dir_found=[];
  // Call the function to get blast db files with extensions nhr or phr
  get_dir($blast_dbs_path, $dir_found, ['nhr','phr']);

  if(empty($dir_found)) { // no blast dbs found
    echo "<div class=\"alert alert-danger\" style=\"padding:10px\">";
    echo "<div class=\"alert-body\" style=\"text-align:center\"> BLAST databases not found </div>";
    echo "</div>";
    // exit;
  }
  else{
      asort($dir_found); // sort alphabetically
      // var_dump($dir_found);

      // Check if an annotations_conf.json file exists to get the blast dbs information
      if (file_exists($json_files_path."/tools/annotations_conf.json") ) {
        $ann_json_file = file_get_contents("$json_files_path/tools/annotations_conf.json");
        $annot_hash = json_decode($ann_json_file, true);

        $annot_filename = preg_replace('/.+\//','',$annot_file);
        
        // get a array of blast dbs defined for this annotation file
        $blast_db_array = $annot_hash[$annot_filename]["gene_blast_dbs"];
        
        if (isset($blast_db_array)) {
          if (empty($blast_db_array)) {
            // do not search for gene sequence when "gene_blast_dbs" is defined as empty: "gene_blast_dbs": []
            echo '<script>
                    $("#seq_frame_section").hide();
                  </script>'; 
            // exit;
          }
          else {
            // search for gene sequence when "gene_blast_dbs" is defined and contain a list of blast dbs
            find_gene_in_blast_db($blast_dbs_path, $dir_found, $blast_db_array, $gene_name);
            //echo "<h5>gene_blast_dbs is defined and contain a list of blast dbs</h5>";
          }
        } else {
          // search for gene sequence in all blast dbs when "gene_blast_dbs" is NOT defined in the "annotation_conf.json" file
          find_gene_in_blast_db($blast_dbs_path, $dir_found, null, $gene_name);
        } 
      // }
    } else {
      // search for gene sequence in all blast dbs when there is not an "annotation_conf.json" file
      find_gene_in_blast_db($blast_dbs_path, $dir_found, null, $gene_name);
    }
  }
}
?>
<br>
</div>
