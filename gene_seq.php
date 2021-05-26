<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#seq_section" aria-expanded="true">
  Sequences
</div>

<div id="seq_section" class="collapse show">


<?php

$bdb_path = $blast_dbs_path;
$sps_found = get_dir_and_files($bdb_path); // call the function

foreach ($sps_found as $bdb) {

  if ( preg_match('/\.nhr$|\.phr$/', $bdb, $match) ) {
    $bdb = str_replace(".phr","",$bdb);
    $bdb = str_replace(".nhr","",$bdb);
    $full_path_db = $bdb_path."/".$bdb;

    exec("blastdbcmd -db {$full_path_db} -entry " . escapeshellarg($gene_name) ."| sed 's/lcl|//'" ,$ret);

    $blast_db = str_replace(".fasta","",$bdb);
    $blast_db = str_replace("_"," ",$blast_db);

    if ($ret) {
      echo "<h5>$blast_db</h5>";
      echo "<div class=\"card bg-light\">";
      echo "<div class=\"card-body\" style=\"font-family:courier\">".implode("<br>",$ret)."</div>";
      echo "</div><br>";
    }
    $ret=null;
  }

}


?>
<br>
</div>
