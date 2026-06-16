<?php
// ─── Download mode: regenerate and stream CSV without HTML ─────────────────────────────
// Triggered when the download form sends download=1
if (isset($_POST["download"]) && $_POST["download"] === "1") {

  ob_start();
  if (file_exists($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php")) {
    include_once($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php");
  } elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/configuration_path.php")) {
    include_once($_SERVER['DOCUMENT_ROOT']."/configuration_path.php");
  }
  include_once "$conf_path/easyGDB_conf.php";
  include_once __DIR__ . "/phen_ex_utils.php";

  $species    = trim($_POST["species"] ?? "");
  $dataset    = trim($_POST["dataset"]  ?? "");
  $passport_dl = load_passport($passport_path, $species);
  $dataset_data_dl = [];
  foreach ($passport_dl["phenotype_files"] ?? [] as $filename) {
    $key = preg_replace('/[^a-z0-9_]/i', '_', strtolower(
      preg_replace('/\.txt$/i', '', $filename)
    ));
    if ($key === $dataset) { $dataset_data_dl = ["filename" => $filename]; break; }
  }
  $acc_raw      = trim($_POST["accessions"] ?? "");
  $accessions   = array_filter(array_map("trim", explode(",", $acc_raw)));
  $accessions   = array_filter($accessions, fn($a) => preg_match('/^[\w\-\.\s]+$/', $a));
  $traits       = $_POST["traits"] ?? [];
  $pheno_file      = $dataset_data_dl["filename"] ?? "";
  $pheno_in        = "$passport_path/$species/$pheno_file";
  $acc_name_dl     = $passport_dl["acc_link"] ?? "ACC Name";
  $hidden_indices  = $passport_dl["hidden_search_traits"][$pheno_file] ?? [];
  $missing_vals = ["NA","na","-9","9999","-",".",""];

  // Process using the same reusable function as normal mode
  [$out_rows, , , , , ] = process_phenotype($pheno_in, $accessions, $traits, $missing_vals, $acc_name_dl, $hidden_indices);

  ob_end_clean();

  $filename = "phenotype_" . preg_replace('/[^a-z0-9_]/i','_',$species) . "_" . preg_replace('/[^a-z0-9_]/i','_',$dataset) . "_" . date("d-m-Y") . ".csv";
  header("Content-Type: text/csv; charset=utf-8");
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Cache-Control: no-cache");

  $out = fopen("php://output", "w");
  foreach ($out_rows as $row) fputcsv($out, $row);
  fclose($out);
  exit;
}

// ─── Processing function (used in both download and normal modes) ──────────────────────────────
function process_phenotype($pheno_in, $accessions, $traits, $missing_vals, $acc_name = "Taxa", $hidden_indices = []) {
  $acc_set       = array_flip($accessions);
  $col_values    = [];
  $raw_data      = [];
  $dummy_col_map = [];
  $dummy_traits  = [];
  $ordinal_traits= [];
  $binary_traits = [];

  if (!file_exists($pheno_in)) return [[], [], [], [], [], []];

  $fh     = fopen($pheno_in, "r");
  $header = str_getcsv(trim(fgets($fh)), "\t");

  $acc_idx = array_search($acc_name, $header);
  if ($acc_idx === false) $acc_idx = 0;  // fallback to first column

  $trait_indices = [];
  foreach ($traits as $t) {
    $idx = array_search($t, $header);
    if ($idx !== false) $trait_indices[$t] = $idx;
  }

  // First pass: collect values to detect column types
  while (($line = fgets($fh)) !== false) {
    if (trim($line) === "") continue;
    $row  = str_getcsv(trim($line), "\t");
    $taxa = trim($row[$acc_idx] ?? "");
    if (!array_key_exists($taxa, $acc_set)) continue;
    $raw_data[] = $row;
    foreach ($trait_indices as $t => $idx) {
      $val = trim($row[$idx] ?? "NA");
      if (!in_array($val, $missing_vals)) $col_values[$t][] = $val;
    }
  }
  fclose($fh);

  // Detect column type
  $col_types = [];
  foreach ($trait_indices as $t => $idx) {
    $vals    = $col_values[$t] ?? [];
    $unique  = array_unique($vals);
    $n_uniq  = count($unique);
    $all_num = !empty($vals) && count(array_filter($vals, fn($v) => !is_numeric($v))) === 0;
    if (!$all_num)         $col_types[$t] = "nominal";
    elseif ($n_uniq === 2) $col_types[$t] = "binary";
    elseif ($n_uniq <= 10) $col_types[$t] = "ordinal";
    else                   $col_types[$t] = "quantitative";
  }

  // Output header
  $out_header = ["Taxa"];
  foreach ($trait_indices as $t => $idx) {
    if ($col_types[$t] === "nominal") {
      $cats       = array_values(array_unique($col_values[$t] ?? []));
      sort($cats);
      $dummy_cols = array_map(fn($c) => $t."_".preg_replace('/[^A-Za-z0-9]/','_',$c), $cats);
      $dummy_col_map[$t] = ["cats" => $cats, "cols" => $dummy_cols];
      $dummy_traits[$t]  = $cats;
      $out_header        = array_merge($out_header, $dummy_cols);
    } else {
      $out_header[] = $t;
      if ($col_types[$t] === "binary")  $binary_traits[]  = $t;
      if ($col_types[$t] === "ordinal") $ordinal_traits[] = $t;
    }
  }

  // Output rows
  $out_rows   = [$out_header];
  $pheno_taxa = [];
  $n_out_cols = count($out_header) - 1;

  foreach ($raw_data as $row) {
    $taxa    = trim($row[$acc_idx] ?? "");
    $out_row = [$taxa];
    foreach ($trait_indices as $t => $idx) {
      $val = trim($row[$idx] ?? "NA");
      if (in_array($val, $missing_vals)) $val = "NA";
      if ($col_types[$t] === "nominal") {
        foreach ($dummy_col_map[$t]["cats"] as $cat) {
          $out_row[] = ($val === "NA") ? "NA" : (($val === $cat) ? "1" : "0");
        }
      } else {
        $out_row[] = $val;
      }
    }
    $out_rows[]   = $out_row;
    $pheno_taxa[] = $taxa;
  }

  // Accessions with no phenotype data → NAs
  $pheno_set = array_flip($pheno_taxa);
  $no_pheno  = [];
  foreach ($accessions as $acc) {
    if (!array_key_exists($acc, $pheno_set)) {
      $out_rows[] = array_merge([$acc], array_fill(0, $n_out_cols, "NA"));
      $no_pheno[] = $acc;
    }
  }

  return [$out_rows, $col_types, $dummy_traits, $dummy_col_map,
          $ordinal_traits, $binary_traits, $no_pheno, count($pheno_taxa)];
}

// ─── Normal mode: display warnings page ──────────────────────────────────────────────
// HTML output starts here
?>
<?php include_once realpath("../../header.php");?>
<?php include_once realpath("../modal.html");?>

<?php
ob_start();
if (file_exists($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php")) {
  include_once($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php");
} elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/configuration_path.php")) {
  include_once($_SERVER['DOCUMENT_ROOT']."/configuration_path.php");
}
include_once "$conf_path/easyGDB_conf.php";
include_once __DIR__ . "/phen_ex_utils.php";
ob_end_clean();

// Load passport.json directly for the species
// (same approach as phen_ex_input.php and phen_ex_get_data.php)
// load_passport(), filename_to_label() → defined in phen_ex_utils.php

// ─── Validation ───────────────────────────────────────────────────────────────
$errors = [];

$species = trim($_POST["species"] ?? "");
$dataset = trim($_POST["dataset"]  ?? "");

$passport = load_passport($passport_path, $species);
if (!$passport) $errors[] = "Invalid species.";

// Rebuild the dataset list from phenotype_files
$dataset_data = [];
if ($passport) {
  foreach ($passport["phenotype_files"] ?? [] as $filename) {
    $key = preg_replace('/[^a-z0-9_]/i', '_', strtolower(
      preg_replace('/\.txt$/i', '', $filename)
    ));
    if ($key === $dataset) {
      $dataset_data = ["filename" => $filename, "phen_name" => filename_to_label($filename)];
      break;
    }
  }
}
if (empty($dataset_data)) $errors[] = "Invalid dataset.";

$acc_raw    = trim($_POST["accessions"] ?? "");
$accessions = array_filter(array_map("trim", explode(",", $acc_raw)));
$accessions = array_filter($accessions, fn($a) => preg_match('/^[\w\-\.\s]+$/', $a));
if (count($accessions) === 0) $errors[] = "No accessions selected.";

$traits = $_POST["traits"] ?? [];
if (count($traits) === 0) $errors[] = "No phenotype traits selected.";

$pheno_file      = $dataset_data["filename"] ?? "";
$pheno_in        = "$passport_path/$species/$pheno_file";
$acc_name        = $passport["acc_link"] ?? "ACC Name";
$dataset_label   = $dataset_data["phen_name"] ?? $dataset;
$hidden_indices  = $passport["hidden_search_traits"][$pheno_file] ?? [];
$missing_vals = ["NA","na","-9","9999","-",".",""];

// Redirect back to the form if there are validation errors
if (!empty($errors)) {
  header("Location: phen_ex_input.php?errors=" . urlencode(implode("|", $errors)));
  exit;
}

// ─── Process ──────────────────────────────────────────────────────────────────
[$out_rows, $col_types, $dummy_traits, $dummy_col_map,
 $ordinal_traits, $binary_traits, $no_pheno, $pheno_rows]
  = process_phenotype($pheno_in, $accessions, $traits, $missing_vals, $acc_name, $hidden_indices);

$out_header = $out_rows[0] ?? ["Taxa"];
$has_warnings = !empty($dummy_traits) || !empty($ordinal_traits)
             || !empty($binary_traits) || !empty($no_pheno);
?>

<div style="margin:20px">
  <a href="phen_ex_input.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to selection</a>
</div>

<h1 class="text-center">
 Phenotype Extraction <i class="fas fa-filter" style="color:#555"></i>
</h1>
<p class="text-center text-muted">
  Results for <strong><?php echo htmlspecialchars(ucfirst($species)); ?></strong>
  <?php if (!empty($dataset_label)): ?>
    &nbsp;·&nbsp; <span class="badge badge-secondary"><?php echo htmlspecialchars($dataset_label); ?></span>
  <?php endif; ?>
</p>
<br>

<div id="tool-container" style="margin:auto; max-width:900px; padding: 20px">

  <!-- Summary Cards -->
  <div class="row text-center" style="margin-bottom:20px">
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <h5 class="card-title" style="color:#229dff"><?php echo number_format(count($accessions)); ?></h5>
        <p class="card-text text-muted">Accessions selected</p>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <h5 class="card-title" style="color:#229dff"><?php echo number_format($pheno_rows); ?></h5>
        <p class="card-text text-muted">With phenotype data</p>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <h5 class="card-title" style="color:#229dff"><?php echo count($out_header) - 1; ?></h5>
        <p class="card-text text-muted">Output columns</p>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <h5 class="card-title" style="color:<?php echo count($no_pheno) > 0 ? '#f39c12' : '#229dff'; ?>">
          <?php echo count($no_pheno); ?>
        </h5>
        <p class="card-text text-muted">Without phenotype</p>
      </div></div>
    </div>
  </div>

  <!-- Selected Traits -->
  <div class="card shadow-sm" style="margin-bottom:20px; padding:15px">
    <h6><i class="fas fa-sliders-h" style="color:#229dff"></i> Selected traits</h6>
    <div style="margin-top:8px">
      <?php
      $type_colors = ["quantitative"=>"info","binary"=>"warning","ordinal"=>"warning","nominal"=>"primary"];
      foreach ($traits as $t):
        $type  = $col_types[$t] ?? "quantitative";
        $badge = $type_colors[$type] ?? "info";
      ?>
      <span class="badge badge-<?php echo $badge; ?>" style="margin:3px;font-size:0.85em;padding:5px 10px">
        <?php echo htmlspecialchars(str_replace('_',' ',$t)); ?>
        <small style="opacity:0.85">(<?php echo $type; ?>)</small>
      </span>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Warnings (collapsible) -->
  <?php
  $n_warnings = (!empty($dummy_traits) ? 1 : 0)
              + (!empty($ordinal_traits) ? 1 : 0)
              + (!empty($binary_traits) ? 1 : 0)
              + (!empty($no_pheno) ? 1 : 0);
  ?>
  <?php if ($n_warnings > 0): ?>
  <div class="card shadow-sm" style="margin-bottom:20px">
    <div class="card-header" style="cursor:pointer; background:#fff"
         data-toggle="collapse" data-target="#warnings_panel">
      <div style="display:flex; align-items:center; justify-content:space-between">
        <span>
          <i class="fas fa-exclamation-triangle" style="color:#f39c12"></i>
          <strong style="margin-left:6px">Warnings</strong>
          <span class="badge badge-warning" style="margin-left:8px"><?php echo $n_warnings; ?></span>
        </span>
        <i class="fas fa-chevron-down text-muted" style="font-size:0.85em"></i>
      </div>
    </div>
    <div class="collapse" id="warnings_panel">
      <div class="card-body" style="padding:15px">

        <?php if (!empty($dummy_traits)): ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          <strong>Nominal traits converted to dummy variables:</strong>
          <ul style="margin:10px 0 0 0">
            <?php foreach ($dummy_traits as $t => $cats): ?>
            <li>
              <code><?php echo htmlspecialchars(str_replace('_',' ',$t)); ?></code> &rarr;
              <?php foreach ($dummy_col_map[$t]["cols"] as $col): ?>
                <code><?php echo htmlspecialchars($col); ?></code>
              <?php endforeach; ?>
              <small class="text-muted">(1/0 encoding, one column per category)</small>
            </li>
            <?php endforeach; ?>
          </ul>
          <small>Each dummy column can be used as an independent GWAS phenotype in GAPIT.</small>
        </div>
        <?php endif; ?>

        <?php if (!empty($ordinal_traits)): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>Ordinal traits detected:</strong>
          <?php foreach ($ordinal_traits as $t): ?>
            <span class="badge badge-warning"><?php echo htmlspecialchars(str_replace('_',' ',$t)); ?></span>
          <?php endforeach; ?>
          <br><small style="margin-top:6px;display:block">
            These traits have few unique numeric values. Consider a rank-based inverse normal transformation in R before running GAPIT:
            <code>y &lt;- qnorm(rank(y, na.last="keep") / (sum(!is.na(y)) + 1))</code>
          </small>
        </div>
        <?php endif; ?>

        <?php if (!empty($binary_traits)): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>Binary traits detected:</strong>
          <?php foreach ($binary_traits as $t): ?>
            <span class="badge badge-warning"><?php echo htmlspecialchars(str_replace('_',' ',$t)); ?></span>
          <?php endforeach; ?>
          <br><small style="margin-top:6px;display:block">
            GAPIT will treat these as quantitative. For a more rigorous analysis consider
            PLINK <code>--logistic</code> for binary phenotypes.
          </small>
        </div>
        <?php endif; ?>

        <?php if (!empty($no_pheno)): ?>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i>
          <strong><?php echo count($no_pheno); ?> accessions</strong>
          have no phenotype data — included with <code>NA</code>. GAPIT will exclude them automatically.
          <div style="margin-top:8px">
            <button class="btn btn-sm btn-outline-secondary" type="button"
                    data-toggle="collapse" data-target="#no_pheno_list">
              <i class="fas fa-list"></i> Show accessions (<?php echo count($no_pheno); ?>)
            </button>
            <div class="collapse" id="no_pheno_list">
              <div style="margin-top:8px;max-height:150px;overflow-y:auto;
                          background:#fff;border:1px solid #dee2e6;border-radius:4px;padding:8px;font-size:0.85em">
                <?php echo implode(", ", array_map("htmlspecialchars", $no_pheno)); ?>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Download Section -->
  <div class="card shadow-sm" style="padding:20px">
    <h5><i class="fas fa-download" style="color:#229dff"></i> Download phenotype file</h5>
    <p class="text-muted" style="font-size:0.9em">
      CSV formatted for GAPIT (Taxa column + selected traits<?php echo !empty($dummy_traits) ? ", nominal traits expanded to dummy variables" : ""; ?>).
    </p>

    <!-- Download Form -->
    <form id="download_form" action="phen_ex_run.php" method="post">
      <input type="hidden" name="download"    value="1">
      <input type="hidden" name="species"     value="<?php echo htmlspecialchars($species); ?>">
      <input type="hidden" name="dataset"     value="<?php echo htmlspecialchars($dataset); ?>">
      <input type="hidden" name="accessions"  value="<?php echo htmlspecialchars(implode(",", $accessions)); ?>">
      <?php foreach ($traits as $t): ?>
        <input type="hidden" name="traits[]" value="<?php echo htmlspecialchars($t); ?>">
      <?php endforeach; ?>

      <div class="row align-items-center" style="margin-top:10px">
        <div class="col-md-8">
          <div style="display:flex;align-items:center">
            <i class="fas fa-table fa-2x" style="color:#229dff;margin-right:15px"></i>
            <div>
              <strong>phenotype_<?php echo htmlspecialchars($species); ?>.csv</strong><br>
              <small class="text-muted">
                <?php echo number_format($pheno_rows); ?> accessions ·
                <?php echo count($out_header) - 1; ?> columns
                <?php if (!empty($dummy_traits)): ?>
                  <span class="badge badge-info" style="font-size:0.75em">
                    <?php echo count($dummy_traits); ?> nominal → dummy
                  </span>
                <?php endif; ?>
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <button type="submit" id="download_btn" class="btn btn-info btn-block">
            <i class="fas fa-file-download"></i> Download CSV
          </button>
        </div>
      </div>
    </form>

    <div class="alert alert-info" style="margin-top:15px;font-size:0.9em">
      <i class="fas fa-info-circle"></i> <strong>Next steps:</strong>
      Use this file with your VCF to run:
      <code>PLINK → GCTA (PCA) → EMMAX (Kinship) → GAPIT</code>.
      Then upload your GAPIT results to the
      <a href="/easy_gdb/tools/gwas/gwas_results.php">GWAS Results viewer</a>.
    </div>
  </div>

</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script>
document.getElementById("download_form").addEventListener("submit", function() {
  var btn = document.getElementById("download_btn");
  btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Generating...';
  btn.disabled = true;
  // Reset button after 5 seconds in case of issues (e.g. no response from server)
  setTimeout(function() {
    btn.innerHTML = '<i class="fas fa-file-download"></i> Download CSV';
    btn.disabled = false;
  }, 5000);
});
</script>

<style>
  #tool-container .card-title { font-size:1.5rem; font-weight:700; margin-bottom:0; }
</style>
