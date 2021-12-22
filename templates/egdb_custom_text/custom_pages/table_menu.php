<!-- http://localhost:8000/easy_gdb/custom_view.php?file_name=table_to_page.php&table_name=table_eg.txt&link_field=ACC%20Name -->


<?php 
  $table_file = test_input($_GET["table_name"]);
  $link_field = test_input($_GET["link_field"]);
  $table_title = str_replace(".txt", "", $table_file);
?>

<div class="page_container" style="margin-top:20px">
  <h1 class="text-center">Tables Menu</h1>
  <br>
  <ul>
    <?php 
      if ($dh = opendir("$custom_text_path/custom_pages/tables")){

        while (($file_name = readdir($dh)) !== false){ //iterate all files in dir
          
          if (!preg_match('/^\./', $file_name) && preg_match('/\.txt/', $file_name) ){
            $toolbar_name = str_replace(".txt", "", $file_name);
            echo '<li><a href="/easy_gdb/custom_view.php?file_name=table_to_page.php&table_name=tables/'.$file_name.'&link_field=ACC Name" >'.$file_name.'</a></li>';
          }
          
        }
        
      }
    ?>
  </ul>

</div>

<br>


