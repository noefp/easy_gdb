<?php

// GET selected line (one position SNP) tabix command and vcf_file with path from AJAX call
$tabix_cmd = $_POST["tabix_cmd"];
$vcf_file = $_POST["vcf_file"];

// create array to store results in html format, ready to join for printing
$html_array = array();

// get VCF line using tabix
$tabix_out = shell_exec($tabix_cmd);
$vcf_line = trim($tabix_out);
$tabix_out = "";

// get header line with all the accessions from the vcf file
$header_array = [];

$header_cmd = "zgrep -m 1 \"^#CHROM\" $vcf_file";

if ( file_exists($vcf_file)) {
  $header_file = shell_exec($header_cmd);
  $header_array = explode("\t",$header_file);
  $header_file = "";
}

// get columns of the vcf selected line
$data_array = explode("\t",trim($vcf_line));

// create ACC table header
$acc_table_header = explode(':',$data_array[8]);

array_push($html_array,"<thead><tr><th>ACC</th>");

foreach ($acc_table_header as $th) {
  array_push($html_array,"<th>$th</th>");
}
array_push($html_array,"</tr></thead>\n");

// start ACC table body
array_push($html_array,"<tbody>\n");

// find all accessions with variants
$acc_data = preg_grep("/^[0-1]\/1\:/", $data_array);
$data_array = [];

//iterate each matching column
foreach ($acc_data as $index => $col) {
  // print ACC linked to passport using the column index as key to find the ACC name in the header line
  array_push($html_array,"<tr><td><a href=\"#\">$header_array[$index]</a></td>");
  
  // save accession results in an array
  $acc_snp_array = explode(':',$col);
  
  // print acc results
  foreach ($acc_snp_array as $val) {
    array_push($html_array,"<td>$val</td>");
  }
    
  array_push($html_array,"</tr>\n");
}

array_push($html_array,"</tbody>\n");

$header_array=[];

// return results in an array with the html code ready to print
echo json_encode($html_array);

?>
