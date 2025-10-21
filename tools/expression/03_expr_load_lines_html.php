<!-- #####################             Lines             ################################ -->
  <center>
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#line_chart_frame" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Lines
    </div>

    <div id="line_chart_frame" class="collapse hide" style="border:2px solid #666; padding-top:7px">
      

      <div id="lines_frame">
          <button id="lines_btn" type="button" class="btn btn-danger">Lines</button>
          <button id="bars_btn" type="button" class="btn btn-primary">Bars</button>

        <!-- toolbar activation -->
        <div class="custom-control custom-switch" style="display: flex; justify-content: flex-end; margin-right: 10px;">
        <input type="checkbox" class="custom-control-input" id="tools_lines">
        <label class="custom-control-label" for="tools_lines">
          <span id="tools_lines"><b>Tools</b></span>
        </label>
        </div> 
         <!-- chart  -->
        <div id="chart_lines" style="min-height: 550px;"></div>
        
      </div>
        
      
      
    </div>
  </center>
