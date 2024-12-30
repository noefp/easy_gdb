<?php include realpath('../header.php'); ?>
<script src="/easy_gdb/js/openGPlink.js"></script>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/07_gene_enrichment.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<br>
  <h1 class="text-center">Gene Set Enrichment</h1>

<div>

  <div class="form margin-20" style="margin:auto; max-width:900px">
    
    <label for="txtGenes">Paste a list of gene IDs</label>
    <textarea name="txtGenes" id="txtGenes" class="form-control" rows="10">
<?php echo "$input_gene_list" ?>
    </textarea>
    <br>

    <label>Please, choose one of the species available for Gene Ontolgy enrichment analysis:</label>
    <br>

<?php
if ( file_exists("$json_files_path/tools/enrichment.json") ) {
  
  $enrichment_json_file = file_get_contents("$json_files_path/tools/enrichment.json");
  // var_dump($enrichment_json_file);
  $enrichment_hash = json_decode($enrichment_json_file, true);
  //var_dump($enrichment_hash);
  $counter = 1;
  foreach($enrichment_hash as $key => $value) {
    
    if ($enrichment_hash[$key]["gprofiler_sps"]) {
      echo "<div class=\"form-check-inline\">";
      echo "<label class=\"form-check-label\">";
      if ($counter == 1) {
        echo "<input type=\"radio\" class=\"form-check-input radio_sps\" name=\"optradio\" value=\"".$key."\" checked>".$key;
      } else {
        echo "<input type=\"radio\" class=\"form-check-input radio_sps\" name=\"optradio\" value=\"".$key."\">".$key;
      }
      $counter++;
      echo "</label>";
      echo "</div>";
    }
  }
  
}

?>
    <br><br>
    <div class="form-group">
      <label for="sel1">If your gene IDs are not from any of these species choose one of the possible gene ID lookup sets:</label>
      <select class="form-control" id="sel1" name="lookup_db">
      </select>
    </div>

<p>
  The gene set enrichment analsysis will be redirected to <a href="https://biit.cs.ut.ee/gprofiler/gost" target="_blank">g:Profiler</a>, 
  Developed by <a href="https://doi.org/10.1093/nar/gkz369" target="_blank">Raudvere et al 2019</a>.
</p>

    <button id="submit_enrichment" type="submit" class="btn btn-success float-right">Submit</button>

  </div>
</div>
<br>
<br>
<?php include realpath('../footer.php'); ?>


<style>
  .margin-20 {
    margin: 20px;
  }
</style>


<script>
  $(document).ready(function () {
    
    function change_datasets(sps_name) {
      
      var file_path = <?php echo json_encode($lookup_path); ?>;
      // alert("Species: "+sps_name+", value: "+enrichment_obj[sps_name]['lookup_files'][0]);
      
      $( "#sel1" ).html( "<option value=\"none\">None</option>" );
      
      enrichment_obj[sps_name]["lookup_files"].forEach(function(dataset) {
        var dataset_name = dataset.replace(".txt", "");
        dataset_name = dataset_name.replace(/_/g, " ");
        $( "#sel1" ).append( "<option value=\""+file_path+"/"+dataset+"\">"+dataset_name+"</option>" );
      });
    }
    
    var enrichment_obj = <?php echo json_encode($enrichment_hash); ?>;
    
    first_dataset = $("input[name='optradio']:checked").val();
    change_datasets(first_dataset);
    var selected_dataset = first_dataset
    var sps_profiler = enrichment_obj[first_dataset]["gprofiler_sps"];
    
    
    // Change available blast dbs in selected category
    $('.radio_sps').change(function () {
      selected_dataset = $("input[name='optradio']:checked").val();
      // alert("selected_dataset: "+selected_dataset);
      change_datasets(selected_dataset);
      sps_profiler = enrichment_obj[selected_dataset]["gprofiler_sps"];
    });
    
    
    $('#submit_enrichment').click(function () {

      var lookup_dataset = $('#sel1').val();
      var gene_lookup_input = $('#txtGenes').val();
      filtered_input = gene_lookup_input.replace(/\s*\n+\s*/g, '\n');
      
      filtered_input = filtered_input.replace(/$/g, '\n');
      filtered_input = filtered_input.replace(/^\n$/g, '');
      filtered_input = filtered_input.replace(/ */g, '');
      
      var gene_count = (filtered_input.match(/\w+[\n|$]/g)||[]).length;
      // alert("filtered_input: "+filtered_input+", gene_count: "+gene_count+", lookup_db: "+lookup_dataset);
      
      //check input genes from gene lookup before sending form
      var max_input = "<?php echo $max_lookup_input ?>";
      if (!max_input) {
         max_input = 10000;
      }
      if (gene_count > max_input) {
        alert("A maximum of "+max_input+" gene IDs can be provided as input, your input has: "+gene_count);
        return false;
      }
      if (gene_count == 0) {
        alert("No gene IDs were provided as input");
        return false;
      }
      
      //call PHP file ajax_get_names_array.php to get the gene list to autocomplete from the selected dataset file
      function ajax_call(gene_list,lookup_db,sps) {
      
        jQuery.ajax({
          type: "POST",
          url: 'gene_enrichment_ajax.php',
          data: {'gene_list': gene_list, 'lookup_db': lookup_db},

          success: function (names_array) {
            
            var names = JSON.parse(names_array);
            
            //alert("names: "+names);
            openGPlink('https://biit.cs.ut.ee/gprofiler/gost',
              {query: names, organism: sps, target:'GO', all_results: true, numeric_namespace:'NO_NAMESPACE', sources: [ "GO:MF", "GO:CC", "GO:BP", "KEGG", "TF", "REAC", "MIRNA", "HPA", "CORUM", "HP", "WP" ]}
            )
            
          }
        });
      
      }; // end ajax_call
      
      ajax_call(filtered_input,lookup_dataset,sps_profiler);
      // return true;
    });

  });
</script>
