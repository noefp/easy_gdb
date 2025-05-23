
  <!--  // // Load colours and ranges of expression -->
  <?php
  $error=false;
  $message = "";

  if(!isset($colors) || !isset($ranges_text) || !isset($ranges) || empty($colors) || empty($ranges_text) || empty($ranges))
  {
    // Default colours and ranges of expression
    $colors = ["#eceff1","#b3e5fc","#80cbc4","#ffee58","#ffb74d","#ff8f00","#ff4f00","#cc0000","#D72C79","#801C5A","#6D3917"];
    $ranges_text =["<1",">=1",">=2",">=5",">=10",">=50",">=100",">=200",">=500",">=1000",">=5000"];
    $ranges=[[0,0.99],[1,1.99],[2,4.99],[5,9.99],[10,49.99],[50,99.99],[100,199.99],[200,499.99],[500,999.99],[1000,4999.99],[5000,50000]];

  }else{
    if((count($colors) != count($ranges_text)) ||  (count($ranges_text) != count($ranges))){

      $colors = ["#eceff1","#b3e5fc","#80cbc4","#ffee58","#ffb74d","#ff8f00","#ff4f00","#cc0000","#D72C79","#801C5A","#6D3917"];
      $ranges_text =["<1",">=1",">=2",">=5",">=10",">=50",">=100",">=200",">=500",">=1000",">=5000"];
      $ranges=[[0,0.99],[1,1.99],[2,4.99],[5,9.99],[10,49.99],[50,99.99],[100,199.99],[200,499.99],[500,999.99],[1000,4999.99],[5000,50000]];

      $error=true;
      $message = "The size of the attributes expression colors in easyGDB_conf do not match !!!";
    }
  }

//  Custom colours and ranges of expression
    if ($annot_hash) {
      $annot_hash_color=$annot_hash[$dataset_name_ori]['expression_colors'];
      if($annot_hash_color)
      {
        if(count(array_diff(['colors','ranges_txt','ranges'],array_keys($annot_hash_color))) === 0)
          {
            if((count($annot_hash_color['colors'])||count($annot_hash_color['ranges_txt'])||count($annot_hash_color['ranges_txt'])) !=0 ) // if json variables aren`t empty 
            {
              if((count($annot_hash_color['colors']) == count($annot_hash_color['ranges_txt'])) && (count($annot_hash_color['ranges_txt']) == count($annot_hash_color['ranges'])))
              {
                $colors=$annot_hash_color['colors'];
                $ranges_text=$annot_hash_color['ranges_txt'];
                $ranges=$annot_hash_color['ranges'];

                $error=false;
                
              }else{
                $error=true;
                $message = "The size of the attributes expression colors in expression_info.json do not match !!!";
              }
            }
          }
       }
  }
?>

<script type='text/javascript'>

  if(<?php echo json_encode($error)?>){
    console.error('<?php echo json_encode($message)?>');
  }
   // get JSON values
    const colors= <?php echo json_encode($colors)?>;
    const ranges_text =<?php echo json_encode($ranges_text)?>;
    const ranges =<?php echo json_encode($ranges)?>;

</script>