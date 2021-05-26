
      <div id="gdb_footer">
        
        <?php 
          $logos_path = $root_path."".$images_path."/logos";
        // if ($dh = opendir($logos_path)){
        //   while (($file_name = readdir($dh)) !== false){ //iterate all files in dir
          
            // if (!preg_match('/^\./', $file_name)) { //discard hidden files
              // if (!is_dir($lab_path."/".$file_name) && preg_match('/\.json/', $file_name) ){
              
                  $logos_json = file_get_contents($logos_path."/logos.json");
                  // var_dump($labs_json);
                  $jlogo = json_decode($logos_json, true);
                  
                  foreach($jlogo["logos"] as $logo) {
              
              echo "<a href='".$logo["link"]."' target='_blank'><img class='m-2' height='".$logo["height"]."' src='".$images_path."/logos/".$logo["image"]."'></a>";
            }
        //
        //   }
        // }
        ?>
        
        <!-- <a href=<?php// echo "$logo1_link";?> target="_blank"><img class="m-2" height=<?php// echo "$logo1_height";?> src=<?php// echo "$images_path/$logo1";?> ></a>
        <a href=<?php// echo "$logo2_link";?> target="_blank"><img class="m-2" height=<?php// echo "$logo2_height";?> src=<?php// echo "$images_path/$logo2";?> ></a>
        <a href=<?php// echo "$logo3_link";?> target="_blank"><img class="m-2" height=<?php// echo "$logo3_height";?> src=<?php// echo "$images_path/$logo3";?> ></a> -->
        
        
        <p>
          All data and services offered on this site are &copy; copyrighted. Distribution via internet or other media is prohibited.
          <a href="cookies.php">Cookie policy</a>
        </p>

      </div>

    </div> <!-- close page container div -->

  </body>
</html>

<style>
  #gdb_footer {
    margin-top:20px;
    border-top-style: solid;
    border-top-width: 1px;
    border-top-color: #d8d8d8;
    background-color: #fff;
    font-size: 12px;
    text-align: center;
    width: 100%;
    position: absolute;
    left:0;
    right:0;
  }
  
</style>