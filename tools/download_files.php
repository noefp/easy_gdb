
<?php
// include_once "conf/db_paths.php"; //to get downloads path

function get_dir_and_files($root, $dir_name, $sub_structure) {

  $dir_list = array(); //create a hash to store directories
  $file_list = array(); //create an array to store files
  $counter = 1;
  // echo "<p>DIR: $root/$dir_name</p>";
  
    if (is_dir($root."/".$dir_name)){

      if ($dh = opendir($root."/".$dir_name)){
        while (($file_name = readdir($dh)) !== false){ //iterate all files in dir

          if ( !preg_match('/^\./', $file_name) && !preg_match('/^_h5ai/', $file_name) ) { //discard hidden files
            
            if (!is_dir($root."/".$dir_name."/".$file_name)){
              //save downloadable files
              array_push($file_list,"<li><a href=\"/$dir_name/$file_name\" download>$file_name</a></li>");
              
              //print downloadable files
              //echo "<li><a href=\"/$dir_name/$file_name\" download>$file_name</a></li>";
            }
            else {
              //save directory path and name in hash
              $dir_list[$dir_name."/".$file_name]=$file_name;
            } 
          }
        } // end of while loop
        
        asort($file_list);
        
        //print downloadable files
        echo join("\n",$file_list);
        
        asort($dir_list);
        
        //iterate and print all subdirectories found
        foreach ($dir_list as $dir_name => $file_name) {
          if ($sub_structure) {//print and load subdirs
            echo "<h4 class=\"sub_header\">$file_name/</h4><ul class=\"download_list\" >";
            get_dir_and_files($root, $dir_name, 1);
            echo "</ul>";
          }
          else {//print first dirs and load subdirs
            $counter++;
            $link_name = preg_replace('/\s|\./', '', $file_name);
            echo "<h3><a class=\"collapsed\" href=\"#$link_name\" data-toggle=\"collapse\" aria-expanded=\"true\"><i class=\"fa fa-chevron-circle-down\"></i><i class=\"fa fa-chevron-circle-right\"></i> $file_name</a></h3><div id=\"$link_name\" class=\"card collapse show bg-light\"><div class=\"card-body\"><ul class=\"download_list\" >";

            get_dir_and_files($root, $dir_name, 1);
            echo "</ul></div></div><br>";
          }
        }
                
      } // end of opendir
    } // end of if is_dir
}

get_dir_and_files($root_path, $downloads_path, 0); // call the function for the downloads dir

?>

<style>

.download_list {
  padding-left:0px;
  font-size: 18px;
  margin-bottom: 0px;
  margin-left: 20px;
}

ul ul ul {
  list-style-type: circle;
}

.sub_header {
  font-size: 18px;
  margin-top: 10px;
  margin-bottom: 0px;
  color: #555;
}

[aria-expanded="true"] .fa-chevron-circle-right, 
[aria-expanded="false"] .fa-chevron-circle-down {
    display:none;
}

</style>
