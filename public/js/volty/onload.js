var base_url = window.location.origin;

global_ref_obj = {
			'REG_NO' : '733023400203',
			'ST':30,
			'PT':30
		}
		$(document).ready(
				function() {
					isOpera = !!window.opera
							|| navigator.userAgent.indexOf(' OPR/') >= 0; // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
					isFirefox = typeof InstallTrigger !== 'undefined'; // Firefox 1.0+
					isSafari = Object.prototype.toString.call(
							window.HTMLElement).indexOf('Constructor') > 0; // At least Safari 3+: "[object HTMLElementConstructor]"
					isChrome = !!window.chrome && !isOpera; // Chrome 1+
					isIE = /*@cc_on!@*/false || !!document.documentMode;
					// 	var req_obj={}
					// // 	req_obj.REGNO='733024026445';
					// 	req_obj.REGNO='733023400203';
					// 	req_obj.DATE='RECENT';
					// 	$.post("/history", req_obj, function(data) {
					// 		console.log(data);
					// 	});
				});
function RENAME(){
	var main_obj={};
	main_obj.FROM='volty';
	main_obj.TO='volty';
	var json_object={};
	json_object.REGNO='733023398993';
	json_object.TYPE='None';
	json_object.NAME='TATA DUMPER';
	json_object.DATE='2016-04-07';
	json_object.IMEI='733023398993';
	main_obj.VEH=json_object;
	main_obj.OPTION='SHIFT';
	console.log(JSON.stringify(main_obj));
	$.post("/logistics", main_obj, function(data) {
		console.log(data);
		alert(JSON.stringify(data));
		// 		if(data.hasOwnProperty('CATEGORIES')){
		// 			for(var cat_count=0;cat_count<data.CATEGORIES.length;cat_count++){

		// 			}
		// 		}
	});
}
function DELETE(){
	var main_obj={};
	main_obj.SUPER_USER='volty';
	main_obj.SUPER_PASS='volty1';
	main_obj.REGNO='733023398993';
//	var json_object={};
//	json_object.REGNO='733023398993';
//	json_object.TYPE='None';
//	json_object.NAME='TATA DUMPER';
//	json_object.DATE='2016-04-07';
//	json_object.IMEI='733023398993';
//	main_obj.VEH=json_object;
	main_obj.OPTION='D_VEHICLE';
	console.log(JSON.stringify(main_obj));
	$.post("/logistics", main_obj, function(data) {
		console.log(data);
		alert(JSON.stringify(data));
		// 		if(data.hasOwnProperty('CATEGORIES')){
		// 			for(var cat_count=0;cat_count<data.CATEGORIES.length;cat_count++){

		// 			}
		// 		}
	});
		
}	function GET_RECENT_POINT(regno, date) {
		/*var t=document;
			var o=t.createElement('script');
			o.setAttribute('type','text/javascript');
			o.setAttribute('src',base_url+'/js/volty/jquery-1.11.0.min.js');
			t.lastChild.firstChild.appendChild(o);
		*/	
			//var veh_fn=this;
			//new Worker("http://localhost:3000/js/volty/jquery-1.11.0.min.js");
			
			GET_RECENT_POINT1(regno, date);

	}
		function GET_RECENT_POINT1(regno, date) {
			if($.ajaxSettings.headers["X-CSRF-TOKEN"]) {
				delete $.ajaxSettings.headers["X-CSRF-TOKEN"]; 
			}

			document.getElementById('gmap').innerhtml = '';
			$('#gmap').html('');

			if($('#voltyHistoryMap').length)
				$('#voltyHistoryMap').remove();
			var req_obj = {}
			// 	req_obj.REGNO='733024026445';
			// 	req_obj.REGNO='733023400203';
			// 	req_obj.DATE='RECENT';
			if (date != 'RECENT') {
				if (isValidDate(date) == false) {
					return {
						'ERROR' : 'FILL VALID DATE'
					};
				}
			}
			req_obj.REGNO = regno;
			req_obj.DATE = date;
			req_obj.OPTION = 'RECENT';
//http://transync.in:8044/logistics
			$.post("http://volty.logistiks.com:8044/logistics", req_obj, function(data) {
				console.log(data);
				//alert(JSON.stringify(data));
				//alert(JSON.stringify(data));
				
 				if(data.hasOwnProperty('ERROR')){
 					//alert(data.ERROR);
					$('#gmap').html('<span class="nomap_exist">'+data.ERROR+'</span>');
 				}else{
					/*global_ref_obj.SINGLE_HISTORY = new SINGLE_VEH(
							'Amaze_konark', data, data.DATE, 'gmap');
					global_ref_obj.SINGLE_HISTORY.SHOW_DATA_ON_MAP();*/
					var xfy = data.XY; 
					//alert(xfy);
					var xc = xfy.split(",");
					//alert("Data: "+xc);
					//alert(xc[0]);
					//alert(xc[1]);

					var map = new google.maps.Map(document.getElementById('gmap'), {
					      center: {lat: parseFloat(xc[0]), lng: parseFloat(xc[1])},
					      zoom: 17
					    });

					var contentString = '<div id="infobox">'+
					      'Time     : '+data.T+
					      '<br> LAT      : '+xc[0]+
					      '<br> LNG      : '+xc[1]+
					      '<br> Location : '+data.GC+
					      //'Status : '+
					      '</div>';
					  var infowindow = new google.maps.InfoWindow({
					    content: contentString
					  });

					//var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png';
					
				    var image = base_url+"/images/volty/busmoving_2.png";
				    //alert(image);
				    var beachMarker = new google.maps.Marker({
				        position: {lat:parseFloat(xc[0]), lng: parseFloat(xc[1])},
				        map: map,
				        icon: image
				    });
					beachMarker.addListener('click', function() {
						infowindow.open(map, beachMarker);
					});
					
					infowindow.open(map, beachMarker);					
 				}
			});
		}
		function HISTORY_OLD(regno, date) {
			if($.ajaxSettings.headers["X-CSRF-TOKEN"])
			delete $.ajaxSettings.headers["X-CSRF-TOKEN"];

			$('#gmap').html('');

			if($('#voltyHistoryMap').length)
				$('#voltyHistoryMap').remove();

			var req_obj = {}
			// 	req_obj.REGNO='733024026445';
			// 	req_obj.REGNO='733023400203';
			// 	req_obj.DATE='RECENT';
			req_obj.REGNO = regno;
			if (date != 'RECENT') {
				if (isValidDate(date) == false) {
					return {
						'ERROR' : 'FILL VALID DATE'
					};
				}
			}
			req_obj.DATE = date;
			req_obj.OPTION = "HISTORY";
			$.post("http://volty.logistiks.com:8044/logistics", req_obj,
					function(data) {
						//console.log(data);
						if (data.hasOwnProperty('SI')) {
							veh_spd_range = (data.SI).split(',');
						} else {
							veh_spd_range = [ 60, 100 ];
						}
						if (global_ref_obj.hasOwnProperty('SINGLE_HISTORY')) {
							if (global_ref_obj.SINGLE_HISTORY
									.hasOwnProperty('MARKER_WORKER')) {
								global_ref_obj.SINGLE_HISTORY.MARKER_WORKER
										.terminate();
							}
							if (global_ref_obj.SINGLE_HISTORY
									.hasOwnProperty('WORKER')) {
								global_ref_obj.SINGLE_HISTORY.WORKER
										.terminate();
							}
							if (global_ref_obj.SINGLE_HISTORY
									.hasOwnProperty('TRIP_WORKER')) {
								global_ref_obj.SINGLE_HISTORY.TRIP_WORKER
										.terminate();
							}
						}
						if(data.hasOwnProperty('ERROR')){
							//alert(data.ERROR);
							//$('#gmap').html(data.ERROR);
							$('#gmap').html('<span class="nomap_exist">'+data.ERROR+'</span>');
						}else{

							/** STart : Iframe History **/
							document.getElementById('gmap').innerhtml = '';
							var link = "http://volty.logistiks.com:8044/history/"+regno+"/"+date;
							var iframe = document.createElement('iframe');
							iframe.frameBorder=0;
							iframe.width="100%";
							iframe.height="100%";
							iframe.id="voltyHistoryMap";
							iframe.setAttribute("src", link);
							document.getElementById("gmap").appendChild(iframe);			
							return;
							/** END : IFrame History **/

							global_ref_obj.SINGLE_HISTORY = new SINGLE_VEH(
									'Amaze_konark', data, data.DATE, 'gmap');
							global_ref_obj.SINGLE_HISTORY.SHOW_DATA_ON_MAP();
						}
						
					});
		}

		/**** START : History Tracking using Iframe***/
		function HISTORY(regno, placementdate, placementtime, currentdate, currenttime) {
			$('#gmap').html('');
			/** START : Iframe History **/
			document.getElementById('gmap').innerhtml = '';
			//var link = "http://volty.logistiks.com:8044/history/"+regno+"/"+date;
			var link = "http://volty.logistiks.com:8044/history/"+regno+"/"+placementdate+"/"+placementtime+"/"+currentdate+"/"+currenttime
;
			var iframe = document.createElement('iframe');
			iframe.frameBorder=0;
			iframe.width="100%";
			iframe.height="100%";
			iframe.id="voltyHistoryMap";
			iframe.setAttribute("src", link);
			document.getElementById("gmap").appendChild(iframe);			
			return;
			/** END : IFrame History **/
		}
		/**** END : History Tracking using Iframe***/

		/**** START : LIVE Tracking using Iframe***/
		function LIVE(regno, placementdate, placementtime, currentdate, currenttime) {
			$('#gmap').html('');			
			/** START : Iframe History **/
			document.getElementById('gmap').innerhtml = '';
			var link = "http://volty.logistiks.com:8044/livetracking/"+regno+"/"+placementdate+"/"+placementtime+"/"+currentdate+"/"+currenttime
;
			var iframe = document.createElement('iframe');
			iframe.frameBorder=0;
			iframe.width="100%";
			iframe.height="100%";
			iframe.id="voltyHistoryMap";
			iframe.setAttribute("src", link);
			document.getElementById("gmap").appendChild(iframe);			
			return;
			/** END : IFrame History **/
		}
		/**** END : LIVE Tracking using Iframe***/

		function CREATE_NEW_VEH(username, password, device_imei, sim_imei,
				reg_no, operator, mobile_no, veh_name, date) {
			var obj = {};
			if (username == '') {
				obj.ERROR = "USERNAME EMPTY";
				return obj;
			} else if (password == '') {
				obj.ERROR = "PASSWORD EMPTY";
				return obj;
			} else if (device_imei == "" || device_imei.length != '12') {
				obj.ERROR = "ENTER 12 DIGIT DEVICE NUMBER";
				return obj;
			} else if (sim_imei == "" || sim_imei.length != '20') {
				obj.ERROR = "ENTER 20 DIGIT SIM NUMBER";
				return obj;
			} else if (reg_no == "") {
				obj.ERROR = "ENTER VEHICLE REGISTRATION NUMBER";
				return obj;
			} else if (veh_name == "") {
				obj.ERROR = "ENTER VEHICLE NAME";
				return obj;
			} else if (mobile_no == "" || mobile_no.length != 10) {
				obj.ERROR = "ENTER 10 DIGIT MOBILE NUMBER";
				return obj;
			} else if (isValidDate(date) == false) {
				return {
					'ERROR' : 'FILL VALID DATE'
				};
			} else {
				var global_jsonobject = {};
				global_jsonobject.SUB_USER = username;
				global_jsonobject.SUPER_USER = username;
				global_jsonobject.SUPER_PASS = password;
				global_jsonobject.DIMEI = device_imei;
				global_jsonobject.SIMEI = sim_imei;
				global_jsonobject.REGNO = reg_no;
				global_jsonobject.O = operator;
				global_jsonobject.M = mobile_no;
				global_jsonobject.VNAME = veh_name;
				global_jsonobject.TYPE = 'None';
				global_jsonobject.DATE = date;
// 				var obj={};
				global_jsonobject.OPTION='VEH';
// 				obj.OP = 'ADD';
// 				obj.JSON=global_jsonobject;
				$.post("/logistics", global_jsonobject, function(data) {
					console.log(data);
					alert(JSON.stringify(data));
					//	 		if(data.hasOwnProperty('CATEGORIES')){
					//	 			for(var cat_count=0;cat_count<data.CATEGORIES.length;cat_count++){

					//	 			}
					//	 		}
				});
			}

		}
		function NEW_VEH() {
			CREATE_NEW_VEH('volty', 'volty1', '123456789011',
					'12345678901234567811', 'AP 20 TT 1231', 'Airtel',
					'1234567891','TATA', '2016-02-26');
		}
		function isValidDate(date) {
			var matches = /(\d{4})[-](\d{2})[-](\d{2})/.exec(date);
			if (matches == null)
				return false;
			var day = matches[3];
			var month = matches[2] - 1;
			var year = matches[1];
			var composedDate = new Date(year, month, day);
			return composedDate.getDate() == day
					&& composedDate.getMonth() == month
					&& composedDate.getFullYear() == year;
		}
		function float_min_toSec(min){
			var min_float=min.toString().split(".");
		    if(min_float[1]){
		    	var Sec = min_float[0] * 60+parseInt((min_float[1] * 60)/100) ;
		    }
		    else{
		    	var Sec = min_float[0] * 60;
		    }
		    return Sec;
		}