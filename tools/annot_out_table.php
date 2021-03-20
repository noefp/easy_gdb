<?php include realpath('../header.php'); ?>

<?php include_once realpath ("$conf_path/database_access.php"); ?>

<div class="page_container" style="margin-top:40px">
<div class="data_table_frame">

<?php
$gNamesArr=array_filter(explode("\n",trim($_POST["txtGenes"])),function($gName) {return ! empty($gName);});

if(sizeof($gNamesArr)==0)
{
	echo "<h1>No genes to search provided.</h1>";
}
else
{
	// Connecting to db
  $dbconn = pg_connect(getConnectionString());

	// Getting all annotation types.
  $query="SELECT annotation_type_id,annotation_type from annotation_type";
  // $query="SELECT distinct annotation_type_id from annotation where annot_type not like 'GO %' order by annotation_type desc";
  $res=pg_query($query) or die("Couldn't query database.");

$annot_type_hash = array();

while ($line = pg_fetch_array($res, null, PGSQL_ASSOC)) {
  // echo "<p>".$line["annotation_type_id"]." ".$line["annotation_type"]."</p>";
  $annot_type_hash[ $line["annotation_type_id"] ] = $line["annotation_type"];
}
$annotTypeIds = array_keys($annot_type_hash);
$annotTypes = array_values($annot_type_hash);

  // $annotTypes=pg_fetch_all_columns($res);

  $gNameValues=implode(",",array_map(function($input) {if(empty(trim($input))) return ""; else  return "'" . trim(pg_escape_string($input))."'" ;},$gNamesArr));

  $query="SELECT searchValues.search_name as \"input\", array_agg( distinct (g.gene_name)) as \"genes\", array_agg(distinct (annotation.annot_desc, annotation.annotation_type_id)) \"annot\"
  FROM
  gene g inner join gene_annotation on gene_annotation.gene_id=g.gene_id
  inner join annotation on annotation.annotation_id=gene_annotation.annotation_id
  right join unnest(array[{$gNameValues}]) WITH ORDINALITY AS searchValues(search_name,ord) on search_name=g.gene_name
  group by searchValues.search_name, searchValues.ord
  order by searchValues.ord asc";
  
  // $query="SELECT searchValues.search_name as \"input\", array_agg( distinct (g.gene_name)) as \"genes\", array_agg(distinct (annotation.annot_desc, annotation.annotation_type_id)) \"annot\"
  // FROM
  // gene g inner join gene_annotation on gene_annotation.gene_id=g.gene_id
  // inner join annotation on annotation.annotation_id=gene_annotation.annotation_id
  // inner join annotation_type on annotation_type.annotation_type_id=annotation.annotation_type_id
  // right join unnest(array[{$gNameValues}]) WITH ORDINALITY AS searchValues(search_name,ord) on search_name=g.gene_name
  // group by searchValues.search_name, searchValues.ord
  // order by searchValues.ord asc";
  
  $dbRes=pg_query($query) or die('Query failed: ' . pg_last_error());
  echo "<table class=\"table table-striped table-bordered\" id=\"tblResults\"><thead><tr><th>input</th>";

  echo implode("",array_map(function($type) {return "<th style=\"min-width:200px\">{$type}</th>";},$annotTypes));
  echo "</tr></thead><tbody>";
  while($row=pg_fetch_array($dbRes,null, PGSQL_ASSOC)) {


  // Creating Gene columns:

		// Interpreting array returned by database - removing 3 characters in the end and at the beginning.
		$geneEntries=array_map(function($geneCol) { return explode(",",$geneCol);},explode(")\",\"(",substr($row["genes"],3,-3)));

		// Removing \" enclosing the the multi word gene names.
		array_walk($geneEntries,function(&$entry) {$entry[0]=str_replace("\\\"","",$entry[0]);});

		// Get all anotations for this row and creating the columns.
		$annotStr="";
    // Interpreting array returned by database - removing 3 characters in the end and at the beginning.
    $annotEntries=array_map(function($annotRow) {
      preg_match("/(.*),([^,]*)/",$annotRow,$matches);
      return array(0=>$matches[1],1=>$matches[2]);
    },explode(")\",\"(",substr($row["annot"],3,-3)));

    // Removing \" enclosing the the multi word annotation types.
    array_walk($annotEntries,function(&$entry){$entry[1]=str_replace("\\\"","",$entry[1]);});

    // Creating columns for each annotation filled with content if a matching annotation was found in the array.
    $annotStr=implode(array_map(function($type) use($annotEntries) {return "<td>" .
      implode(
        array_map(function($currAnnot){return str_replace("\\\"","",$currAnnot[0]);},
        array_filter($annotEntries,function($item) use($type) { return $item[1] == $type;})),
      ";")
    . "</td>";},$annotTypeIds));

    echo "<tr><td><a href=\"/aetar_db/gene.php?name={$row["input"]}\" target=\"_blank\">{$row["input"]}</a></td>{$annotStr}</tr>";

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
	dom:'Bfrtip',
  buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
  ]
});
  
$("#tblResults_filter").addClass("float-right");
$("#tblResults_info").addClass("float-left");
$("#tblResults_paginate").addClass("float-right");
  
//   buttons:[{
//     extend:'csv',
//     text:'Download',
//     title:"egdb_gene_annotation",
//     fieldBoundary: '',
//     fieldSeparator:"\t"
//   },
//   {
//     extend:'excel',
//     text:'Excel',
//     title:"",
//     fieldSeparator:"\t"
//   },
// 'copy'],
// bFilter:false,
// ordering:true,
// select:"multi+shiftString"

</script>


<?php include_once '../footer.php';?>
