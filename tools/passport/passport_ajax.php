<?php

// GET selected line (one position SNP) tabix command and vcf_file with path from AJAX call
$col_index = $_POST["col_index"]+1;
$pass_file = $_POST["query_file"];

// create array to store results in html format, ready to join for printing

// get VCF line using tabix
$no_spc_file = str_replace(" ","\ ",$pass_file);

$shell_cmd = "tail -n +2 $no_spc_file | cut -f $col_index | sort -u";
$shell_res = shell_exec($shell_cmd);

$is_numeric = 1;


if ( preg_match("/[A-Za-z]/", $shell_res) || $shell_res =="\n" || $shell_res == null ){
  $is_numeric = 0;
}

$shell_array = explode("\n",$shell_res);
// $shell_res = "";
$html_array = array();

if ($is_numeric) {
  
  array_push($html_array, "<option name=\"gt\">></option>");
  array_push($html_array, "<option name=\"gteq\">>=</option>");
  array_push($html_array, "<option name=\"eq\">=</option>");
  array_push($html_array, "<option name=\"leq\"><=</option>");
  array_push($html_array, "<option name=\"lt\"><</option>");
  
} else {
    foreach (array_filter($shell_array) as $option) {
      array_push($html_array,"<option name=\"$option\">$option</option>");
    }
  } 

// echo json_encode($shell_res);
$shell_res = "";
echo json_encode($html_array);


?>
