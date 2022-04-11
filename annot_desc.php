<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#annot_section" aria-expanded="true">
  <i class="fas fa-sort" style="color:#229dff"></i> Functional descriptions
</div>

<div id="annot_section" class="collapse show">
  <br>

<?php
// Get annotation types
include_once("tools/get_annotation_types.php");


// load annotation links in hash
$annot_hash;

if ( file_exists("$annotation_links_path/annotation_links.json") ) {
    
    $annot_json_file = file_get_contents("$annotation_links_path/annotation_links.json");
    // var_dump($annot_json_file);
    $annot_hash = json_decode($annot_json_file, true);
    // var_dump($annot_hash);
    // echo "<p>".$annot_hash["TAIR10"]."</p>";
}


// $query = "SELECT * FROM annotation JOIN gene_annotation USING(annotation_id) JOIN gene USING(gene_id) WHERE gene_id='".pg_escape_string($gene_id)."'";
$query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) WHERE gene_id='".pg_escape_string($gene_id)."'";
// $query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) FULL OUTER JOIN annotation_type USING(annotation_type_id) WHERE gene_id='".pg_escape_string($gene_id)."'";

$res = pg_query($query) or die('Query failed: ' . pg_last_error());


// Printing results in HTML
echo "<table class=\"table annot_table\">\n<tr><th>Gene ID</th><th>Description</th><th>Source</th></tr>\n";

// Get annotations
while ($line = pg_fetch_array($res, null, PGSQL_ASSOC)) {
     $q_term = $line["annotation_term"];
     $q_desc = $line["annotation_desc"];
     $annot_type_id = $line["annotation_type_id"];
     
     $annot_type = $all_annotation_types[$annot_type_id];
     
     $q_link = "#";

     if ($annot_type == "TAIR10" || $annot_type == "Araport11") {
       $q_term = preg_replace('/\.\d$/','',$q_term);
     }
     if ($annot_hash[$annot_type]) {
       $q_link = $annot_hash[$annot_type];
       $q_link = preg_replace('/query_id/',$q_term,$q_link);
     }

     echo "<tr><td><a href=\"$q_link\" target=\"_blank\">$q_term</a></td><td>$q_desc</td><td>$annot_type</td></tr>\n";
}



// echo "<tr><td><a href=\"/jbrowse/?loc=$jb_gene_name&tracks=DNA%2Cgene%20models%20v0.61\" target=\"_blank\">$gene_name_displayed</a></td><td>Genome browser</td><td>OliveTree DB</td></tr>\n";

echo "</table>\n\n";

// Free resultset
pg_free_result($res);
?>

<br>
</div>
