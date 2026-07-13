<!-- HEADER -->
<?php include_once realpath("../../header.php");
      include_once realpath("$easy_gdb_path/tools/common_functions.php");
      include_once realpath("../modal.html");
?>

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<!-- HELP LINK -->
<div class="margin-20">
  <a class="float-right" href="#" target="blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<!-- CONTENT -->
<?php
if (file_exists($json_files_path."/tools/seq_exp.json")) {
    $seq_json_file = file_get_contents($json_files_path."/tools/seq_exp.json");
    $seq_hash = json_decode($seq_json_file, true);
    $json_exists = true;
    $seq_dir_array = array_keys($seq_hash);

    $is_dir = false;
    $first_dir = true;

    sort($seq_dir_array);

    foreach ($seq_dir_array as $seq_dataset) {
      if(isset($seq_hash[$seq_dataset]['blast_db'])) {
        $is_dir = true;
      }
    }
} else {
    echo "<div class=\"alert alert-danger\"> <b>seq_exp.json not found</b></div>";
    $json_exists = false;
}
?>

<div id="container" class="form margin-20" style="margin:auto; max-width:900px">
<h2 style="text-align: center;">Sequence Explorer</h2>

<?php
if($is_dir) {
    echo '<label for="dataset_select">Select organism</label>';
    echo '<select class="form-control form-control-lg" id="dataset_select" name="seq_dataset">';
    foreach ($seq_dir_array as $seq_dataset) {
        if(isset($seq_hash[$seq_dataset]['blast_db'])) {
            $data_set_name = str_replace("_", " ", $seq_dataset);
            if($first_dir) { $first_folder = $seq_dataset; $first_dir = false; }
            echo "<option value=\"$seq_dataset\">$data_set_name</option>";
        }
    }
    echo '</select>';
} else {
    $first_folder = "";
}
?>

<br>
<div class="form-group" style="margin:0px !important">
  <label for="gene_search">Search a gene</label>
  <div class="input-group mb-3">
    <input id="gene_search" type="text" class="form-control form-control-lg" placeholder="Gene name">
    <div class="input-group-append">
      <button id="search_btn" class="btn btn-success" style="margin-right:10px">
        <i class="fas fa-search" style="font-size:20px; color:white; width:50px"></i>
      </button>
    </div>
  </div>
</div>

<div id="results_container" style="display:none">
  <div id="section_jbrowse"></div>
  <div id="section_coordinates"></div>
  <div id="section_sequences"></div>
</div>

</div> <!-- end container -->

<!-- JAVASCRIPT -->
<script>
var seq_store = {};
var jbrowse_url = '';

