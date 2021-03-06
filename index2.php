<!DOCTYPE html>

<HTML lang="en">

<head>

  <!-- META TAGS -->

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320--> 	<!-- prevents users from zooming in --> 

	<meta name="author" content="Jose Manriquez">

	<meta name = "description" content="This web app is an application designed for BBBS to find places and events for their matches.">

	<meta name="keywords" content="BBBS, hack,hackathon, hack4austin, big, brothers, sisters, kids, places, locations, events, cheap, free, place, food, fun, children, child">

	<base href="/">

	

	

	<title>BBBS: Events For Kids</title>



  <!-- STYLES -->

	<link rel="icon" href="//www.bbbs.org/atf/cf/%7B8778d05c-7ccb-4dee-9d6e-70f27c016cc9%7D/ANIMATED_FAVICON.GIF" type="image/x-icon"><link rel="shortcut icon" href="//www.bbbs.org/atf/cf/%7B8778d05c-7ccb-4dee-9d6e-70f27c016cc9%7D/favicon.ico" type="image/x-icon">

	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"> <!--Bootstrap -->

	<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet"> <!-- Awesome Icons -->

    <link rel="stylesheet" href="https://js.arcgis.com/3.9/js/esri/css/esri.css"> <!-- ESRI maps -->

	<link rel="stylesheet" href="css/loading.css" type="text/css"> <!-- Loading bar -->

		<!--[if lte IE 7]><link rel="css/old-centering.css /><![endif]-->

	<link href="css/flat-ui.css" rel="stylesheet">

		

	

	<link href="/css/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">	

	<link rel="stylesheet" href="css/main-style.css"> <!-- Custom CSS styles-->



		

	

	<style>

		.small-menu {display: none !important;}

		.large-menu {display: none! important;}

		

		/* iphone */

		@media only screen and (min-device-width : 320px) and (max-device-width : 480px) {

			img { max-width: 100%; }

			.app-title {font-size: 27px;}

			

		}



		/* ipad */

		@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {

			img { max-width: 100%; }

			

		}

		

      html, body, #mainWindow { 

        height: 100%; width: 100%;  

        margin: 0; padding: 0;

		background: #eee;

      } 

	  

	  h1{color: #7AC142;}

	  #mainWindow { margin: 0 !important; height: auto;}

      #map{ 

        padding:0px;

        border:solid 1px #343642;

        margin:0px auto; 

		overflow-x: hidden !important ;

		height: 100%;

		width: 100% ;

		

      }

		

	  

	  .rows, .col { overflow: hidden; position: absolute; }

    .rows { left: 0; right: 0; }

    .col { top: 0; bottom: 0; }

    .scroll-x { overflow-x: hidden !important; }

    .scroll-y { overflow-y: auto; }



	</style>

	

	 

	

</head>

