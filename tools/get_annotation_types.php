<?php
// Get annotation types
$annot_type_query = "SELECT * FROM annotation_type";
$annot_type_res = pg_query($annot_type_query) or die('Query failed: ' . pg_last_error());

$all_annotation_types = array();

if ($annot_type_res) {
  while ($line = pg_fetch_array($annot_type_res, null, PGSQL_ASSOC)) {
    $annotation_type_id = $line["annotation_type_id"];
    $annotation_type = $line["annotation_type"];
    $all_annotation_types[$annotation_type_id] = $annotation_type;
  }
  
}
?>
