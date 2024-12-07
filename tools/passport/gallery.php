<!-- LOAD LIBRARIES -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<?php 
  //echo "$images_path<br>"; // tiene una "/" al inicio    ¡¡ OJO !!
  //echo "sp_name: $sp_name<br>";
  $gallery_path  = "$root_path$images_path/gallery/$sp_name/$acc_name";
  //echo "gallery_path = $gallery_path<br>";
  $thumbnails = [];
  $active = 'active';


  if (is_dir($gallery_path) ) {
    //echo "existe gallery_path: $gallery_path<br>";
    echo"<div class=\"container p-1 my-1 bg-secondary text-white\"><div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\"><center><h1><i class=\"fa-solid fa-images\"></i><b> Gallery </b></h1></center>";
    echo"</div></div>";
  
    //Left and right controls . carousel
    echo"<div id=\"galleryCarousel\" class=\"container carousel slide p-7 my-3 border\" data-ride=\"carousel\"><div class=\"carousel-inner\"><br>";
    echo "<a class=\"carousel-control-prev\" href=\"#galleryCarousel\" role=\"button\" data-slide=\"prev\"><span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span><span class=\"sr-only\">Previous</span></a>";
    echo "<a class=\"carousel-control-next\" href=\"#galleryCarousel\" role=\"button\" data-slide=\"next\"><span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span><span class=\"sr-only\">Next</span></a>";

    if ($dir = opendir($gallery_path) ) {
      // Read files of directory
      while (($file = readdir($dir)) !== false) {
        // Check that $file is an img
        if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file) ) {
          // Generate img path
          $img_path = "$images_path/gallery/$sp_name/$acc_name/$file";
          //echo "img_path: $img_path<br>";
          echo "<div class=\"carousel-item $active\"><img src=\"$img_path\" class=\"d-block carousel-img-small\" alt=\"$file image\"></div>";
          $active = ''; // Reset active 

          // Add imgs to array
          $thumbnails[] = $img_path;
        }
      }
      //close directory
      closedir($dir);
    }
  } else {
    //echo "Gallery_path not found for $acc_name<br>";
  }

?>

  </div>
	
  <!-- Thumbnail Navigation -->
  <div class="d-flex justify-content-center mt-3">
    <div class="thumbnail-container mx-3">
      <?php 
        foreach ($thumbnails as $index => $thumb) {
          echo "<img src=\"$thumb\" class=\"thumbnail\" data-target=\"#galleryCarousel\" data-slide-to=\"$index\" alt=\"Thumbnail\">";
        }
      ?>
    </div>
  </div>
</div>


<style>

  .carousel-img-small {
    width: 50%; /* Adjust the percentage */
    margin: 0 auto; /* Center the image horizontally */
  }
  .carousel-item img {
    transition: transform 0.3s ease; /* Soft transition */
  }
  .carousel-item img:hover {
    transform: scale(2); /* zoom with mouse */
  }
  
  /* Thumbnail styles */
  .thumbnail {
    width: 100px; /* Adjust the size of the thumbnails */
    height: 100px; /* Adjust the size of the thumbnails */
    object-fit: cover;
    cursor: pointer;
    opacity: 0.5; /* Reduce opacity for transparency effect */
    transition: opacity 0.5s; /* Smooth transition for opacity */
	  margin-bottom: 10px; /* Space between thumbnails */
  }
  .thumbnail:hover {
    opacity: 1; /* Full opacity on hover */
  }
  .thumbnail-container {
    display: flex;
    align-items: center;
    overflow-x: auto;
    white-space: nowrap;
	  height: 300 px; /* adjust height to fit your needs, añadido nuevo */
  }

  /* Change color of arrows to black */
  .carousel-control-prev-icon, .carousel-control-next-icon {
    filter: invert(1);
  }

</style>


<script>
  $(document).ready(function(){
    $('#galleryCarousel').carousel();
  });


  function scrollThumbnails(offset) {
    const container = document.querySelector('.thumbnail-container');
    container.scrollLeft += offset;
  }
</script>