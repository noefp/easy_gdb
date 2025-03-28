<?php
// include_once "db_paths.php";
// File is ignored by .gitignore - should contain:
// getBlastdbcmdPath and getBlastdbBaseLocation.
// These functions are returning corresponding paths and taking no arguments. Path of directory ends with /


function getFastaFile($gids,$dbPath) {
  // $blastdbcmdPath=getBlastdbcmdPath();

  // echo $blastdbcmdPath."\n";
  // echo $blastDbLocation."\n";
  // echo escapeshellarg(implode(",",$gids))."\n";

	exec("blastdbcmd -db {$dbPath} -entry " . escapeshellarg(implode(",",$gids)) ."| sed 's/lcl|//'" ,$ret);
  // exec("{$blastdbcmdPath} -db {$dbPath} -entry " . escapeshellarg(implode(",",$gids)) ."| sed 's/lcl|//'" ,$ret);
	return implode("\n",$ret);
}


if(isset($_POST["gids"])) {

  if(isset($_POST["blast_db"])) {
		header('Content-Type: application/octet-stream');
		$filename="egdb_sequences_" . date("Y-m-d.His") . ".fasta";
		header("Content-Disposition: attachment;filename={$filename}");

    $gids=array_map(function($row) {
			return trim($row);
		}
		,explode("\n",$_POST["gids"]));

    // echo $_POST["blast_db"]."\n";
    // echo $_POST["gids"]."\n";
    // echo $gids."\n";

		echo getFastaFile($gids,$_POST["blast_db"]);

  }

}

?>