$(document).ready(function() {

  var json_files_path = "<?php echo $json_files_path; ?>";
  var seq_path = "<?php echo $root_path."/"?>";
  var first_folder = "<?php echo $first_folder; ?>";
  window.json_files_path = json_files_path;
  window.seq_path = seq_path;
  window.first_folder = first_folder;
  var json_exists = "<?php echo json_encode($json_exists); ?>";
  var names = [];

  if(json_exists == "false") {
    $("#container").hide();
  } else {
    update_json_info(json_files_path, first_folder, seq_path);
  }

  $("#gene_search").autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(names, request.term);
      response(results.slice(0, 10));
    }
  });

  $('#dataset_select').change(function() {
    var seq_dataset = $('#dataset_select').val();
    sessionStorage.removeItem('sv_gene_name');
    update_json_info(json_files_path, seq_dataset, seq_path);
  });
  window.addEventListener('pageshow', function(event) {
    if(event.persisted) {
      var saved_gene = sessionStorage.getItem('sv_gene_name');
      var saved_dir  = sessionStorage.getItem('sv_seq_dir');
      var saved_up   = sessionStorage.getItem('sv_upstream');
      var saved_down = sessionStorage.getItem('sv_downstream');
      if(saved_gene) {
        $('#gene_search').val(saved_gene);
        if(saved_up)   $('#genomic_upstream_bp').val(saved_up);
        if(saved_down) $('#genomic_downstream_bp').val(saved_down);
        if(saved_dir && saved_dir !== first_folder) {
          $('#dataset_select').val(saved_dir);
        }
        setTimeout(function() {
          get_gene_structure(saved_gene);
        }, 600);
      }
    }
  });

  $('#search_btn').click(function() {
    var gene_name = $('#gene_search').val();
    if(gene_name === "") {
      $("#search_input_modal").html("No gene name provided");
      $('#no_gene_modal').modal();
      return false;
    }
    get_gene_structure(gene_name);
  });

  function update_json_info(json_files_path, seq_dir, seq_path) {
    jQuery.ajax({
      type: 'POST',
      url: 'ajax_update_seq_exp_info.php',
      data: {
        'json_files_path': json_files_path,
        'seq_dir':         seq_dir,
        'seq_path':        seq_path
      },
      success: function(data) {
        var json_info = JSON.parse(data);
        names = json_info.genes_array;
        jbrowse_url = json_info.jbrowse_url || '';
      }
    });
  }

  function get_gene_structure(gene_name) {

    $('#genomic_upstream_bp').val(0);
    $('#genomic_downstream_bp').val(0);
    sessionStorage.setItem('sv_gene_name', gene_name);
    sessionStorage.setItem('sv_seq_dir', $('#dataset_select').val() || first_folder);
    sessionStorage.setItem('sv_upstream', 0);
    sessionStorage.setItem('sv_downstream', 0);

    var spinner = "<div class='text-center' style='padding:30px'>" +
                  "  <div class='spinner-border text-secondary' role='status'>" +
                  "    <span class='sr-only'>Loading...</span>" +
                  "  </div>" +
                  "</div>";

    // show results container with spinners in each section
    $('#section_jbrowse').html(spinner);
    $('#section_coordinates').html(spinner);
    $('#section_sequences').html(spinner);
    $('#results_container').show();

    var seq_dir = $('#dataset_select').val() || first_folder;

    // Section 1: JBrowse - no AJAX needed, build immediately
    var first_mrna_id = '';
    var jb_html = '';
    // JBrowse will be built after gene_structure arrives

    // Section 2: Gene coordinates - ajax_get_gene_structure.php
    jQuery.ajax({
      type: 'POST',
      url: 'ajax_get_gene_structure.php',
      data: {
        'gene_name':       gene_name,
        'seq_dir':         seq_dir,
        'seq_path':        seq_path,
        'json_files_path': json_files_path
      },
      success: function(data) {
        var gene_structure = JSON.parse(data);
        window.current_gene_structure = gene_structure;
        window.current_gene_name = gene_name;

        // Section 1: JBrowse - now we have gene_structure
        var jb_url = '';
        if(jbrowse_url !== '') {
          var first_mrna_id   = Object.keys(gene_structure.mRNAs)[0];
          var first_mrna_name = gene_structure.mRNAs[first_mrna_id].name || first_mrna_id;
          jb_url = jbrowse_url.replace('{gene_name}', encodeURIComponent(first_mrna_name));
        }
        if(jb_url !== '') {
          var jb_html = "";
          jb_html += "<div id='jbrowse_container' style='margin-top:20px'>";
          jb_html += "  <div style='margin-bottom:5px'>";
          jb_html += "    <a class='float-left jbrowse_link' href='" + jb_url + "' target='_blank'>Full screen</a>";
          jb_html += "    <button class='close float-right' onclick=\"$('#jbrowse_container').hide();\" title='Close'><span>&times;</span></button>";
          jb_html += "  </div>";
          jb_html += "  <div style='clear:both'></div>";
          jb_html += "  <iframe src='" + jb_url + "' style='border:1px solid rgb(80,80,80); height:300px; width:100%;'>";
          jb_html += "    <p>Your browser does not support iframes.</p>";
          jb_html += "  </iframe>";
          jb_html += "</div>";
          $('#section_jbrowse').html(jb_html);
        } else {
          $('#section_jbrowse').html('');
        }

        // Section 2: Gene coordinates table
        var coord_html = "";
        coord_html += "<div style='margin-top:20px'>";
        coord_html += "  <button class='btn btn-outline-secondary btn-sm' type='button' onclick=\"$('#gff_table_collapse').toggle()\">";
        coord_html += "    <i class='fas fa-table'></i> Gene coordinates";
        coord_html += "  </button>";
        coord_html += "  <div id='gff_table_collapse' style='display:none'>";
        coord_html += "    <div class='card bg-light text-dark' style='margin-top:5px'>";
        coord_html += "      <div class='card-body' style='overflow-x:auto; padding:10px'>";
        coord_html += "        <table class='table table-bordered table-sm' style='line-height:1; font-size:13px; margin-bottom:0'>";
        coord_html += "          <thead><tr><th>Chr</th><th>Feature</th><th>Start</th><th>End</th><th>Strand</th><th>Info</th></tr></thead>";
        coord_html += "          <tbody>";
        if(gene_structure.gff_lines && gene_structure.gff_lines.length > 0) {
          $.each(gene_structure.gff_lines, function(i, row) {
            coord_html += "<tr><td style='text-align:center'>" + row.chr + "</td><td style='text-align:center'>" + row.feature + "</td><td style='text-align:center'>" + row.start + "</td><td style='text-align:center'>" + row.end + "</td><td style='text-align:center'>" + row.strand + "</td><td style='font-size:11px; word-break:break-all'>" + row.info + "</td></tr>";
          });
        }
        coord_html += "          </tbody>";
        coord_html += "        </table>";
        coord_html += "      </div>";
        coord_html += "    </div>";
        coord_html += "  </div>";
        coord_html += "</div>";
        $('#section_coordinates').html(coord_html);

        // Section 3: Launch sequences AJAX in parallel
        window.get_sequences(gene_structure, gene_name);
      }
    });
  }

  window.get_sequences = function get_sequences(gene_structure, gene_name) {
    var upstream   = Math.min(parseInt($('#genomic_upstream_bp').val())   || 0, 3000);
    var downstream = Math.min(parseInt($('#genomic_downstream_bp').val()) || 0, 3000);
    var ext_start = gene_structure.start;
    var ext_end   = gene_structure.end;

    if(gene_structure.strand == '-') {
      ext_start = Math.max(0, ext_start - downstream);
      ext_end   = ext_end + upstream;
    } else {
      ext_start = Math.max(0, ext_start - upstream);
      ext_end   = ext_end + downstream;
    }

    jQuery.ajax({
      type: 'POST',
      url: 'ajax_get_sequences.php',
      data: {
        'seq_dir':         $('#dataset_select').val() || first_folder,
        'seq_path':        seq_path,
        'json_files_path': json_files_path,
        'chr':    gene_structure.chr,
        'start':  ext_start,
        'end':    ext_end,
        'strand': gene_structure.strand,
        'mrnas':  JSON.stringify(gene_structure.mRNAs)
      },
      success: function(data) {
        var sequences = JSON.parse(data);
        show_sequences(gene_structure, sequences, gene_name, ext_start, ext_end, upstream, downstream);
      }
    });
  }


  function translate(cds) {
    var codon_table = {
      'TTT':'F', 'TTC':'F', 'TTA':'L', 'TTG':'L',
      'CTT':'L', 'CTC':'L', 'CTA':'L', 'CTG':'L',
      'ATT':'I', 'ATC':'I', 'ATA':'I', 'ATG':'M',
      'GTT':'V', 'GTC':'V', 'GTA':'V', 'GTG':'V',
      'TCT':'S', 'TCC':'S', 'TCA':'S', 'TCG':'S',
      'CCT':'P', 'CCC':'P', 'CCA':'P', 'CCG':'P',
      'ACT':'T', 'ACC':'T', 'ACA':'T', 'ACG':'T',
      'GCT':'A', 'GCC':'A', 'GCA':'A', 'GCG':'A',
      'TAT':'Y', 'TAC':'Y', 'TAA':'*', 'TAG':'*',
      'CAT':'H', 'CAC':'H', 'CAA':'Q', 'CAG':'Q',
      'AAT':'N', 'AAC':'N', 'AAA':'K', 'AAG':'K',
      'GAT':'D', 'GAC':'D', 'GAA':'E', 'GAG':'E',
      'TGT':'C', 'TGC':'C', 'TGA':'*', 'TGG':'W',
      'CGT':'R', 'CGC':'R', 'CGA':'R', 'CGG':'R',
      'AGT':'S', 'AGC':'S', 'AGA':'R', 'AGG':'R',
      'GGT':'G', 'GGC':'G', 'GGA':'G', 'GGG':'G'
    };
    var protein = "";
    for(var i = 0; i < cds.length - 2; i += 3) {
      var codon = cds.substring(i, i + 3).toUpperCase();
      protein += codon_table[codon] || '?';
    }
    return protein;
  }

  function show_sequences(gene_structure, sequences, gene_name, ext_start, ext_end, upstream, downstream) {
    var html = "";
    var first_mrna_id = Object.keys(gene_structure.mRNAs)[0];
    var jb_loc = first_mrna_id || gene_name;




    html += "<ul class='nav nav-tabs' style='margin-top:20px'>";
    var first_tab = true;
    $.each(gene_structure.mRNAs, function(mrna_id, mrna_data) {
      var active = first_tab ? "active" : "";
      var tab_id = mrna_id.replace(/\./g, '_');
      html += "<li class='nav-item'>";
      html += "  <a class='nav-link " + active + "' data-toggle='tab' href='#tab_" + tab_id + "'>" + mrna_id + "</a>";
      html += "</li>";
      first_tab = false;
    });
    html += "</ul>";

    html += "<div class='tab-content' style='margin-top:20px'>";
    first_tab = true;

    seq_store = {};
    $.each(gene_structure.mRNAs, function(mrna_id, mrna_data) {
      var tab_id = mrna_id.replace(/\./g, '_');
      var cds_seq = sequences.sequences[mrna_id].cds_seq || "";
      seq_store[tab_id] = {
        genomic:    sequences.genomic_seq,
        transcript: sequences.sequences[mrna_id].transcript_seq || "",
        cds:        cds_seq,
        protein:    cds_seq ? translate(cds_seq) : ""
      };
    });

    $.each(gene_structure.mRNAs, function(mrna_id, mrna_data) {
      var active = first_tab ? "active" : "";
      var tab_id = mrna_id.replace(/\./g, '_');
      html += "<div id='tab_" + tab_id + "' class='tab-pane " + active + "'>";
      html += "<div class='card' style='margin-top:10px'>";
      html += "  <div class='card-header' data-toggle='collapse' href='#genomic_" + tab_id + "' style='cursor:pointer'>";
      html += "    <b>Genomic sequence</b>";
      html += "  </div>";
      html += "  <div id='genomic_" + tab_id + "' class='collapse'>";
      html += "  <div class='card-body' style='padding:8px 15px; background:#f8f9fa; border-bottom:1px solid rgba(0,0,0,.125)'>";
      html += "    <div class='form-inline'>";
      html += "      <small style='margin-right:10px'><b>Extend:</b></small>";
      html += "      <small style='margin-right:5px'>Upstream</small>";
      html += "      <input type='number' id='genomic_upstream_bp' class='form-control form-control-sm' value='0' min='0' max='3000' style='width:70px; margin-right:10px'>";
      html += "      <small style='margin-right:5px'>Downstream</small>";
      html += "      <input type='number' id='genomic_downstream_bp' class='form-control form-control-sm' value='0' min='0' max='3000' style='width:70px; margin-right:10px'>";
      html += "      <small class='text-muted' style='margin-right:10px'>(max 3000 bp)</small>";
      html += "      <button type='button' class='btn btn-outline-secondary btn-sm' onclick='update_genomic_seq()'><i class='fas fa-sync-alt'></i> Update</button>";
      html += "    </div>";
      html += "  </div>";
      html += "    <div class='card-body'>";
      html += "      <div id='genomic_content_" + tab_id + "'>";
      html += "      <div style='margin-bottom:10px'>";
      html += "        <small><b>Legend: </b></small>";
      html += "        <span style='background-color:#c0c0c0; color:#000000; border:1px solid #ddd; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Flanking region</small></span>";
      html += "        <span style='background-color:#8cb4e7; color:#000000; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>5'UTR</small></span>";
      html += "        <span style='background-color:#339933; color:#ffffff; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Exon</small></span>";
      html += "        <span style='background-color:#FFFFFF; color:#000000; border:1px solid #ddd; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Intron</small></span>";
      html += "        <span style='background-color:#e7b071; color:#000000; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>3'UTR</small></span>";
      html += "        <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; float:right' onclick=\"download_fasta('" + mrna_id + "_genomic', seq_store['" + tab_id + "'].genomic)\"><i class='fas fa-file-download'></i> Download FASTA</button>";
      html += "        <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; margin-right:10px; float:right' onclick=\"blast_redirect('" + tab_id + "', 'genomic')\"><i class='fas fa-dna'></i> BLAST</button>";
      html += "      </div>";
      html += "      <br>";
      html += "      <p style='font-family:monospace; word-break:break-all'>" + color_genomic_seq(sequences.genomic_seq, mrna_data, ext_start, ext_end, gene_structure.strand, upstream, downstream) + "</p>";
      html += "      </div>"; // end genomic_content
      html += "    </div>";
      html += "  </div>";
      html += "</div>";
      html += "<div class='card' style='margin-top:10px'>";
      html += "  <div class='card-header' data-toggle='collapse' href='#transcript_" + tab_id + "' style='cursor:pointer'>";
      html += "    <b>Transcript</b>";
      html += "  </div>";
      html += "  <div id='transcript_" + tab_id + "' class='collapse'>";
      html += "    <div class='card-body'>";
      html += "      <div style='margin-bottom:10px'>";
      html += "        <small><b>Legend: </b></small>";
      html += "        <span style='background-color:#8cb4e7; color:#000000; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>5'UTR</small></span>";
      html += "        <span style='background-color:#339933; color:#ffffff; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>CDS</small></span>";
      html += "        <span style='background-color:#e7b071; color:#000000; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>3'UTR</small></span>";
      html += "      <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; float:right' onclick=\"download_fasta('" + mrna_id + "_transcript', seq_store['" + tab_id + "'].transcript)\"><i class='fas fa-file-download'></i> Download FASTA</button>";
      html += "        <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; margin-right:10px; float:right' onclick=\"blast_redirect('" + tab_id + "', 'genomic')\"><i class='fas fa-dna'></i> BLAST</button>";
      html += "      </div>";
      html += "      <br>";
      html += "      <p style='font-family:monospace; word-break:break-all'>" + color_transcript_seq(sequences.sequences[mrna_id].transcript_seq, mrna_data, ext_start, gene_structure.strand) + "</p>";
      html += "    </div>";
      html += "  </div>";
      html += "</div>";
      if(seq_store[tab_id].cds) {
        html += "<div class='card' style='margin-top:10px'>";
        html += "  <div class='card-header' data-toggle='collapse' href='#protein_" + tab_id + "' style='cursor:pointer'>";
        html += "    <b>Protein</b>";
        html += "  </div>";
        html += "  <div id='protein_" + tab_id + "' class='collapse'>";
        html += "    <div class='card-body'>";
        html += "      <div style='margin-bottom:10px'>";
        html += "        <small><b>Legend: </b></small>";
        html += "        <span style='background-color:#FFD700; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Hidrophobic</small></span>";
        html += "        <span style='background-color:#ADD8E6; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Polar</small></span>";
        html += "        <span style='background-color:#FF6B6B; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Acidic</small></span>";
        html += "        <span style='background-color:#90EE90; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Basic</small></span>";
        html += "        <span style='background-color:#D3D3D3; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Stop</small></span>";
        html += "      <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; float:right' onclick=\"download_fasta('" + mrna_id + "_protein', seq_store['" + tab_id + "'].protein)\"><i class='fas fa-file-download'></i> Download FASTA</button>";
        html += "        <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; margin-right:10px; float:right' onclick=\"blast_redirect('" + tab_id + "', 'protein')\"><i class='fas fa-dna'></i> BLAST</button>";
        html += "      </div>";
        html += "      <br>";
        html += "      <p style='font-family:monospace; word-break:break-all'>" + color_protein_seq(seq_store[tab_id].protein) + "</p>";
        html += "    </div>";
        html += "  </div>";
        html += "</div>";
      }

      html += "</div>";
      first_tab = false;
    });
    html += "</div>";

    $('#section_sequences').html(html);
  }

});



