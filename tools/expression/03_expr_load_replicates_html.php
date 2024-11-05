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
      </div>

    </div>
  </center>
