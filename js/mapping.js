var gMap;
var gLocator;
var gServiceAreaTask;
var gAddressGraphic;
var gAreaGraphic;
var gLayer;
var gIdLookup = {};

require(
	[
		"esri/map", "esri/config",
		"esri/layers/ArcGISDynamicMapServiceLayer",
		"esri/SpatialReference",
		"esri/layers/FeatureLayer",
		"esri/tasks/locator",
		"esri/graphic",
		"esri/InfoTemplate",
		"esri/symbols/SimpleMarkerSymbol",
		"esri/symbols/Font",
		"esri/symbols/TextSymbol",
		"esri/layers/GraphicsLayer",
		"dojo/_base/array",
		"esri/Color",
		"esri/tasks/GeometryService",
		"esri/tasks/BufferParameters",
		"esri/tasks/IdentifyTask",
		"esri/tasks/IdentifyParameters",
		"esri/symbols/SimpleFillSymbol",
		"esri/symbols/SimpleLineSymbol",
		"dojo/number",
		"dojo/parser",
		"dojo/dom",
		"dijit/registry",
		"esri/tasks/ServiceAreaTask",
		"esri/tasks/ServiceAreaParameters",
		"dijit/form/Button",
		"dijit/form/Textarea",
		"dijit/layout/BorderContainer",
		"dijit/layout/ContentPane",
		"dojo/domReady!"
	],

	function(
		Map,
		esriConfig,
		ArcGISDynamicMapServiceLayer,
		SpatialReference,
		FeatureLayer,
		Locator,
		Graphic,
		InfoTemplate,
		SimpleMarkerSymbol,
		Font,
		TextSymbol,
		GraphicsLayer,
		arrayUtils,
		Color,
		GeometryService,
		BufferParameters,
		IdentifyTask,
		IdentifyParameters,
		SimpleFillSymbol,
		SimpleLineSymbol,
		number,
		parser,
		dom,
		registry,
		ServiceAreaTask,
		ServiceAreaParameters)
	{
		console.log("###parser.parse()");
		parser.parse();

		esriConfig.defaults.io.proxyUrl = "/proxy";

		gMap = new Map("map",
			{
				basemap: "streets",
				center: [-93.5, 41.431],
				zoom: 5,
				autoResize: true,
				spatialReference: new SpatialReference(102100)
			}
		);

		var gsvc = new GeometryService("http://tasks.arcgisonline.com/ArcGIS/rest/services/Geometry/GeometryServer");

		gMap.on("click", executeIdentifyTask);
		
		//create identify tasks and setup parameters
		identifyTask = new IdentifyTask("http://services2.arcgis.com/DlASPyTb2UPEalFT/arcgis/rest/services/BBBS_Address/FeatureServer/0/query");

		identifyParams = new IdentifyParameters();
		identifyParams.tolerance = 10;
		identifyParams.returnGeometry = true;
		//identifyParams.layerIds = [0];
		identifyParams.layerOption = IdentifyParameters.LAYER_OPTION_ALL;
		identifyParams.width = gMap.width;
		identifyParams.height = gMap.height;

		//gTrafficLayer = new ArcGISDynamicMapServiceLayer("http://gis.srh.noaa.gov/arcgis/rest/services/RIDGERadar/MapServer");

		//gMap.addLayer(gTrafficLayer);

		var eventLayer = new FeatureLayer("http://services2.arcgis.com/DlASPyTb2UPEalFT/arcgis/rest/services/BBBS_Address/FeatureServer/0", { "id": "BPPAS" });

		gLocator = new Locator("http://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer");
		gLocator.on("address-to-locations-complete", showResults);
		gMap.addLayer(eventLayer);

		gLayer = new GraphicsLayer();

		gMap.addLayer(gLayer);

		var identifyTask, identifyParams;

		// listen for button click then geocode
		$("#locate").click(locate);

		//var searchbtn = registry.byId("locate");
		//searchbtn.on("click", locate);
		//searchbtn.set('Class', 'searchbtn');
		//gMap.infoWindow.resize(200, 125);

		function executeIdentifyTask(event)
		{
			console.log("###executeIdentifyTask()");
			var g = event.mapPoint;
			
			g.setSpatialReference(new SpatialReference(102100));

			var params = new BufferParameters();

			params.distances = [ 500 ];
			params.bufferSpatialReference = new esri.SpatialReference({wkid: 102100});
			params.outSpatialReference = gMap.spatialReference;
			params.unit = GeometryService['Feet'];
			params.geometries = [g];
			gsvc.buffer(params, showBuffer);
		}

		function showBuffer(bufferedGeometries)
		{
			console.log("###showBuffer()");
			var symbol = new SimpleFillSymbol(
				SimpleFillSymbol.STYLE_SOLID,
				new SimpleLineSymbol(SimpleLineSymbol.STYLE_SOLID, new Color([255,0,0,0.65]), 2),
				new Color([255,0,0,0.35]));

			//var graphic = new Graphic(bufferedGeometries[0], symbol);
			//gMap.graphics.add(graphic);

			identifyParams.geometry = bufferedGeometries[0];

			var e = gMap.extent;

			e.setSpatialReference(new SpatialReference(102100));
			identifyParams.mapExtent = e;

			var deferred = identifyTask.execute(identifyParams).addCallback(
				function(response)
				{
					console.log("###identifyTask.execute().callback()");
					// response is an array of identify result objects
					// Let's return an array of features.
					return arrayUtils.map(response, function(result)
						{
							var feature = result.feature;
							alert(feature.attributes['ID']);
						}
					);
				}
			);
		}

		// InfoWindow expects an array of features from each deferred
		// object that you pass. If the response from the task execution
		// above is not an array of features, then you need to add a callback
		// like the one above to post-process the response and return an
		// array of features.
		//gMap.infoWindow.setFeatures([deferred]);
		//gMap.infoWindow.show(event.mapPoint);

		function locate()
		{
			console.log("###locate()");
			gMap.graphics.clear();
			
			var address = { "SingleLine": $('#address').val() };

			gLocator.outSpatialReference = gMap.spatialReference;
			
			var options =
			{
				address: address,
				outFields: ["Loc_name"]
			}
			gLocator.addressToLocations(options);
		}
		
		function showResults(evt)
		{
			console.log("###showResults()");
			var candidate;
			var symbol = new SimpleMarkerSymbol();
			var infoTemplate = new InfoTemplate("Location", "Address: ${address}<br />Score: ${score}<br />Source locator: ${locatorName}");
			
			symbol.setStyle(SimpleMarkerSymbol.STYLE_SQUARE);
			symbol.setColor(new Color([153, 0, 51, 0.75]));

			var geom;
			
			arrayUtils.every(evt.addresses,
				function (candidate)
				{
					console.log("###arrayUtils.every(evt.address)" + candidate.address + " : " + candidate.score);
					
					if (candidate.score > 80)
					{
						console.log(candidate.location);
						
						var attributes =
						{
							address: candidate.address,
							score: candidate.score,
							locatorName: candidate.attributes.Loc_name
						};
						
						geom = candidate.location;
						gAddressGraphic = new Graphic(geom, symbol, attributes, infoTemplate);

						// add a graphic to the map at the geocoded location
						gMap.graphics.add(gAddressGraphic);
						
						// add a text symbol to the map listing the location of the matched address.
						var displayText = candidate.address;
						var font = new Font(
							"16pt",
							Font.STYLE_NORMAL,
							Font.VARIANT_NORMAL,
							Font.WEIGHT_BOLD,
							"Helvetica"
						);

						var textSymbol = new TextSymbol(
							displayText,
							font,
							new Color("#666633")
						);
						
						textSymbol.setOffset(0, 8);

						gMap.graphics.add(new Graphic(geom, textSymbol));

						// break out of loop after one candidate with score greater  than 80 is found.
						return false;
					}
				}
			); // arrayUtils.every(evt.addresses)
		
			if (geom !== undefined)
			{
				gMap.centerAndZoom(geom, 12);
				
				// Create drive time polygon
				CreateDriveTime();
			}
		} // function showResults(evt)
	} // function()
); // require()


