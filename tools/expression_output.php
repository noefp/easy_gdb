<?php include realpath('../header.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


<?php 
  $expr_file = $_POST["expr_file"];
  $gene_list = $_POST["gids"];
  $dataset_name = preg_replace('/.+\//',"",$expr_file);
  $dataset_name = preg_replace('/_/'," ",$dataset_name);
  $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$dataset_name);
  
  if(isset($gene_list)) {
    $gids=array_map(function($row) {
      return rtrim($row);
    },explode("\n",$gene_list));
  }
  
?>

<div class="page_container" style="margin-top:20px">
  <h1 id="dataset_title" class="text-center"><?php echo "$dataset_name" ?></h1>
  <br>
  <div class="data_table_frame">


  <center>
  <div id="chart1" style="min-height: 365px;width:90%; border:2px solid #666;"></div><br>

  <div id="chart2_frame" style="width:90%; border:2px solid #666; padding-top:7px"">
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
    <div id="chart2" style="min-height: 365px;"></div><br>
  </div>
  </center>

  <br>

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
  
   echo "<div style=\"width:90%; margin: auto; overflow: scroll;\"><table class=\"table\" id=\"tblResults\">";

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
        
        echo "<tr><td>$gene_name</td>";
        
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

<br>

<?php include realpath('../footer.php'); ?>

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
  
  var test = [{
      name: "Sample1",
      data: [
        [1,2.99],[1, 8.89],[1, 5.2]
      ]},
      {name: "Sample2",
      data: [
        [2,5.93],[2, 22],[2, 17.03]
      ]},
      {name: "Sample3",
      data: [
        [3,5.93],[3, 22],[3, 17.03]
      ]},
      {name: "Sample4",
      data: [
        [4,15.5],[4, 16],[4, 17.03]
      ]},
      {name: "Sample5",
      data: [
        [5,5.93],[5, 22],[5, 17.03]
      ]},
      {name: "Sample6",
      data: [
        [6,5.93],[6, 6],[6, 5.53]
      ]},
      {name: "Sample7",
      data: [
        [7,5.93],[7, 22],[7, 17.03]
      ]},
      {name: "Sample8",
      data: [
        [8,0.93],[8, 0.222],[8, 1.03]
      ]},
      {name: "Sample9",
      data: [
        [9,5.93],[9, 22],[9, 17.03]
      ]},
      {name: "Sample10",
      data: [
        [10,5.16],[10, 0.18],[10, 21.58]
      ]
    }];

    // alert("scatter_one_gene: "+JSON.stringify(test) );
  
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
  // series: [
  //   {
  //     name: 'Gene1',
  //     data: [12,4,3,15,2,4,17,2,0.54,2.30]
  //   },
  //   {
  //     name: 'Gene2',
  //     data: [1,4.5,3.9,11,5,4.5,14,2,2.54,3.30]
  //   },
  //   {
  //     name: 'Gene3',
  //     data: [12,4,3,15,2,4,17,2,0.54,2.30]
  //   },
  //   {
  //     name: 'Gene4',
  //     data: [1,4.5,3.9,11,5,4.5,14,2,2.54,3.30]
  //   },
  //   {
  //     name: 'Gene5',
  //     data: [12,4,3,15,2,4,17,2,0.54,2.30]
  //   },
  //   {
  //     name: 'Gene6',
  //     data: [1,4.5,3.9,11,5,4.5,14,2,2.54,3.30]
  //   },
  //   {
  //     name: 'Gene7',
  //     data: [12,4,3,15,2,4,17,2,0.54,2.30]
  //   },
  //   {
  //     name: 'Gene8',
  //     data: [12,4,3,15,2,4,17,2,0.54,2.30]
  //   },
  //   {
  //     name: 'Gene9',
  //     data: [1,4.5,3.9,11,5,4.5,14,2,2.54,3.30]
  //   },
  //   {
  //     name: 'Gene10',
  //     data: [9,3,3,14,5,6,12,4,3,13]
  //   }
  // ],
  chart: {
    height: 350,
    type: 'heatmap',
  },
  dataLabels: {
    enabled: false
  },
  colors: ["#FF0000"],
  title: {
    text: 'Expression values of gene selection (RPKM)'
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




</script>


