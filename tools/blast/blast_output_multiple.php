<?php include_once realpath("../../header.php");?>

<script type="text/javascript" src="../../js/kinetic-v5.1.0.min.js"></script>
<script src="../../js/blast_canvas_graph.js?v=<?php echo time(); ?>"></script>


<br><br>
<center>
    <h1>BLAST Results</h1>
</center>
<br><br>

<center>
<div class="frame_container">
<br>
  <h4>Selected Databases:</h4>
  <?php if (!empty($_POST['blast_db'])): ?>
    <ul>
        <?php foreach ($_POST['blast_db'] as $db): ?>
            <?php
            // echo "<li>".$db."</li>";
                // Remove file extensions
                $display_name = preg_replace('/\.[a-z]+\.(phr|nhr)$/i', '', $db);
                // Replace underscores with spaces
                $display_name = str_replace("_", " ", $display_name);
            ?>
            <li><?php echo htmlspecialchars($display_name); ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No databases selected.</p>
<?php endif; ?>
  <div class="align-right">
      <a href="blast_input.php">
          <i class="fas fa-reply"></i> New Search
      </a>
  </div>
</div>
<br><br>

<div class="frame_container">
    <div class="blast_canvas_frame">
       
        <div id="sgn_blast_graph" style="display:none">
            <div id="myCanvas">Your browser does not support the HTML5 canvas</div>
        </div>
        
    </div>

    <!-- Legend -->
    <!-- <div id="qid-legend" style="
        display: flex; 
        align-items: flex-end; 
        justify-content: flex-end; 
        margin: 20px auto;">
        <span style="width: 200px; 
            height: 15px; 
            background: linear-gradient(to right, hsl(0, 100%, 70%), hsl(0, 100%, 10%)); 
            border: 1px solid #ccc; 
            margin-right: 10px; 
            border-radius: 2px;">
        </span>
        <span style="font-size: 12px; font-weight: bold;">QID(%)</span>
    </div> -->
    <!-- End of Legend -->
</div>
<br>
<br>
<!-- Table Data -->

<div class="frame_container">
    <!-- Table and Dropdown Section -->
      <div style="display: flex; justify-content: flex-start; align-items: flex-start;">
      <!-- Dropdown Button -->
      <div class="dropdown" style="margin-left: 0;">
            <button class="dropbtn">Download Options <i class="fas fa-caret-down"></i></button>
            <div class="dropdown-content">
                <h4>Full Results</h4>
                <a id="download_blast_table" style="cursor:pointer">
                    <i class="far fa-file" style="font-size:20px; color:#229dff;"></i> Download (.txt)
                </a>
                <a id="download_blast_table_csv" style="cursor:pointer">
                    <i class="far fa-file" style="font-size:20px; color:#229dff;"></i> Download (.csv)
                </a>
                <a id="download_blast_table_tsv" style="cursor:pointer">
                    <i class="far fa-file" style="font-size:20px; color:#229dff;"></i> Download (.tsv)
                </a>
                <h4>Selected Results</h4>
                <a id="downloadSelectedTxt" style="cursor:pointer">
                    <i class="far fa-file" style="font-size:20px; color:#229dff;"></i> Download Selected (.txt)
                </a>
                <a id="downloadSelectedCsv" style="cursor:pointer">
                    <i class="far fa-file" style="font-size:20px; color:#229dff;"></i> Download Selected (.csv)
                </a>
                <a id="downloadSelectedTsv" style="cursor:pointer">
                    <i class="far fa-file" style="font-size:20px; color:#229dff;"></i> Download Selected (.tsv)
                </a>
            </div>
        </div>
    </div>
    <br>
    <!-- Table Data -->
    <div class="table-responsive">
      <div id="SGN_output" style="margin:20px;"></div>
    </div>
</div>

<!-- End of Table Data -->
</center>
<?php

//echo getcwd();

$query = $_POST["query"];
//all_blast_results = []; // Array to store results for each database
$blast_dbs = []; //its paths
$blast_results = []; // blast raw resylts
$blast_prog = $_POST["blast_prog"];
$max_hits = $_POST["max_hits"];
$evalue = $_POST["evalue"];
$blast_matrix = $_POST["blast_matrix"];
$blast_filter = $_POST["blast_filter"];
$num_input_seqs = substr_count($query,">");
$query_name='';
$task = $_POST["task"];