function update_genomic_seq() {
  if(!window.current_gene_structure || !window.current_gene_name) return;

  var upstream   = Math.min(parseInt($('#genomic_upstream_bp').val())   || 0, 500);
  var downstream = Math.min(parseInt($('#genomic_downstream_bp').val()) || 0, 500);

  sessionStorage.setItem('sv_upstream',   upstream);
  sessionStorage.setItem('sv_downstream', downstream);

  var gene_structure = window.current_gene_structure;
  var ext_start = gene_structure.start;
  var ext_end   = gene_structure.end;

  if(gene_structure.strand == '-') {
    ext_start = Math.max(0, ext_start - downstream);
    ext_end   = ext_end + upstream;
  } else {
    ext_start = Math.max(0, ext_start - upstream);
    ext_end   = ext_end + downstream;
  }
  $.each(gene_structure.mRNAs, function(mrna_id, mrna_data) {
    var tab_id = mrna_id.replace(/\./g, '_');
    $('#genomic_content_' + tab_id).html(
      "<div class='text-center' style='padding:20px'>" +
      "  <div class='spinner-border text-secondary' role='status'>" +
      "    <span class='sr-only'>Loading...</span>" +
      "  </div>" +
      "</div>"
    );
  });

  jQuery.ajax({
    type: 'POST',
    url: 'ajax_get_sequences.php',
    data: {
      'seq_dir':         $('#dataset_select').val() || window.first_folder,
      'seq_path':        window.seq_path,
      'json_files_path': window.json_files_path,
      'chr':    gene_structure.chr,
      'start':  ext_start,
      'end':    ext_end,
      'strand': gene_structure.strand,
      'mrnas':  JSON.stringify(gene_structure.mRNAs)
    },
    success: function(data) {
      var sequences = JSON.parse(data);

      $.each(gene_structure.mRNAs, function(mrna_id, mrna_data) {
        var tab_id = mrna_id.replace(/\./g, '_');
        if(seq_store[tab_id]) seq_store[tab_id].genomic = sequences.genomic_seq;

        var genomic_html = "";
        genomic_html += "<div style='margin-bottom:10px'>";
        genomic_html += "  <small><b>Legend: </b></small>";
        genomic_html += "  <span style='background-color:#c0c0c0; color:#000000; border:1px solid #ddd; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Flanking region</small></span>";
        genomic_html += "  <span style='background-color:#8cb4e7; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>5\'UTR</small></span>";        
        genomic_html += "  <span style='background-color:#339933; color:#ffffff; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Exon</small></span>";
        genomic_html += "  <span style='background-color:#FFFFFF; border:1px solid #ddd; padding:2px 8px; border-radius:3px; margin-right:5px'><small>Intron</small></span>";
        genomic_html += "  <span style='background-color:#e7b071; border:1px solid #ccc; padding:2px 8px; border-radius:3px; margin-right:5px'><small>3\'UTR</small></span>";
        genomic_html += "  <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; float:right' onclick=\"download_fasta('" + mrna_id + "_genomic', seq_store['" + tab_id + "'].genomic)\"><i class='fas fa-file-download'></i> Download FASTA</button>";
        genomic_html += "  <button type='button' class='btn btn-secondary btn-sm' style='margin-bottom:10px; margin-right:10px; float:right' onclick=\"blast_redirect('" + tab_id + "', 'genomic')\"><i class='fas fa-dna'></i> BLAST</button>";
        genomic_html += "</div>";
        genomic_html += "<br>";
        genomic_html += "<p style='font-family:monospace; word-break:break-all'>" + color_genomic_seq(sequences.genomic_seq, mrna_data, ext_start, ext_end, gene_structure.strand, upstream, downstream) + "</p>";

        $('#genomic_content_' + tab_id).html(genomic_html);
      });
    }
  });
}

