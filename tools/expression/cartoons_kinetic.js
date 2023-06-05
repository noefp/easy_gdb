

function get_expr_color(expr_val) {
   var expr_color = [200,200,200];
   
   if (expr_val >= 1 && expr_val <= 1.99) {
     // expr_color = "#ffe999";
     expr_color = [255,233,153];
   }
   else if (expr_val >= 2 && expr_val <= 9.99) {
     // expr_color = "#fb4";
     expr_color = [255,187,68];
   }
   else if (expr_val >= 10 && expr_val <= 49.99) {
     // expr_color = "#ff7469";
     expr_color = [255,116,105];
   }
   else if (expr_val >= 50 && expr_val <= 99.99) {
     // expr_color = "#de2515";
     expr_color = [222,37,21];
   }
   else if (expr_val >= 100 && expr_val <= 199.99) {
     // expr_color = "#b71005";
     expr_color = [183,16,5];
   }
   else if (expr_val >= 200 && expr_val <= 4999.99) {
     // expr_color = "#7df";
     expr_color = [119,221,255];
   }
   else if (expr_val > 5000) {
     // expr_color = "#0f0";
     expr_color = [0,255,0];
   }
   // alert("color: "+expr_color);
   return expr_color;
}

function load_image(canvas,kj_layer,imgs_group,img,img_x,img_y,img_w,img_h,sample_name,gene_expr) {
 
 //alert("color: "+gene_expr);
   var expr_color = get_expr_color(gene_expr);
 
   kj_layer.add(imgs_group);
   canvas.add(kj_layer);
 
   //alert("Hi: "+imgObj["cartoons"][i]["image"])
 
   //var img_id = imgObj["cartoons"][i]["img_id"];

   var tmp_imgObj = new Image();


   tmp_imgObj.onload = function() {

     var kj_image = new Kinetic.Image({
       id: sample_name+"_kj_image",
       x: img_x,
       y: img_y,
       image: tmp_imgObj,
       width: img_w,
       height: img_h
     });
   
     imgs_group.add(kj_image)
     kj_layer.add(imgs_group);
     canvas.add(kj_layer);
 
     //fix cache bug
     kj_image.cache();
     kj_image.filters([Kinetic.Filters.RGB]);
     kj_image.red(expr_color[0]).green(expr_color[1]).blue(expr_color[2]);
     // kj_image.red(210).green(34).blue(34);
     kj_image.draw();
 
 
 
     // clicking on gene names (only finds top layer)
    // kj_image.on('mousedown', function() {
    //   alert("sample_id: "+kj_image.getAttr("id"));
    // });
 
   };

   tmp_imgObj.src = img_path+"/expr/cartoons/"+img;

}



// var c = document.getElementById("myCanvas");
// var ctx = c.getContext("2d", { willReadFrequently: true });



//define the canvas
function create_canvas(canvas_h,canvas_w){
  
  var canvas_width = canvas_w;
  var canvas_height = canvas_h;

    //Create canvas stage
  var canvas = new Kinetic.Stage({
    container: "myCanvas",
    width: canvas_width,
    height: canvas_height
  });
  
  return canvas;
}


//display overlapping tissue imgs and group them
//      var tissue_img_group = new Kinetic.Group();


function draw_gene_cartoons(canvas,imgObj,cartoons_all_genes,active_gene){
  
  canvas.clear();
  var kj_layer = new Kinetic.Layer();
  var imgs_group = new Kinetic.Group();


  for (let i = 0; i < imgObj["cartoons"].length; i++) {
  
    var img = imgObj["cartoons"][i]["image"];
    var img_x = imgObj["cartoons"][i]["x"];
    var img_y = imgObj["cartoons"][i]["y"];
    var img_w = imgObj["cartoons"][i]["width"];
    var img_h = imgObj["cartoons"][i]["height"];
    var sample_name = imgObj["cartoons"][i]["sample"];

    //var gene_expr = cartoons_all_genes["Unc93b1"][sample_name];
    var gene_expr = cartoons_all_genes[active_gene][sample_name];
  
  
    load_image(canvas,kj_layer,imgs_group,img,img_x,img_y,img_w,img_h,sample_name,gene_expr)

  }//end for loop

}
