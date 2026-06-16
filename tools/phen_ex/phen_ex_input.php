<?php include_once realpath("../../header.php");?>
<?php include_once realpath("../modal.html");?>
<?php include_once realpath("phen_ex_info_modal.php");?>
<?php include_once realpath("phen_ex_utils.php");?>

<?php
// Load icons for species, datasets and traits from $json_files_path/tools/custom_tools_icons.json
$icons_files = "$json_files_path/tools/custom_tools_icons.json";
if (file_exists($icons_files)) {
  $icons = json_decode(file_get_contents($icons_files), true) ?? [];
}

// Only includes species that have a passport.json with phenotype_files
// Get subdirectories of passport directory
// Read subdirectories of $passport_path 
$subdir_name = [];
if (is_dir($passport_path) && $sub_dh = opendir($passport_path)) {
  while ($species = readdir($sub_dh)) {
    if (!preg_match('/^\./', $species) && is_dir("$passport_path/$species")) {
      $subdir_name[] = $species;
    }
  }
  closedir($sub_dh);
}

$species_config = [];
$germplasm      = [];

//  Load germplasm_list.json if it exists (optional)
$germplasm_file = "$passport_path/germplasm_list.json";
if (file_exists($germplasm_file)) {
  $germplasm = json_decode(file_get_contents($germplasm_file), true) ?? [];
}

//  Build $species_config from folders that have a valid passport.json 
if (empty($subdir_name)) {
  $load_error = "No species found.";
} else {
  foreach ($subdir_name as $sp_key) {
    $passport = load_passport($passport_path, $sp_key);
    if (empty($passport["phenotype_files"])) continue;

    // Build species label: use germplasm_list.json if available, otherwise format the folder name
    if (isset($germplasm[$sp_key])) {
      $sp_info = array_values($germplasm[$sp_key])[0] ?? [];
      $common  = $sp_info["common_name"] ?? $sp_key;
      $sps     = $sp_info["sps_name"]    ?? "";
      $label   = $sps ? "$common ($sps)" : $common;
    } else {
      // If germplasm_list.json is absent, label is derived from the folder name
      $label = ucwords(str_replace('_', ' ', $sp_key)); 
    }
    $species_config[$sp_key] = [
      "label"    => $label,
      "passport" => $passport,
    ];
  }

  if (empty($species_config)) {
    $load_error = "No species with phenotype data found.";
  }
}

//  Level 1: species 
$keys_species     = array_keys($species_config);
$selected_species = isset($_GET["species"]) ? $_GET["species"] : ($keys_species[0] ?? "");
$selected_species = array_key_exists($selected_species, $species_config)
                    ? $selected_species : ($keys_species[0] ?? "");
$sp_entry         = $species_config[$selected_species] ?? [];
$passport         = $sp_entry["passport"] ?? [];
$acc_name         = $passport["acc_link"] ?? "ACC Name";
$phenotype_files  = $passport["phenotype_files"] ?? [];

//  Level 2: dataset 
$datasets = [];
foreach ($phenotype_files as $filename) {
  $key = preg_replace('/[^a-z0-9_]/i', '_', strtolower(
    preg_replace('/\.txt$/i', '', $filename)
  ));
  $datasets[$key] = ["filename" => $filename, "phen_name" => filename_to_label($filename)];
}

$keys_datasets    = array_keys($datasets);
$selected_dataset = isset($_GET["dataset"]) ? $_GET["dataset"] : ($keys_datasets[0] ?? "");
$selected_dataset = array_key_exists($selected_dataset, $datasets)
                    ? $selected_dataset : ($keys_datasets[0] ?? "");
$dataset_data     = $datasets[$selected_dataset] ?? [];

//  Load traits and accessions for the selected species/dataset   
$pheno_file     = $dataset_data["filename"] ?? "";
$pheno_path     = "$passport_path/$selected_species/$pheno_file";
$hidden_indices = $passport["hidden_search_traits"][$pheno_file] ?? [];
$pheno_result   = load_phenotype_data($pheno_path, $acc_name, $hidden_indices);