function addTraffic()
{
	require(
		[
			"esri/layers/ArcGISDynamicMapServiceLayer"
		],
		
		function(ArcGISDynamicMapServiceLayer)
		{
			//gTrafficLayer = new ArcGISDynamicMapServiceLayer("http://traffic.arcgis.com/arcgis/rest/services/World/Traffic/MapServer?token=XYOPXRdr3-Po6ikWBhtkvm1Wa-LGSnljjGZ3zjczIh9cKnku4Xcy6CTgGXDpNhpn_vPnMUC0auVX92wcSEOKTlYNXHUHdN734wac26PymYiNBvHmqD4ZTikdXH4nLLXlwv8f122ZxKnrv1HJhJP4Ew..");
		}
	);
}

function ZoomLocation(lng, lat)
{
	require(
		[
			"esri/map",
			"esri/config",
			"esri/geometry/Point",
			"esri/SpatialReference",
			"esri/layers/FeatureLayer",
			"esri/tasks/locator",
			"esri/graphic",
			"esri/InfoTemplate",
			"esri/symbols/SimpleMarkerSymbol",
			"esri/symbols/Font",
			"esri/symbols/TextSymbol",
			"dojo/_base/array",
			"esri/Color",
			"dojo/number",
			"dojo/parser",
			"dojo/dom",
			"dijit/registry",
			"esri/tasks/ServiceAreaTask",
			"esri/tasks/ServiceAreaParameters",
			"dijit/form/Button",
			"dijit/form/Textarea",
			"dijit/layout/BorderContainer",
			"dijit/layout/ContentPane",
			"dojo/domReady!"
		],

		function (
			Map,
			esriConfig,
			Point,
			SpatialReference,
			FeatureLayer,
			Locator,
			Graphic,
			InfoTemplate,
			SimpleMarkerSymbol,
			Font,
			TextSymbol,
			arrayUtils,
			Color,
			number,
			parser,
			dom,
			registry,
			ServiceAreaTask,
			ServiceAreaParameters)
		{
			console.log("###ZoomLocation()");
			
			var symbol = new SimpleMarkerSymbol();
			var infoTemplate = new InfoTemplate("Location", "Address: ${address}<br />Score: ${score}<br />Source locator: ${locatorName}");

			symbol.setStyle(SimpleMarkerSymbol.STYLE_SQUARE);
			symbol.setColor(new Color([153, 0, 51, 0.75]));

			var geom = new Point(lng, lat);

			gAddressGraphic = new Graphic(geom, symbol);

			//add a graphic to the map at the geocoded location
			gMap.graphics.add(gAddressGraphic);

			//add a text symbol to the map listing the location of the matched address.
			var displayText = "Your current location";
			var font = new Font(
				"16pt",
				Font.STYLE_NORMAL,
				Font.VARIANT_NORMAL,
				Font.WEIGHT_BOLD,
				"Helvetica"
			);

			var textSymbol = new TextSymbol(
				displayText,
				font,
				new Color("#666633")
			);
			
			textSymbol.setOffset(0, 8);

			gMap.graphics.add(new Graphic(geom, textSymbol));
			gMap.centerAndZoom(geom, 12);
			CreateDriveTime();
		}

	); // require()
} //ZoomLocation()