<body class="claro" onload="getLocation()">

	

	<!-- LOADING SCREEN -->



	<div class="outer" id="splashscreen">

		<div class="middle">

			<div class="inner">

				<img src="images/bbbs-logo.png" alt="BBBS Logo" style="padding: 10px 0px 10px 0px;" />

				<div class="row app-name" >

					Events for Kids

				</div>

				

				<p id="loading-text">

					Looking for Events<br>

					Please Wait

				</p>	

				

				<div style="height: 32px; width: 32px; margin: 0 auto;">

				<i class="fa fa-refresh fa-spin fa-lg fa-2x" style="color:#fff; padding-top: 5px;"></i> 

				</div>

				<!-- option 2 <div class="loader"><span></span><span></span><span></span></div> <!--loading dots 

					 option 3 <div class="wobblebar" style="width: 100%;"> 	Loading... 	</div>

				-->

			</div>

		</div>

	</div>

	

	

	

	

	

	

	<!-- NAV BAR FOR BIG SCREENS!!! -->

    <div id="large-menu" class="navbar navbar-inverse navbar-fixed-top hideMenu" role="navigation">

      <div class="container">

        <div class="navbar-header">

          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

            <span class="sr-only">Toggle navigation</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

          </button>

          <a class="navbar-brand" href="//bigmentoring.com/"><img src="images/logo.png" height="35x;" style="margin-top:-10px;"/></a>

        </div>

        <div class="navbar-collapse collapse">

          <ul class="nav navbar-nav">

			<li><a href="pages/admin.html">Admin</a></li>

			<li><a href="admin.html">Add Event</a></li>

            <li class="dropdown">

              <a href="#" class="dropdown-toggle" data-toggle="dropdown">About Us <b class="caret"></b></a>

              <span class="dropdown-arrow"></span>

              <ul class="dropdown-menu" style="overflow: hidden;">

                <li><a href="#">Big Brothers Big Sisters</a></li>

                <li><a href="#">BBBS Central Texas</a></li>

                <li><a href="#">The Web Dev Team</a></li>

                <li class="divider"></li>

                <li><a href="#">Support Us!</a></li>

              </ul>

            </li>

          </ul>

          <ul class="nav navbar-nav navbar-right">

			<li><a href="#" onclick="getLocation();">Find Me</a></li>

            <li> 

				<form class="navbar-form navbar-right" action="#" role="search">

			      <div class="form-group">

			        <div class="input-group">

			          <input class="form-control"  id="address" type="search" placeholder="Search Address">

			          <span class="input-group-btn"> 					  

			            <button id="locate"  type="button"  class="searchbtn" onclick="locate();" > Hey!</button>

			          </span>            

			        </div>

			      </div>               

			    </form>

			</li>

          </ul>

        </div><!--/.nav-collapse -->

      </div>

    </div>

	

	

	

	

	

	

	<div id="mainWindow" data-dojo-type="dijit/layout/BorderContainer" 

		data-dojo-props="design:'sidebar', gutters:false" 

		class="body rows scroll-y scroll-x visible-md visible-lg" style="height:100%;width: 100%;">



		

		<div id="map" 

		   data-dojo-type="dijit/layout/ContentPane" 

		   data-dojo-props="region:'center'" >

		</div>

	</div>

	

	



	<div id="small-menu" style = "display: none;" >

		

		

			<div class="col-xs-12" style =" z-index: 10; position: relative; padding: 2px 2px 0px 2px;  ">

          <nav class="navbar navbar-inverse navbar-embossed " role="navigation">

            <div class="navbar-header">

              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">

                <span class="sr-only">Toggle navigation</span>

              </button>

			  <a class="navbar-brand" href="#"><img src="images/logo.png" alt="logo" height="35px;" style="margin-top:-10px;"/></a>

			

            </div>

            <div class="collapse navbar-collapse" id="navbar-collapse-01">

              <ul class="nav navbar-nav navbar-left" style="overflow: hidden;">           

                <li><a href="pages/admin.html">Admin</a></li>

                <li><a href="pages/comingSoon.html">Map</a></li>

				<li class="dropdown">

                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">About Us <b class="caret"></b></a>

                  <span class="dropdown-arrow"></span>

                  <ul class="dropdown-menu" style="overflow: hidden;">

                    <li><a href="//www.bbbs.org">Big Brothers Big Sisters</a></li>

                    <li><a href="//bigmentoring.org">BBBS Central Texas</a></li>

                    <li><a href="pages/comingSoon.html">The Web Dev Team</a></li>

                    <li class="divider"></li>

                    <li><a href="//github.com/Hack4Austin2014/BBBS.Alfa">Support Us!</a></li>

                  </ul>

                </li>

               </ul>

            </div><!-- /.navbar-collapse -->

          </nav><!-- /navbar -->

        </div>

	

	

	

		<!-- /NAV BAR FOR MOBILE DEVICES SCREENS!!! -->

		

	

	

	

	

	

	

	

	

			

		

	  <div style="background: #EDEFF1; min-height:1000px; width:100%; padding:2px; ">

		<div  style="width:100%;  ">

			<div class="col-xs-12 app-title" style =" z-index: 1; position: relative;">

				Events for Kids

			</div>		

			

				

				

			<div class = "col-xs-12" id="accordion" style =" z-index: 11; position: relative; height: 100%; width:100%; padding-top: 10px;" >

					

			</div>

				

				

			

		</div> <!-- row -->

		

	  </div>	

	</div> <!-- small visibility container-->



  <!-- SCRIPTS -->	

	<!-- JS FILES-->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script><!-- jquery -->

    <script src="//js.arcgis.com/3.9/"></script>	

	<script src='//cdn.firebase.com/js/client/1.0.15/firebase.js'></script><!--firebase -->

	<script src='//cdn.firebase.com/js/simple-login/1.4.1/firebase-simple-login.js'></script>

	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> <!-- bootstrap -->

	<script src="js/config.js"></script>

	<script src="js/mapping.js"></script>

	<script src="js/jquery-ui-1.10.4.custom.min.js"></script>

	<!-- 

    <script src="js/flatui-checkbox.js"></script>

    <script src="js/flatui-radio.js"></script>

    <script src="js/jquery.tagsinput.js"></script>

    <script src="js/jquery.placeholder.js"></script>

    <script src="js/typeahead.js"></script>

	-->

	

	<script>

		$(function() {

			$( '#accordion' ).accordion(

				{event: "click hoverintent"},

				{heightStyle: "content"},

				{collapsible: true}

			);

			

		});



		var acc; 	

		function getLocation()

		{

			if (navigator.geolocation)

			{

				navigator.geolocation.getCurrentPosition(showPosition);

				

				//window.setTimeout("redirect()",2000);

			}

			else{x.innerHTML="Geolocation is not supported by this browser.";}

		}  

		

		function showPosition(position){

			ZoomLocation(position.coords.longitude, position.coords.latitude); 

		}

		

		function redirect(){

			$('#splashscreen').fadeOut('slow', function()

			{

				jQuery('#small-menu').addClass('visible-xs');

				jQuery('#large-menu').addClass('visible-md visible-lg showMenu');

				jQuery('#large-menu').removeClass('hideMenu');

				

				

				

				

			});

		}

		/*

			$(document).ready(function() {

				$('#sidebar').portamento({gap: 100});	// set a 100px gap rather than the default 10px

			});	

		*/



