<?php include_once realpath("../../header.php");?>
<?php include_once realpath("../modal.html");?>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/02_blast.php" target="blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>
<br>

<h1 class="text-center">BLAST <i class="fas fa-dna" style="color:#555"></i></h1>
<br>
<div id="tool-container" class="margin-20" style="margin:auto; max-width:900px">

<?php 
if (isset($multiple_blast_db) && $multiple_blast_db) {
  $multiple_blast_db = 1;
  echo "<form id=\"blast_form\" action=\"loading_blast.php\" method=\"post\">";
} else {
  $multiple_blast_db = 0;
  echo "<form id=\"blast_form\" action=\"blast_output_single.php\" method=\"post\">";
}
?>

    <div class="form-group blast_attr">
      <label for="blast_sequence">Paste a sequence</label>
      <textarea id="blast_sequence" class="form-control sequence_blast_box" rows="12"><?php echo "$blast_example"; ?></textarea>
      <textarea id="blast_sequence_output" name="query" class="form-control sequence_blast_box d-none"></textarea>
    </div>

    <div class="row">

      <div class="col-sm-6 col-md-6 col-lg-">
        <?php  include_once 'blast_dbs_select.php';?>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-">
        <label for="blast_program" class="yellow_col">BLAST program</label>
        <select class="form-control blast_box" id="blast_program" name="blast_prog">
          <option value='blastn'>BLASTn</option>
          <option value='blastp'>BLASTp</option>
          <option value='blastx' selected>BLASTx</option>
          <!-- <option value='tblastn'>tBLASTn</option> -->
          <!-- <option value='tblastx'>tBLASTx</option> -->
        </select>
      </div>

    </div>

    <hr>
    <div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#adv_opt" aria-expanded="true" style="text-align:center">
      <i class="fas fa-sort" style="color:#229dff"></i> <h3 style="display:flex inline"> Advanced options </h3> <i for="collapse_section" class="fas fa-sort" style="color:#229dff"></i>
    </div>
    <!-- <a data-toggle="collapse" data-target="#adv_opt" class="btn btn-light" style="background:gray">BLAST options</a> -->
    
    <div id="adv_opt" class="collapse">

      <div class="row text-left">

        <div class="col-sm-6 col-md-6 col-lg-">
          <label for="blast_hits" class="yellow_col">Max hit number</label>
          <select class="form-control blast_box" id="blast_hits" name="max_hits">
            <option value='10' selected>10</option>
            <option value='20'>20</option>
            <option value='40'>40</option>
            <option value='60'>60</option>
          </select>
        </div>

        <div class="col-sm-6 col-md-6 col-lg-">
            <label for="blast_eval" class="yellow_col">Max <i>e</i> value</label>
            <select class="form-control blast_box" id="blast_eval" name="evalue">
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
          <select class="form-control blast_box" id="blast_matrix" name="blast_matrix">
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
          <label class="yellow_col" for="blast_task">Task</label>
          <select class="form-control blast_box" id="blast_task" name="task">
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
    var multiple_blast_db = <?php echo ((isset($multiple_blast_db)) && ($multiple_blast_db===1)) ? 1 : 0; ?>;
    // alert("multiple_blast_db: "+ multiple_blast_db);

    
    // $('#blast_task').change(function () {
    //   var task = $('#blast_task').val();
    //   alert("task: "+task);
    // });

    $('#blast_button').click(function () {
      var seq_type = "nt";

      var input_seq = $('#blast_sequence').val().trim();
      // alert("input_seq: "+input_seq);


      if(!multiple_blast_db)
      { var blast_db = $('#sel1').val();
        var blast_db_type = $('#sel1').children(":selected").attr("dbtype");
      }else
      {  var blast_db_type = ($('#nucleotide_db_list .nucleotide-checkbox:checked, #protein_db_list .protein-checkbox:checked').val()) ? 
                             ($('#nucleotide_db_list .nucleotide-checkbox:checked, #protein_db_list .protein-checkbox:checked').val()) : "";
      }
          
      var blast_program = $('#blast_program').val();

      var task = $('#blast_task').val();

      //input_seq_lines = input_seq.match(/^>[^\n]+\n([^>]*)/gm); // get only sequence lines, ignore fasta headers

      
     //      Check input genes from BLAST output before sending form
    // ---------------------------------------------------------------------------------------------------------------------------
      const input_seq_header_sequence = [];
      if(input_seq.match(/^>/gm)){ // if fasta format

        let match;
        const regex = /^>([^\n]+)\n([^>]*)/gm;

        // Regular expression: /^>([^\n]+)\n([^>]*)/gm
        //
        // ^>         → matches lines that start with the character '>'
        // ([^\n]+)   → group 1: capture one or more (+) characters of the header (that are not newlines) until the newline
        // \n         → newline character separating the header from the sequence
        // ([^>]*)    → group 2: captures the sequence until the next '>' or the end of the text (all characters that are not '>')
        //
        // Flags:
        // g → global, find all matches in the text
        // m → multiline, makes  match the start/end of each line (not just the start/end of the whole text)

        // while loop to find all matches in the input sequence
        while ((match = regex.exec(input_seq)) !== null) {
          // alert("match: "+ JSON.stringify(match[1])+ " , "+ JSON.stringify(match[2]));
          const header = ">" + match[1].trim(); // header [1]position is the first capturing group ([^\n]+)
          const sequence = match[2].replace(/\s+/g,"").trim();  // sequence (remove all whitespace) [2]position is the second capturing group ([^>]*)
          input_seq_header_sequence.push({header, sequence});  // push object with header and sequence; 
        }
      }else{
        // if not fasta format, consider whole input as a single sequence
        input_seq_header_sequence.push({header: "", sequence: input_seq.replace(/\n/g,"").trim()}); // remove newlines from sequence "g" for global;
      }

      // alert("sequences in input: "+ JSON.stringify(input_seq_header_sequence));
// -----------------------------------------------------------------------------------------------------------------------------

      // if input sequence is empty
      if (input_seq === "") {
        // alert("Please provide an input sequence");
        $("#search_input_modal").html("Please provide an input sequence");
        $('#no_gene_modal').modal()
        return false;
      }

     // check that at least one BLAST database is selected
      if(blast_db_type === "" || typeof blast_db_type === "undefined") {
          // alert("Please select at least one BLAST database");
        $("#search_input_modal").html("Please select at least one BLAST database");
        $('#no_gene_modal').modal()
        return false;
      }

      seqnum = input_seq_header_sequence.length // count number of sequences in input form
      var max_input = "<?php echo isset($max_blast_input) ? $max_blast_input : 0 ?>";
      
      if (!max_input) {
        max_input = 10;
      }

      // if more than max_input sequences, prevent form submission
      if (seqnum > max_input) {
        // alert("A maximum of "+max_input+" sequences can be provided as input, your input has: "+seqnum);
        $("#search_input_modal").html( "A maximum of "+max_input+" sequences can be provided as input, your input has: "+seqnum);
        $('#no_gene_modal').modal()
        return false;
      }


     // validate each input sequence
      var check_input = input_seq_header_sequence.some(function(seq, index){

        var seq_length = seq.sequence.length;

        var nt_count = (seq.sequence.match(/[ACGNTacgnt]/g)).length // count nucleotides characters in sequence

        if (nt_count < seq_length*0.9) { // if less than 90% of characters are nucleotides, consider it a protein sequence
          seq_type = "prot";
        }

        if (!seq.sequence || seq_length < 5) {
          // alert("Please provide a valid input sequence");
          $("#search_input_modal").html("Please provide a valid input sequence");
          $('#no_gene_modal').modal();
          return true;
        }
        
        if (seq_type === "nt" && blast_program === "blastp") {
              // alert("BLASTp can not be used for an input nucleotide sequence");
              $("#search_input_modal").html("BLASTp can not be used for an input nucleotide sequence");
              $('#no_gene_modal').modal()
              return true;
          }

        if (seq_type === "prot" && blast_program !== "blastp") {
              // alert("Input protein sequences can only be used with BLASTp");
              $("#search_input_modal").html("Input protein sequences can only be used with BLASTp");
              $('#no_gene_modal').modal()
              return true;
          }

        if (blast_program === "blastn" && blast_db_type.match("\.phr")) {
              // alert("BLASTn can not be used for a protein database");
              $("#search_input_modal").html("BLASTn can not be used for a protein database");
              $('#no_gene_modal').modal()
              return true;
          }
          // alert("blast_db_type: "+blast_db_type);

        if ((blast_program === "blastp" || blast_program === "blastx") && !blast_db_type.match("\.phr")) {
          //   // $('#blast_form').submit(function() {
          //     // alert("BLASTp and BLASTx can only be used for a protein database");
              $("#search_input_modal").html("BLASTp and BLASTx can only be used for a protein database");
              $('#no_gene_modal').modal()
          //     // console.log("BLASTp and BLASTx can only be used for a protein database");
              return true;
          //   // });
          }
          
          // check that only letters are provided in the sequence input field for protein sequences and nucleotide sequences  
          const nueclotides_proteins_char = /[^A-Za-z]/; // only letters
          if(nueclotides_proteins_char.test(seq.sequence) ) {
            // alert("Please provide a valid input sequence");
            if(seq.header !== "") {
              $("#search_input_modal").html("<b>"+ seq.header + "</b>" + "<br>Please provide a valid input sequence" + " (only letters are allowed)");
            }
            else {
              $("#search_input_modal").html("Please provide a valid input sequence" + " (only letters are allowed)");
            }
              $('#no_gene_modal').modal();
              return true;
          }

        //   - blastp-fast, blastp-short → proteins
        //   - blastx-fast → nucleotides (translate to  proteins)
        //   - blastn-short, dc-megablast, megablast → nucleotides

          if (seq_type === "nt" && blast_program === "blastn" && (task === 'blastp-fast' || task === "blastp-short")) {
              $("#search_input_modal").html("<b>Task: "+task+"</b><br> can not be used for an input nucleotide sequence");
              $('#no_gene_modal').modal()
              return true;
          }
        
        if (seq_type === "prot" && blast_program === "blastp" && (task !== 'blastp-fast' && task !== "blastp-short" && task !== "none")) {
              $("#search_input_modal").html("<b>Task: "+task+"</b><br> can not be used for an input proteins sequence");
              $('#no_gene_modal').modal()
              return true;
          }

        if (seq_type === "nt" && blast_program === "blastx" && (task !== "blastx-fast" && task !== "none")) {
              $("#search_input_modal").html("<b>Task: "+task+"</b><br> can not be used for BLASTx program");
              $('#no_gene_modal').modal()
              return true;
          }
      });

      if ((check_input) ? false : true) {
        // if all inputs are valid, submit form
        let seq_output = input_seq_header_sequence.map(item => `${item.header.replace(/[^A-Za-z0-9 >]/g,"_")}\n${item.sequence}`).join("\n");
        // alert("text: "+ text);
        $("#blast_sequence_output").text(seq_output);
        return true;
      }else {
        // if any input is invalid, prevent form submission
        return false;
      }
    });
  });
</script>

<style>

.collapse_section{
/*  text-decoration: underline;*/
  /* background-color:white; */
  color:black;
  border-radius: 5px;
  }  

  .collapse_section:hover  {
/*  text-decoration: underline;*/
  background-color: #6c757d;
  color:#fff;
}
</style>