$blast_task = "";

if ($task == "none") {
  $blast_task = "";
} else {
  $blast_task = "-task $task";
}


$res_html = array();
$res_tab_txt = array();
$json_array = array();


array_push($res_html, "<table id=\"blast_table\" class=\"table \">");
array_push($res_html, "<tr>");
array_push($res_html, "<th>Select</th>"); // Add checkbox header
array_push($res_html, "<th>Description</th>");
array_push($res_html, "<th>Species</th>");
array_push($res_html, "<th>SubjectId</th>");
array_push($res_html, "<th>Qid%</th>");
array_push($res_html, "<th>Aln</th>");
array_push($res_html, "<th>e_value</th>");
array_push($res_html, "<th>Score</th>");
// array_push($res_html, "<th>Description</th>");
array_push($res_html, "</tr>");
array_push($res_tab_txt, "Specie\tDatabase\tQueryID\tSubjectId\tQid%\tAln\tmismatches\tgapopen\tqstart\tqend\tsstart\tsend\te_value\tScore\tDescription");


$length_list = [];

if ($num_input_seqs == 0) {
  $num_input_seqs = 1;
  $query = ">query_seq\n".$query;
}
$blast_filter = ($blast_filter == "on") ? "yes" : "no";
$tem_query = $query;

function find_file_in_directory($directory, $filename) {
  $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($directory),
      RecursiveIteratorIterator::SELF_FIRST
  );

  foreach ($iterator as $file) {
      if ($file->isFile() && basename($file) === $filename) {
          return $file->getPathname();
      }
  }

  return null;
}

// $blast_dbs = [];
// foreach ($_POST['blast_db'] as $filename) {
//   $name = str_replace(" ", "_", $filename);
//   $find_path = find_file_in_directory($blast_dbs_path . "/proteins", $name);

//   if (!$find_path) {
//       $find_path = find_file_in_directory($blast_dbs_path . "/nucleotides", $name);
//   }

//   if ($find_path) {
//       $blast_dbs[] = $find_path;
//   } else {
//       error_log("File not found: " . $name);
//   }
// }


$blast_dbs = [];
foreach ($_POST['blast_db'] as $filename) {
  $name = str_replace(" ", "_", $filename);
  $find_path = find_file_in_directory($blast_dbs_path, $name);

  if (!$find_path) {
      $find_path = find_file_in_directory($blast_dbs_path, $name);
  }

  if ($find_path) {
      $db_path = preg_replace('/\.(phr|nhr)$/', '', $find_path);
      $blast_dbs[] = $db_path;
  } else {
      error_log("File not found: " . $name);
  }
}



function _get_subject_link($s_link_hash,$db_name,$subject_name,$s_start,$s_end) {
  
  $db_name = str_replace(' ',"_", $db_name);
  $s_link = "/easy_gdb/gene.php?name={$subject_name}";
  $target_type="_blank";

  //if ($s_link_hash[$db_name]) {
  if (array_key_exists($db_name, $s_link_hash)) {
    $s_link = $s_link_hash[$db_name];
    
    if (preg_match('/\{subject\}/', $s_link, $match)) {
      $s_link = preg_replace('/\{subject\}/',$subject_name,$s_link);
    }
    else if (preg_match('/\{chr\}/', $s_link, $match)) {
      $s_link = preg_replace('/\{chr\}/',$subject_name,$s_link);
      $s_link = preg_replace('/\{start\}/',$s_start,$s_link);
      $s_link = preg_replace('/\{end\}/',$s_end,$s_link);
    }
    else {
      $target_type="_self";
    }
  }
  
  return array($s_link,$target_type);
}

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

// function _get_species_link($s_link_hash, $nameFD, $species_path) {
//   // Default species link
//   $s_link = "{$species_path}/{$nameFD}.php";
//   $target_type = "_blank";

//   // Check if the species name exists in the link hash
//   if (array_key_exists($nameFD, $s_link_hash)) {
//       $s_link = $s_link_hash[$nameFD];

