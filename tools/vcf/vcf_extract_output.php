<!-- HEADER -->
<?php include_once realpath("../../header.php");?> 

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a class="float-left pointer_cursor" style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<center><h1>Variant Explorer</h1></center>

<br>
<!-- HTML -->
<div class="page_container">

<?php
  //get input variables
  
  $vcf_chr = $_GET["vcf_chr"];
  $vcf_start = trim($_GET["vcf_start"]);
  $vcf_end = trim($_GET["vcf_end"]);
  $vcf_dir = $_GET["snp_file"];
  // echo "<p>vcf_chr: $snp_file</p>";

  // if (file_exists("$vcf_path/vcf.json")) {
  $vcf_json_file = file_get_contents($json_files_path."/tools/vcf.json");
  $vcf_hash = json_decode($vcf_json_file, true);
  // }

  if($vcf_dir != "") {

    $passport_dir = $vcf_hash[$vcf_dir]["passport_folder"];
    $jb_data_folder = $vcf_hash[$vcf_dir]["jb_data_folder"];
    $vcf_chr_file = $chr_file_array = $vcf_hash[$vcf_dir]["chr_files"][$vcf_chr];
    $snp_eff_subtable=$vcf_hash[$vcf_dir]["snp_eff_subtable"];
    $tabix_command = "tabix $vcf_path/$vcf_dir"."/$vcf_chr_file ".$vcf_chr.":".$vcf_start."-".$vcf_end;
  }else {

    $passport_dir = $vcf_hash["passport_folder"];
    $jb_data_folder = $vcf_hash["jb_data_folder"];
    $vcf_chr_file = $chr_file_array = $vcf_hash["chr_files"][$vcf_chr];
    $snp_eff_subtable=$vcf_hash["snp_eff_subtable"];
    $tabix_command = "tabix $vcf_path"."/$vcf_chr_file ".$vcf_chr.":".$vcf_start."-".$vcf_end;
  }
  
  // run tabix
  // $tabix_command = "tabix $vcf_path"."/$vcf_chr_file ".$vcf_chr.":".$vcf_start."-".$vcf_end;
  //echo "<br><p>command: $tabix_command</p>";
  ini_set( 'memory_limit', '1024M' );
  $tabix_out = shell_exec($tabix_command);
  //echo "<p>output: $tabix_out</p>";
  
  $lines_array = array_filter(explode("\n",$tabix_out));
  $tabix_out = "";

  //print SNPs table
  if( count($lines_array) == 0) {
    echo "<div class=\"alert alert-danger\" role=\"alert\" style=\"text-align:center; margin-top:10px\"> <b>No SNPs found</b> in $vcf_chr $vcf_start-$vcf_end</div>";
  }
  else
  {
    echo '<div class="row">';
    echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">';
    
    echo "<div class=\"card bg-light text-dark\">";
    echo "  <div class=\"card-body\"><b>SNPs found:</b> ".count($lines_array)." in $vcf_chr $vcf_start-$vcf_end</div>";
    echo "</div><br>";

    echo "<table id=\"snp_table\" class=\"table table-striped table-bordered\" style=\"line-height: 1; font-size:14px; width:100%;\">\n";
    echo "<thead><tr><th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th>";
    if($snp_eff_subtable)
    {
      echo "<th>SNPeff</th>";
    }
    echo "<th></th></tr></thead>\n";

    echo "<tbody>\n";
  
  $table_counter = 1;
  
  foreach ($lines_array as $vcf_line) {
    
    $data_array = explode("\t",$vcf_line);
    
    $snp_info = array_slice($data_array,0,8);

    $data_array = [];
    
    echo "<tr>";
  
    foreach ($snp_info as $index => $snp_val) {

      if ($index == 0 || $index == 3 || $index == 4) {
        echo "<td>$snp_val</td>";
      } 
      elseif ($index == 1) {
        $jbrowse_link = "/jbrowse/?data=data%2F$jb_data_folder&loc={chr}%3A{start}..{end}";
        $jbrowse_link = str_replace("{chr}",$snp_info[0],$jbrowse_link);
        $jbrowse_link = str_replace("{start}",$snp_info[1]-50,$jbrowse_link);
        $jbrowse_link = str_replace("{end}",$snp_info[1]+50,$jbrowse_link);
        echo "<td><a href=\"$jbrowse_link\" target=\"_blank\">$snp_val</a></td>";
      }  
      elseif ($index == 2) {
        echo "<td style='font-size:12px'>Ca1_$snp_info[1]</td>";
      } 
      elseif ($index == 7 && $snp_eff_subtable) {
        $snp_eff = preg_replace("/.+ANN=/",'',$snp_val);
        $eff_array = explode("|",$snp_eff);
        
       
          $snpeff_table = "<table id='subtable'  style=\"font-size:11px; width:100%\">";
          $snpeff_table.="<tr>";
          foreach($snp_eff_subtable["head"] as $head)
          {
            $snpeff_table.="<th>$head</th>";
          }
          $snpeff_table .= "</tr><tr>";

          foreach($snp_eff_subtable["data"] as $data)
          {
            $data = $data-1;
            $snpeff_table .="<td >$eff_array[$data]</td>";
          }

          $snpeff_table .="</tr>";
          $snpeff_table .= "</table>";
          echo "<td style='padding:0 !important'>$snpeff_table</td>";
        } 
    }
    echo "<td><a class=\"acc_link\" name=\"snp_pos\" value=\"$snp_info[1]\"\>ACC Info</a></td>";
    echo "</tr>\n";
  }
  echo "</tbody></table>\n";
  }
  
  echo "</div>\n";
  
  

