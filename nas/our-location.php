<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<title>Accommodate - Our Locations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<link rel="shortcut icon" href="images/favicon.png">
    
	<!-- CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/flexslider.css" rel="stylesheet" type="text/css" />
	<link href="css/prettyPhoto.css" rel="stylesheet" type="text/css" />
	<link href="css/animate.css" rel="stylesheet" type="text/css" media="all" />
    <link href="css/owl.carousel.css" rel="stylesheet">
    <link href="css/wizard.css" rel="stylesheet">
    <link href="css2/style.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/style.css" rel="stylesheet" type="text/css" />
    
	<!-- FONTS -->
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500italic,700,500,700italic,900,900italic' rel='stylesheet' type='text/css'>
	<link href="font-awesome/css/font-awesome.css" rel="stylesheet">	
    
	<!-- SCRIPTS -->
	<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!--[if IE]><html class="ie" lang="en"> <![endif]-->
	
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/jquery.prettyPhoto.js" type="text/javascript"></script>
	<script src="js/jquery.nicescroll.min.js" type="text/javascript"></script>
	<script src="js/superfish.min.js" type="text/javascript"></script>
	<script src="js/jquery.flexslider-min.js" type="text/javascript"></script>
	<script src="js/owl.carousel.js" type="text/javascript"></script>
	<script src="js/animate.js" type="text/javascript"></script>
	<script src="js/jquery.BlackAndWhite.js"></script>
	<script src="js/myscript.js" type="text/javascript"></script>
	<script>		
		//PrettyPhoto
		jQuery(document).ready(function() {
			$("a[rel^='prettyPhoto']").prettyPhoto();

			$(".selLabel").click(function () {
			    $('.dropdown').toggleClass('active');
			});
			  
			$(".dropdown-list li").click(function() {
			    $('.selLabel').text($(this).text());
			    $('.dropdown').removeClass('active');
			});

			$(".selLabel2").click(function () {
			    $('.dropdown2').toggleClass('active');
			});
			  
			$(".dropdown-list2 li").click(function() {
			    $('.selLabel2').text($(this).text());
			    $('.dropdown2').removeClass('active');
			});
		});
	</script>
</head>
<body>

<div>

	<!-- PAGE -->
	<div id="page">
	
		<!-- HEADER -->
		<?php
		require_once('self/server/header.php')
	?><!-- //HEADER -->
		
		
		<!-- HOME -->
		<section id="home" class="w3ls-banner">
			<div class="slider">
				<div class="callbacks_container">
					<ul class="rslides callbacks callbacks1" id="slider4">
						<li>
							<div class="w3layouts-banner-top">
								<div class="container">
									<div class="agileits-banner-info">
										<h4>OUR LOCATIONS</h4>
									</div>	
								</div>
							</div>
						</li>
						<li>
							<div class="w3layouts-banner-top w3layouts-banner-top1">
								<div class="container">
									<div class="agileits-banner-info">
										<h4>OUR LOCATIONS</h4>
									</div>	
								</div>
							</div>
						</li>
						<li>
							<div class="w3layouts-banner-top w3layouts-banner-top2">
								<div class="container">
									<div class="agileits-banner-info">
										<h4>OUR LOCATIONS</h4>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="clearfix"> </div>
				<!--banner Slider starts Here-->
			</div>
		</section><!-- //HOME -->

		<!--sevices-->
		
		<div class="advantages" style="background-color: #fff;">
			<div class="container">
				<div class="advantages-main">
						<!--<h3 class="title-w3-agileits" style="color: #0f2453;">Our Locations</h3>-->
						<p class="about-para-w3ls" style="text-align: center;">For leasing and other information, give us a call on: 01 454 5951 OR 0806 067 1077. Alternatively, send an email to: hello@studentaccommod8.com and a member of the Student Accommod8 team will be in touch.</p>
				   
		<div id="availability-agileits">
			<div class="col-md-3 book-form-left-w3layouts">
				<h2 style="padding: 53.5px 0; margin: 0px;">FIND A ROOM</h2>
			</div>
			<div class="col-md-9 book-form">
			   <form action="#" method="post" class="row">
					<div class="col-md-4 text-center" style="padding-top: 25px;">
						<p>Prefered Room Type</p>
						<div class="dropdown">
					    <span class="selLabel">Select a Room</span>
						    <input type="hidden" name="cd-dropdown">
						    <ul class="dropdown-list">
						      <li data-value="1">
						        <span>One-man Room</span>
						      </li>
						      <li data-value="2">
						        <span>Two-man Room</span>
						      </li>
						      <li data-value="3">
						        <span style="color: #0f2453;">Three-man Room</span>
						      </li>
						      <li data-value="4">
						        <span>Four-man Room</span>
						      </li>
						    </ul>
					  	</div>
					</div>
					<div class="col-md-4 text-center" style="padding-top: 25px;">
						<p>Prefered Room Location</p>
						<div class="dropdown2">
					    <span class="selLabel2">Select a Location</span>
						    <input type="hidden" name="cd-dropdown">
						    <ul class="dropdown-list2">
						      <li data-value="1">
						        <span>Yaba / Lagos</span>
						      </li>
						      <li data-value="2">
						        <span>Ago-Iwoye / Ogun</span>
						      </li>
						      <li data-value="3">
						        <span style="color: #0f2453;">LASU / Lagos</span>
						      </li>
						      </ul>
					  	</div>
					</div>
					<div class="col-md-4 text-center">
						  <input type="submit" value="Check Availability" onclick="navigate()">
					</div>
				</form>
			</div>
    
                       
			<div class="clearfix"> </div>
		</div>
				   
				   <div class="advantage-bottom" style="margin-top: 50px;">
					 <div class="col-md-6 left-w3ls wow bounceInLeft" data-wow-delay="0.3s">
					 	<a href="location.html">
					 		<div class="advantage-block ">
								<i class="fa fa-map-marker" aria-hidden="true"></i>
						 		<h4>Pine House, Yaba / Lagos State</h4>
						 		<p>Each room comes fully furnished with the following facilities. Click to view our branch in Yaba.</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>Single Unit and Single bed</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>Fitted Wardrobes</p>
								<p><i class="fa fa-check" aria-hidden="true"></I>Air Condition</p>
								
								<p><a href="location.html" class="btn btn-readmore" style="display: block; margin: auto; width: 80%;">View More</a></p>
						 	</div>
					 	</a>
					 </div>
					 <div class="col-md-6 right-w3ls wow zoomIn" data-wow-delay="0.3s">
					 	<a href="sycamore.html">
					 		<div class="advantage-block">
								<i class="fa fa-map-marker" aria-hidden="true"></i>
						 		<h4>Sycamore House, Ago-Iwoye / Ogun State</h4>
						 		<p>Each room comes fully furnished with the following facilities. Click to view our branch in OOU, Ago Iwoye.</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>Bunk beds</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>Fitted Wardrobes</p>
								<p><i class="fa fa-check" aria-hidden="true"></I>Wall Fans</p>
								
								<p><a href="sycamore.html" class="btn btn-readmore" style="display: block; margin: auto; width: 80%;">View More</a></p>
						 	</div>
					 	</a>
					 </div>

