<?php 
  //echo "$images_path<br>"; // tiene una "/" al inicio    ¡¡ OJO !!
  //echo "sp_name: $sp_name<br>";
  $gallery_path  = "$root_path/$images_path/gallery/$sp_name/$acc_name";
  //echo "gallery_path = $gallery_path<br>";
  $thumbnails = [];
  $active = 'active';
  $is_gallery = is_dir($gallery_path) ;

  if ($is_gallery) {
    //echo "existe gallery_path: $gallery_path<br>";
    // echo"<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#gallery\" aria-expanded=\"true\>";
    // echo"<div class=\"container p-1 my-3 bg-secondary text-white collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#gallery\" aria-expanded=\"true\">
    //       <center><h1><i class=\"fas fa-sort\"></i><b> Gallery </b><i class=\"fa-solid fa-images\"></i></h1></center>";
    // echo"</div>";

echo '
<div class="container p-1 my-3 bg-secondary text-white collapse_section pointer_cursor collapse_background"
     data-toggle="collapse" data-target="#gallery" aria-expanded="true"
     style="display:flex; align-items:center; justify-content:center; position:relative; user-select:none; background-color: #6b6b6b;">
    <i class="fas fa-sort" style="position:absolute; left:10px"></i>
    <h1><b> Gallery </b></h1></div>';

  
    //Left and right controls . carousel
    echo"<div id=\"gallery\" class=\"container collapse show\" aria-labelledby=\"gallery\" style=\"border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); margin-bottom: 50px !important\">";
    echo"<div id=\"galleryCarousel\" class=\"container carousel slide p-1 my-3\" data-ride=\"carousel\"><div class=\"carousel-inner\"><br>";
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
    // echo "Gallery_path not found for $acc_name<br>";
  }

?>

  </div> <!-- close galleryCarousel -->
	
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
</div>  <!-- close collapse --> 
</div> <!-- close galleryCarousel -->
</div> <!-- close container -->


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
  }

  /* Change color of arrows to black */
  .carousel-control-prev-icon, .carousel-control-next-icon {
    filter: invert(1);
  }

</style>


<script>

  $(document).ready(function(){
    var gallery=<?php echo json_encode($is_gallery); ?>;

    if(gallery) {

        $('#galleryCarousel').carousel(); // Initialise the carousel
        // Intersection Observer to control the visibility of the carousel
        const galleryCarousel = document.getElementById('galleryCarousel');
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              $('#galleryCarousel').carousel('cycle'); // Active
            } else {
              $('#galleryCarousel').carousel('pause'); // Pause
            }
          });
        }, { threshold: 0.5 }); // It is visible if it is seen, at least, 50% of carousel

        observer.observe(galleryCarousel);
    }
  });
</script>