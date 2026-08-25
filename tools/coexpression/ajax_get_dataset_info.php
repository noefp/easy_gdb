<?php
$json_file = $_POST["json_file"];
$coex_file = $_POST["coex_dataset"];
$text="";

if (file_exists($json_file)) {
$coexp_json_info = json_decode(file_get_contents($json_file), true);
$coexp_file = $coexp_json_info[basename($coex_file)];
$text= (isset($coexp_file["description_text"]) && !empty($coexp_file["description_text"])) ? $coexp_file["description_text"]: "";

}
  echo json_encode($text);
?>