<div class="col-md-6 right-w3ls wow zoomIn" data-wow-delay="0.3s" style="padding-top: 30px;">
					 	<a href="maple.html">
					 		<div class="advantage-block">
								<i class="fa fa-map-marker" aria-hidden="true"></i>
						 		<h4>Maple / Lagos State</h4>
						 		<p>Each room comes fully furnished with the following facilities. Click to view our branch in LASU,Lagos State.</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>Bunk beds</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>Fitted Wardrobes</p>
								<p><i class="fa fa-check" aria-hidden="true"></i>En-suite shower and toilet</p>
                                                                <p><i class="fa fa-check" aria-hidden="true"></I>Wall Fans</p>
								
								<p><a href="maple.html" class="btn btn-readmore" style="display: block; margin: auto; width: 80%;">View More</a></p>
						 	</div>
					 	</a>
					 </div>
					<div class="clearfix"> </div> 
					
					
					
					
					</div>
				</div>
			</div>
		</div>
		<!--//sevices-->

	</div><!-- //PAGE -->
	
	<!-- CONTACTS -->
	<section id="contacts">
	</section><!-- //CONTACTS -->
	
	<!-- FOOTER -->
<?php
		require_once('self/server/footer.php')
	?><!-- //FOOTER -->
	
	
	<!-- //Modal -->
	
	<!-- //Modal -->

</div>
<?php
		require_once('self/server/modals.php')
	?>
<script src="js2/responsiveslides.min.js"></script>
<script>
			// You can also use "$(window).load(function() {"
			$(function () {
			  // Slideshow 4
			  $("#slider4").responsiveSlides({
				auto: true,
				pager:false,
				nav:false,
				speed: 500,
				namespace: "callbacks",
				before: function () {
				  $('.events').append("<li>before event fired.</li>");
				},
				after: function () {
				  $('.events').append("<li>after event fired.</li>");
				}
			  });
		
			});
</script>
<script type='text/javascript'>
    function navigate(){
        var n = $('.selLabel2').html();
        var url = "our-location.html"
        if (n.trim() == "Yaba / Lagos State") url = "location.html";
        if (n.trim() == "LASU / Lagos State") url = "maple.html";
        if (n.trim() == "Ago-Iwoye / Ogun State") url = "sycamore.html";
        location.replace(url);
        return false;
    }
</script>
</body>
</html>