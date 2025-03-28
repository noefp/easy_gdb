<?php
// set the momeory limit accordinglly
ini_set('memory_limit', '512M');

include_once realpath("../../../configuration_path.php");
include_once realpath("$conf_path/easyGDB_conf.php");
$baseDir = $blast_dbs_path."/";

function findPath($dbPath, $baseDir) {

  // echo "$dbPath <br>";


  // include_once realpath("../../../configuration_path.php");
  // include_once realpath("$conf_path/easyGDB_conf.php");
  // $baseDir = $blast_dbs_path."/";

  $dbPath = str_replace(' ', "_", $dbPath);

  $allowedExtensions = ['fasta', 'fa', 'fna', 'ffn', 'faa', 'mpfa', 'frn', 'pep.fasta'];

  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));

  foreach ($iterator as $file) {
      // error_log("Examining file: " . $file->getPathname());

      if ($file->isFile()) {
          $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
          if (in_array($extension, $allowedExtensions)) {
              $baseName = $file->getFilename();
              // error_log("Checking basename: $baseName against $dbPath");
              if ($baseName === $dbPath) {
                  // error_log("Database found: " . $file->getPathname());
                  return $file->getPathname();
              }
          }
      }
  }
  return null;
}


function getFastaFile($gids, $dbPath) {
    exec("blastdbcmd -db {$dbPath} -entry " . escapeshellarg(implode(",", $gids)) . " | sed 's/lcl|//'", $ret);
    return implode("\n", $ret);
}




if (isset($_POST["gids"]) && isset($_POST["selected_dbs"])) {

    $gids = array_map('trim', explode("\n", $_POST["gids"]));
    $databases = explode(",", $_POST["selected_dbs"]);
    header('Content-Type: application/octet-stream');

    //CHANGE THE FILE NAME TO RELATED DATABASE!!!!!
    $filename="pulsebase_sequences_" . date("Y-m-d.His") . ".fasta";
		header("Content-Disposition: attachment;filename={$filename}");
    $results = [];

    foreach ($databases as $dbPath) {
        $resolvedPath = findPath($dbPath, $baseDir);
        if (!empty($resolvedPath)) {
          $sequences = getFastaFile($gids, $resolvedPath);
          if (!empty($sequences)) {
            $results[] = $sequences;
            // ob_flush();
            ob_clean();
            flush();
          }
          

        }
    }

    echo implode("\n\n", $results);
    exit;

}
?>