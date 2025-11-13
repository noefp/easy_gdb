<?php
    $file = $_POST['file'];
    $lines=get_lines($file);
    $data_set_name = preg_replace('/\.[a-z]{3}$/', "", basename($file));
    $data_set_name = str_replace("_", " ", $data_set_name);

    $data=[];    
    $data['second_line'] = $lines["second_line"];
    $data['input_list'] = $lines['input'];
    $data['data_set_name'] = $data_set_name;
    
    echo json_encode($data);

      function get_lines($file_path) {
        $lines = file($file_path);
        $lines_info = [];
        $lines_info['second_line'] = isset($lines[1]) ? explode(";",$lines[1]) : 'No second line available';
        $lines_info['input'] = array_map('input_list',array_slice($lines,1,4)); // 
        return $lines_info;
      }
      function input_list($list) {
        return (explode("\t",explode("\t",$list)[0]));
      }

    ?>