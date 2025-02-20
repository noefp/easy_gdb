<?php

// function blast_dbs($datasets) {
  
  $datasets = $_POST["category"];
  // $datasets = str_replace(" ","\ ",$datasets);
  
  if ($dh = opendir($datasets)){
    
    $datasets = str_replace(" ","\ ",$datasets);
  
    while (($bdb = readdir($dh)) !== false){ //iterate all files in dir
      
      if (!preg_match('/^\./', $bdb)) { //discard hidden files
        
        if (!is_dir($datasets."/".$bdb)){
          
          if (preg_match('/\.nhr$|\.phr$/', $bdb, $match)) {
            $bdb = str_replace(".phr","",$bdb);
            $bdb = str_replace(".nhr","",$bdb);
            $blast_db = str_replace(".fasta","",$bdb);
            $blast_db = str_replace("_"," ",$blast_db);
            
            echo "<option dbtype=\"$match[0]\" value=\"$datasets/$bdb\"> $blast_db</option>";
          }
          
        }
      }
    }
  }
  
// }
?>