// <!-- HTML ACC info tables -->
if(count($lines_array) > 0)
{ echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
    <div class="card bg-light text-dark">
      <div class="card-body" style="width: auto; overflow: auto">
      <table id="selected_snp_table" class="table-bordered" style="line-height: 1; font-size:14px;text-align:center;vertical-align:middle; width:100%">
      <thead><tr id="selected_snp_head"><th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th>';
  if($snp_eff_subtable)
  {
    echo "<th>SNPeff</th>";
  }
  echo '</tr></thead>
      <tbody>
        <tr id="selected_snp"></tr>
      </tbody>
    </table>
  </div></div><br>
  <div style="width: auto; overflow: auto">
    <table id="acc_table" class="table table-striped table-bordered"  style="line-height:1;font-size:14px">
      <thead><tr><th>Click on ACC info to visualize accession details</th></tr></thead>
    </table>
  </div>  
  </div>
</div>
';
  //refresh variables
  $snp_info = [];
  $lines_array = [];}
?>

<br>
<br>
</div> <!-- page_container -->
<!-- END HTML -->
<vr>
<!-- CSS -->
 <style>

.acc_link {
  cursor:pointer;
}

#selected_snp_table th, td {
  padding: .25rem;
}

#snp_table > tbody > tr > td:last-child
{
    position: sticky !important;
    right: 0 !important;
    /* background-color: #fff; */
    border-left: 2px solid #ddd;
    z-index: 13;
}


#snp_table > tbody > tr:nth-child(odd) td:last-child {
  background: #f2f2f2;
}
#snp_table > tbody > tr:nth-child(even) td:last-child {
  background: #fff;
}

 #subtable td, #subtable th {
    padding-left: 10px !important;
    padding-right: 10px !important;
    border: 1px solid #ddd !important;
  }

</style>

<!-- JS DATATABLE -->
 <script src="../../js/datatable.js"></script>
