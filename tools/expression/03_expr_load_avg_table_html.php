
  <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#avg_table" aria-expanded="true">
    <i class="fas fa-sort" style="color:#229dff"></i> Average values
  </div>

  <div id="avg_table" class="collapse hide">

  <div id="load" class="loader"></div>

  <!-- <div id="avg_table_frame" class="data_table_frame hide"> -->

<?php
  
  echo implode("\n", $table_code_array);
  
?>

    <!-- </div>  data_table_frame end -->
  
  </div> <!-- avg_table end -->
  
  <style>
 
 table.dataTable td,th  {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; 
  }
  
    .td-tooltip {
      cursor: pointer;
    }
  
  </style>


<script src="../../js/datatable.js"></script>
<script type="text/javascript">
$(document).ready(function(){

//  comparatos table flags
let comparator_table = <?php echo json_encode(isset($comparator_table) && $comparator_table==1 ? 1 : 0) ?>;

    $("#avg_table").on('shown.bs.collapse', function(){

      $('#load').remove();
      $('#tblResults').css("display","table"); // transform the table information into table format

    
    // ################ Comparator table ################

      if (comparator_table)
       {
        // add the dataset header to the table 
        var dataseetHeader = <?php echo json_encode((isset($dataset_header) && !empty($dataset_header) ? implode("",$dataset_header) : ""))?>;
        var Header = '<tr><th style="border: none"></th>' + dataseetHeader + '</tr>';

      // insert a new row with the dataset header at the beginning of the table header <thead>
      if(dataset_header !== "")
        {$('#tblResults thead:eq(0)').prepend(Header);}

        // initialize the datatable with the BASIC TABLE
        datatable_basic("#tblResults");          
      }
      else
    // ################ Expression table ################ 

      {datatable("#tblResults","");}
      // $(".td-tooltip").tooltip();
  });
});     
  
</script>
  