$accessions    = $pheno_result["accessions"] ?? [];
$pheno_columns = $pheno_result["traits"]     ?? [];
$load_error    = $pheno_result ? "" : "Phenotype file not found: $pheno_file";

$form_errors = [];
if (isset($_GET["errors"]) && !empty($_GET["errors"])) {
  $form_errors = explode("|", urldecode($_GET["errors"]));
}
// Flags for conditional display of species/dataset selectors and info modals
$single_species = count($species_config) === 1;
$multi_dataset    = count($datasets) > 1;
$total_accessions = count($accessions);
$step = 1;
?>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/11_phen_ex.php" target="blank">
    <i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help
  </a>
</div>
<br>

<h1 class="text-center">
  Phenotype Extraction <i class="fas fa-filter" style="color:#555"></i>
</h1>
<p class="text-center text-muted">Select a species, dataset and traits to generate a phenotype CSV for analysis</p>
<br>

<div id="tool-container" style="margin:auto; max-width:960px; padding:20px">

<?php if (!empty($form_errors)): ?>
  <div class="alert alert-danger">
    <?php foreach ($form_errors as $e): ?>
      <p style="margin:0"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($load_error): ?>
  <div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($load_error); ?>
  </div>
<?php else: ?>

<form id="phen_ex_form" action="phen_ex_run.php" method="post">

  <!--   Species selector   -->
  <?php if (!$single_species): ?>
  <div class="card shadow-sm" style="margin-bottom:20px; padding:20px">
    <h5>
      <span class="badge badge-info mr-2"><?php echo $step++; ?></span> Select species
      <button type="button" class="info_icon" data-toggle="modal" data-target="#phen_ex_help">i</button>
    </h5>
    <div class="row" style="margin-top:10px">
      <?php foreach ($species_config as $key => $sp): ?>
      <div class="col-md-6" style="margin-bottom:10px">
        <div class="species-card <?php echo ($key === $selected_species) ? 'selected' : ''; ?>"
             data-key="<?php echo $key; ?>"
             onclick="selectSpecies('<?php echo $key; ?>')">
          <?php $specie_icon = get_icon($key, $icons[SPECIES_ICONS] ?? null,$icons[SPECIES_ICON_DEFAULT] ?? null); ?>
          <i class="fas <?php echo $specie_icon; ?>" style="color:#229dff; font-size:1.4em"></i>
          <span style="margin-left:8px"><?php echo htmlspecialchars($sp["label"]); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="species" id="species_input" value="<?php echo $selected_species; ?>">
    <input type="hidden" name="dataset" id="dataset_input" value="<?php echo $selected_dataset; ?>">
  </div>

  <?php else: ?>
  <input type="hidden" name="species" id="species_input" value="<?php echo $selected_species; ?>">
  <input type="hidden" name="dataset" id="dataset_input" value="<?php echo $selected_dataset; ?>">
  <?php endif; ?>

  <!--  Dataset selector  -->
  <?php if ($multi_dataset): ?>
  <div class="card shadow-sm" style="margin-bottom:20px; padding:20px">
    <h5>
      <span class="badge badge-info mr-2"><?php echo $step++; ?></span> Select dataset / tissue / stage
    </h5>
    <div id="datasets_container" style="margin-top:5px">
      <div class="row" id="datasets_row" style="margin-top:10px">
        <?php foreach ($datasets as $dkey => $ds): ?>
        <div class="col-md-3 col-sm-6" style="margin-bottom:8px">
          <div class="dataset-card <?php echo ($dkey === $selected_dataset) ? 'selected' : ''; ?>"
               data-key="<?php echo $dkey; ?>"
               onclick="selectDataset('<?php echo $dkey; ?>')">
            <?php $dataset_icon = get_icon($dkey, $icons[DATASET_ICONS] ?? null, $icons[DATASET_ICON_DEFAULT] ?? null); ?>
            <i class="fas <?php echo $dataset_icon; ?>" style="color:#229dff; font-size:1em"></i>
            <span style="margin-left:6px; font-size:0.9em">
              <?php echo htmlspecialchars($ds["phen_name"]); ?>
            </span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!--  Trait selector  -->
  <div class="card shadow-sm" style="margin-bottom:20px; padding:20px">
    <h5>
      <span class="badge badge-info mr-2"><?php echo $step++; ?></span> Select phenotype traits
      <small class="text-muted" style="font-size:0.75em; margin-left:8px">Choose one or more</small>
      <?php if ($multi_dataset): ?>
        <small class="text-muted" style="font-size:0.75em; margin-left:8px">
          — <em id="current_dataset_label"><?php echo htmlspecialchars($dataset_data["phen_name"] ?? $selected_dataset); ?></em>
        </small>
      <?php endif; ?>
    </h5>
    <div class="row" id="traits_container" style="margin-top:12px">
      <?php foreach ($pheno_columns as $col): ?>
      <div class="col-md-6" style="margin-bottom:8px">
        <div class="form-check trait-card">
          <input class="form-check-input trait-check" type="checkbox"
                 name="traits[]" value="<?php echo htmlspecialchars($col); ?>"
                 id="trait_<?php echo htmlspecialchars($col); ?>">
          <label class="form-check-label" for="trait_<?php echo htmlspecialchars($col); ?>">
            <?php $icon = get_icon($col, $icons[TRAIT_ICONS] ?? null, $icons[TRAIT_ICONS_DEFAULT] ?? null); ?>
            <i class="fas <?php echo $icon; ?>" style="color:#229dff; width:16px"></i>
            <?php echo htmlspecialchars(str_replace('_', ' ', $col)); ?>
          </label>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:10px">
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllTraits(true)">Select all</button>
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllTraits(false)">Deselect all</button>
      <span id="trait_count" class="text-muted" style="margin-left:12px; font-size:0.9em">0 traits selected</span>
    </div>
  </div>

  <!--   Accession selector  -->
  <div class="card shadow-sm" style="margin-bottom:20px; padding:20px">
    <h5>
      <span class="badge badge-info mr-2"><?php echo $step++; ?></span> Select accessions
      <span id="acc_total_badge" class="badge badge-secondary" style="font-size:0.75em">
        <?php echo number_format($total_accessions); ?> available
      </span>
    </h5>
    <ul class="nav nav-tabs" style="margin-top:12px">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#tab_list">
          <i class="fas fa-list"></i> Browse & search
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab_paste">
          <i class="fas fa-paste"></i> Paste list
        </a>
      </li>
    </ul>
    <div class="tab-content" style="padding-top:15px">
      <div class="tab-pane fade show active" id="tab_list">
        <div class="row" style="margin-bottom:8px">
          <div class="col-md-5">
            <input type="text" id="acc_search" class="form-control form-control-sm"
                   placeholder="Search accession ID...">
          </div>
          <div class="col-md-7" style="padding-top:4px">
            <button type="button" class="btn btn-sm btn-outline-info" onclick="selectAllVisible()">Select all visible</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Deselect all</button>
            <span id="acc_count" class="text-muted" style="margin-left:10px; font-size:0.9em">
              0 / <?php echo number_format($total_accessions); ?> selected
            </span>
          </div>
        </div>
        <div id="acc_list_container" style="height:260px; overflow-y:auto; border:1px solid #dee2e6; border-radius:4px; padding:8px; background:#fafafa">
          <div id="acc_list"></div>
          <div id="acc_list_more" class="text-muted text-center" style="display:none; font-size:0.85em; padding:6px">
            Showing first 500. Use search to filter.
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="tab_paste">
        <label class="text-muted" style="font-size:0.9em">Paste accession IDs, one per line:</label>
        <textarea id="acc_paste" class="form-control" rows="8"
                  placeholder="ACC001&#10;ACC002&#10;..."></textarea>
        <div style="margin-top:8px">
          <button type="button" class="btn btn-sm btn-info" onclick="applyPastedList()">
            <i class="fas fa-check"></i> Apply list
          </button>
          <span id="paste_result" class="text-muted" style="margin-left:10px; font-size:0.9em"></span>
        </div>
      </div>
    </div>
    <input type="hidden" name="accessions" id="acc_hidden">
  </div>

  <!--   Submit button  -->
  <div class="text-center">
    <button type="submit" id="submit_btn" class="btn btn-info btn-lg">
      <i class="fas fa-file-csv"></i> Generate phenotype CSV
    </button>
    <div id="submit_error" class="text-danger" style="margin-top:8px; display:none"></div>
  </div>
