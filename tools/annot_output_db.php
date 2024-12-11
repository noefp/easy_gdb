<?php include realpath('../header.php'); ?>

<?php include_once realpath ("$conf_path/database_access.php"); ?>

<div class="page_container" style="margin-top:40px">
<div class="data_table_frame">

<?php
$gNamesArr=array_filter(explode("\n",trim($_POST["txtGenes"])),function($gName) {return ! empty($gName);});

if(sizeof($gNamesArr)==0) {
	echo "<h1>No genes to search provided.</h1>";
}
else {
  
  //TEST
  //print("<pre>".print_r($gNamesArr,true)."</pre>");
 
 
	// Connecting to db
  $dbconn = pg_connect(getConnectionString());
 
  // Get annotation types
  include_once("get_annotation_types.php");
  
	// load annotation links in hash
	$annot_hash;

	if ( file_exists("$json_files_path/tools/annotation_links.json") ) {
	    $annot_json_file = file_get_contents("$json_files_path/tools/annotation_links.json");
	    $annot_hash = json_decode($annot_json_file, true);
	}
	
	// Getting all annotation types.
  $query="SELECT annotation_type_id,annotation_type from annotation_type"; // array with annotation type ids

  $res=pg_query($query) or die("Couldn't query database.");

  $annotTypes=pg_fetch_all_columns($res);

  $gNameValues=implode(",",array_map(function($input) {if(empty(trim($input))) return ""; else  return "'" . trim(pg_escape_string($input))."'" ;},$gNamesArr));
  
  $query="SELECT searchValues.search_name as \"input\", array_agg( distinct (g.gene_name)) as \"genes\", array_agg(distinct (annotation.annotation_term, annotation.annotation_desc, annotation.annotation_type_id)) \"annot\"
  FROM
  gene g inner join gene_annotation on gene_annotation.gene_id=g.gene_id
  inner join annotation on annotation.annotation_id=gene_annotation.annotation_id
  inner join annotation_type on annotation_type.annotation_type_id=annotation.annotation_type_id
  right join unnest(array[{$gNameValues}]) WITH ORDINALITY AS searchValues(search_name,ord) on search_name=g.gene_name
  group by searchValues.search_name, searchValues.ord
  order by searchValues.ord asc";
  
  $dbRes=pg_query($query) or die('Query failed: ' . pg_last_error());
  
  
  echo "<table class=\"table table-striped table-bordered\" id=\"tblResults\"><thead><tr><th>input</th>";

  foreach ($annotTypes as $type) {
    echo "<th style=\"min-width:100px\">".$all_annotation_types[$type]." ID</th>";
    echo "<th style=\"min-width:200px\">".$all_annotation_types[$type]." Description</th>";
  }
  // echo implode("",array_map(function($type) {return "<th style=\"min-width:200px\">{$type}</th>";},$annotTypes));
  echo "</tr></thead><tbody>";
  
  
  while($row=pg_fetch_array($dbRes,null, PGSQL_ASSOC)) {

    //TEST
    //print("<pre>".print_r($row,true)."</pre>");


  // Creating Gene columns:

		// Parse gene array returned by database - removing 3 characters in the end and at the beginning.
		$geneEntries=array_map(function($geneCol) { return explode(",",$geneCol);},explode(")\",\"(",substr($row["genes"],3,-3)));

		// Removing \" enclosing the the multi word gene names.
		array_walk($geneEntries,function(&$entry) {$entry[0]=str_replace("\\\"","",$entry[0]);});

		// Get all anotations for this row and create the annotation columns.
		$annotStr="";
    // Parse annotation array returned by database, removed 3 characters in the end and at the beginning. Saved terms, description and annotation type in $annotEntries
    $annotEntries=array_map(function($annotRow) {
      preg_match("/([^,]*),(.+),(\d+)/",$annotRow,$matches);
      return array(0=>$matches[1],1=>$matches[2],2=>$matches[3]);
    },explode(")\",\"(",substr($row["annot"],3,-3)));


    //TEST
    //print("<pre>".print_r($row["input"],true)."</pre>");


echo "<tr><td><a href=\"/easy_gdb/gene.php?name={$row["input"]}\" target=\"_blank\">{$row["input"]}</a></td>";

foreach ($annotTypes as $type) {
$terms_array = [];
$annots_array = [];
	foreach ($annotEntries as $annot_row) {
		if ($annot_row[2] == $type) {
			// echo "{$annot_row[0]}";
      $q_link = "#";
			$annot_type = $all_annotation_types[$type];
      if ($annot_type == "TAIR10" || $annot_type == "Araport11") {
        $annot_row[0] = preg_replace('/\.\d$/','',$annot_row[0]);
      }
      if ($annot_hash[$annot_type]) {
        $q_link = $annot_hash[$annot_type];
        $q_link = preg_replace('/query_id/',$annot_row[0],$q_link);
      }

      // $term_linked = "<a href=\"$q_link\" target=\"_blank\">$annot_row[0]</a>";
			
			array_push($terms_array,"<a href=\"$q_link\" target=\"_blank\">$annot_row[0]</a>");
		}
	}
	echo "<td>".implode($terms_array,"; <br>")."</td><td>";
	
	foreach ($annotEntries as $annot_row) {
		if ($annot_row[2] == $type) {
			// echo "{$annot_row[1]}";
			array_push( $annots_array, str_replace("\\\"","",$annot_row[1]) );
		}
	}
	echo implode($annots_array,"; <br>")."</td>";
	
}
echo "</tr>";

	}
	echo "</tbody></table>\n";
	// Freeing result and closing connection.
	pg_free_result($dbRes);
	pg_close($dbconn);
}
?>

</div>
</div>
<br>
<br>
<br>

<script type="text/javascript">
$("#tblResults").dataTable({
  "order": [],
	dom:'Bfrtlpi',
  "oLanguage": {
     "sSearch": "Filter by:"
   },
  buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
  ]
});
  
$("#tblResults_filter").addClass("float-right");
$("#tblResults_info").addClass("float-left");
$("#tblResults_paginate").addClass("float-right");
  
</script>


<?php include_once '../footer.php';?>