<script type="text/javascript">
  //var counter = 0;
  var vcf_dir = "<?php echo "$vcf_dir" ?>";
  
  $('.acc_link').click(function () {

    // The row and header to show selected SNP
    var row = $(this).parent().parent().clone(); 
    var header = $('#snp_table_wrapper table thead tr').clone();

    header.find('th:last-child').remove()
    $('#selected_snp_head').html(header.html());
    
    row.children('td').last().remove(); // remove the last column with the link
    $('#selected_snp').html(row.html())
// -----------------------------------------------------------

    var snp_pos = $(this).attr('value');

    if(vcf_dir != "") {
      var tabix_cmd = "<?php echo "tabix $vcf_path/$vcf_dir"."/$vcf_chr_file"." ".$vcf_chr.":" ?>"+snp_pos+"-"+snp_pos;
      //var acc_path = "<?php //echo "$root_path"."/".$downloads_path."/vcf/acc_header.txt" ?>";
      var vcf_file = "<?php echo "$vcf_path/$vcf_dir"."/$vcf_chr_file" ?>";
      
    }else {
      var tabix_cmd = "<?php echo "tabix $vcf_path"."/$vcf_chr_file"." ".$vcf_chr.":" ?>"+snp_pos+"-"+snp_pos;
      //var acc_path = "<?php //echo "$root_path"."/".$downloads_path."/vcf/acc_header.txt" ?>";
      var vcf_file = "<?php echo "$vcf_path"."/$vcf_chr_file" ?>";
    }
    var passport_dir = "<?php echo "$passport_dir" ?>";
   
    //alert("acc_path: "+acc_path );

    //call PHP file ajax_get_names_array.php to get the gene list to autocomplete from the selected dataset file
    function ajax_call(tabix_cmd,vcf_file,passport_dir,vcf_dir) {
    
      jQuery.ajax({
        type: "POST",
        url: 'vcf_ajax_acc_data.php',
        data: {'tabix_cmd': tabix_cmd, 'vcf_file': vcf_file, 'passport_dir': passport_dir, 'vcf_dir':vcf_dir},

        success: function (html_array) {
          //alert("hi: "+html_array);
          
          var table_rows = JSON.parse(html_array);
          
          
          if ($.fn.DataTable.isDataTable("#acc_table")) {
            $('#acc_table').DataTable().clear().destroy();
          }
            
          $('#acc_table').html(table_rows.join());
            
          //  datatable('#acc_table',''); 
            $("#acc_table").dataTable({
              dom:'Bfrtlpi',
              "oLanguage": {
                "sSearch": "Filter by:"
                },
                buttons: [
                  {
                    extend: 'copy',
                    exportOptions: {
                      columns: ':visible'
                    }
                  },
                  {
                    extend: 'csv',
                    exportOptions: {
                      columns: ':visible'
                    }
                  },
                  {
                    extend: 'excel',
                    exportOptions: {
                      columns: ':visible'
                    }
                  },
                  {
                    extend: 'pdf',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                      columns: ':visible'
                    }
                  },
                  'colvis'
                ],
                "sScrollX": "100%",
                "sScrollXInner": "100%",
                "bScrollCollapse": true,
              });

            $("#acc_table_filter").addClass("float-right");
            $("#acc_table_info").addClass("float-left");
            $("#acc_table_paginate").addClass("float-right");          
        }
      });
    
    }; // end ajax_call
    
    ajax_call(tabix_cmd,vcf_file,passport_dir,vcf_dir);
    // return true;
  });
  
  
  // $("#snp_table").dataTable({
  //   // fixedColumns: {
  //   //   // leftColumns: 0, // the first column will not be fixed
  //   //   rightColumns: 1 // the last column will be fixed
  //   // },
  //   dom:'Bfrtlpi',
  //   "oLanguage": {
  //     "sSearch": "Filter by:"
  //     },
  //   buttons: [
  //     'copy', 'csv', 'excel',
  //       {
  //         extend: 'pdf',
  //         orientation: 'landscape',
  //         pageSize: 'LEGAL'
  //       },
  //      'colvis'
  //     ],
  //       "sScrollX": "100%",
  //       "sScrollXInner": "100%",
  //       "bScrollCollapse": true,
  //   });

  datatable_with_subtable('#snp_table','#subtable');

  $("#snp_table_filter").addClass("float-right")
  $("#snp_table_info").addClass("float-left");
  $("#snp_table_paginate").addClass("float-right");

</script>



<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>