<!-- http://localhost:8000/easy_gdb/custom_view.php?file_name=row_data.php&row_data=table_eg.txt,2,1 -->
<!-- row_data= tabular_file,row,title_column -->

<div style="max-width:900px; margin:auto">
  <br>
  <?php
  $row_data = test_input($_GET["row_data"]);
  [$table_file,$row_count,$title_col] = explode(",", $row_data);
    
  ?>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <?php 
        
        if ( file_exists("$custom_text_path/custom_pages/$table_file") ) {
          
          $tab_file = file_get_contents("$custom_text_path/custom_pages/$table_file");
          $rows = explode("\n", $tab_file);
          $cols = explode("\t", $rows[$row_count]);
          $header = explode("\t", $rows[0]);
          
          echo "<center><h1>".$cols[$title_col]."</h1></center><br>";
          for ($col_count = 0; $col_count <= sizeof($cols); $col_count++) {
            if ($header[$col_count]) {
              echo "<p><b>".$header[$col_count].":</b> ".$cols[$col_count]."</p>";
            }
          }
          
        }
      ?>
    </div>
  </div>


  <br>

</div>

