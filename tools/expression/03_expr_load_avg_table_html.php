  <div class="data_table_frame">

    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#avg_table" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Average values
    </div>

    <div id="avg_table" class="collapse hide">

<?php
  
  echo implode("\n", $table_code_array);
  
?>

    </div> <!-- avg_table end -->
  
  </div> <!-- data_table_frame end -->