</form>
<?php endif; ?>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script>
var ALL_ACCESSIONS  = <?php echo json_encode(array_values($accessions)); ?>;
var CURRENT_SPECIES = <?php echo json_encode($selected_species); ?>;
var CURRENT_DATASET = <?php echo json_encode($selected_dataset); ?>;
var selected = new Set();

//  AJAX 

// show a spinner while loading species/dataset data
function fetchData(species, dataset, callback) {
  document.getElementById("acc_list").innerHTML =
    '<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</span>';
  document.getElementById("traits_container").innerHTML =
    '<span class="text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</span>';

    // build URL with query parameters
  var url = "phen_ex_get_data.php?species=" + encodeURIComponent(species)
          + (dataset ? "&dataset=" + encodeURIComponent(dataset) : "");

  fetch(url)
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.error) { alert("Error: " + data.error); return; }
      callback(data);
    })
    .catch(function(err) {
      document.getElementById("acc_list").innerHTML =
        '<span class="text-danger">Error loading data. Please try again.</span>';
    });
}

function selectSpecies(key) {
  document.querySelectorAll(".species-card").forEach(function(c) { c.classList.remove("selected"); });
  var card = document.querySelector(".species-card[data-key='" + key + "']");
  if (card) card.classList.add("selected");

  fetchData(key, null, function(data) {
    CURRENT_SPECIES = data.species;
    CURRENT_DATASET = data.dataset;
    document.getElementById("species_input").value = data.species;
    document.getElementById("dataset_input").value  = data.dataset;
    var label = document.getElementById("current_dataset_label");
    if (label) label.textContent = data.dataset_label;
    renderDatasets(data.datasets, data.dataset);
    renderTraits(data.traits);
    ALL_ACCESSIONS = data.accessions;
    selected.clear();
    renderAccList();
    updateAccTotal(data.total_accessions);
  });
}

