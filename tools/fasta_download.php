<?php include realpath('../header.php'); ?>
<?php include realpath('modal.html'); ?>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/04_sequence_extraction.php" target="_blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<br>

<div id="dlgDownload">
  <br>
  <h3 class="text-center">Gene Sequence Downloading</h3>
  <div class="form margin-20" style="margin:auto; max-width:900px">
    <form id="download_fasta_form" action="blast/blastdbcmd.php" method="post">
      <label for="txtDownloadGenes">Paste a list of gene IDs</label>
      <textarea class="form-control" id="txtDownloadGenes" rows="8" name="gids">
<?php echo "$input_gene_list" ?>
      </textarea>
      <br>

      <div class="form-group">
        <?php include_once 'blast/blast_dbs_select.php';?>
      </div>

      <button class="button btn btn-info float-right" id="btnSend" type="submit" form="download_fasta_form" formmethod="post">Download</button>
      </form>
      <br>
      <br>
  </div>

</div>

<?php include realpath('../footer.php'); ?>


<style>
  .margin-20 {
    margin: 20px;
  }
</style>


<script>
  $(document).ready(function () {

    $('#download_fasta_form').submit(function () {
      var gene_lookup_input = $('#txtDownloadGenes').val();
      var gene_count = (gene_lookup_input.match(/\n/g)||[]).length

      // alert("gene_lookup_input: "+gene_lookup_input+", gene_count: "+gene_count);

      //check input genes from gene lookup before sending form
      var max_input = "<?php echo $max_extract_seq_input ?>";
      
      if (!max_input) {
        max_input = 1000;
      }
      
      if (gene_count > max_input) {
          // alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+gene_count);
          $("#search_input_modal").html("A maximum of "+max_input+" sequences can be provided as input, your input has: "+gene_count);
          $('#no_gene_modal').modal();
          return false;
      }

      if ((gene_count == 0) && (gene_lookup_input == "") ) {
          // alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+gene_count);
          $("#search_input_modal").html("No gene IDs were provided as input");
          $('#no_gene_modal').modal();
          return false;
      }


      return true;
    });

  });
</script>
