<?php include realpath('../header.php'); ?>

<div id="dlgDownload">
  <br>
  <h3 class="text-center">Gene Sequence Downloading</h3>
  <div class="form margin-20">
    <form id="download_fasta_form" action="blastdbcmd.php" method="post">
      <label for="txtDownloadGenes">Paste a list of gene IDs</label>
      <textarea class="form-control" id="txtDownloadGenes" rows="8" name="gids">
gene1
gene2
gene3
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

<?php include_once '../footer.php';?>


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
      if (gene_count > 500) {
          alert("A maximum of 500 sequences can be provided as input, your input has: "+gene_count);
          return false;
      }

      return true;
    });

  });
</script>
