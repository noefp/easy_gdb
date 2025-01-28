<?php

// GET selected line (one position SNP) tabix command and vcf_file with path from AJAX call

$table_id = $_POST["id"];
$file_path = $_POST["path"];
$unique_link= $_POST["link"];
$pass_dir=$_POST["pass"];

$html_array=[];

// create array to store results in html format, ready to join for printing

$tab_file = file($file_path);

// get header array by columns
$file_header = array_shift($tab_file);
$header_cols = explode("\t", $file_header);
    
// array_push($html_array,"<div id=$table_id class=\"p-7 my-3 table_collapse\">");
// array_push($html_array,"<div class=\"data_table_frame\">");
array_push($html_array,"<table id=\"tblAnnotations_$table_id\" class=\"tblAnnotations table table-striped table-bordered\">\n");

    $field_number = 0;

    // //   TABLE HEADER
 array_push($html_array,"<thead><tr>\n");

    foreach ($header_cols as $head_index => $hcol) {

    array_push($html_array,"<th>$hcol</th>");
        
    // find column index for unique identifier that will link to accession info
    if ($unique_link == $hcol) {
        $field_number = $head_index;
    }
} //close foreach
      
array_push($html_array, "</tr></thead><tbody>");
      
foreach ($tab_file as $line) { 
    $columns = explode("\t", $line);
    array_push($html_array, "<tr>");
    foreach ($columns as $col_index => $col) {
            if ($col_index == $field_number) {
              array_push($html_array,"<td><a href=\"03_passport_and_phenotype.php?pass_dir=$GLOBALS[pass_dir]&acc_id=$col\">$col</a></td>");
            } else {  
              array_push($html_array,"<td>$col</td>");
            }
    } // end foreach columns
        array_push($html_array,"</tr>");
} // end foreach lines

array_push($html_array,"</tbody></table><br><br></div>");

echo json_encode($html_array);
?>