function CreateDriveTime()
{
	require(
		[
			"esri/map", "esri/config", "esri/SpatialReference",
			"esri/tasks/ServiceAreaTask", "esri/tasks/ServiceAreaParameters", "esri/tasks/FeatureSet",
			"esri/symbols/SimpleMarkerSymbol", "esri/symbols/SimpleLineSymbol", "esri/symbols/SimpleFillSymbol",
			"esri/geometry/Point", "esri/graphic",
			"dojo/parser", "dojo/dom", "dijit/registry",
			"esri/Color", "dojo/_base/array",
			"dijit/layout/BorderContainer", "dijit/layout/ContentPane",
			"dijit/form/HorizontalRule", "dijit/form/HorizontalRuleLabels", "dijit/form/HorizontalSlider",
			"dojo/domReady!"
		],
		function (
			Map,
			esriConfig,
			SpatialReference,
			ServiceAreaTask,
			ServiceAreaParameters,
			FeatureSet,
			SimpleMarkerSymbol,
			SimpleLineSymbol,
			SimpleFillSymbol,
			Point,
			Graphic,
			parser,
			dom,
			registry,
			Color,
			arrayUtils)
		{
			console.log("###CreateDriveTime()");
			parser.parse();

			//This sample requires a proxy page to handle communications with the ArcGIS Server services. You will need to
			//replace the url below with the location of a proxy on your machine. See the 'Using the proxy page' help topic 
			//for details on setting up a proxy page.
			esriConfig.defaults.io.proxyUrl = "/proxy";

			var params = new ServiceAreaParameters();
			params.defaultBreaks = [5];
			params.outSpatialReference = new SpatialReference(102100);
			params.returnFacilities = false;

			gServiceAreaTask = new ServiceAreaTask("http://sampleserver3.arcgisonline.com/ArcGIS/rest/services/Network/USA/NAServer/Service Area");

			var features = [];
			features.push(gAddressGraphic);
			
			var facilities = new FeatureSet();
			facilities.features = features;
			
			params.facilities = facilities;

			//solve
			gServiceAreaTask.solve(
				params,
				function (solveResult)
				{
					console.log("###gServiceAreaTask.solve(solveResult)" + solveResult);
					var result = solveResult;
					var serviceAreaSymbol = new SimpleFillSymbol(
						"solid",
						new SimpleLineSymbol("solid", new dojo.Color([232, 104, 80]), 2),
						new dojo.Color([232, 104, 80, 0.25])
					);
					var polygonSymbol = new SimpleFillSymbol(
						"solid",
						new SimpleLineSymbol("solid", new Color([232, 104, 80]), 2),
						new Color([232, 104, 80, 0.25])
					);
					
					arrayUtils.forEach(
						solveResult.serviceAreaPolygons,
						function (serviceArea)
						{
							serviceArea.setSymbol(polygonSymbol);
							gMap.graphics.add(serviceArea);
							gAreaGraphic = serviceArea;
							QueryAddress();
						}
					);
				},
				function (err)
				{
					console.log(err.message);
				}
			); // solve()
		} // function()
	); // require()
} //CreateDriveTime()

