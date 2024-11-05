<!-- HEADER -->
<?php include_once realpath("../../header.php");?>

<div class="page_container" style="margin-top:20px">
  <h1 class="text-center">Passport</h1>
  <br>
  <ul>
    <?php 
    // find all directories in the passport path
      if ($dh = opendir("$passport_path")){

        while ( ($dir_name = readdir($dh) ) !== false) { //iterate all dirs
          //echo "dir_name: $dir_name <br>";
          if (!preg_match('/^\./', $dir_name) && is_dir($passport_path."/".$dir_name) ) {
            
            // Display the main directory
            //echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=$dir_name\">$dir_name</a></li>"; // TAMBIÉN IMPRIME LAS QUE NO TIENEN SUBDIR

            // If there are subdirectories, iterate them
            $sub_path = $passport_path."/".$dir_name;
            $subdirs = false;

            if ($sub_dh = opendir($sub_path) ) {
              while ( ($sub_dir_name = readdir($sub_dh) ) !== false) { // iterate all subdir's

                if (!preg_match('/^\./', $sub_dir_name) && is_dir($sub_path."/".$sub_dir_name) ) {
                  $subdirs = true;
                  break;
                  //echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=$dir_name/$sub_dir_name\">$dir_name/$sub_dir_name</a></li>";
                }
              } // end while subdirs
              closedir ($sub_dh);
            } else {
              //if (!preg_match('/^\./', $dir_name) && is_dir($passport_path."/".$dir_name) ){
              //  echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=$dir_name\">$dir_name</a></li>";
              //}
            }

            if ($subdirs) {
              echo "<li><a href=\"view_subdirectories.php?dir_name=$dir_name\">$dir_name</a></li>";
            } else {
              echo "<li><a href=\"02_pass_file_to_datatable.php?dir_name=$dir_name\">$dir_name</a></li>";
            }

          }
          
        } //end while
        closedir($dh);
      } // end open dir
    ?>
  </ul>
</div>
<br>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php"); ?>

