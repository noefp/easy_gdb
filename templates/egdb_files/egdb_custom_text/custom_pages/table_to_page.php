<!-- http://localhost:8000/easy_gdb/custom_view.php?file_name=table_to_page.php&table_name=table_eg.txt&link_field=ACC%20Name -->


<?php 
  $table_file = test_input($_GET["table_name"]);
  $link_field = test_input($_GET["link_field"]);
  $table_title = str_replace(".txt", "", $table_file);
?>

<div class="page_container" style="margin-top:20px">
  <h1 class="text-center"><?php echo "$table_title" ?></h1>
  <br>
  <div class="data_table_frame">

<?php
if ( file_exists("$custom_text_path/custom_pages/$table_file") ) {
  $tab_file = file("$custom_text_path/custom_pages/$table_file");

  echo "<table class=\"table\" id=\"tblResults\"><thead><tr>";
  
  $columns = [];
  $row_count = 0;
  $col_count = 0;
  $field_number = 0;
  
  foreach ($tab_file as $line) {
    $columns = explode("\t", $line);

    if ($row_count == 0) {
      foreach ($columns as $col) {
        echo "<th>$col</th>";
        $col_count++;
        if ($link_field == $col) {
          $field_number = $col_count;
        }
      }
      echo "</tr></thead>";
      
      $col_count = 0;
    } 
    else {
      echo "<tr>";

      foreach ($columns as $col) {
        $col_count++;
        
        if ($col_count == $field_number) {
          echo "<td><a href=\"/easy_gdb/custom_view.php?file_name=row_data.php&row_data=".$table_file.",".$row_count.",".($field_number-1)."\">$col</a></td>";
          // echo "<td><a href=\"row_data.php?row_data=".$table_file.",".$row_count.",".($field_number-1)."\">$col</a></td>";
        } else {
          echo "<td>$col</td>";
        }
      }
      echo "</tr>";
      $col_count = 0;
    }
    $row_count++;
  
  
  }

  echo "</table>";
  
}
?>

  </div>
</div>

<br>

<script type="text/javascript">
  $("#tblResults").dataTable({
  	"dom":'Bfrtip',
    "ordering": false,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
  });
  
  $("#tblResults_filter").addClass("float-right");
  $("#tblResults_info").addClass("float-left");
  $("#tblResults_paginate").addClass("float-right");

</script>