//       // Replace placeholders in the link
//       if (preg_match('/\{nameFD\}/', $s_link)) {
//           $s_link = preg_replace('/\{nameFD\}/', $nameFD, $s_link);
//       } else {
//           $target_type = "_self"; // Default target if no placeholder is present
//       }
//   }

//   // Return the species link and target type
//   return array($s_link, $target_type);
// }



// Loop through each database in the blast_db array
foreach ($blast_dbs as $blast_db) {


  $blast_cmd = "";

  if ($blast_prog == "blastn") {
      $blast_cmd = "\"$query\" | $blast_prog -db $blast_db -dust $blast_filter -evalue $evalue $blast_task -num_descriptions $max_hits -num_alignments $max_hits -html -max_hsps 3";
  } elseif ($blast_prog == "tblastn") {
      $blast_cmd = "\"$query\" | $blast_prog -db $blast_db -seg $blast_filter -evalue $evalue $blast_task -num_descriptions $max_hits -num_alignments $max_hits -html -max_hsps 3";
  } elseif (in_array($blast_prog, ["blastp", "blastx", "tblastx"])) {
      $blast_cmd = "\"$query\" | $blast_prog -db $blast_db -seg $blast_filter -evalue $evalue $blast_task -matrix $blast_matrix -num_descriptions $max_hits -num_alignments $max_hits -html";
  }
  // echo $blast_cmd."<br><br>";

  $blast_res = shell_exec('printf ' . $blast_cmd);
  $blast_res = str_replace('<a name=', "<a id=", $blast_res);
  // echo $blast_res."<br>";

  if ($blast_res) {
    $blast_results[$blast_db] = $blast_res;
  } else {
    echo "<p>Error executing BLAST for database: $blast_db</p>";
  }

  // Replace underscores with spaces
  $nameDB = substr($blast_db, strrpos($blast_db, "/") + 1);
  $nameFD = basename(dirname($blast_db));


  // list($species_link, $target_type) = _get_species_link($s_link_hash, $nameFD, $species_path);

  echo "<div style=\"margin:60px;min-width:1020px\"><h3>Results for Database: $nameDB</h3></div>";
  
  echo "<div style=\"margin:60px;min-width:1020px\">$blast_res</div>";





  

  $links_hash=[];

  // if ( file_exists("$blast_dbs_path/blast_links.json") ) {
  if ( file_exists("$json_files_path/tools/blast_links.json") ) {
      $links_json_file = file_get_contents("$json_files_path/tools/blast_links.json");
      $links_hash = json_decode($links_json_file, true);
      // if ($links_hash["annot_file"]) {
      //   $annot = $links_hash["annot_file"];
      // }
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
    $e_value = 0.0;
    $score = 0;
    $desc = "";

    $one_hsp = 0;
    $append_desc = 0;
    $query_line_on = 0;
    $query_length = 0;


    $lines = explode("\n", $blast_res);
  
    foreach ($lines as $line) {
      //echo $line;
      //echo "<br>";
      // matching QUERY=
      // display the result html.

      if (preg_match('/Query\=\<\/b\>\s*(\S*)/', $line, $match)) {
        $query = $match[1];
        $query_name = $match[0];
        $blast_db_name = preg_replace('/.*\//',"", $blast_db);
        $blast_db_name = str_replace('_'," ", $blast_db_name);
        //echo $blast_db_name;
        //array_unshift($res_html, "<center><h3>".$query." Blast Result</h3></center>");
        $query_line_on = 1;
      }
      // Query Length
      if ($query_line_on){
        if (preg_match('/Length\=(\d+)/', $line, $match)) {
          $query_length = $match[1];
          $query_line_on = 0;
        }
      }
      // Not matching length
      // not yet append descrition
      // discription
      if ($append_desc) {
        if (!preg_match('/^Length/', $line, $match)) {
          $new_desc_line = preg_replace('/\s+/'," ", $line);
          $desc .= $new_desc_line;
          //echo $desc;
        }
        else {
          $append_desc = 0;
        }
      }







      // cicar.ICC4958.gnm2.scaffold26623 length=5488> cicar.ICC4958.gnm2.Ca7 length=45279478> cicar.ICC4958.gnm2.Ca1 length=39901017> cicar.ICC4958.gnm2.scaffold15166 length=4390
      if (preg_match('/^>\s*([^\s]+)\s*(.*)/', $line, $match)) {
        //echo $blast_db_name;
        //echo $subject;
        $append_desc = 1;
        

        if ($subject) {
          //echo $subject;
          
          list($s_link,$target_type) = _get_subject_link($links_hash,$blast_db_name,$subject,$sstart,$send);
          $coordinates_checked = _check_coordinates($sstart,$send);
          $sstart = $coordinates_checked[0];
          $send = $coordinates_checked[1];
          $mm = $mismatch-$gaps; 
          // remove quotes from descriptions
          $desc = preg_replace('/[\'\"]+/','',$desc);

          $species=str_replace("_", " ", $nameFD);

          array_push($res_html, "<tr><td><input type=\'checkbox\' class=\'row-select\'></td><td>$desc</td><td style=\"white-space: nowrap;\">$species</td><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"$s_link\" target=\"$target_type\">$subject</a></td><td style=\"text-align: right;\">$id</td><td style=\"text-align: right;\">$aln</td><td style=\"text-align: right;\">$e_value</td><td style=\"text-align: right;\">$score</td></tr>");
          
          array_push($res_tab_txt, "$nameFD\t$nameDB\t$query\t$subject\t$id\t$aln_total\t$mm\t$gapopen\t$qstart\t$qend\t$sstart\t$send\t$e_value\t$score\t$desc");

          if (strlen($desc) > 150) {
            $desc = substr($desc,0,150)." ...";
          }
          
          
          $description_hash = array();

          $description_hash["name"] = $subject;
          $description_hash["id_percent"] = $id;
          $description_hash["score"] = $score;
          $description_hash["description"] = $desc;
          $description_hash["qstart"] = $qstart;
          $description_hash["qend"] = $qend;
          $description_hash["db"] = $nameDB;
          array_push($json_array, $description_hash);
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
          $e_value = 0.0;
          $score = 0;
          $desc = "";
          $one_hsp = 0;
          //$append_desc = 1;
          

        }


      if (preg_match('/^>/', $line, $match)) {
         
        //NCBI terms
        if (preg_match('/\<a title\=\"Show report for ([^\"]+).+\<\/a\>(.+)/', $line, $match)) {
          $subject = $match[1];
          $desc = $match[2];
          $desc = trim($desc, " ");
        }
        else if (preg_match('/^>([^\s\<]+).+\<\/a\>(.*)/', $line, $match)) {
         $subject = $match[1];
         $desc = $match[2];
         $desc = trim($desc, " ");
        }

      }
    


      }



      if (preg_match('/Score\s*=/', $line, $match)) {
        if($one_hsp == 1) {
          //echo "opps";
          $coordinates_checked = _check_coordinates($sstart,$send);
          $sstart = $coordinates_checked[0];
          $send = $coordinates_checked[1];

          list($s_link,$target_type) = _get_subject_link($links_hash,$blast_db_name,$subject,$sstart,$send);

          $mm = $mismatch-$gaps;       
          // remove quotes from descriptions
          $desc = preg_replace('/[\'\"]+/','',$desc);
          
          $species=str_replace("_", " ", $nameFD);
          array_push($res_html, "<tr><td><input type=\'checkbox\' class=\'row-select\'></td><td>$desc</td><td style=\"white-space: nowrap;\">$species</td><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"$s_link\" target=\"$target_type\">$subject</a></td><td style=\"text-align: right;\">$id</td><td style=\"text-align: right;\">$aln</td><td style=\"text-align: right;\">$e_value</td><td style=\"text-align: right;\">$score</td></tr>");
          // array_push($res_html, "<tr><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"/easy_gdb/gene.php?name=$subject\" target=\"_blank\">$subject</a></td><td>$id</td><td>$aln</td><td>$e_value</td><td>$score</td><td>$desc</td></tr>");
          array_push($res_tab_txt, "$nameFD\t$nameDB\t$query\t$subject\t$id\t$aln_total\t$mm\t$gapopen\t$qstart\t$qend\t$sstart\t$send\t$e_value\t$score\t$desc");

          if (strlen($desc) > 150) {
            $desc = substr($desc,0,150)." ...";
          }
          
          
          $description_hash = array();

          $description_hash["name"] = $subject;
          $description_hash["id_percent"] = $id;
          $description_hash["score"] = $score;
          $description_hash["description"] = $desc;
          $description_hash["qstart"] = $qstart;
          $description_hash["qend"] = $qend;
          $description_hash["db"] = $nameDB;
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
          $e_value = 0.0;
          $score = 0;
        }
      }
      if (preg_match('/Score\s*=\s*([\d\.]+)\s*bits/', $line, $match)) {
        $score = $match[1];
        $one_hsp = 1;
        $append_desc = 0;
        //echo $score;
      }
      if (preg_match('/Expect\s*=\s*([\d\.\-e]+)/', $line, $match)) {
        $e_value = $match[1];
      }

      if (preg_match('/Identities\s*=\s*(\d+)\/(\d+)/', $line, $match)) {
        $aln_matched = $match[1];
        $aln_total = $match[2];
        $aln = "$aln_matched/$aln_total";
        $id = sprintf("%.2f", $aln_matched*100/$aln_total);
        $mismatch = $aln_total - $aln_matched;
      }

      if (preg_match('/Identities\s*=\s*(\d+)\/(\d+)/', $line, $match)) {
        $aln_matched = $match[1];
        $aln_total = $match[2];
        $aln = "$aln_matched/$aln_total";
        $id = sprintf("%.2f", $aln_matched*100/$aln_total);
        $mismatch = $aln_total - $aln_matched;
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
          //echo $sstart;
        }
      }

      if (preg_match('/^Query/', $line, $match)) {
        if (preg_match('/(\d+)\s*$/', $line, $match)) {
          $qend = $match[1];

          $gap_num = preg_match_all("/\-+/",$line);
          $gapopen = $gapopen + $gap_num;
        }
      }

      if (preg_match('/^Sbjct/', $line, $match)) {
        if (preg_match('/(\d+)\s*$/', $line, $match)) {
          $send = $match[1];

          $gap_num = preg_match_all("/\-+/",$line);
          $gapopen = $gapopen + $gap_num;
        }
      }
    }

    if ($subject) {
      $coordinates_checked = _check_coordinates($sstart,$send);
      $sstart = $coordinates_checked[0];
      $send = $coordinates_checked[1];
      
      list($s_link,$target_type) = _get_subject_link($links_hash,$blast_db_name,$subject,$sstart,$send);
      $mm = $mismatch-$gaps;  
      // remove quotes from descriptions
      // desc format need to double check
      //echo $desc;
      $desc = preg_replace('/[\'\"]+/','',$desc);
      $species=str_replace("_", " ", $nameFD);
      
      array_push($res_html, "<tr><td><input type=\'checkbox\' class=\'row-select\'></td><td>$desc</td><td style=\"white-space: nowrap;\">$species</td><td><a id=\"$subject\" class=\"blast_match_ident\" href=\"$s_link\" target=\"$target_type\">$subject</a></td><td style=\"text-align: right;\">$id</td><td style=\"text-align: right;\">$aln</td><td style=\"text-align: right;\">$e_value</td><td style=\"text-align: right;\">$score</td></tr>");
      array_push($res_tab_txt, "$nameFD\t$nameDB\t$query\t$subject\t$id\t$aln_total\t$mm\t$gapopen\t$qstart\t$qend\t$sstart\t$send\t$e_value\t$score\t$desc");

      if (strlen($desc) > 150) {
        $desc = substr($desc,0,150)." ...";
      }
      $description_hash = array();
      $description_hash["name"] = $subject;
      $description_hash["id_percent"] = $id;
      $description_hash["score"] = $score;
      $description_hash["description"] = $desc;
      $description_hash["qstart"] = $qstart;
      $description_hash["qend"] = $qend;
      $description_hash["db"] = $nameDB;
      array_push($json_array, $description_hash);
    }
      $blast_table = join('', $res_html);
      $blast_out_txt = join('\n', $res_tab_txt);



    }


    $blast_tables[$blast_db] = $blast_table; #html
    $json_arrays[$blast_db] = $json_array; 
    $blast_out_txts[$blast_db] = $blast_out_txt; #text
    $query = $tem_query;

    // $res_html = array();
    // $res_tab_txt = array();
    // $json_array = array()



}

