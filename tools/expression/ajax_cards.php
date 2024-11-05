<?php

$expr_file = $_POST["expr_file"];
$dbTitle = $_POST["db_title"];
$dbLogo = $_POST["db_logo"];
$imgPath = $_POST["img_path"];
$sampleArray = $_POST["sample_array"];
$expr_img_array = $_POST["expr_img_array"];
$colors =$_POST["colors"];
$ranges = $_POST["ranges"];


$php_array = array();
$card_color = "#f63";
$gold_card = max($expr_file);
$black_card = min($expr_file);
$gold = 0;


// print("<pre>".print_r($heatmap_series,true)."</pre>");
// print("<pre>".print_r($expr_img_array,true)."</pre>");
// echo "<hr>";

$counter = 0;
foreach ($expr_file as $expr_val) {
 foreach($ranges as $index => $range)
 {
    if(($expr_val >= $range[0]) && ($expr_val <= $range[1]))
    {
      $card_color = $colors[$index];
    } 
    
    if ($expr_val == $black_card && $expr_val < $ranges[0][1]) {
      $card_color = "#000";
    }

    if ($expr_val == $gold_card && $expr_val >=$ranges[0][1]) {
      $gold = 1;
    }
  }
  
  array_push($php_array, '<div class="flip-card  rounded float-left">');
  array_push($php_array, '<div class="flip-card-inner">');
  if ($gold) {
    array_push($php_array, "<div class=\"flip-card-front expr_card_body rounded gold\">");
  } else {
    array_push($php_array, "<div class=\"flip-card-front expr_card_body rounded\" style=\"background-color: $card_color;\">");
  }
  array_push($php_array, '<div id="back_content" style="position: relative; top:80px">');
  array_push($php_array, "<img src=\"$dbLogo\" style=\"height:50px;\">");
  array_push($php_array, "<h4>$dbTitle</h4>");
  array_push($php_array, '</div>');
  array_push($php_array, '</div>');
  
  if ($gold) {
    array_push($php_array, "<div class=\"flip-card-back expr_card_body rounded gold\">");
  } else {
    array_push($php_array, "<div class=\"flip-card-back expr_card_body rounded\" style=\"background-color: $card_color;\">");
  }
  
  
  $sample_name = $sampleArray[$counter];
  $font_style = "font-size: 16px; ";
  
  // if ($sample_name == "Germinating Seed") {
  //   // $sample_name = "WWWWWWWWW W";
  //   // $sample_name = "WWWWWWWWW WWWWW";
  //   // $sample_name = "WWWWWWWWW WWWWWWWWW";
  //   // $sample_name = "WWWWWWWWW WWWWWWWWW WWWWWWWWW";
  //   // $sample_name = "WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WW";
  //   // $sample_name = "WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW W";
  //   // $sample_name = "WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW W";
  //   // $sample_name = "WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW WWWWWWWWW W";
  // }
  $sample_length = strlen($sample_name);


  if ( $sample_length >=80) {
    $font_style = "font-size: 9px; line-height: 9.5px;";
  }else if ( $sample_length >=60) {
    $font_style = "font-size: 10px; line-height: 12px;";
  } else if ( $sample_length >=40) {
    $font_style = "font-size: 10px; line-height: 15px;";
  } else if ( $sample_length >=28) {
    $font_style = "font-size: 10px; line-height: 22px;";
  } else if ( $sample_length >=19) {
    $font_style = "font-size: 12px; line-height: 25px;";
  } else if ( $sample_length >=15) {
    $font_style = "font-size: 12px;";
  } else if ($sample_length >=12) {
    $font_style = "font-size: 14px; ";
  }
  
  
  array_push($php_array, "<p class=\"expr_card_title rounded\" style=\"$font_style\">");
  
  array_push($php_array, "<b>$sample_name</b>");
  array_push($php_array, '</p>');
  
  $sample_img = "expr_placeholder.jpeg";
  if ($expr_img_array[$sample_name]) {
    $sample_img = $imgPath."/expr/".$expr_img_array[$sample_name];
  }
  
  //array_push($php_array, "<p>$sample_img</p>");
  array_push($php_array, "<img src=\"$sample_img\" class=\"rounded expr_card_image\">");
  // array_push($php_array, "<img src=\"$imgPath/expr/placeholder1.png\" class=\"rounded expr_card_image\">");
  
  $card_value_width = 50;
  $expr_length = strlen((string)$expr_val);
  
  if ( $expr_length >=8) {
    $card_value_width = 95;
  } else if ( $expr_length >=6) {
    $card_value_width = 80;
  } else if ($expr_length ==5) {
    $card_value_width = 65;
  }
  
  array_push($php_array, "<div class=\"expr_card_value rounded-circle float-right\" style=\"width:${card_value_width}px\"><b>$expr_val</b></div>");
  
  array_push($php_array, '</div>');
  
  array_push($php_array, '</div>');
  array_push($php_array, '</div>');
  
  $gold = 0;
  $counter++;
    
}
array_push($php_array, <<<EOS

<script type="text/javascript">

  $(document).ready(function () {

    $(".flip-card-back").click(function(){

      $('.flip-card-inner').animate({  borderSpacing: 0 }, {
          step: function(now,fx) {
            $(".flip-card-inner").css('transform','rotateY('+now+'deg)');  
          },
          duration:'slow'
      },'swing');
  
    });

    $(".flip-card-front").click(function(){

      $('.flip-card-inner').animate({  borderSpacing: 180 }, {
          step: function(now,fx) {
            $(".flip-card-inner").css('transform','rotateY('+now+'deg)');  
          },
          duration:'slow'
      },'swing');
  
    });

  });
</script>

EOS
);


  //rsort($file_array);
  //echo "hello";
  echo json_encode($php_array);

?>
