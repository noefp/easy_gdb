<a href="/easy_gdb/tools/expression_input.php" class="float-right" style="text-decoration: underline;" target="_blank">Go to gene expression atlas</a>
<br>

<div class="page_container" style="margin-top:20px">
  <h1 class="text-center">Expression datasets</h1>
  <br>
  <ul>
    <?php 
      if ($dh = opendir("$custom_text_path/custom_pages/expression_dataset_info")){

        while (($file_name = readdir($dh)) !== false){ //iterate all files in dir
          
          if (!preg_match('/^\./', $file_name) && preg_match('/\.php/', $file_name) ){
            $data_set_name = str_replace(".php","",$file_name);
            
            echo '<li><a href="/easy_gdb/custom_view.php?file_name=expression_dataset_info/'.$file_name.'">'.$data_set_name.'</a></li>';
          }
          
        }
        
      }
    ?>
  </ul>

</div>

<br>