function download_fasta(id, sequence) {
  var fasta = ">" + id + "\n" + sequence;
  var blob = new Blob([fasta], {type: 'text/plain'});
  var url = URL.createObjectURL(blob);
  var a = document.createElement('a');
  a.href = url;
  a.download = id + '.fasta';
  a.click();
  URL.revokeObjectURL(url);
}

function color_genomic_seq(genomic_seq, mrna_data, gene_start, gene_end, strand, upstream, downstream) {
  var seq_length = genomic_seq.length;
  var position_types = new Array(seq_length).fill('intron');

  function to_offset(feat_start, feat_end) {
    if(strand == '-') {
      return { s: gene_end - feat_end, e: gene_end - feat_start };
    } else {
      return { s: feat_start - gene_start, e: feat_end - gene_start };
    }
  }

  $.each(mrna_data.exons, function(i, exon) {
    var o = to_offset(exon.start, exon.end);
    for(var j = o.s; j <= o.e; j++) position_types[j] = 'exon';
  });

  if(mrna_data.five_prime_UTR && mrna_data.five_prime_UTR.length > 0) {
    $.each(mrna_data.five_prime_UTR, function(i, utr) {
      var o = to_offset(utr.start, utr.end);
      for(var j = o.s; j <= o.e; j++) position_types[j] = 'five_UTR';
    });
  }
  if(mrna_data.three_prime_UTR && mrna_data.three_prime_UTR.length > 0) {
    $.each(mrna_data.three_prime_UTR, function(i, utr) {
      var o = to_offset(utr.start, utr.end);
      for(var j = o.s; j <= o.e; j++) position_types[j] = 'three_UTR';
    });
  }

  if((!mrna_data.five_prime_UTR || mrna_data.five_prime_UTR.length === 0) &&
     (!mrna_data.three_prime_UTR || mrna_data.three_prime_UTR.length === 0)) {
    var cds_offsets = [];
    $.each(mrna_data.CDS, function(i, cds) {
      var o = to_offset(cds.start, cds.end);
      cds_offsets.push(o.s, o.e);
    });
    var first_cds = cds_offsets.length > 0 ? Math.min.apply(null, cds_offsets) : -1;
    var last_cds  = cds_offsets.length > 0 ? Math.max.apply(null, cds_offsets) : -1;
    $.each(mrna_data.exons, function(i, exon) {
      var o = to_offset(exon.start, exon.end);
      for(var j = o.s; j <= o.e; j++) {
        if(position_types[j] === 'exon' && first_cds >= 0) {
          if(j < first_cds || j > last_cds) {
            position_types[j] = (j < first_cds) ? 'five_UTR' : 'three_UTR';
          }
        }
      }
    });
  }
  upstream   = upstream   || 0;
  downstream = downstream || 0;
  if(strand == '-') {
    if(upstream > 0) {
      for(var j = 0; j < upstream; j++) position_types[j] = 'flanking';
    }
    if(downstream > 0) {
      for(var j = seq_length - downstream; j < seq_length; j++) position_types[j] = 'flanking';
    }
  } else {
    if(upstream > 0) {
      for(var j = 0; j < upstream; j++) position_types[j] = 'flanking';
    }
    if(downstream > 0) {
      for(var j = seq_length - downstream; j < seq_length; j++) position_types[j] = 'flanking';
    }
  }

  var colors = {
    'intron':    { bg: '#ffffff00', text: '#000000' },
    'flanking':  { bg: '#c0c0c0', text: '#000000' },
    'exon':      { bg: '#339933', text: '#ffffff' },
    'five_UTR':  { bg: '#8cb4e7', text: '#000000' },
    'three_UTR': { bg: '#e7b071', text: '#000000' }
  };

  var colored_seq   = "";
  var current_type  = position_types[0];
  var current_chunk = "";

  for(var j = 0; j < seq_length; j++) {
    if(position_types[j] == current_type) {
      current_chunk += genomic_seq[j];
    } else {
      colored_seq += "<span style='background-color:" + colors[current_type].bg + "; color:" + colors[current_type].text + "'>" + current_chunk + "</span>";
      current_type  = position_types[j];
      current_chunk = genomic_seq[j];
    }
  }
  colored_seq += "<span style='background-color:" + colors[current_type].bg + "; color:" + colors[current_type].text + "'>" + current_chunk + "</span>";
  return colored_seq;
}

