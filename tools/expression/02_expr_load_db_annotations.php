<?php
  //################################################################################################## ADD ANNOTATIONS
  
  // Get annotation types
  include_once realpath ("$conf_path/database_access.php");
  
  $dbconn = 0;
  
  if (getConnectionString()) {
    $dbconn = pg_connect(getConnectionString());
  }
  
  if ($dbconn) {
    include_once("../get_annotation_types.php");
    
  	// load annotation links in hash
  	$external_db_annot_hash;

  	if ( file_exists("$json_files_path/tools/annotation_links.json") ) {
  	    $annot_json_file = file_get_contents("$json_files_path/tools/annotation_links.json");
  	    $external_db_annot_hash = json_decode($annot_json_file, true);
  	}
    
    
    // Getting all annotation types.
    $query="SELECT annotation_type_id,annotation_type from annotation_type"; // array with annotation type ids

    $res=pg_query($query) or die("Couldn't query database.");

    $annotTypes=pg_fetch_all_columns($res);
  
    $gNamesArr=array_filter(explode("\n",trim($_POST["gids"])),function($gName) {return ! empty($gName);});
  
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
  
  
    $annotations_hash2;
  
    while($row=pg_fetch_array($dbRes,null, PGSQL_ASSOC)) {
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
            if ($external_db_annot_hash[$annot_type]) {
              $q_link = $external_db_annot_hash[$annot_type];
              $q_link = preg_replace('/query_id/',$annot_row[0],$q_link);
            }

            array_push($terms_array,"<a href=\"$q_link\" target=\"_blank\">$annot_row[0]</a>");
          } //close if
        } // close foreach annot_row
      
      
        $gene_name = $row["input"];
      
        $annotations_hash2[$gene_name] .= "<td>".implode($terms_array,"; <br>")."</td><td>";
      
        foreach ($annotEntries as $annot_row) {
          if ($annot_row[2] == $type) {
            array_push( $annots_array, str_replace("\\\"","",$annot_row[1]) );
          }
        }
        $annotations_hash2[$gene_name] .= implode($annots_array,"; <br>")."</td>";

      } // close foreach type
    } // end while
  
  } // close if dbconnect
  
  //##################################################################################################
?>
