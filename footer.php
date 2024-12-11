
      <div id="gdb_footer">
        
        <?php
          if ( file_exists("$custom_text_path/custom_footer.php") ) {
            include_once realpath("$custom_text_path/custom_footer.php");
          }
          else {
            // $logos_path = $root_path."".$images_path."/logos";
              
            // $logos_json = file_get_contents($logos_path."/logos.json");
            $logos_json = file_get_contents($json_files_path."/customization/logos.json");
            
            
            //var_dump($logos_json);
            $jlogo = json_decode($logos_json, true);
          
            foreach($jlogo["logos"] as $logo) {
              echo "<a href='".$logo["link"]."' target='_blank'><img class='m-2' height='".$logo["height"]."' src='".$images_path."/logos/".$logo["image"]."'></a>";
            }
          }
        ?>
        <br>
        <p style="display:inline">
          <a href="/easy_gdb/cookies.php">Cookie policy</a>.
          This site is based on <a href="https://github.com/noefp/easy_gdb" target="_blank">easyGDB</a> software.
        </p>
      </div>
      
    </div> <!-- close page container div -->
    
  </body>
</html>

<style>
  #gdb_footer {
    margin-top:20px;
    padding-bottom:15px;
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