function color_transcript_seq(transcript_seq, mrna_data, gene_start, strand) {
  console.log("has_cds:", mrna_data.CDS && mrna_data.CDS.length > 0);
  console.log("CDS length:", mrna_data.CDS ? mrna_data.CDS.length : 0);
  console.log("five_prime_UTR:", mrna_data.five_prime_UTR ? mrna_data.five_prime_UTR.length : 0);
  var seq_length = transcript_seq.length;
  var position_types = new Array(seq_length).fill('CDS');

  var utr5_length = 0;
  var utr3_length = 0;

  if(mrna_data.five_prime_UTR && mrna_data.five_prime_UTR.length > 0) {
    $.each(mrna_data.five_prime_UTR, function(i, utr) {
      utr5_length += utr.end - utr.start + 1;
    });
  } else if(mrna_data.exons && mrna_data.exons.length > 0 && mrna_data.CDS && mrna_data.CDS.length > 0) {
    var cds_coords = [];
    $.each(mrna_data.CDS, function(i, cds) { cds_coords.push(cds.start, cds.end); });
    var first_cds = Math.min.apply(null, cds_coords);
    var last_cds  = Math.max.apply(null, cds_coords);

    if(strand == '-') {
      $.each(mrna_data.exons, function(i, exon) {
        if(exon.start > last_cds) {
          utr5_length += exon.end - exon.start + 1;
        } else if(exon.end > last_cds) {
          utr5_length += exon.end - last_cds;
        }
      });
    } else {
      $.each(mrna_data.exons, function(i, exon) {
        if(exon.end < first_cds) {
          utr5_length += exon.end - exon.start + 1;
        } else if(exon.start < first_cds) {
          utr5_length += first_cds - exon.start;
        }
      });
    }
  }

  if(mrna_data.three_prime_UTR && mrna_data.three_prime_UTR.length > 0) {
    $.each(mrna_data.three_prime_UTR, function(i, utr) {
      utr3_length += utr.end - utr.start + 1;
    });
  } else if(mrna_data.exons && mrna_data.exons.length > 0 && mrna_data.CDS && mrna_data.CDS.length > 0) {
    var cds_coords = [];
    $.each(mrna_data.CDS, function(i, cds) { cds_coords.push(cds.start, cds.end); });
    var first_cds = Math.min.apply(null, cds_coords);
    var last_cds  = Math.max.apply(null, cds_coords);

    if(strand == '-') {
      $.each(mrna_data.exons, function(i, exon) {
        if(exon.end < first_cds) {
          utr3_length += exon.end - exon.start + 1;
        } else if(exon.start < first_cds) {
          utr3_length += first_cds - exon.start;
        }
      });
    } else {
      $.each(mrna_data.exons, function(i, exon) {
        if(exon.start > last_cds) {
          utr3_length += exon.end - exon.start + 1;
        } else if(exon.end > last_cds) {
          utr3_length += exon.end - last_cds;
        }
      });
    }
  }

  for(var j = 0; j < utr5_length; j++) {
    position_types[j] = 'five_UTR';
  }
  for(var j = seq_length - utr3_length; j < seq_length; j++) {
    position_types[j] = 'three_UTR';
  }

  var colors = {
    'CDS':       { bg: '#339933', text: '#FFFFFF' },
    'five_UTR':  { bg: '#8cb4e7', text: '#000000' },
    'three_UTR': { bg: '#e7b071', text: '#000000' }
  };

  var colored_seq   = "";
  var current_type  = position_types[0];
  var current_chunk = "";

  for(var j = 0; j < seq_length; j++) {
    if(position_types[j] == current_type) {
      current_chunk += transcript_seq[j];
    } else {
      colored_seq += "<span style='background-color:" + colors[current_type].bg + "; color:" + colors[current_type].text + "'>" + current_chunk + "</span>";
      current_type  = position_types[j];
      current_chunk = transcript_seq[j];
    }
  }
  colored_seq += "<span style='background-color:" + colors[current_type].bg + "; color:" + colors[current_type].text + "'>" + current_chunk + "</span>";
  return colored_seq;
}