function QueryAddress()
{
	require(
		[
			"esri/tasks/query",
			"esri/tasks/QueryTask",
			"esri/SpatialReference",
			"esri/geometry/webMercatorUtils"
		],
		function (
			Query,
			QueryTask,
			SpatialReference,
			webMercatorUtils)
		{
			console.log("###QueryAddress()");
			
			var query = new Query();
			//var geom = webMercatorUtils.geographicToWebMercator(g.geometry);
			var g = gAreaGraphic;

			g.geometry.setSpatialReference(new SpatialReference(102100));
			query.geometry = g.geometry;
			query.spatialRelationship = Query.SPATIAL_REL_INTERSECTS;
			query.outSpatialReference = { wkid: 102100 };
			query.returnGeometry = true;
			query.outFields = ["Category", "ID"];

			queryTask = new QueryTask("http://services2.arcgis.com/DlASPyTb2UPEalFT/ArcGIS/rest/services/BBBS_Address/FeatureServer/0/query");
			queryTask.execute(query, showResults);

			//eventLayer.queryFeatures(query, showResults);

		}
	);
} //QueryAddress()

function showResults(result)
{
	console.log("###showResults(): count= " + result.features.length);
	var fbID = [];
	
	for (i = 0; i < result.features.length; i++)
	{
		console.log("result[" + i + "]: " + result.features[i].attributes.ID);
		fbID.push(result.features[i].attributes.ID);
		gIdLookup[result.features[i].attributes.ID] = result.features[i].geometry;
	}

	if (fbID.length > 0)
	{
		GetEvents(fbID);
	}
	else
	{
		console.log("###showResults(): no firebase IDs");
	}
} //showResults()

