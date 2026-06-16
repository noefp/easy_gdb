<?php
// ─── phen_ex_utils.php ───────────────────────────────────────────────────────
// Shared utility functions for the Phenotype Extraction tool.

// ─── get_icon() ──────────────────────────────────────────────────────────────
// Returns the FontAwesome class matching a keyword in $icons.
// Falls back to $default if no keyword matches.

function get_icon($key, $icons, $default = 'fa-square-full') {
  if (!is_array($icons)) return $default ?? 'fa-square-full';
  foreach ($icons as $keyword => $icon) {
    if (stripos($key, $keyword) !== false) return $icon;
  }
  return $default ?? 'fa-square-full';
}

// ─── filename_to_label() ─────────────────────────────────────────────────────
// Converts a phenotype filename to a human-readable label.
// "Ripe_fruit_descriptors.txt" → "Ripe Fruit Descriptors"
// "Leaf.txt"                   → "Leaf"

function filename_to_label($filename) {
  $name = preg_replace('/\.txt$/i', '', $filename);
  $name = str_replace('_', ' ', $name);
  return ucwords(strtolower($name));
}

// ─── load_passport() ─────────────────────────────────────────────────────────
// Reads the passport.json for a species and returns it as an array.
// Returns null if the file does not exist.

function load_passport($passport_path, $species) {
  $f = "$passport_path/$species/passport.json";
  return file_exists($f) ? json_decode(file_get_contents($f), true) : null;
}

// ─── load_phenotype_data() ───────────────────────────────────────────────────
// Reads a phenotype file and returns traits and accessions.
//
// $hidden_indices — column indices from hidden_search_traits in passport.json.
// Indices are base-1 (first column = 1), converted to base-0 internally.
// The acc_name column and all hidden columns are excluded from traits.
// Accessions are read from the acc_name column, deduplicated and sorted.

function load_phenotype_data($pheno_path, $acc_name, $hidden_indices = []) {
  if (!file_exists($pheno_path)) return null;

  $fh     = fopen($pheno_path, "r");
  $header = str_getcsv(trim(fgets($fh)), "\t");

  $acc_idx = array_search($acc_name, $header);
  if ($acc_idx === false) $acc_idx = 0;

  // Convert base-1 indices from passport.json to base-0 column names
  $hidden_cols = [];
  foreach ($hidden_indices as $idx) {
    $idx_0 = $idx - 1;
    if (isset($header[$idx_0])) $hidden_cols[] = $header[$idx_0];
  }

  // Exclude acc_name + hidden_search_traits columns
  $exclude = array_merge([$acc_name], $hidden_cols);
  $traits  = array_values(array_filter($header,
    fn($col) => !in_array($col, $exclude) && trim($col) !== ""
  ));

  // Read accessions — deduplicate and sort
  $accessions = [];
  while (($line = fgets($fh)) !== false) {
    if (trim($line) === "") continue;
    $row = str_getcsv($line, "\t");
    if (!empty($row[$acc_idx])) $accessions[] = trim($row[$acc_idx]);
  }
  fclose($fh);

  $accessions = array_values(array_unique($accessions));
  sort($accessions);

  return ["traits" => $traits, "accessions" => $accessions];
}
