<?php include realpath('../../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


<?php 
  $expr_file = $_POST["expr_file"];
  $gene_list = $_POST["gids"];
  $dataset_name_ori = preg_replace('/.+\//',"",$expr_file);
  $dataset_name = preg_replace('/_/'," ",$dataset_name_ori);
  $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
  
  if(isset($gene_list)) {
    $gids=array_map(function($row) {
      return rtrim($row);
    },explode("\n",$gene_list));
  }
  
	if ( file_exists("$expression_path/expression_info.json") ) {
	    $annot_json_file = file_get_contents("$expression_path/expression_info.json");
	    $annot_hash = json_decode($annot_json_file, true);
	}
  
?>

<br>
<a href="/easy_gdb/tools/expression/expression_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<div class="page_container" style="margin-top:20px">
  <h1 id="dataset_title" class="text-center"><?php echo "$dataset_name" ?></h1>
  <br>
  <div class="data_table_frame">


  <center>
    
    
    <div id="line_chart_frame" style="width:95%; border:2px solid #666; padding-top:7px">
    
      <div id="chart_lines" style="min-height: 550px;"></div>
    
    </div>
    
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#heatmap_graph" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Heatmap
    </div>

    <div id="heatmap_graph" class="collapse hide">
    
      <div id="chart1_frame" style="width:95%; border:2px solid #666; padding-top:7px">
        <button id="red_color_btn" type="button" class="btn btn-danger">Red palette</button>
        <button id="blue_color_btn" type="button" class="btn btn-primary">Blue palette</button>
        <button id="range_color_btn" type="button" class="btn" style="color:#FFF">Color palette</button>
    
        <div id="chart1" style="min-height: 400px;"></div>
    
      </div>
    </div>
  
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#replicates_graph" aria-expanded="true">
      <i class="fas fa-sort" style="color:#229dff"></i> Replicates
    </div>

    <div id="replicates_graph" class="collapse hide">
  
      <div id="chart2_frame" style="width:95%; border:2px solid #666; padding-top:7px">
        <div class="form-group d-inline-flex" style="width: 450px;">
          <label for="sel1" style="width: 150px; margin-top:7px">Select gene:</label>
          <select class="form-control" id="sel1">
            <?php
              foreach ($gids as $gene) {
                echo "<option value=\"$gene\">$gene</option>";
              }
            ?>
          </select>
        </div>
        <div id="chart2" style="min-height: 365px;"></div>
      </div>
  
    </div>
  </center>

  <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#avg_table" aria-expanded="true">
    <i class="fas fa-sort" style="color:#229dff"></i> Average values
  </div>

  <div id="avg_table" class="collapse hide">

<?php

$sample_names = [];
$heatmap_one_gene = [];
$heatmap_series = [];
// $scatter_one_gene = [];
$scatter_one_sample = [];
$scatter_all_genes = [];

$found_genes = [];

if ( file_exists("$expr_file") && isset($gids) ) {
  $tab_file = file("$expr_file");
  
   echo "<div style=\"width:95%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">";

    $columns = [];
    $replicates = [];
    $average = [];
    $header_printed = 0;
    
    $first_line = array_shift($tab_file);
    $header = explode("\t", rtrim($first_line));
    
    
    //gets each replicate value for each gene
    foreach ($tab_file as $line) {
      $columns = explode("\t", rtrim($line));
      
      $col_count = 0;
      $gene_name = $columns[0];
      
      // if gene found in input list
      if (in_array($gene_name,$gids)) {
        
        array_push($found_genes,$gene_name);
        
        foreach ($columns as $col) {
          
          $sample_name = $header[$col_count];
          
          if ($col_count != 0) {
            
            if ($replicates[$sample_name]) {
             array_push($replicates[$sample_name], $col);
            } else {
             $replicates[$sample_name] = [];
             array_push($replicates[$sample_name], $col);
            }
            
          }
          
          $col_count++;
        } // end column foreach
        
        //print header with sample names
        if (!$header_printed) {
          echo "<thead><tr><th>".$header[0]."</th>";
          foreach ($replicates as $r_key => $r_value) {
            echo "<th>$r_key</th>";
          }
          echo "</tr></thead>";
          $header_printed = 1;
          $sample_names = array_keys($replicates);
        }
        
        $q_link = "";
        if ($annot_hash[$dataset_name_ori]) {
          if ($annot_hash[$dataset_name_ori]["link"]) {
            if ($annot_hash[$dataset_name_ori]["link"] == "#") {
              echo "<tr><td>$gene_name</td>";
            }
            else {
              $q_link = $annot_hash[$dataset_name_ori]["link"];
              $q_link = preg_replace('/query_id/',$gene_name,$q_link);
              echo "<tr><td><a href=\"$q_link\" target=\"_blank\">$gene_name</a></td>";
            }
          }
          else {
            echo "<tr><td><a href=\"/easy_gdb/gene.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td>";
          }
        }
        else {
          echo "<tr><td>$gene_name</td>";
        }
        
        
        $scatter_pos = 1;
        
        // print expression average values $r_key is like "Sample1" and $r_value is like [4.4,2.3,8.1]
        foreach ($replicates as $r_key => $r_value) {
          
          $a_sum = array_sum($r_value);
          $a_reps = count($r_value);
        
          $average = sprintf("%1\$.2f",$a_sum/$a_reps);
          echo "<td>$average</td>";
          
          //save heatmap data
          $heatmap_one_gene["name"] = $gene_name;
          if ($heatmap_one_gene["data"]) {
            array_push($heatmap_one_gene["data"], $average);
          } else {
            $heatmap_one_gene["data"] = [];
            array_push($heatmap_one_gene["data"], $average);
          }
          
          //save scatter data
          //save replicates
          foreach ($r_value as $one_rep) {
            $one_replicate_pair = [$scatter_pos, $one_rep];
            
            //save samples and add replicates
            $scatter_one_sample["name"] = $r_key;
            if ($scatter_one_sample["data"]) {
              array_push($scatter_one_sample["data"], $one_replicate_pair );
            } else {
              $scatter_one_sample["data"] = [];
              array_push($scatter_one_sample["data"], $one_replicate_pair );
            }
            
          }
          $scatter_pos++;
          
          //save gene and add samples with replicates
          if ($scatter_all_genes[$gene_name]) {
            array_push($scatter_all_genes[$gene_name], $scatter_one_sample );
          } else {
            $scatter_all_genes[$gene_name] = [];
            array_push($scatter_all_genes[$gene_name], $scatter_one_sample );
          }
          $scatter_one_sample = [];
        }
        echo "</tr>";
        
        array_push($heatmap_series, $heatmap_one_gene);
        
        
        
        
        $replicates = [];
        $heatmap_one_gene = [];
        // $scatter_one_gene = [];
        $scatter_one_sample = [];
      } // end if gene in input list
      
      
    } // each line, each gene foreach
    echo "</table></div>";

  // } // if gene_list
  
} // if expr file exists

?>

  </div>
  
  </div>
</div>

<br>

<?php include realpath('../../footer.php'); ?>

<script type="text/javascript">
  var sample_array = <?php echo json_encode($sample_names) ?>;
  var heatmap_series = <?php echo json_encode(array_reverse($heatmap_series)) ?>;
  var scatter_one_gene = <?php echo json_encode($scatter_all_genes[$found_genes[0]]) ?>;
  var scatter_all_genes = <?php echo json_encode($scatter_all_genes) ?>;
  var gene_list = <?php echo json_encode($found_genes) ?>;
  var scatter_title = gene_list[0]+' Expression values';
  
  if (gene_list.length == 0) {
    $( "#chart1" ).css("display","none");
    $( "#chart2_frame" ).css("display","none");
    $( "#dataset_title" ).html("No gene was found in the selected dataset. Please, check gene names.");
    
  }
  
  var color_ranges=[{from:0,to:0.99,name:"0-0.99",color:"#ffffff"},{from:1,to:2.99,name:"1-2.99",color:"#f0c320"},{from:3,to:9.99,name:"3-9.99",color:"#ff8800"},{from:10,to:49.99,name:"10-49.99",color:"#ff7469"},{from:50,to:99.99,name:"50-99.99",color:"#de2515"},{from:100,to:199.99,name:"100-199.99",color:"#b71005"},{from:200,to:4999.99,name:"200-4999.99",color:"#0bb4ff"},{from:5000,to:20000,name:"5000-infinite",color:"#aaaaaa"}];
  
  $( "#red_color_btn" ).click(function() {
    // alert("hi");
    heatmap_chart.updateOptions({
      colors: ["#ff0000"],
      plotOptions: {
        heatmap: {
          shadeIntensity: 0.5,
          radius: 0,
          useFillColorAsStroke: true,
          colorScale: {
            ranges: []
          }
        }
      }
    });
    
  });
  
  $( "#blue_color_btn" ).click(function() {
    // alert("hi");
    heatmap_chart.updateOptions({
      colors: ["#008FFB"],
      plotOptions: {
        heatmap: {
          colorScale: {
            ranges: []
          }
        }
      }
    });
    
  });
  
  $( "#range_color_btn" ).click(function() {
    // alert("hi: "+color_ranges);
    heatmap_chart.updateOptions({
      colors: ["#777777"],
      plotOptions: {
        heatmap: {
          colorScale: {
            ranges: color_ranges
          }
        }
      }
    });
    
  });
  
  $( "#sel1" ).change(function() {
    // alert( this.value );
    scatter_title = this.value+' Expression values';
    scatter_one_gene = scatter_all_genes[this.value];
    
    
    scatter_chart.updateOptions({
      title: {
        text: scatter_title,
         align: 'center'
      }
    })
    scatter_chart.updateSeries(
      scatter_one_gene
    )

  });
  
  
  // alert("scatter_one_gene: "+JSON.stringify(scatter_one_gene) );
  // alert("heatmap_series: "+heatmap_series);
  
  
var options = {
  series: scatter_one_gene,
  // series: test,
   chart: {
    height: 350,
    type: 'scatter',
    zoom: {
      enabled: false,
      type: 'xy'
    }
  },
  colors: ["#ea5545", "#f46a9b", "#ef9b20", "#edbf33", "#ede15b", "#bdcf32", "#87bc45", "#27aeef", "#b33dc6",'#546E7A'],
  title: {
    text: scatter_title,
    align: 'center',
    style: {
      fontSize: '24'
    }
  },
  xaxis: {
    type: 'category',
    categories: sample_array,
    // categories: ["Sample1", "Sample2", "Sample3","Sample4", "Sample5", "Sample6","Sample7", "Sample8", "Sample9", "Sample10"],
    tickAmount: sample_array.length-1
  },
  yaxis: {
    tickAmount: 5
  },
  legend: {
    show: false
    // position: 'top'
  }
};

var scatter_chart = new ApexCharts(document.querySelector("#chart2"), options);
scatter_chart.render();


// alert("heatmap_series: "+JSON.stringify(heatmap_series) );


var options = {
  series: heatmap_series,
  chart: {
    height: 350,
    type: 'heatmap',
  },
  dataLabels: {
    enabled: true
  },
  colors: ["#777777"],
  plotOptions: {
    heatmap: {
      shadeIntensity: 0.5,
      radius: 0,
      useFillColorAsStroke: true,
      colorScale: {
        ranges: color_ranges
      }
    }
  },
  title: {
    text: 'Heatmap'
  },
  
  xaxis: {
    type: 'category',
    categories: sample_array,
    tickAmount: sample_array.length-1
  }
  
};

var heatmap_chart = new ApexCharts(document.querySelector("#chart1"), options);
heatmap_chart.render();


      
  $("#tblResults").dataTable({
    "dom":'Bfrtip',
    "ordering": false,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
  });

  $("#tblResults_filter").addClass("float-right");
  $("#tblResults_info").addClass("float-left");
  $("#tblResults_paginate").addClass("float-right");





var options = {
  series: heatmap_series,
  chart: {
  height: 500,
  type: 'line',
  zoom: {
    enabled: false,
    type: 'xy'
  },
  toolbar: {
    show: true
  }
  },
  colors: ["#ea5545", "#f46a9b", "#ef9b20", "#edbf33", "#ede15b", "#bdcf32", "#87bc45", "#27aeef", "#b33dc6",'#546E7A'],
  dataLabels: {
    enabled: true,
    offsetY: -5
  },
  stroke: {
    curve: 'straight'
  },
  title: {
    text: 'Lines',
    align: 'left'
  },
  markers: {
    size: 3
  },
  xaxis: {
    categories: sample_array
  },
  yaxis: {
    title: {
      text: 'Expression value'
    }
  },
  legend: {
    position: 'top',
    horizontalAlign: 'center',
    inverseOrder: true,
    floating: true,
    offsetY: -30,
    offsetX: 25
  },
  tooltip: {
    inverseOrder: true
  }
};

  var line_chart = new ApexCharts(document.querySelector("#chart_lines"), options);
  line_chart.render();

</script>

<style>
  #range_color_btn{
/*  height: 50px;*/
  border-color: #b71005;
  background: -moz-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  background: -webkit-linear-gradient(-90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  background: linear-gradient(90deg, #f0c320 0%,#f0c320 25%,#ff8800 50%,#ff7469 51%,#ff0000 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0c320', endColorstr='#ff0000',GradientType=1 );
  }
</style>
  

