<?php include realpath('../header.php'); ?>
<br>
<div id="dlgDownload">
  <h3 class="text-center">Gene Annotation Search</h3>
  <div class="form margin-20">

    <form id="gene_version_lookup">
      <label for="txtGenes">Paste a list of gene IDs</label>
      <textarea name="txtGenes" id="txtGenes" class="form-control" rows="10">
gene1
gene2
gene3
      </textarea>
      <br>
      <button type="submit" class="btn btn-success float-right" form="gene_version_lookup" formaction="annot_out_table.php" formmethod="post">search</button>
    </form>
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

    $('#gene_version_lookup').submit(function () {
      var gene_lookup_input = $('#txtGenes').val();
      var gene_count = (gene_lookup_input.match(/\n/g)||[]).length

      // alert("gene_lookup_input: "+gene_lookup_input+", gene_count: "+gene_count);

      //check input genes from gene lookup before sending form
      if (gene_count > 100) {
          alert("A maximum of 100 sequences can be provided as input, your input has: "+gene_count);
          return false;
      }

      return true;
    });

  });
</script>