/*

* hoverIntent | Copyright 2011 Brian Cherne

* http://cherne.net/brian/resources/jquery.hoverIntent.html

* modified by the jQuery UI team

*/

$.event.special.hoverintent = {

setup: function() {

$( this ).bind( "mouseover", jQuery.event.special.hoverintent.handler );

},

teardown: function() {

$( this ).unbind( "mouseover", jQuery.event.special.hoverintent.handler );

},

handler: function( event ) {

var currentX, currentY, timeout,

args = arguments,

target = $( event.target ),

previousX = event.pageX,

previousY = event.pageY;

function track( event ) {

currentX = event.pageX;

currentY = event.pageY;

};

function clear() {

target

.unbind( "mousemove", track )

.unbind( "mouseout", clear );

clearTimeout( timeout );

}

function handler() {

var prop,

orig = event;

if ( ( Math.abs( previousX - currentX ) +

Math.abs( previousY - currentY ) ) < 7 ) {

clear();

event = $.Event( "hoverintent" );

for ( prop in orig ) {

if ( !( prop in event ) ) {

event[ prop ] = orig[ prop ];

}

}

// Prevent accessing the original event since the new event

// is fired asynchronously and the old event is no longer

// usable (#6028)

delete event.originalEvent;

target.trigger( event );

} else {

previousX = currentX;

previousY = currentY;

timeout = setTimeout( handler, 100 );

}

}

timeout = setTimeout( handler, 100 );

target.bind({

mousemove: track,

mouseout: clear

});

}

};

</script>





		



</body>

</html>