<?php
// set the momeory limit accordingly
ini_set('memory_limit', '512M');

include_once realpath("../../../configuration_path.php");
include_once realpath("$conf_path/easyGDB_conf.php");
$baseDir = $blast_dbs_path."/";


// FIND PATH
function findPath($dbPath, $baseDir) {
  $dbPath = str_replace(' ', "_", $dbPath);

  $allowedExtensions = ['nhr','nin','nsq','nog','nsd','nsi','phr','pin','pog','psd','psi','psq'];

  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));

  foreach ($iterator as $file) {
    if ($file->isFile()) {
      $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
      if (in_array($extension, $allowedExtensions)) {
        $baseName = $file->getFilename();
        if ($baseName === $dbPath) {
          return $file->getPathname();
        }
      }
    }
  }
  return null;
}


// BLASTDBCMD
function getFastaFile($gids, $dbPath) {
    exec("blastdbcmd -db {$dbPath} -entry " . escapeshellarg(implode(",", $gids)) . " | sed 's/lcl|//'", $ret);
    return implode("\n", $ret);
}


// POST
if (isset($_POST["gids"]) && isset($_POST["selected_dbs"])) {
  $gids = array_map('trim', explode("\n", $_POST["gids"]));
  $databases = explode(",", $_POST["selected_dbs"]);
  header('Content-Type: application/octet-stream');

  // CHANGE NAME
  $filename="$dbTitle" . "_" . date("Y-m-d_His") . ".fasta";
  header("Content-Disposition: attachment;filename={$filename}");
  $results = [];

  // FUNCTION
  foreach ($databases as $dbPath) {
    $resolvedPath = findPath($dbPath, $baseDir);
    $resolvedPath = preg_replace('/\.(nhr|phr)$/i', '', $resolvedPath);
    if (!empty($resolvedPath)) {
      $sequences = getFastaFile($gids, $resolvedPath);
      if (!empty($sequences)) {
        $results[] = $sequences;
        ob_clean();
        flush();
      }
    }
  }
  echo implode("\n\n", $results);
  exit;
}


?>