$name = str_replace("</b>", "", $query_name);
 
 
?>


<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script>


  var num_input_seqs = '<?php echo $num_input_seqs ?>';
  var blast_table_string = '<?php echo $blast_out_txt ?>';
  var query_name = '<?php echo $name; ?>';
  var species_files = '<?php echo $species_path; ?>';

  if (num_input_seqs == 1) {
    var seq_length = '<?php echo $query_length ?>';
    var blast_table_html = '<?php echo $blast_table ?>';
    var sgn_graph_array = <?php echo json_encode($json_array) ?>;

    // Sort `sgn_graph_array` by Qid (desc), e_value (asc), and score (desc)
    sgn_graph_array.sort((a, b) => {
      if (a.id_percent !== b.id_percent) return b.id_percent- a.id_percent; // Descending Qid
      // if (a.evalue !== b.evalue) return a.evalue - b.evalue; // Ascending e_value ---> not used
      return b.score - a.score; // Descending Score
    });

    // Insert sorted table into DOM
    jQuery("#SGN_output").html(blast_table_html);
    // addHyperlinkToThirdColumn('/var/www/html/egdb_files_chickpea10k/egdb_species');

    // Sort the rows of the table by Qid%, e_value, and Score
    const table = document.querySelector("#SGN_output table");
    const rows = Array.from(table.querySelectorAll("tr")).slice(1); // Exclude header row

    rows.sort((rowA, rowB) => {
      const qidA = parseFloat(rowA.children[4].textContent); // Qid% column
      const evalueA = parseFloat(rowA.children[6].textContent); // e_value column
      const scoreA = parseFloat(rowA.children[7].textContent); // Score column

      const qidB = parseFloat(rowB.children[4].textContent);
      const evalueB = parseFloat(rowB.children[6].textContent);
      const scoreB = parseFloat(rowB.children[7].textContent);

      // Sorting logic
      const qidDiff = qidB - qidA; // Descending Qid%
      if (qidDiff !== 0) return qidDiff;

      const evalueDiff = evalueA - evalueB; // Ascending e_value
      if (evalueDiff !== 0) return evalueDiff;

      return scoreB - scoreA; // Descending Score
    });

    // Append sorted rows back to the table
    rows.forEach(row => table.appendChild(row));
    

    // Show BLAST graph and draw sorted results
    jQuery('#sgn_blast_graph').css("display", "inline");
    draw_blast_graph(sgn_graph_array, seq_length);
  }

