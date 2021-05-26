<?php

include_once realpath("$easy_gdb_path/tools/common_functions.php");
// include_once '../common_functions.php';

$sps_found = get_dir_and_files($blast_dbs_path); // call the function

echo "<div class=\"form-group\">";
echo  "<label for=\"sel1\">Select Data set</label>";
echo  "<select class=\"form-control\" id=\"sel1\" name=\"blast_db\">";


foreach ($sps_found as $bdb) {
  if (preg_match('/\.nhr$|\.phr$/', $bdb, $match)) {
    $bdb = str_replace(".phr","",$bdb);
    $bdb = str_replace(".nhr","",$bdb);
    $blast_db = str_replace(".fasta","",$bdb);
    $blast_db = str_replace("_"," ",$blast_db);
    echo "<option dbtype=\"$match[0]\" value=\"$blast_dbs_path/$bdb\">$blast_db</option>";
  }
}

echo   "</select>";
echo   "</div>";

?>
