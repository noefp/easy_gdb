
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
    $("#avg_table").on('shown.bs.collapse', function(){

      $('#load').remove();
      $('#tblResults').css("display","table");
      datatable("#tblResults","");


      $(".td-tooltip").tooltip();
  });
});   
  
</script>
  
