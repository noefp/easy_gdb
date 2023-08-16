<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#annot_section"><h3>Results found</h3></div>

<div id="annot_section" class="collapse show">
  <br>
  <div class="data_table_frame">


<?php
// Performing SQL query

// Get annotation types
include_once("../get_annotation_types.php");

if ( $quoted_search ) {
  $desc_input = preg_replace('/[\"\<\>\t\;]+/','',strtolower($raw_input) );
  // echo "<p>filtered $desc_input</p>";
  
} elseif ( preg_match('/\s+/',$search_input) ) {
  $desc_input = preg_replace('/\s+/','%|%',strtolower($search_input) );
} else {
  $desc_input = strtolower($search_input);
}

echo "\n<br><h3>modified Input</h3>\n<div class=\"card bg-light\"><div class=\"card-body\">$desc_input</div></div><br>\n";

//latest estable search query
//$query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) FULL OUTER JOIN annotation_version USING(annotation_version_id) FULL OUTER JOIN species USING(species_id) WHERE lower(gene_name) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annotation_desc) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annotation_term) ILIKE '%".pg_escape_string($search_input)."%'";


//full text search test
$query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) FULL OUTER JOIN annotation_version USING(annotation_version_id) FULL OUTER JOIN species USING(species_id) WHERE lower(gene_name) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annotation_desc) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR annotation_term ILIKE '%".pg_escape_string($search_input)."%'";





// $query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) WHERE lower(gene_name) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annotation_desc) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annotation_term) ILIKE '%".pg_escape_string($search_input)."%'";


// $query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) FULL OUTER JOIN annotation_type USING(annotation_type_id) WHERE lower(gene_name) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annot_desc) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annot_term) ILIKE '%".pg_escape_string($search_input)."%'";
// $query = "SELECT * FROM annotation JOIN gene_annotation USING(annotation_id) JOIN gene USING(gene_id) WHERE lower(annot_desc) SIMILAR TO '%".pg_escape_string($desc_input)."%' OR lower(annot_term) ILIKE '%".pg_escape_string($search_input)."%'";
// $query = "SELECT * FROM annotation WHERE annot_desc ILIKE '%$search_input%' OR annot_term ILIKE '%$search_input%'";


$res = pg_query($query) or die('Query failed: ' . pg_last_error());
// if (pg_fetch_assoc($res)) {
if ($res) {
  // Printing results in HTML
  echo "<table id=\"tblAnnotations\" class=\"table annot_table\">\n<thead><tr><th>Gene</th><th>Term</th><th>Description</th><th>Source</th><th>Version</th><th>Species</th></tr></thead>\n<tbody>\n";
  // $counter = 0;
  while ($line = pg_fetch_array($res, null, PGSQL_ASSOC)) {
      $found_gene = $line["gene_name"];
      $found_term = $line["annotation_term"];
      $found_desc = $line["annotation_desc"];
      $found_type_id = $line["annotation_type_id"];
      
      $version = $line["annotation_version"];
      $sps = $line["species_name"];
      
      $found_type = $all_annotation_types[$found_type_id];
      
      echo "<tr><td><a href=\"/easy_gdb/gene.php?name=$found_gene\" target=\"_blank\">$found_gene</a></td><td>$found_term</td><td>$found_desc</td><td style=\"white-space: nowrap;\">$found_type</td><td style=\"text-align:right\">$version</td><td><i>$sps</i></td></tr>\n";
      // $counter++;
      // if ($counter >= $max_row) {
        // echo "<tr><td colspan=\"4\">Number of annotations found exceeded the limit to display, Please refine your search.</td></tr>\n";
        // break;
      // }
  }
  echo "</tbody>\n</table>\n";
}
else {
  echo "<p>No results found.</p>\n";
}
// Free resultset
pg_free_result($res);
?>
  </div>
</div>