function color_protein_seq(protein) {
  var aa_colors = {
    'A':'#FFD700', 'V':'#FFD700', 'I':'#FFD700', 'L':'#FFD700',
    'M':'#FFD700', 'F':'#FFD700', 'W':'#FFD700', 'P':'#FFD700',
    'S':'#ADD8E6', 'T':'#ADD8E6', 'C':'#ADD8E6', 'Y':'#ADD8E6',
    'N':'#ADD8E6', 'Q':'#ADD8E6',
    'D':'#FF6B6B', 'E':'#FF6B6B',
    'K':'#90EE90', 'R':'#90EE90', 'H':'#90EE90',
    '*':'#D3D3D3'
  };
  var colored_aa_seq = "";
  for(var i = 0; i < protein.length; i++) {
    var aa    = protein.substring(i, i + 1).toUpperCase();
    var color = aa_colors[aa] || '#FFFFFF';
    colored_aa_seq += "<span style='background-color:" + color + "'>" + aa + "</span>";
  }
  return colored_aa_seq;
}

function blast_redirect(tab_id, seq_type) {
  var sequence = seq_store[tab_id][seq_type];
  if(seq_type === 'protein') {
    sequence = sequence.replace(/\*$/, '');
  }
  var fasta = ">" + tab_id + "_" + seq_type + "\n" + sequence;
  sessionStorage.setItem('blast_sequence', fasta);
  window.location.href = '../blast/blast_input.php';
}
</script>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");
?>
