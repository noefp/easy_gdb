<!-- HEADER -->
<?php

use function PHPSTORM_META\map;

 include_once realpath("../../header.php");
      include_once realpath("../modal.html");?> 

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="#" target="blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a class="float-left pointer_cursor" style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>
<center><h1>Variant Explorer</h1></center>
<br>
<!-- HTML -->
 <!-- Loading animation -->
<div  id="loader_spinner" class="text-center">
      <div class="spinner-border" role="status"></div>
      </div>
<!-- End loading animation -->

<!-- Main container -->
<div id="page_container_main" class="page_container" style="display:none">

<?php
  //get input variables 
  // $vcf_chr = $_GET["vcf_chr"];
  // $vcf_start = trim($_GET["vcf_start"]);
  // $vcf_end = trim($_GET["vcf_end"]);
  $snp_id = $_GET["snp_id"];
  $vcf_dir = $_GET["snp_file"];
  
  preg_match('/(\d+)_(\d+)/', $snp_id, $matches);
  $vcf_chr = 'chr'.$matches[1];
  $vcf_pos = $matches[2];

// ----------------Get JSON Information-----------------------------------------------------------------------------------------------------------------------------------------------------------

  $vcf_json_file = file_get_contents($json_files_path."/tools/vcf.json");
  $vcf_hash = json_decode($vcf_json_file, true);
  // }

  if($vcf_dir != "") {
    $jb_data_folder = $vcf_hash[$vcf_dir]["jb_data_folder"];
    $vcf_chr_file = $chr_file_array = $vcf_hash[$vcf_dir]["chr_files"][$vcf_chr];
    $snp_eff_subtable=$vcf_hash[$vcf_dir]["snp_eff_subtable"];
    $tabix_command = "tabix $vcf_path/$vcf_dir"."/$vcf_chr_file ".$vcf_chr.":".$vcf_pos."-".$vcf_pos;
  }else {
    $jb_data_folder = $vcf_hash["jb_data_folder"];
    $vcf_chr_file = $chr_file_array = $vcf_hash["chr_files"][$vcf_chr];
    $snp_eff_subtable=$vcf_hash["snp_eff_subtable"];
    $tabix_command = "tabix $vcf_path"."/$vcf_chr_file ".$vcf_chr.":".$vcf_pos."-".$vcf_pos;
  }
  //------------vfc file ------------------------
  //check if vfc file exists and get header line with all the accessions from the vcf file
  $header_array = [];
  $acc_list_in_array = [];
  $data_array = [];
  $vcf_chr_file_path = "$vcf_path/$vcf_dir/$vcf_chr_file";

  // if vcf_dir is empty, remove double slashes in the path
  $vcf_chr_file_path = str_replace("//", "/", $vcf_chr_file_path);
  
  if ( file_exists($vcf_chr_file_path)) {
    $header_cmd = "zgrep -m 1 \"^#CHROM\" $vcf_chr_file_path";
    $header_file = shell_exec($header_cmd);
    $header_array = explode("\t",$header_file);
    $header_file = "";

    $acc_array=array_slice($header_array,9); // get the accessions from the header line
    $acc_array = array_map('trim', $acc_array); // trim whitespace from each item

  // Retrieves the 'acc_list' parameter from the GET request then splits it by newlines, commas, or spaces, trims whitespace from each item then removes empty entries,
  // and reindexes the resulting array.
    $acc_list =array_values(array_filter(array_map('trim',preg_split("/[\n, ]+/",$_GET["acc_list"])))); 
    $acc_list_in_array = array_intersect(array_map('strtoupper',$acc_array) ,array_map('strtoupper', array_unique($acc_list))); // Returns an array containing all the values of array1 that are present in array2.  

    // run tabix
    ini_set( 'memory_limit', '1024M' );
    $tabix_out = shell_exec($tabix_command);
    // echo "<p>output: $tabix_out</p>";   
    $lines_array = array_filter(explode("\n",$tabix_out));
    $tabix_out = "";
  }
  // -------------------------------------------------------
  $html_array = array();
  // ---------------------------------------------------------
  

  // if no SNPs found, show alert
  if(!isset($lines_array)) {
    echo "<div class=\"alert alert-danger\" role=\"alert\" style=\"text-align:center; margin-top:10px\"> <b>$vcf_chr_file not found</b></div>";
  }
  else if( count($lines_array) == 0 ) {
    echo "<div class=\"alert alert-danger\" role=\"alert\" style=\"text-align:center; margin-top:10px\"> <b> $snp_id </b> not found</div>";
  }
  else if(count($acc_list) == 0)
  {
    echo '<div class="alert alert-danger" role="alert" style="text-align:center; margin-top:10px"> <b>Acc ID list empty </b> </div>';
  }
  else if(count($acc_list_in_array) == 0)
  {
    echo '<div class="alert alert-danger" role="alert" style="text-align:center; margin-top:10px"> <b>Acc ID list does not match </b> </div>';
  }
  else
  {
    // filter the acc id list
    echo '<div style="display: flex; justify-content: flex-end;" class="form-inline"><label for="searchBox">Filter by:</label><input type="text" id="searchBox" class="form-control" style="margin-left:5px; width:auto ; margin-bottom:5px" placeholder="Accession IDs..."></div>';

    // ----General Container with the checkbox list and buttons
    echo '<div class=" alert bg-light" role="alert" style="border: 1px solid #ddd;">';
    
    // buttons group (select all and unselect all) and title
    echo '
    <div class="d-flex flex-wrap align-items-center justify-content-start position-relative">
      
      <div class="d-flex flex-nowrap">
        <button id="selectAllButton" class="btn btn-secondary" type="button"><span>Select all</span></button>
        <button id="unselectAllButton" class="btn btn-secondary" style="margin-left:.5rem" type="button"><span>Unselect all</span></button>
      </div>

      <!-- Responsive title for larger screen view -->
      <!-- This will be displayed only on medium and larger screens -->
      <div class="d-none d-md-block" style="position: absolute; left: 50%; transform: translateX(-50%);">
        <h5>Acc ID list</h5>
      </div>

      <!-- Responsive title for smaller screen view -->
      <!-- This will be displayed only on small screens --> 
      <div class="d-block d-md-none" style="width: 100%;text-align: center; margin-top:0.5rem;">
        <h5 ">Acc ID list</h5>
      </div>

    </div>';

    // checkbox list
    echo'<br>
    <div id="checkboxContainer" class="row">';

      foreach($acc_list_in_array as $acc) {
        echo "<label  class=\"pointer_cursor\"><input type=\"checkbox\" class=\"pointer_cursor\" value=$acc checked>$acc</label>";
      }
    echo '</div>';
    // end checkbox list

    // button to reload the table
    echo '<div style="display: flex; justify-content: flex-end;"><button id="selectButton" class="btn btn-primary" style="margin:15px 0px 0px 0px">Reload table</button></div>';
    echo '</div>';

  // End General container with checkbox list and buttons ------------------    
    echo "<div class=\"card bg-light text-dark\">";
    echo " <div style=\"text-align:center\" class=\"card-body\"><b>SNP ID: </b> $snp_id</div>";
    echo "</div>";
 

    // -----Print SNPs table-----------

    echo "<br><div id=\"load\" class=\"loader\"></div>";
    echo '<div id="no_match" class="alert alert-danger" role="alert" style="text-align:center; margin-top:10px;display:none"> <b> No Acc IDs found  </b> </div>';


    echo '<div id="table_container" style="display:show; margin-top:20px">';

    echo "<table id=\"snp_table\" class=\"table table-striped table-bordered\" style=\"line-height: 1; font-size:14px;\">\n";
    echo "<thead><tr>";
    echo "<th>ACC</th>";
    echo " <th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th>";
    if($snp_eff_subtable)
    {
      echo "<th>SNPeff</th>";
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


    // print_r($result);

    // add header with the acc information
    foreach($acc_table_header as $th) {
      echo "<th>$th</th>";
    }
    echo "</tr></thead>\n";
  // ---------------END Header table---------------------------------------------

  // ------------Start Body table--------------------------------------------
  $html_snp_table_array = [];
  echo "<tbody>\n";

  foreach ($lines_array as $vcf_line) {
    
    $data_array = explode("\t",trim($vcf_line));

    // snp information
    $snp_info = array_slice($data_array,0,8);

    // array_push($html_snp_table_array,"<tr>");
  
    foreach ($snp_info as $index => $snp_val) {

      if ($index == 0 || $index == 3) {
        array_push($html_snp_table_array,"<td>$snp_val</td>");
      }
      elseif($index == 4){
        array_push($html_snp_table_array,"<td>$snp_val</td>");
        $alt=$snp_val;
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
    $acc_filtered = array_intersect(array_keys($acc_filtered_data),array_keys($acc_list_in_array)); // array_keys returns the index of the array

    foreach ($acc_filtered as $acc) {
      $acc_info_array = explode(':',$acc_filtered_data[$acc]);
      $acc_name = $acc_list_in_array[$acc];
      echo "<tr><td><a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$vcf_dir&acc_id=$acc_name\" target=\"_blank\">$acc_name</a></td>";
      echo implode("",$html_snp_table_array);

      // ---------------------------------------------------------------------------------------------
      $cont=0;
      foreach ($acc_table_header as $acc_header_name) {
        if(in_array($acc_header_name,explode(':',$acc_info_alleles)))
        {
          echo "<td>$acc_info_array[$cont]</td>";
          $cont++;
        }else
        {
          echo "<td>.</td>";
        }
      }

      // ----------------------------------------------------------------------------------------------
      
      // foreach ($acc_array as $acc_value) {
      //   echo "<td>$acc_value</td>";
      // }


      echo "</tr>\n";
      }
      $html_snp_table_array = [];
    }
  echo "</tbody></table>\n";
  
  // ------------End Body table--------------------------------------------
  }
  echo "</div>\n";
?>
<br>
<br>
</div> <!-- page_container -->

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>
<!-- END HTML -->


<!-- CSS -->
 <style>

.acc_link {
  cursor:pointer;
}

#selected_snp_table th, td {
  padding: .25rem;
}


 #subtable td, #subtable th {
    padding-left: 10px !important;
    padding-right: 10px !important;
    border: 1px solid #ddd !important;
  }

  #checkboxContainer {
  display: flex ;
  padding: auto;
  max-height: calc(3*16px + 35px); 
  overflow-y: auto;
  width: 100%;
  box-sizing: border-box;
}