function selectDataset(key) {
  document.querySelectorAll(".dataset-card").forEach(function(c) { c.classList.remove("selected"); });
  var card = document.querySelector(".dataset-card[data-key='" + key + "']");
  if (card) card.classList.add("selected");
   
  fetchData(CURRENT_SPECIES, key, function(data) {
    CURRENT_DATASET = data.dataset;
    document.getElementById("dataset_input").value = data.dataset;
    var label = document.getElementById("current_dataset_label");
    if (label) label.textContent = data.dataset_label;
    renderTraits(data.traits);
    ALL_ACCESSIONS = data.accessions;
    selected.clear();
    renderAccList();
    updateAccTotal(data.total_accessions);
  });
}

function renderDatasets(datasets, activeKey) {
  // Icons injected from the icons JSON file
  var datasetIcons = <?php echo json_encode($icons["DATASET_ICONS"] ?? []); ?>;
  var datasetIconDefault = <?php echo json_encode($icons["DATASET_ICON_DEFAULT"] ?? "fa-seedling"); ?>;
  if (datasets.length <= 1) return;  // nada que renderizar
  var html = "";
  datasets.forEach(function(ds) {
    var icon = datasetIconDefault;
    Object.keys(datasetIcons).forEach(function(kw) {
      if (ds.key.toLowerCase().indexOf(kw) !== -1) icon = datasetIcons[kw];
    });
    var active = (ds.key === activeKey) ? "selected" : "";
    html += '<div class="col-md-3 col-sm-6" style="margin-bottom:8px">'
          + '<div class="dataset-card ' + active + '" data-key="' + ds.key + '" '
          + 'onclick="selectDataset(\'' + ds.key + '\')">'
          + '<i class="fas ' + icon + '" style="color:#229dff;font-size:1em"></i>'
          + '<span style="margin-left:6px;font-size:0.9em">' + ds.phen_name + '</span>'
          + '</div></div>';
  });
  document.getElementById("datasets_row").innerHTML = html;
}