// Add download handlers for all data
$("#download_blast_table").click(function () {
  download(blast_table_string, "BLAST_tabular_result.txt", "text/plain");
});

$("#download_blast_table_csv").click(function () {
  const csvContent = convertToCSV(blast_table_string); // Convert tabular data to CSV
  download(csvContent, "BLAST_tabular_result.csv", "text/csv");
});

$("#download_blast_table_tsv").click(function () {
  const tsvContent = convertToTSV(blast_table_string); // Keep tabs as is for TSV format
  download(tsvContent, "BLAST_tabular_result.tsv", "text/tab-separated-values");
});

// Function to escape and format data for CSV
function convertToCSV(data) {
  const rows = data.split("\n"); // Split data into rows
  return rows
    .map((row) => {
      const fields = row.split("\t"); // Split each row into fields (tab-separated)
      return fields
        .map((field) => {
          // Escape fields by enclosing in double quotes if needed
          if (field.includes(",") || field.includes("\t") || field.includes('"')) {
            return `"${field.replace(/"/g, '""')}"`; // Escape internal quotes
          }
          return field;
        })
        .join(","); // Join fields with commas
    })
    .join("\n"); // Join rows with newlines
}

// Function to format data for TSV
function convertToTSV(data) {
  const rows = data.split("\n"); // Split data into rows
  return rows
    .map((row) => {
      const fields = row.split("\t"); // Split each row into fields (tab-separated)
      return fields
        .map((field) => {
          // No need to escape for TSV, return the field as is
          return field;
        })
        .join("\t"); // Join fields with tabs
    })
    .join("\n"); // Join rows with newlines
}


  


