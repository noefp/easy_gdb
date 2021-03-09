<?php include_once realpath("../../header.php");?>

<script type="text/javascript" src="/easy_gdb/js/kinetic-v5.1.0.min.js"></script>
<script src="/easy_gdb/js/blast_canvas_graph.js"></script>

<div style="margin:20px">
  <a href="blast_input.php"><i class='fas fa-reply' style='color:#229dff'></i> Go back to input</a>
  <a id="download_blast_table" class="float-right" style="cursor:pointer"><i class='far fa-file' style='font-size:24px;color:#229dff'></i> Download result in tabular format</a>
</div>

<div class="blast_canvas_frame">
  <center>
    <div id="sgn_blast_graph" style="display:none">
        <div id="myCanvas">
          Your browser does not support the HTML5 canvas
        </div>
    </div>
  </center>
  <br>
  <div id="SGN_output" style="margin:20px"></div>
</div>

<?php

// check how many input sequences to choose the output

  $query = $_POST["query"];
  $blast_db = $_POST["blast_db"];
  $blast_prog = $_POST["blast_prog"];
  $max_hits = $_POST["max_hits"];
  $evalue = $_POST["evalue"];
  $blast_matrix = $_POST["blast_matrix"];
  $blast_filter = $_POST["blast_filter"];
  // $blast_filter = "no";

  // echo "$query<br><br>";
  $num_input_seqs = substr_count($query,">");
  // echo "$num_input_seqs<br><br>";
  // echo "$blast_db<br><br>";
  // echo "$blast_prog<br><br>";
  // echo "$max_hits<br><br>";
  // echo "$evalue<br><br>";
  // echo "$blast_matrix<br><br>";

  if ($blast_filter == "on") {
    $blast_filter = "yes";
  }
  else {
    $blast_filter = "no";
  }

  // echo "$blast_filter  It should be yes or no <br><br>";

  if ($blast_prog == "blastn") {
    $blast_cmd = "\"$query\" | $blast_prog -db $blast_db -dust $blast_filter -evalue $evalue -num_descriptions $max_hits -num_alignments $max_hits -html -max_hsps 3";
  }
  if ($blast_prog == "tblastn") {
    $blast_cmd = "\"$query\" | $blast_prog -db $blast_db -seg $blast_filter -evalue $evalue -num_descriptions $max_hits -num_alignments $max_hits -html -max_hsps 3";
  }

  if ($blast_prog == "blastp" || $blast_prog == "blastx" || $blast_prog == "tblastx") {
    $blast_cmd = "\"$query\" | $blast_prog -db $blast_db -seg $blast_filter -evalue $evalue -matrix $blast_matrix -num_descriptions $max_hits -num_alignments $max_hits -html";
  }

  // echo "$blast_cmd<br><br>";

  $blast_res = shell_exec('printf '.$blast_cmd);

  echo "<div style=\"margin:20px;min-width:1020px\">$blast_res</div>";


  function _check_coordinates($tmp_start,$tmp_end) {

    $final_start = $tmp_start;
    $final_end = $tmp_end;

    if ($tmp_start > $tmp_end) {
      $final_start = $tmp_end;
      $final_end = $tmp_start;
    }

    $coord_array = array($final_start,$final_end);
    return $coord_array;
  }

  if ($num_input_seqs == 1) {

  $query = "";
  $subject = "";
  $id = 0.0;
  $aln = 0;
  $aln_total = 0;
  $mismatch = 0;
  $gaps = 0;
  $gapopen = 0;
  $qstart = 0;
  $qend = 0;
  $sstart = 0;
  $send = 0;
  $evalue = 0.0;
  $score = 0;
  $desc = "";

  $one_hsp = 0;
  $append_desc = 0;
  $query_line_on = 0;
  $query_length = 0;

  $res_html = array();
  $res_tab_txt = array();
  $json_array = array();

  array_push($res_html, "<table id=\"blast_table\" class=\"table\">");
  array_push($res_html, "<tr><th>SubjectId</th><th>Qid%</th><th>Aln</th><th>evalue</th><th>Score</th><th>Description</th></tr>");
  array_push($res_tab_txt, "QueryID\tSubjectId\tQid%\tAln\tmismatches\tgapopen\tqstart\tqend\tsstart\tsend\tevalue\tScore\tDescription");

  $lines = explode("\n", $blast_res);

  foreach ($lines as $line) {

    if (preg_match('/Query\=\<\/b\>\s*(\S*)/', $line, $match)) {

      $query = $match[1];

      // echo "$line<br><br>query: $query<br><br>";
      $blast_db_name = preg_replace('/.*\//',"", $blast_db);
      $blast_db_name = str_replace('_'," ", $blast_db_name);

      array_unshift($res_html, "<center><h3>".$query." vs ".$blast_db_name."</h3></center>");
      $query_line_on = 1;

      // echo "<br><br>blast_db_name: $blast_db_name<br><br>";
      // echo '<pre>'; print_r($res_html); echo '</pre>';
    }

      if ($query_line_on){
        if (preg_match('/Length\=(\d+)/', $line, $match)) {
          $query_length = $match[1];
          $query_line_on = 0;
        }
      }

      if ($append_desc) {
        if (!preg_match('/^Length/', $line, $match)) {
          $new_desc_line = preg_replace('/\s+/'," ", $line);
          $desc .= $new_desc_line;
        }
        else {
          $append_desc = 0;
        }
      }

      if (preg_match('/^>/', $line, $match)) {
        $append_desc = 1;

        if ($subject) {
          $coordinates_checked = _check_coordinates($sstart,$send);
          $sstart = $coordinates_checked[0];
          $send = $coordinates_checked[1];

          $mm = $mismatch-$gaps;
          array_push($res_html, "<tr><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"/aetar_db/gene.php?name=$subject\" target=\"_blank\">$subject</a></td><td>$id</td><td>$aln</td><td>$evalue</td><td>$score</td><td>$desc</td></tr>");
          array_push($res_tab_txt, "$query\t$subject\t$id\t$aln_total\t$mm\t$gapopen\t$qstart\t$qend\t$sstart\t$send\t$evalue\t$score\t$desc");

          if (strlen($desc) > 150) {
            $desc = substr($desc,0,150)." ...";
          }

          $description_hash = array();

          $description_hash{"name"} = $subject;
          $description_hash{"id_percent"} = $id;
          $description_hash{"score"} = $score;
          $description_hash{"description"} = $desc;
          $description_hash{"qstart"} = $qstart;
          $description_hash{"qend"} = $qend;
          array_push($json_array, $description_hash);

       }
       $subject = "";
       $id = 0.0;
       $aln = 0;
       $aln_total = 0;
       $mismatch = 0;
       $gapopen = 0;
       $qstart = 0;
       $qend = 0;
       $sstart = 0;
       $send = 0;
       $evalue = 0.0;
       $score = 0;
       $desc = "";
       $one_hsp = 0;

       if (preg_match('/^>([^\s\<]+).+\<\/a\>(.*)/', $line, $match)) {
          $subject = $match[1];
          $desc = $match[2];
          $desc = trim($desc, " ");
        //  echo "<br><br>subject: $subject<br><br>";
        //  echo "<br><br>desc: $desc<br><br>";
       }
     }

      if (preg_match('/Score\s*=/', $line, $match)) {

        // echo "<br><br>in: hello<br><br>";
        // echo "<br><br>one_hsp: $one_hsp<br><br>";

        if($one_hsp == 1) {
           $coordinates_checked = _check_coordinates($sstart,$send);
           $sstart = $coordinates_checked[0];
           $send = $coordinates_checked[1];
// echo "<br><br>in2: hello<br><br>";

           $mm = $mismatch-$gaps;
           array_push($res_html, "<tr><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"/aetar_db/gene.php?name=$subject\" target=\"_blank\">$subject</a></td><td>$id</td><td>$aln</td><td>$evalue</td><td>$score</td><td>$desc</td></tr>");
           array_push($res_tab_txt, "$query\t$subject\t$id\t$aln_total\t$mm\t$gapopen\t$qstart\t$qend\t$sstart\t$send\t$evalue\t$score\t$desc");

           if (strlen($desc) > 150) {
             $desc = substr($desc,0,150)." ...";
           }


           $description_hash = array();

           $description_hash{"name"} = $subject;
           $description_hash{"id_percent"} = $id;
           $description_hash{"score"} = $score;
           $description_hash{"description"} = $desc;
           $description_hash{"qstart"} = $qstart;
           $description_hash{"qend"} = $qend;
           array_push($json_array, $description_hash);

           $id = 0.0;
           $aln = 0;
           $aln_total = 0;
           $mismatch = 0;
           $gapopen = 0;
           $qstart = 0;
           $qend = 0;
           $sstart = 0;
           $send = 0;
           $evalue = 0.0;
           $score = 0;
        }
      }

      if (preg_match('/Score\s*=\s*([\d\.]+)\s*bits/', $line, $match)) {
           $score = $match[1];
           $one_hsp = 1;
           $append_desc = 0;
          //  echo "<br><br>score: $score<br><br>";
      }


     if (preg_match('/Expect\s*=\s*([\d\.\-e]+)/', $line, $match)) {
       $evalue = $match[1];
      //  echo "<br><br>evalue: $evalue<br><br>";
     }

     if (preg_match('/Identities\s*=\s*(\d+)\/(\d+)/', $line, $match)) {
        $aln_matched = $match[1];
        $aln_total = $match[2];
        $aln = "$aln_matched/$aln_total";
        $id = sprintf("%.2f", $aln_matched*100/$aln_total);
        $mismatch = $aln_total - $aln_matched;
        // echo "<br><br>aln_matched: $aln_matched<br><br>";
        // echo "<br><br>aln_total: $aln_total<br><br>";
        // echo "<br><br>aln: $aln<br><br>";
     }

     if (preg_match('/Gaps\s*=\s*(\d+)\/\d+/', $line, $match)) {
        $gaps = $match[1];
     }

      if ($qstart == 0) {
        if (preg_match('/^Query\s+(\d+)/', $line, $match)) {
          $qstart = $match[1];
        }
      }

      if ($sstart == 0) {
        if (preg_match('/^Sbjct\s+(\d+)/', $line, $match)) {
          $sstart = $match[1];
        }
      }

      if (preg_match('/^Query/', $line, $match)) {
        if (preg_match('/(\d+)\s*$/', $line, $match)) {
          $qend = $match[1];

          $gap_num = preg_match_all("/\-+/",$line);
          $gapopen = $gapopen + $gap_num;
          // echo "<p class=\"yellow_col\">gapopen3:$subject<br> $line<br>$gap_num $gapopen<br></p>";
        }
      }

      if (preg_match('/^Sbjct/', $line, $match)) {
        if (preg_match('/(\d+)\s*$/', $line, $match)) {
          $send = $match[1];

          $gap_num = preg_match_all("/\-+/",$line);
          $gapopen = $gapopen + $gap_num;
          // echo "<p class=\"yellow_col\">gapopen4:$subject<br> $line<br>$gap_num $gapopen<br></p>";
        }
      }

  }

  $coordinates_checked = _check_coordinates($sstart,$send);
  $sstart = $coordinates_checked[0];
  $send = $coordinates_checked[1];
  // echo "<br><br>in2: hello<br><br>";

  $mm = $mismatch-$gaps;
  array_push($res_html, "<tr><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"/aetar_db/gene.php?name=$subject\" target=\"_blank\">$subject</a></td><td>$id</td><td>$aln</td><td>$evalue</td><td>$score</td><td>$desc</td></tr>");
  array_push($res_tab_txt, "$query\t$subject\t$id\t$aln_total\t$mm\t$gapopen\t$qstart\t$qend\t$sstart\t$send\t$evalue\t$score\t$desc");

  if (strlen($desc) > 150) {
    $desc = substr($desc,0,150)." ...";
  }

  $description_hash = array();

  $description_hash{"name"} = $subject;
  $description_hash{"id_percent"} = $id;
  $description_hash{"score"} = $score;
  $description_hash{"description"} = $desc;
  $description_hash{"qstart"} = $qstart;
  $description_hash{"qend"} = $qend;
  array_push($json_array, $description_hash);

  $blast_table = join('', $res_html);
  $blast_out_txt = join('\n', $res_tab_txt);

}
?>


