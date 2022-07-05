var color_array = ["#ea5545", "#f46a9b", "#ef9b20", "#edbf33", "#ede15b", "#bdcf32", "#87bc45", "#27aeef", "#b33dc6",'#546E7A'];

// ######################################################## Lines

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
  colors: color_array,
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

// ######################################################## Heatmap

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


// ######################################################## Replicates

  var scatter_title = gene_list[0]+' Expression values';
  
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
  colors: color_array,
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