function handleDownload(format) {
const selectedRows = [];
const checkboxes = document.querySelectorAll(".row-select:checked");

checkboxes.forEach((checkbox) => {
  const row = checkbox.closest("tr");
  const rowData = Array.from(row.children)
    .slice(1) // Skip the checkbox column
    .map(cell => cell.textContent.trim());
  selectedRows.push(rowData);
});

if (selectedRows.length === 0) {
  alert("No rows selected!");
  return;
}
 
const header = ["Description", "Specie", "SubjectID", "QID%", "Aln", "E-value", "Score"];

let content = "";
let mimeType = "";

function escapeSpecialCharacters(field, format) {
  if (typeof field === "string") {
    if (format === "csv") {
      // Enclose in double quotes and escape internal double quotes
      return `"${field.replace(/"/g, '""')}"`;
    } else if (format === "tsv" || format === "txt") {
      // No need to escape for TSV or plain text, just return the field
      return field;
    }
  }
  return field; // Return non-string fields as is
}

if (format === "csv") {
  // Escape each field in the rows
  content = [
    header.join(","),
    ...selectedRows.map(row =>
      row.map((field, index) => escapeSpecialCharacters(field, "csv")).join(",")
    ),
  ].join("\n");
  mimeType = "text/csv";
} else if (format === "tsv") {
  content = [
    header.join("\t"),
    ...selectedRows.map(row =>
      row.map((field, index) => escapeSpecialCharacters(field, "tsv")).join("\t")
    ),
  ].join("\n");
  mimeType = "text/tab-separated-values";
} else if (format === "txt") {
  content = [
    header.join("\t"),
    ...selectedRows.map(row =>
      row.map((field, index) => escapeSpecialCharacters(field, "txt")).join("\t")
    ),
  ].join("\n");
  mimeType = "text/plain";
}


  // Trigger download
  const fileName = `Selected_BLAST_rows.${format}`;
  download(content, fileName, mimeType);
}


  document.getElementById("downloadSelectedCsv").addEventListener("click", () => handleDownload("csv"));
  document.getElementById("downloadSelectedTsv").addEventListener("click", () => handleDownload("tsv"));
  document.getElementById("downloadSelectedTxt").addEventListener("click", () => handleDownload("txt"));
 



  function download(content, fileName, mimeType) {
    const blob = new Blob([content], { type: mimeType });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = fileName;
    a.click();

    URL.revokeObjectURL(url);
  }


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

 .frame_container {
  max-width: 1200px;
  padding: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}


        
