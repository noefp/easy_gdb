<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#seq_section" aria-expanded="true">
  <i class="fas fa-sort" style="color:#229dff"></i> Sequences
</div>

<div id="seq_section" class="collapse show">


<?php

$bdb_path = $blast_dbs_path;
$dir_found = get_dir_and_files($bdb_path); // call the function

asort($dir_found);

//foreach ($sps_found as $bdb) {
foreach ($dir_found as $blast_dir) {
	
	if (is_dir($bdb_path.'/'.$blast_dir)) {
		//echo "<h5>$blast_dir</h5>";
	
		$dbs_found = get_dir_and_files($bdb_path.'/'.$blast_dir);
		asort($dbs_found);
	
		foreach ($dbs_found as $bdb) {
			//echo "<h5>$bdb</h5>";

		  if ( preg_match('/\.nhr$|\.phr$/', $bdb, $match) ) {
		    $bdb = str_replace(".phr","",$bdb);
		    $bdb = str_replace(".nhr","",$bdb);
		    $full_path_db = $bdb_path.'/'.$blast_dir."/".$bdb;

		    exec("blastdbcmd -db {$full_path_db} -entry " . escapeshellarg($gene_name) ."| sed 's/lcl|//'" ,$ret);

		    if ($ret) {
			    $blast_db = str_replace(".fasta","",$bdb);
			    $blast_db = str_replace("_"," ",$blast_db);

					if (preg_match('/^category_\d/', $blast_dir, $match) ) {
			      echo "<h5>$blast_db</h5>";
					} else {
				    $blast_category = str_replace("_"," ",$blast_dir);
			      echo "<h5>$blast_category, $blast_db</h5>";
					}
			      echo "<div class=\"card bg-light\">";
			      echo "<div class=\"card-body\" style=\"font-family:courier\">".implode("<br>",$ret)."</div>";
			      echo "</div><br>";
		    }
		    $ret=null;
		  }
	  
		} // close foreach
	}
}


?>
<br>
</div>
