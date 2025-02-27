
<?php
// include_once "conf/db_paths.php"; //to get downloads path

function get_dir_and_files($root, $dir_name, $sub_structure, &$counter) {

  $dir_list = array(); //create a hash to store directories
  $file_list = array(); //create an array to store files
  // $counter = 1;
  // echo "<p>DIR: $root/$dir_name</p>";

    if (is_dir($root."/".$dir_name)){

      if ($dh = opendir($root."/".$dir_name)){
        while (($file_name = readdir($dh)) !== false){ //iterate all files in dir

          if ( !preg_match('/^\./', $file_name) && !preg_match('/^_h5ai/', $file_name) ) { //discard hidden files

            if (!is_dir($root."/".$dir_name."/".$file_name)){
							//save date of last modification
							$file_date = date("Y-m-d", filemtime("$root/$dir_name/$file_name"));
							//save file size
							$file_size = filesize("$root/$dir_name/$file_name");
							// format file size
							if ($file_size >= 1_073_741_824) { // Greater or equal to 1GB
								$file_size = number_format($file_size / 1_073_741_824, 1) . " GB";
							} elseif ($file_size >= 1_048_576) { // Greater or equal to 1MB
								$file_size = number_format($file_size / 1_048_576, 1) . " MB";
							} else { // Less than 1MB
								$file_size = number_format($file_size / 1024, 1) . " KB";
							}
							//add table name
							if (!$file_list) {
								array_push($file_list,"<div class=\"row\"><div class=\"col-sm-6\">File</div><div class=\"col-sm-3\">Last modified</div><div class=\"col-sm-3\">Size</div></div>");
							}
							//save downloadable files
              array_push($file_list,"<div class=\"row\"><div class=\"col-sm-6\"><a href=\"/$dir_name/$file_name\" download><i class=\"fas fa-file\"></i> $file_name</a></div><div class=\"col-sm-3\">$file_date</div><div class=\"col-sm-3\">$file_size</div></div>");

              //print downloadable files
              //echo "<li><a href=\"/$dir_name/$file_name\" download>$file_name</a></li>";
            }
            else {
              //save directory path and name in hash
              $dir_list[$dir_name."/".$file_name]=$file_name;
            }
          }
        } // end of while loop

				//extract and print first line
				$first_row = array_shift($file_list);
				echo $first_row;

        asort($file_list);

        //print downloadable files
        echo join("\n",$file_list);

        asort($dir_list);

        //iterate and print all subdirectories found
        foreach ($dir_list as $dir_name => $file_name) {
					if ($sub_structure) {//print and load subdirs
            // echo "<h4 class=\"sub_header\"><i class=\"fas fa-folder\"></i> $file_name/</h4><ul class=\"download_list\" >";
						$link_name = preg_replace('/\s|\./', '', $file_name) . '-' . $counter;
						echo "<h4 class=\"subdirectories\"><a class=\"collapsed sub_header\" href=\"#$link_name\" data-toggle=\"collapse\" aria-expanded=\"false\"><i class=\"fas fa-angle-down\"></i><i class=\"fas fa-angle-right\"></i> <i class=\"fas fa-folder\"></i> $file_name</a></h4><div id=\"$link_name\" class=\"collapse hide\"><div class=\"sub_header\"><ul class=\"download_list\" >";
						$counter++;

            get_dir_and_files($root, $dir_name, 1, $counter);
            echo "</ul></div></div>";
          }
          else {//print first dirs and load subdirs
            // $counter++;
            $link_name = preg_replace('/\s|\./', '', $file_name) . '-' . $counter;
            echo "<h3><a class=\"collapsed dir_header\" href=\"#$link_name\" data-toggle=\"collapse\" aria-expanded=\"true\"><i class=\"fas fa-angle-down\"></i><i class=\"fas fa-angle-right\"></i> <i class=\"fas fa-folder\"></i> $file_name</a></h3><div id=\"$link_name\" class=\"collapse show\"><div class=\"sub_header\"><ul class=\"download_list\" >";
						$counter++;

            get_dir_and_files($root, $dir_name, 1, $counter);
						echo "</ul></div></div>";
						echo "<hr>";
          }
        }

      } // end of opendir
    } // end of if is_dir
}

$counter = 1;
get_dir_and_files($root_path, $downloads_path, 0, $counter); // call the function for the downloads dir

?>

<style>

.download_list {
  padding-left:0px;
  font-size: 18px;
  margin-bottom: 0px;
  margin-left: 20px;
}

.dir_header {
  color: #555;
}
.dir_header:hover {
  color:#0056b3;
  text-decoration: none;
}

.sub_header {
  font-size: 18px;
  margin-top: 10px;
  margin-bottom: 20px;
  color: #555;
}
.subdirectories {
  margin-top: 10px;
}
.subdirectories:hover {
  color:#0056b3;
  text-decoration: none;
}


[aria-expanded="true"] .fa-angle-right,
[aria-expanded="false"] .fa-angle-down {
    display:none;
}

</style>