function renderTraits(traits) {
  var traitIcons = {
    "day": "fa-calendar-alt", "date": "fa-calendar-alt",
    "weight": "fa-weight",    "height": "fa-ruler-vertical",
    "width": "fa-ruler-horizontal", "length": "fa-ruler-horizontal",
    "color": "fa-palette",   "colour": "fa-palette",
    "shape": "fa-shapes",    "resistance": "fa-shield-alt"
  };
  var html = "";
  traits.forEach(function(col) {
    var icon = "fa-leaf";
    Object.keys(traitIcons).forEach(function(kw) {
      if (col.toLowerCase().indexOf(kw) !== -1) icon = traitIcons[kw];
    });
    var safeId = col.replace(/[^a-zA-Z0-9]/g, "_");
    html += '<div class="col-md-6" style="margin-bottom:8px">'
          + '<div class="form-check trait-card">'
          + '<input class="form-check-input trait-check" type="checkbox" '
          + 'name="traits[]" value="' + col + '" id="trait_' + safeId + '">'
          + '<label class="form-check-label" for="trait_' + safeId + '">'
          + '<i class="fas ' + icon + '" style="color:#229dff;width:16px"></i> ' + col
          + '</label></div></div>';
  });
  document.getElementById("traits_container").innerHTML = html;
  document.querySelectorAll(".trait-check").forEach(function(chk) {
    chk.addEventListener("change", updateTraitCount);
  });
  updateTraitCount();
}

function updateAccTotal(total) {
  var badge = document.getElementById("acc_total_badge");
  if (badge) badge.textContent = total.toLocaleString() + " available";
}

//  Accessions selection logic 
function renderAccList(filter) {
  filter = (filter || "").toLowerCase().trim();
  var filtered = filter
    ? ALL_ACCESSIONS.filter(function(a) { return a.toLowerCase().indexOf(filter) !== -1; })
    : ALL_ACCESSIONS;
  var toShow = filtered.slice(0, 500);
  var html = "";
  toShow.forEach(function(acc) {
    var chk  = selected.has(acc) ? "checked" : "";
    html += '<div class="acc-item' + (selected.has(acc) ? ' acc-selected' : '') + '">'
          + '<input type="checkbox" class="acc-chk" id="chk_' + acc + '" value="' + acc + '" ' + chk
          + ' onchange="toggleAcc(this)"> '
          + '<label for="chk_' + acc + '" style="cursor:pointer;font-size:0.85em;margin:0">' + acc + '</label>'
          + '</div>';
  });
  document.getElementById("acc_list").innerHTML = html || '<span class="text-muted">No accessions found.</span>';
  document.getElementById("acc_list_more").style.display = (filtered.length > 500) ? "block" : "none";
  updateAccCount();
}
function toggleAcc(chk) {
  if (chk.checked) selected.add(chk.value); else selected.delete(chk.value);
  updateAccCount();
}
function selectAllVisible() {
  document.querySelectorAll(".acc-chk").forEach(function(chk) { chk.checked = true; selected.add(chk.value); });
  updateAccCount();
}
function deselectAll() {
  selected.clear();
  document.querySelectorAll(".acc-chk").forEach(function(chk) { chk.checked = false; });
  updateAccCount();
}
function updateAccCount() {
  document.getElementById("acc_count").textContent =
    selected.size.toLocaleString() + " / " + ALL_ACCESSIONS.length.toLocaleString() + " selected";
}
function applyPastedList() {
  var raw  = document.getElementById("acc_paste").value.trim();
  var ids  = raw.split(/[\n,;]+/).map(function(s) { return s.trim(); }).filter(Boolean);
  var valid = new Set(ALL_ACCESSIONS);
  var found = 0; var notFound = [];
  ids.forEach(function(id) {
    if (valid.has(id)) { selected.add(id); found++; } else notFound.push(id);
  });
  renderAccList(document.getElementById("acc_search").value);
  var msg = found + " accessions matched.";
  if (notFound.length) msg += " Not found: " + notFound.slice(0,5).join(", ") + (notFound.length > 5 ? " ..." : "");
  document.getElementById("paste_result").textContent = msg;
}