function GetEvents(ids)
{
	console.log("###GetEvents()");
	
	var arrayTest = new Array();
	var fb = new Firebase($FIREBASE_URL + '/data/events');

	//$('#InfoDiv').empty();
	fb.once('value',
		function(snapshot)
		{
			snapshot.forEach(
				function(userSnap)
				{
					var i = 0;
					//jQuery("#eventsDiv").append("Event: " + userSnap.val().title + "<br/>");
					if (ids == undefined)
					{
						jQuery("#eventsDiv").append("Event: " + userSnap.val().title + "<br/>");
					}
					else
					{
						ids.forEach(
							function(idName)
							{
								if (userSnap.name() == idName || ids == undefined)
								{
									arrayTest.push(userSnap);
									
									var info = userSnap.val();
									
									// console.log(userSnap.val());
									resultHtml = "<img  style='width: 20px; height: 20px' src='images/" +
										info.category + ".png'  title='" + info.category +
										"' /><span style='margin-left: 5px;'><a href='#' onclick='pinMap(\"" +
										userSnap.name() + "\");' class='d'>" + info.title + "</a></span>, <span class='d'>" +
										info.agerange + "</span>, <span class='d'>" + info.pricerange + "</span>";
									
									detailHtml = "<div class='sDetail' id='" + userSnap.name() + "'>" +
										info.description + "<p>" + info.address.street1 + "<br/>" + info.address.city + "," +
										info.address.state + " " + info.address.zip + "</p>Website: <a href='" + info.url + "'>"
										+ info.url + "</a><img src='images/eventpics/" + info.picture +
										"' style='float: left; width: 240px; height: 240px;' /></div>";

									//$('#accordion').append('<div style="font-size:14px;">' + info.title + '</div><div">'+ info.description  +'</div>');
									$('#accordion').append('<div style="font-size: 12px;">' + resultHtml + '</div><div">' + detailHtml + '</div>');
									//console.log(resultHtml);
									//console.log(detailHtml);
									ids.splice(i, 1);
								}
							}
						); // ids.forEach()
					}
				} //function(userSnap)
			); //snapshot.forEach()
			$('#accordion').accordion("refresh");
			redirect();
			$('#st-accordion').accordion({oneOpenedItem	: true });
			//setIds(arrayTest);
			//console.log(arrayTest);
			//console.log(arrayTest[0]);
			//console.log(arrayTest.length);
		} //function(snapshot)
	); //fb.once('value', function(snapshot))
} //GetEvents()

function pinMap(id)
{
	require(
		[
			"esri/map", "esri/config", "esri/geometry/Point", "esri/SpatialReference", "esri/layers/FeatureLayer", "esri/tasks/locator", "esri/graphic",
			"esri/InfoTemplate", "esri/symbols/SimpleMarkerSymbol",
			"esri/symbols/Font", "esri/symbols/TextSymbol",
			"dojo/_base/array", "esri/Color", "esri/symbols/PictureMarkerSymbol",
			"dojo/number", "dojo/parser", "dojo/dom", "dijit/registry",
			"esri/tasks/ServiceAreaTask", "esri/tasks/ServiceAreaParameters",
			"dijit/form/Button", "dijit/form/Textarea",
			"dijit/layout/BorderContainer", "dijit/layout/ContentPane",
			"dojo/domReady!"
		],
		
		function (
			Map,
			esriConfig,
			Point,
			SpatialReference,
			FeatureLayer,
			Locator,
			Graphic,
			InfoTemplate,
			SimpleMarkerSymbol,
			Font,
			TextSymbol,
			arrayUtils,
			Color,
			PictureMarkerSymbol,
			number,
			parser,
			dom,
			registry,
			ServiceAreaTask,
			ServiceAreaParameters
		)
		{
			console.log("###pinMap() pinned: " + id);
			var g = gIdLookup[id];
			var symbol = new SimpleMarkerSymbol();
			var infoTemplate = new InfoTemplate("Location", "Address: ${address}<br />Score: ${score}<br />Source locator: ${locatorName}");

			symbol.setStyle(SimpleMarkerSymbol.STYLE_SQUARE);
			symbol.setColor(new Color([153, 0, 51, 0.75]));

			var pictureMarkerSymbol = new PictureMarkerSymbol('images/marker2.png', 32, 32);
			
			pictureMarkerSymbol.yoffset = 15;

			gLayer.clear();

			var gp = new Graphic(g, pictureMarkerSymbol);
			
			// add a graphic to the map at the geocoded location
			gLayer.add(gp);
			gMap.centerAndZoom(g, 15);

			// add a text symbol to the map listing the location of the matched address.
			var displayText = "Your current location";
			var font = new Font(
				"16pt",
				Font.STYLE_NORMAL,
				Font.VARIANT_NORMAL,
				Font.WEIGHT_BOLD,
				"Helvetica");
			var textSymbol = new TextSymbol(
				displayText,
				font,
				new Color("#666633"));

			textSymbol.setOffset(0, 8);

			//gMap.graphics.add(new Graphic(geom, textSymbol));
			//CreateDriveTime();
		}
	); //require()
} //pinMap(id)
