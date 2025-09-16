<?php

// GET selected line (one position SNP) tabix command and vcf_file with path from AJAX call
$tabix_cmd = $_POST['tabix_cmd']; 
$jb_data_folder = $_POST['jb_data_folder'];
$snp_eff_subtable = json_decode($_POST['snp_eff_subtable'],true);
$acc_array =json_decode($_POST['acc_array'],true);
$acc_list = json_decode($_POST['acc_list'],true);
$acc_table_header = json_decode($_POST['acc_table_header'],true);
$vcf_dir = $_POST['vcf_dir'];


$acc_list_in_array = array_intersect($acc_array,$acc_list); // Returns an array containing all the values of array1 that are present in array2.

// get VCF line using tabix
ini_set( 'memory_limit', '1024M' );
$tabix_out = shell_exec($tabix_cmd);
$vcf_line = trim($tabix_out);
$lines_array = array_filter(explode("\n",$tabix_out));
$tabix_out = "";

$html_snp_acc_table_array = [];

//  array_push($html_snp_acc_table_array,"<table id=\"snp_table\" class=\"table table-striped table-bordered\" style=\"line-height: 1; font-size:14px;\">");
 array_push($html_snp_acc_table_array,"<thead><tr><th>ACC</th><th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th>");
    if($snp_eff_subtable)
    {
      array_push($html_snp_acc_table_array,"<th>SNPeff</th>");
    }
    // add header table columns acc information
    // gets the header of the first row read at position 8 in the array
    // $data_columns = explode("\t",$lines_array[0]);
    // $acc_table_header = explode(':',$data_columns[8]);

  // get all the format information from all the lines
    $format_array = array_map(function($line) {
      return explode(':',explode("\t", $line)[8]);
    }, $lines_array);

    // get the longest array in format
    $acc_table_header=[];
    foreach($format_array as $format)
    {
      if(count($format)>count($acc_table_header))
      {
        $acc_table_header=$format;
      }
    }


    // add header with the acc information
    foreach($acc_table_header as $th) {
      array_push($html_snp_acc_table_array,"<th>$th</th>");
    }
    array_push($html_snp_acc_table_array,"</tr></thead>");

// create array to store results in html format, ready to join for printing


$html_snp_table_array = [];
array_push($html_snp_acc_table_array,"<tbody>");

// get snp information
foreach ($lines_array as $vcf_line) {

  $data_array = explode("\t",trim($vcf_line));

  // snp information
  $snp_info = array_slice($data_array,0,8);

foreach ($snp_info as $index => $snp_val) {

  if ($index == 0 || $index == 3 || $index == 4) {
    array_push($html_snp_table_array,"<td>$snp_val</td>");
  } 
  elseif ($index == 1) {
    $jbrowse_link = "/jbrowse/?data=data%2F$jb_data_folder&loc={chr}%3A{start}..{end}";
    $jbrowse_link = str_replace("{chr}",$snp_info[0],$jbrowse_link);
    $jbrowse_link = str_replace("{start}",$snp_info[1]-50,$jbrowse_link);
    $jbrowse_link = str_replace("{end}",$snp_info[1]+50,$jbrowse_link);
    array_push($html_snp_table_array,"<td><a href=\"$jbrowse_link\" target=\"_blank\">$snp_val</a></td>");
  }  
  elseif ($index == 2) {
    array_push($html_snp_table_array,"<td style='font-size:12px'>Ca1_$snp_info[1]</td>");
  } 
  elseif ($index == 7 && $snp_eff_subtable) {
    $snp_eff = preg_replace("/.+ANN=/",'',$snp_val);
    $snp_eff_array = explode(",",$snp_eff); // in case there are multiple annotations separated by commas
        
    $eff_array_data = array_map(function($eff) {
      return explode("|",$eff);
    }, $snp_eff_array);

    $snp_eff_array_count = count($snp_eff_array);

    $eff_array_length = count(explode("|",$snp_eff_array[0]));

    
      $snpeff_table = "<table id='subtable'  style=\"font-size:11px; width:100%\">";
      $snpeff_table.="<tr>";
      foreach($snp_eff_subtable["head"] as $head)
      {
        $snpeff_table.="<th>$head</th>";
      }
      $snpeff_table .= "</tr>";

    for($x=0;$x<$snp_eff_array_count;$x++)
    {
      $snpeff_table .="<tr>";
      foreach($snp_eff_subtable["data"] as $data)
      {
        $data = $data-1;
        $snpeff_table .="<td>".$eff_array_data[$x][$data]."</td>";
      }
      $snpeff_table .="</tr>";
    }

      $snpeff_table .= "</table>";
      array_push($html_snp_table_array,"<td style='padding:0 !important'>$snpeff_table</td>");
    } 
}
// end snp information

// acc information
    $acc_info = array_slice($data_array,9);
    $acc_info_alleles = $data_array[8];
    $data_array = [];
    $acc_filtered_data = preg_grep("/^[0-1]\/1\:/", $acc_info);
    $acc_filtered = array_intersect(array_keys($acc_filtered_data),array_keys($acc_list_in_array));

    foreach ($acc_filtered as $acc) {
      $acc_array = explode(':',$acc_filtered_data[$acc]);
      $acc_name = $acc_list_in_array[$acc];
      array_push($html_snp_acc_table_array, "<tr><td><a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$vcf_dir&acc_id=$acc_name\" target=\"_blank\">$acc_name</a></td>");
      array_push($html_snp_acc_table_array,$html_snp_table_array);

      // ---------------------------------------------------------------------------------------------
      $cont=0;
      foreach ($acc_table_header as $acc_header_name) {
        if(in_array($acc_header_name,explode(':',$acc_info_alleles)))
        {
          array_push($html_snp_acc_table_array,"<td>$acc_array[$cont]</td>");
          $cont++;
        }else
        {
          array_push($html_snp_acc_table_array,"<td>.</td>");
        }
      }

      // ----------------------------------------------------------------------------------------------
      
      // foreach ($acc_array as $acc_value) {
      //   echo "<td>$acc_value</td>";
      // }


      array_push($html_snp_acc_table_array,"</tr>\n");
    }
  $html_snp_table_array = [];
  }
 array_push($html_snp_acc_table_array,"</tbody></table>");
// return results in an array with the html code ready to print
// echo json_encode($html_array);
echo json_encode($html_snp_acc_table_array);


?>
