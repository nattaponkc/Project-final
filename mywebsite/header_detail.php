<?php 
    //เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
    require_once 'config/condb.php';
	 //insert counter
				$insertCounter = $condb->prepare("INSERT INTO tbl_counter () VALUES ()");             
				$insertCounter->execute();
if(isset($_GET['id'])){

//คิวรี่รายละเอียดสินค้า single row
$stmtProductDetail = $condb->prepare("SELECT * FROM tbl_product WHERE id=:id");
// bindParam
$stmtProductDetail->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$stmtProductDetail->execute();
$rowProduct = $stmtProductDetail->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>';
        // print_r($row);
        //exit;
        //echo $stmtProductDetail->rowCount(); 
        //exit;
  //สร้างเงื่อนไขตรวจสอบการคิวรี่

  if($stmtProductDetail->rowCount() == 0){ //คิวรี่ผิดพลาด
	echo '<!-- sweet alert -->
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
             echo '<script>
                            setTimeout(function() {
                            swal({
                                title: "เกิดข้อผิดพลาด",
                                type: "error"
                            }, function() {
                                window.location = "index.php"; //หน้าที่ต้องการให้กระโดดไป
                            });
                            }, 1000);
                        </script>';
            exit;
     }
	 //update product view
                $updatePview = $condb->prepare("UPDATE tbl_product SET product_view = product_view + 1 WHERE id=:id");
                //bindParam
                $updatePview->bindParam(':id', $_GET['id'] , PDO::PARAM_INT);
                $updatePview->execute();
//คิวรี่ภาพประกอบสินค้า
$queryproductImg = $condb->prepare("SELECT * FROM tbl_product_image WHERE ref_p_id=:id");
// bindParam
$queryproductImg->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$queryproductImg->execute();
$rsImg = $queryproductImg->fetchAll();

} // close if isset
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$rowProduct['product_name'];?> ราคา <?=$rowProduct['price_hot,price_cold,price_frappe'];?> บาท </title>

	<meta content="<?=$rowProduct['product_name'];?>" name="keywords">
    <meta content="<?=$rowProduct['product_name'];?>" name="description">
    <meta property="og:site_name" content=" <?=$rowProduct['product_name'];?>">
    <meta property="og:title" content=" <?=$rowProduct['product_name'];?> " />
    <meta property="og:description" content="<?=$rowProduct['product_name'];?>" />
    <meta property="og:image" itemprop="image" content="assets/product_img/<?=$rowProduct['product_image'];?>">
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?=$rowProduct['product_name'];?>" />
    <meta name="twitter:description" content="<?=$rowProduct['product_name'];?>" />
    <meta name="twitter:image" content="assets/product_img/<?=$rowProduct['product_image'];?>">

    <link href="assets/bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">

    

    <!-- fancybox -->
    <!-- Add jQuery library -->
	<script type="text/javascript" src="assets/fancybox2.1.5/lib/jquery-1.10.2.min.js"></script>

	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="assets/fancybox2.1.5/lib/jquery.mousewheel.pack.js?v=3.1.3"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="assets/fancybox2.1.5/source/jquery.fancybox.pack.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="assets/fancybox2.1.5/source/jquery.fancybox.css?v=2.1.5" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="assets/fancybox2.1.5/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
	<script type="text/javascript" src="assets/fancybox2.1.5/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="assets/fancybox2.1.5/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
	<script type="text/javascript" src="assets/fancybox2.1.5/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="assets/fancybox2.1.5/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			/*
			 *  Simple image gallery. Uses default settings
			 */

			$('.fancybox').fancybox();

			/*
			 *  Different effects
			 */

			// Change title type, overlay closing speed
			$(".fancybox-effects-a").fancybox({
				helpers: {
					title : {
						type : 'outside'
					},
					overlay : {
						speedOut : 0
					}
				}
			});

			// Disable opening and closing animations, change title type
			$(".fancybox-effects-b").fancybox({
				openEffect  : 'none',
				closeEffect	: 'none',

				helpers : {
					title : {
						type : 'over'
					}
				}
			});

			// Set custom style, close if clicked, change title type and overlay color
			$(".fancybox-effects-c").fancybox({
				wrapCSS    : 'fancybox-custom',
				closeClick : true,

				openEffect : 'none',

				helpers : {
					title : {
						type : 'inside'
					},
					overlay : {
						css : {
							'background' : 'rgba(238,238,238,0.85)'
						}
					}
				}
			});

			// Remove padding, set opening and closing animations, close if clicked and disable overlay
			$(".fancybox-effects-d").fancybox({
				padding: 0,

				openEffect : 'elastic',
				openSpeed  : 150,

				closeEffect : 'elastic',
				closeSpeed  : 150,

				closeClick : true,

				helpers : {
					overlay : null
				}
			});

			/*
			 *  Button helper. Disable animations, hide close button, change title type and content
			 */

			$('.fancybox-buttons').fancybox({
				openEffect  : 'none',
				closeEffect : 'none',

				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,

				helpers : {
					title : {
						type : 'inside'
					},
					buttons	: {}
				},

				afterLoad : function() {
					this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
				}
			});


			/*
			 *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
			 */

			$('.fancybox-thumbs').fancybox({
				prevEffect : 'none',
				nextEffect : 'none',

				closeBtn  : false,
				arrows    : false,
				nextClick : true,

				helpers : {
					thumbs : {
						width  : 50,
						height : 50
					}
				}
			});

			/*
			 *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
			*/
			$('.fancybox-media')
				.attr('rel', 'media-gallery')
				.fancybox({
					openEffect : 'none',
					closeEffect : 'none',
					prevEffect : 'none',
					nextEffect : 'none',

					arrows : false,
					helpers : {
						media : {},
						buttons : {}
					}
				});

			/*
			 *  Open manually
			 */

			$("#fancybox-manual-a").click(function() {
				$.fancybox.open('1_b.jpg');
			});

			$("#fancybox-manual-b").click(function() {
				$.fancybox.open({
					href : 'iframe.html',
					type : 'iframe',
					padding : 5
				});
			});

			$("#fancybox-manual-c").click(function() {
				$.fancybox.open([
					{
						href : '1_b.jpg',
						title : 'My title'
					}, {
						href : '2_b.jpg',
						title : '2nd title'
					}, {
						href : '3_b.jpg'
					}
				], {
					helpers : {
						thumbs : {
							width: 75,
							height: 50
						}
					}
				});
			});


		});
	</script>
	<style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}

		/* body {
			max-width: 700px;
			margin: 0 auto;
		} */

	</style>

  </head>
<body>