<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script>
  var num_input_seqs = '<?php echo $num_input_seqs ?>';
  var blast_table_string = '<?php echo $blast_out_txt ?>';
  // alert("num_input_seqs: "+num_input_seqs);

  if (num_input_seqs == 1) {
    var seq_length = '<?php echo $query_length ?>';
    var blast_table_html = '<?php echo $blast_table ?>';
    var sgn_graph_array = <?php echo json_encode($json_array) ?>;

  // alert("sgn_graph_array: "+sgn_graph_array[1]);
  // alert("sgn_graph_array: "+sgn_graph_array[0]["score"]);
  // alert("sgn_graph_array: "+sgn_graph_array);
    jQuery('#sgn_blast_graph').css("display", "inline");
    jQuery("#SGN_output").html(blast_table_html);
    draw_blast_graph(sgn_graph_array, seq_length);
  }

  $("#download_blast_table").click(function() {
    download(blast_table_string, "BLAST_tabular_result.txt", "text/plain");
  });

</script>

<style>
.blast_canvas_frame {
  min-width: 1020px;
  background-color: #fff;
  padding-top:20px;
  padding-bottom:20px;
  padding-left:10px;
  padding-right:10px;
  margin-left:20px;
  margin-right:20px;
  border-radius: 5px;
}
</style>
