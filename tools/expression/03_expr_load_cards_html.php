<!-- #####################             Cards             ################################ -->
    
<?php

  // if ($expr_cards) {

    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#cards_frame" aria-expanded="true">';
      echo '<i class="fas fa-sort" style="color:#229dff"></i> Expression Cards';
    echo '</div>';

    echo '<div id="cards_frame" class="row collapse hide" style=" margin:0px; border:2px solid #666; padding-top:7px">';


      echo '<div class="form-group d-inline-flex" style="width: 450px; margin-left:15px">';
        echo '<label for="card_sel1" style="width: 150px; margin-top:7px"><b>Select gene:</b></label>';
        echo '<select class="form-control" id="card_sel1">';
          
            foreach ($found_genes as $gene) {
              echo "<option value=\"$gene\">$gene</option>";
            }
        
        echo '</select>';
      echo '</div>';


      echo "<div class=\"color-bar\" style=\"margin:20px;\">";  
      echo "<table id=\"color-table-cards\" class=\"color\"></table>";
      echo "</div>"; 



      echo '<div id="card_code"></div>';
    echo '</div>';
    
  // }
?>

<script>

// color table function
function crearFila(colors,ranges,id) {
    const tabla = document.getElementById(id);
    const fila_color = document.createElement('tr');
    let celda=0;

    celda = document.createElement('td')
    celda.style.background='#000000';
    fila_color.appendChild(celda);

    colors.forEach(color => {
        celda = document.createElement('td');
        celda.style.backgroundColor = color;
        fila_color.appendChild(celda);
    });
    celda = document.createElement('td')
    celda.style.backgroundImage='linear-gradient(160deg, #8f6B29, #FDE08D, #DF9F28)';
    fila_color.appendChild(celda);

    

    const fila_range = document.createElement('tr');

    celda = document.createElement('th');
    celda.textContent='Lowest';
    fila_range.appendChild(celda);

    ranges.forEach(range => {
        celda = document.createElement('th');
        celda.textContent=range;
        fila_range.appendChild(celda);
    });
        celda = document.createElement('th');
        celda.textContent='Highest';
        fila_range.appendChild(celda);

    tabla.appendChild(fila_range);
    tabla.appendChild(fila_color);
}

// var expr_cards=<?php //echo $expr_cards?>; 

// if (expr_cards){
  crearFila(colors,ranges_text,'color-table-cards');
// }

</script>

<style>
#color-table-cards{
 width: 80%;
}
#card_code {
  width: 100%;
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 20px; 
  margin-left: auto;
  margin-right: auto;
}  
  
  .expr_card_body {
/*    background-color: #363;*/
    background-image: url("card_pattern.png");
    background-repeat: repeat;
    background-color: #f63;
    height: 280px;
    width: 220px;
    padding: 10px;
    border: 1px solid #000;
    margin-right:5px;
  }

  
  .expr_card_title {
    font: 16px "Lucida Grande", "Trebuchet MS", Verdana, sans-serif;
    background-color: #ec7;
    text-align: center;
    vertical-align: middle;
    width: 200px;
    height: 50px;
    margin-bottom:10px;
    padding-left:3px;
    padding-right:3px;
    border: 1px solid #000;
    line-height: 50px;
  }
  
  .expr_card_image {
    width: 200px;
    height: 200px;
    border: 1px solid #000;
  }
  
  .expr_card_value {
    text-align: center;
    vertical-align: middle;
    font: 16px "Lucida Grande", "Trebuchet MS", Verdana, sans-serif;
    background-color: #ec7;
    width: 50px;
    height: 50px;
    left: 9px;
    position: relative;
    bottom: 42px;
    border: 1px solid #000;
    line-height: 50px;
  }
  
  
  .gold {
    background-image: linear-gradient(160deg, #8f6B29, #FDE08D, #DF9F28);
  } 
  
  
/* FLIP CARD EFFECT*/
  
  /* The flip card container - set the width and height to whatever you want. We have added the border property to demonstrate that the flip itself goes out of the box on hover (remove perspective if you don't want the 3D effect */
  .flip-card {
    background-color: transparent;

    height: 280px;
    width: 220px;
/*    padding: 10px;*/
    margin-right:5px;
    margin-bottom:5px;
 
/*    width: 300px;
    height: 200px;
    border: 1px solid #f1f1f1;
*/    perspective: 1000px; /* Remove this if you don't want the 3D effect */
  }

  /* This container is needed to position the front and back side */
  .flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 1s;
    transform-style: preserve-3d;
  }

  /* Do an horizontal flip when you move the mouse over the flip box container */
/*  .flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
  }
*/  
  
  /* Position the front and back side */
  .flip-card-front, .flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden; /* Safari */
    backface-visibility: hidden;
  }

  /* Style the front side (fallback if image is missing) */
  .flip-card-front {
    background-color: #363;
    color: white;
  }

  /* Style the back side */
  .flip-card-back {
/*    background-image: linear-gradient(180deg, #8f6B29, #FDE08D, #DF9F28);*/
    color: black;
    transform: rotateY(180deg);
  }

  .color-bar{
    width:100%;
    display:block;
    text-align: center;

  }

.color-bar table {
    /* width:80%; */
    margin-left: 30px; 
    margin-left:auto;
    margin-right: auto; 
 } 

 .color-bar td, th {
    height: 20px;
    width: 100px;
    text-align: center;
}
  
</style>
