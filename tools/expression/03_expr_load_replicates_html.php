    <!-- #####################             Replicates           ################################ -->
  <center>
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#replicates_graph" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Replicates
    </div>

    <div id="replicates_graph" class="collapse hide">

      <div id="chart2_frame" style="border:2px solid #666; padding-top:7px">
        <div class="form-group d-inline-flex" style="width: 450px;">
          <label for="sel1" style="width: 150px; margin-top:7px"><b>Select gene:</b></label>
          <select class="form-control" id="sel1">
            <?php
              foreach ($found_genes as $gene) {
                echo "<option value=\"$gene\">$gene</option>";
              }
            ?>
          </select>
        </div>

        <div id="chart2" style="min-height: 365px;"></div>
        <hr>
        <label style="color: black; font-size: 12px; display: show;"><b>Replicate count:</b></label>
        <div id="replicates_count" style="max-height: 100px; overflow-y: auto; padding:5px; display: block;">
          <?php
          $colors_array=["#ea5545", "#f46a9b", "#ef9b20", "#edbf33", "#ede15b", "#bdcf32", "#87bc45", "#27aeef", "#b33dc6",'#546ead',
                          '#666','#999','#ccc','#000',"#a61101", "#c89", "#ab5700", "#798b00", "#437801", "#036aab", "#d0f", "#700982", 
                            "#fe9989", "#f8aedf", "#ffdf64", "#cbff89", "#6befff", "#f77ffa",'#b66'];

          $index=0;
          $colors_array_length=count($colors_array);
          //  var_dump($scatter_all_genes[$found_genes[0]]);
              foreach ($scatter_all_genes[$found_genes[0]] as $gene) {
                if($index==13)
                { echo "<span class=\"badge\" style= \"color:white; background-color: $colors_array[$index]; padding:5px; margin:10px; white-space:nowrap; display: inline-block;\">".$gene['name'].": ".count($gene['data'])."</span>";}
                else
                {echo "<span class=\"badge\" style= \"background-color: $colors_array[$index]; padding:5px; margin:10px; white-space:nowrap; display: inline-block;\">".$gene['name'].": ".count($gene['data'])."</span>";}
                if ($index<$colors_array_length-1) {$index++;} else {$index=0;}
              }
          ?>
        </div>
      </div>

    </div>
  </center>
