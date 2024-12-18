<?php include_once realpath("../../header.php");?>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/02_blast.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<br>

<h3 class="text-center">BLAST</h3>

<div class="margin-20">

  <form id="blast_form" action="blast_output.php" method="post">

    <div class="form-group">
      <label for="blast_sequence">Paste a sequence</label>
      <textarea id="blast_sequence" name="query" class="form-control" rows="12" style="font-family:Monospace">
<?php echo "$blast_example" ?>
      </textarea>
    </div>

    <div class="row">

      <div class="col-sm-6 col-md-6 col-lg-">
        <?php include_once 'blast_dbs_select.php';?>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-">
        <label for="blast_program" class="yellow_col">BLAST program</label>
        <select class="form-control" id="blast_program" name="blast_prog">
          <option value='blastn'>BLASTn</option>
          <option value='blastp'>BLASTp</option>
          <option value='blastx' selected>BLASTx</option>
          <!-- <option value='tblastn'>tBLASTn</option> -->
          <!-- <option value='tblastx'>tBLASTx</option> -->
        </select>
      </div>

    </div>

    <hr>
    <a data-toggle="collapse" data-target="#adv_opt" class="btn btn-light">BLAST options</a>
    <br>
    <div id="adv_opt" class="collapse">

      <div class="row text-left">

        <div class="col-sm-6 col-md-6 col-lg-">
          <label for="blast_hits" class="yellow_col">Max hit number</label>
          <select class="form-control" id="blast_hits" name="max_hits">
            <option value='10' selected>10</option>
            <option value='20'>20</option>
            <option value='40'>40</option>
            <option value='60'>60</option>
          </select>
        </div>

        <div class="col-sm-6 col-md-6 col-lg-">
            <label for="blast_eval" class="yellow_col">Max <i>e</i> value</label>
            <select class="form-control" id="blast_eval" name="evalue">
            <option value='10'>10</option>
            <option value='1e-3' selected>1e-3</option>
            <option value='1e-6'>1e-6</option>
            <option value='1e-9'>1e-9</option>
            <option value='1e-12'>1e-12</option>
          </select>
        </div>

      </div>

      <div class="row text-left">

        <div class="col-sm-6 col-md-6 col-lg-">
          <label for="blast_matrix" class="yellow_col">Matrix</label>
          <select class="form-control" id="blast_matrix" name="blast_matrix">
            <option value='BLOSUM45'>BLOSUM45</option>
            <option value='BLOSUM52'>BLOSUM55</option>
            <option value='BLOSUM62' selected>BLOSUM62</option>
            <option value='BLOSUM80'>BLOSUM80</option>
            <option value='BLOSUM90'>BLOSUM90</option>
          </select>
          
          <br>
          <div class="checkbox" style="margin:0px">
            <label class="yellow_col"><input type="checkbox" id="blast_filter" name="blast_filter"> Filter low complexity</label>
          </div>
        </div>
        
        <div class="col-sm-6 col-md-6 col-lg-">
          <label for="blast_task">Task</label>
          <select class="form-control" id="blast_task" name="task">
          <option value='none' selected>default</option>
          <option value='blastn-short'>blastn-short</option>
          <option value='dc-megablast'>dc-megablast (more sensitive but slower)</option>
          <option value='megablast'>megablast (fast but only for very similar hits)</option>
          <option value='blastp-fast'>blastp-fast</option>
          <option value='blastp-short'>blastp-short</option>
          <option value='blastx-fast'>blastx-fast</option>

          </select>
          
        </div>
        
      </div>

    </div>
    <hr>
    <div class="text-center">
      <button type="submit" id="blast_button" class="btn btn-info">BLAST</button>
    </div>
  </form>
</div>

<br>
<?php include_once realpath("$easy_gdb_path/footer.php");?>

<style>
  .margin-20 {
    margin: 20px;
  }
</style>

<script>
  $(document).ready(function () {

    // $('#blast_program').change(function () {
    //   blast_program = $('#blast_program').val();
    //   alert("blast_program: "+blast_program);
    // });

    $('#blast_button').click(function () {
      var seq_type = "nt";
      var input_seq = $('#blast_sequence').val();
      var blast_db = $('#sel1').val();
      var blast_db_type = $('#sel1').children(":selected").attr("dbtype");;
      var blast_program = $('#blast_program').val();

      input_seq = input_seq.trim();

      var trimmed_seq = input_seq.trim();
      trimmed_seq = trimmed_seq.replace(/^>.+\n/,"");
      trimmed_seq = trimmed_seq.replace(/\n/g,"");
      var seq_length = trimmed_seq.length;

      var nt_count = (trimmed_seq.match(/[ACGNTacgnt]/g)||[]).length

      if (nt_count < seq_length*0.9) {
        seq_type = "prot";
      }

      // alert("nt_count: "+nt_count+" seq_length: "+seq_length+" seq_type: "+seq_type);
      // alert("blast_program: "+blast_program+" blast_db: "+blast_db);

      //check input genes from BLAST output before sending form
      seqnum = input_seq.match(/>/g).length
      var max_input = "<?php echo $max_blast_input ?>";
      
      if (!max_input) {
        max_input = 10;
      }
      
      if (seqnum > max_input) {
          alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+seqnum);
          return false;
      }
      if (!input_seq || seq_length < 5) {
          alert("Please provide a valid input sequence");
          return false;
      }
      if (seq_type == "nt" && blast_program == "blastp") {
          alert("BLASTp can not be used for an input nucleotide sequence");
          return false;
      }
      if (seq_type == "prot" && blast_program != "blastp") {
          alert("Input protein sequences can only be used with BLASTp");
          return false;
      }
      if (blast_program == "blastn" && blast_db_type.match("\.phr")) {
          alert("BLASTn can not be used for a protein database");
          return false;
      }
      if ((blast_program == "blastp" || blast_program == "blastx") && !blast_db_type.match("\.phr")) {
        // $('#blast_form').submit(function() {
          alert("BLASTp and BLASTx can only be used for a protein database");
          return false;
        // });
      }

      return true;
    });

  });
</script>