.left-menu {
    width: 300px;
    background-color: #fff;
    padding: 50px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    /*border-left: 10px solid #229dff;*/


}

/* Menu item styling */
.left-menu a {
    display: block;
    color: #229dff;
    margin-bottom: 15px;
    text-decoration: none;
    font-size: 14px;
}

.left-menu a:hover {
    color: #1a73e8;
}




.float-right {
    float: right;
    margin-left: 10px;
}

/* Dropdown container */
.dropdown {
  position: relative;
  display: inline-block;
}

/* Dropdown button */
.dropbtn {
  background-color: #6c757d;
  color: white;
  padding: 10px 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.dropbtn:hover {
  background-color: #465156;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
  width: 300px;
  padding: 10px;
  border-radius: 5px;
}


.dropdown-content a {
  color: #229dff;
  padding: 8px 12px;
  text-decoration: none;
  display: block;
  border-radius: 4px;
  text-align: left;
}


.dropdown-content a:hover {
  background-color: #f0f0f0;
}

.dropdown:hover .dropdown-content {
  display: block;
}



.frame_container h4 {
  margin-top: 0;
  padding-left: 40px;
  text-align: left;
}

.frame_container ul {
  list-style-type: disc;
  margin: 10px 0;
  padding-left: 60px;
  text-align: left;
}

.frame_container ul li {
  margin-bottom: 5px;
  text-align: left;
}

.align-right {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-top: 10px;
    padding-right: 40px;
}

.align-right a {
    text-decoration: none;
    color: #229dff;
    font-weight: bold;
    font-size: 14px;
}

.align-right a i {
    margin-right: 5px; 
}

.align-right a:hover {
    text-decoration: underline; 
}

 </style>