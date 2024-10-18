
  <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#avg_table" aria-expanded="true">
    <i class="fas fa-sort" style="color:#229dff"></i> Average values
  </div>

  <div id="avg_table" class="collapse show">

    <div class="data_table_frame">

<?php
  
  echo implode("\n", $table_code_array);
  
?>

    </div> <!-- data_table_frame end -->
  
  </div> <!-- avg_table end -->
  
  <style>
 
    table.dataTable td  {
      max-width: 500px;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }
  
    .td-tooltip {
      cursor: pointer;
    }
  
  </style>

  <script type="text/javascript">
  $("#tblResults").dataTable({
    dom:'Bfrtlpi',
    "oLanguage": {
      "sSearch": "Filter by:"
      },
    buttons: [
      'copy', 'csv', 'excel',
        {
          extend: 'pdf',
          orientation: 'landscape',
          pageSize: 'LEGAL'
        },
      'print', 'colvis'
      ],
     "sScrollX": "100%",
      "sScrollXInner": "110%",
      "bScrollCollapse": true,
      "drawCallback": function( settings ) {
      $(".td-tooltip").tooltip();
    },
  });

  $("#tblResults_filter").addClass("float-right");
  $("#tblResults_info").addClass("float-left");
  $("#tblResults_paginate").addClass("float-right");

  $(document).ready(function(){
    $(".td-tooltip").tooltip();
  });
 

  </script>
  