//  Traits 
function toggleAllTraits(state) {
  document.querySelectorAll(".trait-check").forEach(function(chk) { chk.checked = state; });
  updateTraitCount();
}
function updateTraitCount() {
  var n = document.querySelectorAll(".trait-check:checked").length;
  document.getElementById("trait_count").textContent = n + " trait" + (n !== 1 ? "s" : "") + " selected";
}

//  Submit 
document.getElementById("phen_ex_form").addEventListener("submit", function(e) {
  var errDiv = document.getElementById("submit_error");
  errDiv.style.display = "none";
  if (document.querySelectorAll(".trait-check:checked").length === 0) {
    errDiv.textContent = "Please select at least one phenotype trait.";
    errDiv.style.display = "block"; e.preventDefault(); return;
  }
  if (selected.size === 0) {
    errDiv.textContent = "Please select at least one accession.";
    errDiv.style.display = "block"; e.preventDefault(); return;
  }
  document.getElementById("acc_hidden").value = Array.from(selected).join(",");
  var btn = document.getElementById("submit_btn");
  btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Generating...';
  btn.disabled = true;
});

$(document).ready(function () {
  renderAccList();
  $("#acc_search").on("input", function () { renderAccList($(this).val()); });
  $(".trait-check").on("change", updateTraitCount);
});
// Reset submit button state when navigating back to the form
window.addEventListener("pageshow", function(event) {
  var btn = document.getElementById("submit_btn");
  if (btn) {
    btn.innerHTML = '<i class="fas fa-file-csv"></i> Generate phenotype CSV';
    btn.disabled = false;
  }
});
</script>

<style>
  .species-card  { border:2px solid #dee2e6; border-radius:6px; padding:12px 15px; cursor:pointer; transition:all 0.15s; background:#fff; }
  .species-card:hover   { border-color:#229dff; background:#f0f8ff; }
  .species-card.selected{ border-color:#229dff; background:#e8f4ff; font-weight:600; }
  .dataset-card  { border:1px solid #dee2e6; border-radius:5px; padding:8px 12px; cursor:pointer; transition:all 0.15s; background:#fff; }
  .dataset-card:hover   { border-color:#229dff; background:#f0f8ff; }
  .dataset-card.selected{ border-color:#229dff; background:#e8f4ff; font-weight:600; }
  .trait-card    { border:1px solid #dee2e6; border-radius:5px; padding:8px 12px; background:#fff; transition:background 0.1s; }
  .trait-card:hover { background:#f0f8ff; }
  #acc_list_container::-webkit-scrollbar { width:6px; }
  #acc_list_container::-webkit-scrollbar-thumb { background:#ccc; border-radius:3px; }
  #acc_list { display:flex; flex-wrap:wrap; gap:2px 8px; }
  .acc-item { min-width:150px; flex:0 1 180px; max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .acc-item.acc-selected { font-weight:600; }
</style>