#checkboxContainer label {
  margin-left:45px; 
  display:inline-flex;
  white-space: nowrap;
}

#load {
  display: none;
}

</style>
<!-- END CSS -->


<!-- JS DATATABLE -->
 <script src="../../js/datatable.js"></script>
<script type="text/javascript">

  var acc_list_in_array = <?php echo json_encode(array_values($acc_list_in_array)); ?>;

$(document).ready(function() {

  // label width adjustment according to the longest string in the acc_list_in_array
  $('#checkboxContainer label').ready(function() {
    const maxStringLength = Math.max(...acc_list_in_array.map(string => string.length));
    $('#checkboxContainer label').css('width', 10*maxStringLength+'px'); 
  });

  // if sarchBox is written or deleted, filter the checkbox list
  $("#searchBox").on("keyup", function() {
    var searchValue = $(this).val().toLowerCase();
    var acc_array_filtered = acc_list_in_array.filter(function(acc) {
      return acc.toLowerCase().includes(searchValue);
    });

    $('#checkboxContainer label').each(function() {
      var labelText = $(this).text();
      if ($.inArray(labelText, acc_array_filtered) !== -1) {
        // console.log("El texto se encuentra en el array");
        $(this).show();
      } else {
        // console.log("El texto no se encuentra en el array");
        $(this).hide();
      }
    });
  });

  // select all checkboxes
  $('#selectAllButton').click(function() {
    $('#checkboxContainer input[type="checkbox"]').each(function() {
      $(this).prop('checked', true);
    });
    $(this).blur(); // remove focus from the button
  });

  // unselect all checkboxes
  $('#unselectAllButton').click(function() {
    $('#checkboxContainer input[type="checkbox"]').each(function() {
      $(this).prop('checked', false);
    });
    $(this).blur(); // remove focus from the button
  });

  // Reload the table with selected checkboxes
  $('#selectButton').click(function() {
    var selectedAccs = [];
    $('#checkboxContainer input[type="checkbox"]:checked').each(function() {
      selectedAccs.push($(this).val());
    });
    // console.log("Selected Accs: ", selectedAccs);
    if (selectedAccs.length > 0) {
      $('#load').show();
      $('#table_container').hide();
      $('#no_match').hide();
      // console.log("Selected Accs: ", selectedAccs);
         // Call the PHP file to get the SNPs with the selected Accs
      ajax_get_SNPs_table(selectedAccs);
      
    } else {
      // alert("Please select at least one Accession ID.");
      $("#search_input_modal").html( "Please select at least one Accession ID." );
      $('#no_gene_modal').modal();
    }
    $(this).blur(); // remove focus from the button
  }); 

  
  // AJAX call to get the SNPs table with the selected Accs
  function ajax_get_SNPs_table(selectedAccs) {
    jQuery.ajax({
        type: "POST",
        url: 'vcf_ajax_acc_list_data.php',
        data: {
          // 'acc_list': selectedAccs,
          'tabix_cmd': "<?php echo $tabix_command;?>",
          'jb_data_folder': "<?php echo $jb_data_folder;?>",
          'snp_eff_subtable': JSON.stringify(<?php echo json_encode($snp_eff_subtable);?>), //snp_eff_subtable
          'acc_table_header': JSON.stringify(<?php echo json_encode($acc_table_header); ?>),
          'acc_array': JSON.stringify(<?php echo json_encode($acc_list_in_array); ?>),
          'acc_list':  JSON.stringify(selectedAccs),
          'vcf_dir': "<?php echo $vcf_dir; ?>"
        },
        success: function(html_array) {

          var table_rows = JSON.parse(html_array); 
          $('#snp_table').DataTable().clear().destroy();
   
          $('#snp_table').html(table_rows.join());  
          $('#load').hide();

            if($("#snp_table tbody tr").length == 0) {
              $('#table_container').hide();
              $("#no_match").show();
            }else{
              $('#table_container').show();
              datatable_with_subtable('#snp_table','#subtable');
              $("#snp_table_filter").addClass("float-right")
              $("#snp_table_info").addClass("float-left");
              $("#snp_table_paginate").addClass("float-right");
            }



          }
      });
  }

  $('#loader_spinner').remove();
  $('#page_container_main').show();

  if($("#snp_table tbody tr").length == 0) {
    $('#table_container').hide();
    $("#no_match").show();
  }else{
    datatable_with_subtable('#snp_table','#subtable');
    $("#snp_table_filter").addClass("float-right")
    $("#snp_table_info").addClass("float-left");
    $("#snp_table_paginate").addClass("float-right")
  }
});

</script>
