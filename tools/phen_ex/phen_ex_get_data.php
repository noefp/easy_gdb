<?php
// ─── phen_ex_get_data.php ────────────────────────────────────────────────────
// AJAX endpoint — returns JSON with traits and accessions by reading
// the passport.json files in each species folder.

header("Content-Type: application/json");
header("Cache-Control: no-cache");

// Load EasyGDB configuration
if (file_exists($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php")) {
  include_once($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php");
} elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/configuration_path.php")) {
  include_once($_SERVER['DOCUMENT_ROOT']."/configuration_path.php");
}
include_once "$conf_path/easyGDB_conf.php";
include_once __DIR__ . "/phen_ex_utils.php";

// load_passport(), filename_to_label(), load_phenotype_data()
// → defined in phen_ex_utils.php

// ─── Validate parameters ─────────────────────────────────────────────────────
$species = isset($_GET["species"]) ? trim($_GET["species"]) : "";
$dataset = isset($_GET["dataset"]) ? trim($_GET["dataset"]) : "";

if (!preg_match('/^[\w]+$/', $species)) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid species."]);
  exit;
}

// ─── Load germplasm_list.json if it exists (optional) ────────────────────────
$germplasm      = [];
$germplasm_file = "$passport_path/germplasm_list.json";
if (file_exists($germplasm_file)) {
  $germplasm = json_decode(file_get_contents($germplasm_file), true) ?? [];
}

// ─── Load passport.json for the species ──────────────────────────────────────
$passport = load_passport($passport_path, $species);
if (!$passport) {
  http_response_code(404);
  echo json_encode(["error" => "Species not found: $species"]);
  exit;
}
if (empty($passport["phenotype_files"])) {
  http_response_code(404);
  echo json_encode(["error" => "No phenotype files for this species."]);
  exit;
}

$acc_name        = $passport["acc_link"] ?? "ACC Name";
$phenotype_files = $passport["phenotype_files"];

// ─── Build dataset list ───────────────────────────────────────────────────────
$datasets = [];
foreach ($phenotype_files as $filename) {
  $key = preg_replace('/[^a-z0-9_]/i', '_', strtolower(
    preg_replace('/\.txt$/i', '', $filename)
  ));
  $datasets[] = [
    "key"       => $key,
    "filename"  => $filename,
    "phen_name" => filename_to_label($filename),
  ];
}

// ─── Select the requested dataset ────────────────────────────────────────────
$selected_dataset = null;
foreach ($datasets as $ds) {
  if ($ds["key"] === $dataset || $ds["filename"] === $dataset) {
    $selected_dataset = $ds;
    break;
  }
}
if (!$selected_dataset) $selected_dataset = $datasets[0];

// ─── Load traits and accessions from the selected file ───────────────────────
$pheno_path     = "$passport_path/$species/" . $selected_dataset["filename"];
$hidden_indices = $passport["hidden_search_traits"][$selected_dataset["filename"]] ?? [];
$data           = load_phenotype_data($pheno_path, $acc_name, $hidden_indices);

if (!$data) {
  http_response_code(404);
  echo json_encode(["error" => "Phenotype file not found: " . $selected_dataset["filename"]]);
  exit;
}

// ─── Species label — from germplasm_list.json or folder name ─────────────────
if (isset($germplasm[$species])) {
  $sp_info     = array_values($germplasm[$species])[0] ?? [];
  $common_name = $sp_info["common_name"] ?? $species;
  $sps_name    = $sp_info["sps_name"]    ?? "";
  $sp_label    = $sps_name ? "$common_name ($sps_name)" : $common_name;
} else {
  $sp_label = ucwords(str_replace('_', ' ', $species));
}

// ─── Return JSON ─────────────────────────────────────────────────────────────
echo json_encode([
  "species"          => $species,
  "species_label"    => $sp_label,
  "dataset"          => $selected_dataset["key"],
  "dataset_label"    => $selected_dataset["phen_name"],
  "acc_name"         => $acc_name,
  "accessions"       => $data["accessions"],
  "total_accessions" => count($data["accessions"]),
  "traits"           => $data["traits"],
  "datasets"         => $datasets,
]);
