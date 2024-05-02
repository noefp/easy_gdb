<!-- HEADER -->
<?php include_once realpath("../../header.php");?>

<div class="page_container" style="margin-top:20px">
  <h1 class="text-center">Passport</h1>
  <br>
  <ul>
    <?php 
    // find all directories in the passport path
      if ($dh = opendir("$passport_path")){

        while (($dir_name = readdir($dh)) !== false){ //iterate all dirs
          //echo "dir_name: $dir_name <br>";
          
          if (!preg_match('/^\./', $dir_name) && is_dir($passport_path."/".$dir_name) ){
            echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=$dir_name\">$dir_name</a></li>";
          }
          
        } //end while
      } // end open dir
    ?>
  </ul>
</div>
<br>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

