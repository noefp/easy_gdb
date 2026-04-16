<?php 
  //echo "$images_path<br>";  
  //echo "sp_name: $sp_name<br>";
  $gallery_path  = "$root_path/$images_path/gallery/$sp_name/$acc_name";
  //echo "gallery_path = $gallery_path<br>";
  $thumbnails = [];
  $active = 'active';
  $is_gallery = is_dir($gallery_path) ;

  if ($is_gallery) {

    echo "<div class=\"col-xs-12 col-sm-6 col-md-6 col-lg-6\">";
    
    echo"<div id=\"galleryCarousel\" class=\"container carousel slide carousel-fade \" data-ride=\"carousel\" data-interval=\"5000\"><div class=\"carousel-inner\">";
    
    echo "<ol class=\"carousel-indicators\">";
    $index = 0;
    if ($dir = opendir($gallery_path)) {
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {

                // Indicador
                $activeClass = ($index === 0) ? "class='active'" : "";
                echo "<li data-target=\"#galleryCarousel\" data-slide-to=\"$index\" $activeClass></li>";

                $index++;
            }
        }
        closedir($dir);
    }
    echo "</ol>";


    echo "<div class=\"carousel-inner\">";
    if ($dir = opendir($gallery_path) ) { 
      // Read files of directory
      $index = 0;
    
      while (($file = readdir($dir)) !== false) {
        // Check that $file is an img
        if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file) ) {
          // Generate img path

          $img_path = "$images_path/gallery/$sp_name/$acc_name/$file";
          //echo "img_path: $img_path<br>";
          echo "<div class=\"carousel-item $active\"><img src=\"$img_path\" class=\"d-block carousel-img-small\" alt=\"$file image\"></div>"; 
          $active = ''; // Reset active 
          $index++;
          // Add imgs to array
          $thumbnails[] = $img_path;
        }
      }
      //close directory
      closedir($dir);
    }

  echo "</div>"; // carousel-inner

  echo "<a class=\"carousel-control-prev\" href=\"#galleryCarousel\" role=\"button\" data-slide=\"prev\"><span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span></a>";
  echo "<a class=\"carousel-control-next\" href=\"#galleryCarousel\" role=\"button\" data-slide=\"next\"><span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span></a>";

  echo "</div>"; // galleryCarousel
  echo "</div>"; // close col
  
  } else {
    // echo "Gallery_path not found for $acc_name<br>";
  }



?>

<style>

  .carousel-img-small {
    width: 100%; /* Adjust the size of the images in the carousel */
    margin: auto; /* Remove margin between images */
    border-radius: 10px; /*  Add rounded corners to the images */
  }
  
  .carousel-inner {
    margin-bottom: 15px; /* Space between carousel and thumbnails */
  }

  /* indicator color when it is not active */
.carousel-indicators li {
    background-color: #bbb;
}
/* indicator color when it is active */
.carousel-indicators .active {
    background-color: #979797; 
}

/* Change color of arrows to grey */
.carousel-control-prev-icon, .carousel-control-next-icon {
  filter: invert(80%) brightness(90%); 

}
  
</style>
