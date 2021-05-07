<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">More</a>
  <div class="dropdown-menu">

    <?php 
      if ($dh = opendir("$custom_text_path/custom_pages")){

        while (($file_name = readdir($dh)) !== false){ //iterate all files in dir
          
          if (!preg_match('/^\./', $file_name) && preg_match('/\.php/', $file_name) ){
            $toolbar_name = str_replace(".php", "", $file_name);
            echo '<a class="dropdown-item" href="/easy_gdb/custom_view.php?file_name='."$file_name".'">'.$toolbar_name.'</a>';
          }
          
        }
        
      }
    ?>

  </div>
</li>

