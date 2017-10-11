<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<title>Accommodate - Home</title>
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

<!-- PRELOADER -->
<img id="preloader" src="images/preloader.gif" alt="" />
<!-- //PRELOADER -->
<div class="preloader_hide">

	<!-- PAGE -->
	<div id="page">
	
		<!-- HEADER -->
		<?php
		require_once('self/server/header.php')
	?>
		<!-- //HEADER -->
		
		
		<!-- HOME -->
		<section id="home" class="padbot0">





				
			<!-- TOP SLIDER -->
			<div class="flexslider top_slider">
				<ul class="slides">
					<li class="slide1" style="height: 450px;">
						<div class="flex_caption1">
							<p class="title1 captionDelay2 FromTop">Find</p>
							<p class="title2 captionDelay4 FromTop">and Book</p>
							<p class="title3 captionDelay6 FromTop">a Room</p>
							<p class="title4 captionDelay7 FromBottom">Search through a variety of accessible hostels nearest to your Instituition.</p>
						</div>
						<a class="slide_btn FromRight" href="http://studentaccommod8.com/n/new/register" >Book Now</a>
					</li>
					<li class="slide2" style="height: 450px;">
						<div class="flex_caption1">
							<p class="title1 captionDelay6 FromLeft">Decide</p>
							<p class="title2 captionDelay4 FromLeft">who you</p>
							<p class="title3 captionDelay2 FromLeft">live with</p>
							<p class="title4 captionDelay7 FromLeft">Booking a room comes with the flexibility of selecting your roomate.</p>
						</div>
						<a class="slide_btn FromRight" href="http://studentaccommod8.com/n/new/register" >Book Now</a>
					</li>
					<li class="slide3" style="height: 450px;">
						<div class="flex_caption1">
							<p class="title1 captionDelay1 FromBottom">Connect</p>
							<p class="title2 captionDelay2 FromBottom">Central</p>
							
							<p class="title4 captionDelay5 FromBottom">Our online booking process saves you time and stress.</p>
						</div>
						<a class="slide_btn FromRight" href="http://studentaccommod8.com/n/new/register"  data-toggle="modal" data-target="#form-wizard" >Book Now</a>
						
						<!-- VIDEO BACKGROUND -->
						<a id="P2" class="player" data-property="{videoURL:'tDvBwPzJ7dY',containment:'.top_slider .slide3',autoPlay:true, mute:true, startAt:0, opacity:1}" ></a>
						<!-- //VIDEO BACKGROUND -->
					</li>
				</ul>
			</div>
			<div id="carousel" style="display: none;">
				<ul class="slides">
					<li><img src="images/slider/slide3_bg.jpg" alt="" /></li>
					<li><img src="images/slider/slide2_bg.jpg" alt="" /></li>
					<li><img src="images/slider/slide1_bg.jpg" alt="" /></li>
				</ul>
			</div><!-- //TOP SLIDER -->
			<div id="availability-agileits">
			<div class="col-md-3 book-form-left-w3layouts">
				<h2 style="padding: 53.5px 0; margin: 0px;">FIND A ROOM</h2>
			</div>
			<div class="col-md-9 book-form">
			   <form action="#" method="post" onsubmit="return navigate()" class="row">
					<div class="col-md-4 text-center" style="padding-top: 25px;">
						<p>Preferred Room Type</p>
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
						<p>Preferred Room Location</p>
						<div class="dropdown2">
					    <span class="selLabel2">Select a Location</span>
						    <input type="hidden" name="cd-dropdown">
						    <ul class="dropdown-list2">
						      <li data-value="1">
						        <span>Yaba / Lagos State</span>
						      </li>
						      <li data-value="2">
						        <span>Ago-Iwoye / Ogun</span>
						      </li>
						      <li data-value="3">
						        <span style="color: #0f2453;">L.A.S.U / Lagos State</span>
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
		<!-- /about -->
		<div id="home_about" class="purpose_block">	
			<!-- CONTAINER -->
			<div class="container">	
				<!-- ROW -->
				<div class="row text-center">
					<div class="col-lg-12 col-md-12 col-sm-12 animated fadeInLeft" data-appear-top-offset="-200" data-animated="fadeInLeft">
						<h2><b>Who</b> We Are</h2>
						<p><strong>Student Accommod8</strong> is Nigeria’s first and only student accommodation brand providing purpose built student accommodation (PBSA) to Nigerian and West African tertiary institutions . Our primary aim is providing affordable, decent, and well managed accommodation in a campus style setting.</p>
						<a class="btn btn-active" href="our-story.html" style="padding-top: 15px; padding-bottom: 15px; margin-bottom: 15px; margin-top: 15px; letter-spacing: 4px;"><span data-hover="About Us">Read More</span></a>
					</div>
					
				</div><!-- //ROW -->
			</div><!-- //CONTAINER -->
		</div>
 	<!-- //about -->
		</section><!-- //HOME -->
		
		
		<!-- ABOUT -->
		<section id="about" style="padding-top: 50px;" >
			<div class="banner-bottom" data-appear-top-offset="-200" data-animated="fadeInUp" style="padding-top: 50px;">
				<!-- CONTAINER -->
				<div class="container">
				
					<div class="agileits_banner_bottom">
				<h3 class="title-w3-agileits text-center" style="margin: 0px; color: #c02f26;">How It Works</h3>
				<h4> How to book a room in <strong>(4)</strong> easy steps</h4>
			</div>
			<div class="w3ls_banner_bottom_grids">
				<ul class="cbp-ig-grid">
					
					<li>
						<div class="w3_grid_effect">
							<span class="cbp-ig-icon fa-map-marker"></span>
							<h4 class="cbp-ig-title">PICK A LOCATION</h4>
							<span class="cbp-ig-category">Student Accommod8</span>
						</div>
					</li>
					<li>
						<div class="w3_grid_effect">
							<span class="cbp-ig-icon fa-building-o"></span>
							<h4 class="cbp-ig-title">VIEW THE HOSTEL </h4>
							<span class="cbp-ig-category">Student Accommod8</span>
						</div>
					</li>
					<li>
						<div class="w3_grid_effect">
							<span class="cbp-ig-icon fa-edit"></span>
							<h4 class="cbp-ig-title">BOOK INSTANTLY</h4>
							<span class="cbp-ig-category">Student Accommod8</span>
						</div>
					</li>
					<li>
						<div class="w3_grid_effect">
							<span class="cbp-ig-icon fa-user"></span>
							<h4 class="cbp-ig-title">CREATE AN ACCOUNT</h4>
							<span class="cbp-ig-category">Student Accommod8</span>
						</div>
					</li>
				</ul>
			</div>
			<a href="http://studentaccommod8.com/n/new/register" class="btn btn-active" style="width:300px; margin-top: 30px; color: #ffffff; background-color: #72b962; border-color: #72b962; letter-spacing: 4px;">Get Started Here</a>

				</div><!-- //CONTAINER -->
			</div><!-- //SERVICES -->
			
			<!-- PROJECTS -->
		<section id="projects" class="padbot20" style="padding: 60px 0px;">
		
			<!-- CONTAINER -->
			<div class="container text-center">
				<h3 class="title-w3-agileits title-black-wthree">Available Rooms</h3> 
			<div class="projects-wrapper" data-appear-top-offset="-200" data-animated="fadeInUp">
				<div class="priceing-table-main">
				 <div class="col-md-3 price-grid">
					<div class="price-block agile" style="background-color: #ededed;">
						<div class="price-gd-top*">
						<img src="images/r1.jpg" alt=" " class="img-responsive" style="height: 275px;" />
							<h5 class="text-center" style="margin-top:30px; margin-bottom: 10px; color: #304f82;"><strong>3 man room</strong></h5>
						<h4 class="text-center" style="margin-bottom: 5px; color: #c02f26;"><strong>Pine House</strong><br><h5 class="text-center">Yaba, Lagos.</h5></h4>
						</div>
						<div class="price-gd-bottom" style="padding-top:10px; background-color: #ededed;">
							   <div class="price-select" style="background-color: #ededed;">
								<!--<h3><span style="color: #304f82; font-size: 30px; margin-bottom:10px;">&#8358; </span style="color: #c02f26;">265,000</h3>-->
							</div>
							<div class="price-selet" style="background-color: #ededed;">	
														
								<a href="location.html"  class="scroll" style="background-color: #72b962; border-color: #72b962; color: #ffffff;">Book Now</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 price-grid ">
					<div class="price-block agile" style="background-color: #ededed;">
						<div class="price-gd-top*">
						<img src="images/r2.jpg" alt=" " class="img-responsive" style="height: 275px;"/>
							<h5 class="text-center" style="margin-top:30px; margin-bottom: 10px; color: #304f82;"><strong>2 man room</strong></h5>
						<h4 class="text-center" style="margin-bottom: 5px; color: #c02f26;"><strong>Maple House</strong><br><h5 class="text-center">LASU, Lagos.</h5></h4>
						</div>
						<div class="price-gd-bottom" style="padding-top:10px; background-color: #ededed;">
							   <div class="price-select" style="background-color: #ededed;">
								<!--<h3><span style="color: #304f82; font-size: 30px; margin-bottom:10px;">&#8358; </span style="color: #c02f26;">220,000</h3>-->
							</div>
							<div class="price-selet" style="background-color: #ededed;">	
														
								<a href="maple.html"  class="scroll" style="background-color: #72b962; border-color: #72b962; color: #ffffff;">Book Now</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 price-grid lost">
					<div class="price-block agile" style="background-color: #ededed;">
						<div class="price-gd-top*">
						<img src="images/r3.jpg" alt=" " class="img-responsive" style="height: 275px;"/>
							<h5 class="text-center" style="margin-top:30px; margin-bottom: 10px; color: #304f82;"><strong>2 man Room</strong></h5>
						<h4 class="text-center" style="margin-bottom: 5px; color: #c02f26;"><strong>Pine House</strong><br><h5 class="text-center">Yaba, Lagos.</h5></h4>
						</div>
						<div class="price-gd-bottom" style="padding-top:10px; background-color: #ededed;">
							   <div class="price-select" style="background-color: #ededed;">
								<!--<h3><span style="color: #304f82; font-size: 30px; margin-bottom:10px;">&#8358; </span style="color: #c02f26;">315,000</h3>-->
							</div>
							<div class="price-selet" style="background-color: #ededed;">	
														
								<a href="location.html"  class="scroll" style="background-color: #72b962; border-color: #72b962; color: #ffffff;">Book Now</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 price-grid wthree lost">
					<div class="price-block agile" style="background-color: #ededed;">
						<div class="price-gd-top* ">
							<img src="images/r4.jpg" alt=" " class="img-responsive" style="height: 275px; "/>
						<h5 class="text-center" style="margin-top:30px; margin-bottom: 10px; color: #304f82;"><strong>4 man room</strong></h5>
						<h4 class="text-center" style="margin-bottom: 5px; color: #c02f26;"><strong>Sycamore House</strong><br><h5 class="text-center">Ago-Iwoye, Ogun State.</h5></h4>
						</div>
						<div class="price-gd-bottom" style="padding-top:10px; background-color: #ededed;">
							   <div class="price-select" style="background-color: #ededed;">
								<!--<h3><span style="color: #304f82; font-size: 30px; margin-bottom:10px;">&#8358; </span style="color: #c02f26;"></h3>-->
							</div>
							<div class="price-selet" style="background-color: #ededed;">	
														
								<a href="sycamore.html" style="background-color: #72b962; border-color: #72b962; color: #ffffff;">Book Now</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<a class="btn btn-active" href="our-location.html" style="width:300px; margin-top: 30px; color: #ffffff; background-color: #72b962; border-color: #72b962; letter-spacing: 4px;">View More</a>
			</div><!-- //CONTAINER -->
		</section><!-- //PROJECTS -->
			
			<!-- CLEAN CODE -->
			<div class="cleancode_block">
	
				<!-- CONTAINER -->
				<div class="container" data-appear-top-offset="-200" data-animated="fadeInUp">
					<p class="title"><b>WHY</b> CHOOOSE US</p>
					<div class="row">
				      <div class="col-md-3">
				        <div class="box">
				          <div class="box-icon"> <span class="fa fa-4x fa-shield"></span> </div>
				          <div class="info">
				            <h4 class="text-center">24/7 <br> Security</h4>
				            <p>The safety of our Residents is our utmost priority. All our residences have security personnel on site 24/7.</p>
				          </div>
				        </div>
				      </div>
				      <div class="col-md-3">
				        <div class="box">
				          <div class="box-icon"> <span class="fa fa-4x fa-thumbs-up"></span> </div>
				          <div class="info">
				            <h4 class="text-center">Top-notch <br> facility</h4>
				            <p>Each hostel comes equipped with all the necessary facilities every student needs to feel at home away from home.</p>
				          </div>
				        </div>
				      </div>
				      <div class="col-md-3">
				        <div class="box">
				          <div class="box-icon"> <span class="fa fa-4x fa-desktop"></span> </div>
				          <div class="info">
				            <h4 class="text-center">User-friendly <br> Portal</h4>
				            <p>Connect Central was created to make it easy for you to manage your booking process and have access to a variety of tools and information about your accommodation.</p>
				          </div>
				        </div>
				      </div>
				      <div class="col-md-3">
				        <div class="box">
				          <div class="box-icon"> <span class="fa fa-4x fa-user-md"></span> </div>
				          <div class="info">
				            <h4 class="text-center">Efficient <br> Management</h4>
				            <p>Our Student Accommod8 team is made up of talented individuals with experience from a wide range of industry sectors.</p>
				          </div>
				        </div>
				      </div>
				    </div>
				</div>
			</div><!-- //CLEAN CODE -->
			
		</section><!-- //ABOUT -->
		

		<!-- TESTIMONIALS -->
		<section id="testimonials" class="padbot0">
			<!-- visitors -->
			<div class="w3l-visitors-agile" >
				<div class="container">
		            <h3 class="title-w3-agileits title-black-wthree" style="margin-bottom: 5px;">Testimonials</h3> 
				</div>
				<div class="w3layouts_work_grids">
					<div class="slider">
						<div class="flexslider">
							<ul class="slides">
								<li>
									<div class="w3layouts_work_grid_right">
										<h4>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											
										</h4>
										<p>My short time at Pine house has been an awesome experience. Security and comfort of residents is the managements top priority here. </p>
										<h5>Chris</h5>
										<p>Nigeria</p>
									</div>
									<div class="clearfix"> </div>
								</li>
								<li>
									<div class="w3layouts_work_grid_right">
										<h4>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star-o" aria-hidden="true"></i>
											
										</h4>
										<p>My experience so far has been very good due to the privacy and organized setting of the location of the hostel. The location is very cool as well as the services as well as friendliness of other residents. The space and the rooms are all very neat and not crowded. The kitchen too is very large which is very convenient for us. Would I like to stay in pine House again? Yessssss!!</p>
										<h5>Ronke</h5>
										<p>Nigeria</p>
									</div>
									<div class="clearfix"> </div>
								</li>
								<li>
									<div class="w3layouts_work_grid_right">
										<h4>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star-o" aria-hidden="true"></i>
											
										</h4>
										<p>It has really been a nice experience living in pine house. Pine House is a nice place to live, work and study with no worries. It really feels like home away from home. But more need to be done to improve the experience and also to keep up the standard.</p>
										<h5>Emmanuel</h5>
										<p>Nigeria</p>
									</div>
									<div class="clearfix"> </div>
								</li>
								<li>
									<div class="w3layouts_work_grid_right">
										<h4>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star" aria-hidden="true"></i>
											<i class="fa fa-star-o" aria-hidden="true"></i>
											<i class="fa fa-star-o" aria-hidden="true"></i>
											
										</h4>
										<p>My stay has been awesome! Beautiful environment. very conducive for studying. All our needs have been adequately provided. It's been a wonderful experience so far.</p>
										<h5>Teniola</h5>
										<p>Nigeria</p>
									</div>
									<div class="clearfix"> </div>
								</li>
							</ul>
						</div>
					</div>
				</div>	
			</div>
		  <!-- visitors -->
		</section><!-- //TESTIMONIALS -->
		

		<!-- BLOG -->
		<section id="blog" class="padbot0">
			<div class="team">
				<div class="container">
						<h3 class="title-w3-agileits " style="color: #72b962;">Blog Posts</h3>
						<div id="horizontalTab">
							<div class="resp-tabs-container text-center">
							<div class="col-md-6 ">
								<div class="tab1">
										<div class="col-md-6 " style="height: 140px;">
										  <img src="images/be.png" style="height: 140px;">
										</div>
										<div class="col-md-6 team-Info-agileits" style="padding-left: 20px;">
										  <h4>Be Yourself!</h4>
										<div class="post-meta text-uppercase">
										<span>July 5, 2017</span>
										<span>In <a href="">blog</a></span>
										<span>By <a href="">Admin</a></span>
																	</div>
																				
										<div class="social-bnr-agileits footer-icons-agileinfo">
											<div class="price-selet">					
											<a href="blog.html" class="scroll">READ MORE</a>
											</div>
										</div>							
									</div>
									<div class="clearfix"> </div>
								</div>
								</div>
								<div class="col-md-6 ">
								<div class="tab2">
										<div class="col-md-6 " style="height: 140px;">
										  <img src="images/knock.jpg" style="height: 140px;">
										</div>
										<div class="col-md-6 team-Info-agileits" style="padding-left: 20px;">
										  <h4>Go Knocking.</h4>
										<div class="post-meta text-uppercase">
										<span>July 5, 2017</span>
										<span>In <a href="">blog</a></span>
										<span>By <a href="">Admin</a></span>
																	</div>
																				
										<div class="social-bnr-agileits footer-icons-agileinfo">
											<div class="price-selet">					
											<a href="blog.html" class="scroll">READ MORE</a>
											</div>
										</div>							
									</div>
									<div class="clearfix"> </div>
								</div>
								</div>
								<div class="col-md-6" style="margin-top: 10px">
								<div class="tab3">
										<div class="col-md-6 " style="height: 140px;">
										  <img src="images/hangout.jpg" style="height: 140px;">
										</div>
										<div class="col-md-6 team-Info-agileits" style="padding-left: 20px;">
										  <h4>Hang Out In Your Common Room.</h4>
										<div class="post-meta text-uppercase">
										<span>July 5, 2017</span>
										<span>In <a href="">blog</a></span>
										<span>By <a href="">Admin</a></span>
																	</div>
																				
										<div class="social-bnr-agileits footer-icons-agileinfo">
											<div class="price-selet">					
											<a href="blog.html" class="scroll">READ MORE</a>
											</div>
										</div>							
									</div>
									<div class="clearfix"> </div>
								</div>
								</div>
								<div class="col-md-6 " style="margin-top: 10px">
								<div class="tab4">
										<div class="col-md-6 " style="height: 140px;">
										  <img src="images/events.jpg" style="height: 140px;">
										</div>
										<div class="col-md-6 team-Info-agileits" style="padding-left: 20px;">
										  <h4>Sign Up For Events And Activities.</h4>
										<div class="post-meta text-uppercase">
										<span>July 5, 2017</span>
										<span>In <a href="">blog</a></span>
										<span>By <a href="">Admin</a></span>
																	</div>
																				
										<div class="social-bnr-agileits footer-icons-agileinfo">
											<div class="price-selet">					
											<a href="blog.html" class="scroll">READ MORE</a>
											</div>
										</div>							
									</div>
									<div class="clearfix"> </div>
								</div>
								</div>
								<a href="blog.html" class="btn btn-active" style="width:300px; margin-top: 30px; color: #ffffff; background-color: #72b962; border-color: #72b962; letter-spacing: 4px;">View More</a>
							</div>
						</div>
				</div>
			</div>
		</section><!-- //BLOG -->
	</div><!-- //PAGE -->
	
	<!-- CONTACTS -->
	<section id="contacts">
	</section><!-- //CONTACTS -->
	
	<!-- FOOTER -->
	<?php
		require_once('self/server/footer.php')
	?><!-- //FOOTER -->
	

	<!-- //Modal -->
	

	

</div>
<?php
		require_once('self/server/modals.php')
	?>
<script type='text/javascript'>
    function navigate(){
        var n = $('.selLabel2').html();
        var url = "our-location.html"
        if (n.trim() == "Yaba / Lagos State") url = "location.html";
        if (n.trim() == "L.A.S.U / Lagos State") url = "maple.html";
        if (n.trim() == "Ago-Iwoye / Ogun") url = "sycamore.html";
        location.replace(url);
        return false;
    }
</script>
</body>
</html>