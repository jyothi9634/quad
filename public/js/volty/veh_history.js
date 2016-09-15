
var base_url = window.location.origin;

var host = window.location.host;

var pathArray = window.location.pathname.split( '/' );

/**
 * Created by Bala Krishna on 6/20/2015.
 */
var isOpera,isFirefox,isSafari,isChrome,isIE;
var bounds_lisener;
var lisener_zoom;
var map;
var veh_spd_range;
var bounds;
var trip_bounds;
var bounds_lisener;
var step;
var geocoder = new google.maps.Geocoder();
var replay_s_f=true;
var rply_slow=true;
var mapProp = {
					center : new google.maps.LatLng(17.49681, 78.36153),
					zoom : 5,
					noClear:false,
//					maxZoom: 16,
					mapTypeId : google.maps.MapTypeId.ROADMAP
			};
//				map = new google.maps.Map(document.getElementById("gmap"), mapProp);
function SINGLE_VEH(regno,json,date){
	this.REGNO=regno;
	this.PARKED_ARRAY_LIST=[];
	this.STOPPED_ARRAY_LIST=[];
	this.MUST_ARRAY_LIST=[];
	this.TRIP_MARKER_LIST=[];
	this.TRIP_STOPPED_LIST=[];
	this.TRIP_MUST_LIST=[];
	this.MARKER_GEO_OBJ={};
	this.ROUTE_ARRAY=[];
	this.DELAY_TIME=0;
	this.TRIP_DELAY_TIME=0;
	this.LAST_STOPPED_SEC=0;
	this.MARKERS=[];
	this.MYTRIP=[];
	this.HAD_ATTACHMENT={};
	this.MARKER_CONTENT_OBJ={};
	// this.REPORTS_GEO_OBJ={};
	this.LATEST_STEP=0;
	this.DATE=date;
	this.POLYLINE=null;
//	this.POLYLINE_VISIBLE=VISIBLE_DATA();
	this.POLYLINE_VISIBLE=true;
	this.TOTDIST=json.TOTDIST;
	this.HISTORY_DATA=json;
	this.MARKER_ATTACH_ID={};
	this.SHOW_DATA_ON_MAP=function(){
		for(var i_count=0;i_count<this.HISTORY_DATA.LOCATIONS.length;i_count++){
			var obj=this.HISTORY_DATA.LOCATIONS[i_count];
			if (obj == null) {
				/*Invalid data*/
				this.HISTORY_DATA.LOCATIONS.splice(i_count,1);
				i_count--; 
				continue;
			}
			else{
				if(obj.hasOwnProperty('I') && obj.hasOwnProperty('XY') && obj.hasOwnProperty('T')){
					/*Valid data*/ 
					/*if(obj.I=='PWR_DWN' || obj.I=='OTHER'){
							 History_data.LOCATIONS.splice(i_count,1);
							 i_count--; 
							 continue;
						 }*/
					var loc_latlng=obj.XY.split(',');
					if(isNaN(loc_latlng[0]) && isNaN(loc_latlng[1])){
						/*Invalid LATLNG*/
						this.HISTORY_DATA.LOCATIONS.splice(i_count,1);
						i_count--; 
						continue;
					}
					if(obj.I.indexOf('NORMAL')>-1 || obj.I.indexOf('STOPPED')>-1){
						/*valid data*/
						var time_stamp=obj.T.split(':');
						this.HISTORY_DATA.LOCATIONS[i_count].MILLS=parseInt(time_stamp[0]) * 3600 + parseInt(time_stamp[1]) * 60 + parseInt(time_stamp[2]);
					}else{
						/*Invalid data*/
						this.HISTORY_DATA.LOCATIONS.splice(i_count,1);
						i_count--; 
						continue;
					}

				}else{
					/*Invalid data*/
					this.HISTORY_DATA.LOCATIONS.splice(i_count,1);
					i_count--; 
					continue;
				}
			}
		}
//		if(veh_list_click==true){
			map=new google.maps.Map(document.getElementById("gmap"),mapProp);
//			global_fn_obj.SEARCH();
//			global_des_obj.DP=[];
//			var curr_date=global_fn_obj.GET_DATE();
//			if(global_ref_obj.DATE!=curr_date){
//				document.getElementById('showing_info').innerHTML="Last Tracked On  &nbsp; "+ this.DATE;
//				global_fn_obj.SHOW_POPUP('info_popup', 'OVERLAY_POPUP');
//			}
//			if(des_listen_handle)
//				des_listen_handle.remove();
//			if(global_ref_obj.hasOwnProperty('CHAINAGE_DATA'))
//				global_fn_obj.ADD_CHAINAGE_ON_MAP();
			var top_Div = document.createElement('div');
			var top_Cl = new Top_center_Cl(top_Div, map);
			map.controls[google.maps.ControlPosition.TOP_CENTER].push(top_Div);
//			received_data_fn_obj.SHOW_BORDERS(map,infobox)
//		}
//		if(veh_index_obj[list_index]['WATER_TANKER']==true){
//			this.WATER_TANK_TRIPS();
//		}
//		else{
			this.CALCULATE_TRIPS();
//		}
//		if(reports_on==true){
//			this.SHOW_REPORTS();
//			this.DRAW_PIE_CHART();
//		}
//		if(this.HISTORY_DATA.hasOwnProperty('FUELDATA')){
//			this.SHOW_FUEL_REPORT();
//		}
		tripview=false;
		if(bounds_lisener)
			bounds_lisener.remove();
		var veh_fn=this;
		bounds_lisener=google.maps.event.addListener(map, 'bounds_changed', function() {
			if(bounds_lisener)
				bounds_lisener.remove();

			veh_fn.SET_MARKER_ICONS();

//			global_fn_geofence_obj.CHECK_GEOFENCE();
//			global_fn_destination_obj.CHECK_MULTIPLE_DES();
//			global_fn_destination_obj.CHECK_SINGLE_DES();
		});
		var bottom_right_Div = document.createElement('div');
		var bottom_right_Cl = new Bottom_right_Cl(bottom_right_Div, map,this.HISTORY_DATA);
		map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(bottom_right_Div);
//		var bottom_left_Div = document.createElement('div');
//		var bottom_left_Cl = new History_HomeCtrl(bottom_left_Div, map);
//		map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(bottom_left_Div);
//		if(global_ref_obj.GEOCODE==true){
//			$('#history_panel-wrapper').css('display','block');
//			$('#history_tab_controller .panel_close').hide();
//			$('#history_tab_controller .panel_show').show();
//			Panel.hidePanel();
//		}
		if(document.getElementById('Top_Text'))
			document.getElementById('Top_Text').innerHTML="REG NO : "+global_ref_obj.REG_NO;

	};
	this.CALCULATE_TRIPS=function(){
		try{
			var s_dist=0;
			var p_sec=0;
			var trip_idling=0;
			var latlng_name='NO';
//			global_fn_obj.REMOVE_TABLE('reports');
//			global_fn_obj.REMOVE_TABLE('myTable');
			bounds=new google.maps.LatLngBounds();
			var LOCATIONS=this.HISTORY_DATA.LOCATIONS;
			var p_latlng=LOCATIONS[0].XY.split(',');
			bounds.extend(new google.maps.LatLng(p_latlng[0], p_latlng[1]));
			p_latlng=latlng_rnd(p_latlng);
			if(LOCATIONS[0].hasOwnProperty('DEST')){
				latlng_name=LOCATIONS[0].DEST;
			}else{
				latlng_name='NO';
			}
			var prevTS = LOCATIONS[0].T.split(':');
			var prevInfo;
			try{
				prevInfo= LOCATIONS[0].I.split(' ');
			}catch(err){
				prevInfo=['NORMAL:','0'];
			}
			if(prevInfo[0]=='NORMAL' || prevInfo[0]=='NORMAL:'){
				if(prevInfo[2]=='MISS'){
					/*1st point MISS.*/
					this.HISTORY_DATA.LOCATIONS[0].I='NORMAL: '+prevInfo[1];
				}
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=0;
			}
			else if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])>global_ref_obj.PT){
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=0;
				trip_idling=trip_idling+parseInt(prevInfo[3]);
			}
			else if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])<=global_ref_obj.PT){
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=0;
				this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length]=0;
				trip_idling=trip_idling+parseInt(prevInfo[3]);
			}
			for(var i=1;i<LOCATIONS.length;i++){
				var currTS =LOCATIONS[i]["T"].split(':');
				var currInfo;
				try{
					currInfo = LOCATIONS[i]["I"].split(' ');
				}catch(err){
					currInfo=['NORMAL:','0'];
				}
				var n_latlng=LOCATIONS[i]["XY"].split(',');
				bounds.extend(new google.maps.LatLng(n_latlng[0], n_latlng[1]));
				n_latlng=latlng_rnd(n_latlng);

				if(currInfo[0]=='STOPPED:' && currInfo[1]>global_ref_obj.PT){
					this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=i;
					trip_idling=trip_idling+parseInt(currInfo[3]);
					//Calculate trip duration
					var  prevSec = parseInt(prevTS[0]) * 3600 + parseInt(prevTS[1]) * 60 + parseInt(prevTS[2]);
					if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])<=global_ref_obj.PT){
						prevSec +=float_min_toSec(prevInfo[1]);
					}
					else if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])>global_ref_obj.PT){
						p_sec =float_min_toSec(prevInfo[1]);
					}
					var currSec = parseInt(currTS[0]) * 3600 + parseInt(currTS[1]) * 60 + parseInt(currTS[2]);
					if(currSec<global_ref_obj.DAY_START){
						currSec+=24*60*60;
					}
					prevSec+=p_sec;
					if(prevSec<global_ref_obj.DAY_START){
						prevSec+=24*60*60;
					}
					currSec =  (currSec - prevSec)/ 60;
					var distTravel = (currInfo.length > 2) ? (parseFloat(currInfo[2])+parseFloat(s_dist)) : 0 ;
					distTravel=Math.round(distTravel* 10) / 10;
					var averageSpeed = distTravel / (currSec/60);
					var duration=parseInt(Math.round(currSec* 100) / 100);
					var avgSd=Math.round(averageSpeed* 10) / 10;

					var arr = new Array();
					var timeS=sec_to_hh_mm_ss(prevSec);

					arr[0]=timeS;
					arr[1]=currTS.join(':');
					var time=arr.join(' - ');
					distTravel=Math.round(distTravel * 100) / 100;
					prevTS = currTS;
					prevInfo = currInfo;
//					this.VEH_TRIP_REPORT_TABLE(distTravel,duration,avgSd);
//					latlng_name=global_fn_obj.SHOW_DEST_NAME(latlng_name, LOCATIONS[i], distTravel, duration, avgSd, arr, p_latlng, n_latlng, trip_idling);
					p_latlng=n_latlng;
					s_dist=0;
					trip_idling=0;
				}
				else if(currInfo[0]=='STOPPED:' && parseFloat(currInfo[1])<=global_ref_obj.PT){
					s_dist+=parseFloat(currInfo[2]);
					this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length]=i;
					trip_idling=trip_idling+parseInt(currInfo[3]);
				}
				else if(currInfo[2]=='MISS'){
					this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length]=i;
				}
				if(currInfo[0]=='NORMAL:'){
					if(currInfo[2]=='MUST' || LOCATIONS[i].hasOwnProperty('A')){
						this.MUST_ARRAY_LIST[this.MUST_ARRAY_LIST.length]=i;
					}
				}
			}
			var l_latlng=LOCATIONS[LOCATIONS.length-1]["XY"].split(',');
			bounds.extend(new google.maps.LatLng(l_latlng[0], l_latlng[1]));
			map.setZoom(map.getZoom()+1);
			map.fitBounds(bounds);
//			map.setZoom(map.getZoom());
			l_latlng=latlng_rnd(l_latlng);
			var LastInfo;
			try{
				LastInfo  = LOCATIONS[LOCATIONS.length-1]["I"].split(' ');
			}catch(err){
				LastInfo=['NORMAL:','0'];
			}
			var LastInfo_ts  = LOCATIONS[LOCATIONS.length-1]["T"].split(':');
			var prev_sec=parseInt(prevTS[0]) * 3600 + parseInt(prevTS[1]) * 60 + parseInt(prevTS[2]);
			if(prevInfo[0]=='STOPPED:'){
				prev_sec+=float_min_toSec(prevInfo[1]);
			}
			this.LAST_STOPPED_SEC=prev_sec;
			if(LastInfo[0]=='NORMAL' || LastInfo[0]=='NORMAL:'){
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=LOCATIONS.length-1;
				var cur_sec=parseInt(LastInfo_ts[0]) * 3600 + parseInt(LastInfo_ts[1]) * 60 + parseInt(LastInfo_ts[2]);
				if(cur_sec<global_ref_obj.DAY_START){
					cur_sec+=24*60*60;
				}
				if(prev_sec<global_ref_obj.DAY_START){
					prev_sec+=24*60*60;
				}
				cur_sec=(cur_sec-prev_sec)/60;
				var dur=parseInt(Math.round(cur_sec* 100) / 100);
				var array = new Array();
				var timeS=sec_to_hh_mm_ss(prev_sec);
				array[0]=timeS;
				array[1]=LastInfo_ts.join(':');
				if(!isNaN(parseFloat(LastInfo[2]))){
					var distTravel = parseFloat(LastInfo[2])+s_dist;
					distTravel=Math.round(distTravel* 10) / 10;
					var averageSpeed = distTravel / (cur_sec/60);
					var avgSd=Math.round(averageSpeed* 10) / 10;
//					this.VEH_TRIP_REPORT_TABLE(distTravel,dur,' ');
//					global_fn_obj.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], distTravel, dur, avgSd, array, p_latlng, n_latlng, trip_idling);
				}
				else{
//					this.VEH_TRIP_REPORT_TABLE("On Going Trip",dur,' ');
//					global_fn_obj.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], 'TRIP', dur, "On Going", array, p_latlng, n_latlng, trip_idling);
				}
			}
			else if(LastInfo[0]=='STOPPED:' && parseFloat(LastInfo[1])>global_ref_obj.ST && parseFloat(LastInfo[1])<=global_ref_obj.PT){
				this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length]=LOCATIONS.length-1;
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=LOCATIONS.length-1;
				var cur_sec=parseInt(LastInfo_ts[0]) * 3600 + parseInt(LastInfo_ts[1]) * 60 + parseInt(LastInfo_ts[2]);
				if(cur_sec<global_ref_obj.DAY_START){
					cur_sec+=24*60*60;
				}
				if(prev_sec<global_ref_obj.DAY_START){
					prev_sec+=24*60*60;
				}
				cur_sec=(cur_sec-prev_sec)/60;
				if(this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length-1]==(LOCATIONS.length-1)){
					var distTravel=s_dist;
				}
				else{
					var distTravel = (LastInfo.length > 2) ? parseFloat(LastInfo[2])+s_dist : 0 ;
				}
				distTravel=Math.round(distTravel* 10) / 10;
				var averageSpeed = distTravel / (cur_sec/60);
				var avgSd=Math.round(averageSpeed* 10) / 10;
				var dur=parseInt(Math.round(cur_sec* 100) / 100);
				var array = new Array();
				var timeS=sec_to_hh_mm_ss(prev_sec);
				array[0]=timeS;
				array[1]=LastInfo_ts.join(':');
				var time=array.join(' - ');
				if(i!=1){
					trip_idling=trip_idling+parseInt(LastInfo[3]);
//					this.VEH_TRIP_REPORT_TABLE(distTravel,dur,avgSd);
//					global_fn_obj.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], distTravel, dur, avgSd, array, p_latlng, n_latlng, trip_idling);
				}
			}
			else if(LastInfo[0]=='STOPPED:' && parseFloat(LastInfo[1])<=global_ref_obj.ST){
				this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length]=LOCATIONS.length-1;
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=LOCATIONS.length-1;
				var cur_sec=parseInt(LastInfo_ts[0]) * 3600 + parseInt(LastInfo_ts[1]) * 60 + parseInt(LastInfo_ts[2]);
				if(cur_sec<global_ref_obj.DAY_START){
					cur_sec+=24*60*60;
				}
				if(prev_sec<global_ref_obj.DAY_START){
					prev_sec+=24*60*60;
				}
				cur_sec=(cur_sec-prev_sec)/60;
				var dur=parseInt(Math.round(cur_sec* 100) / 100);
				var array = new Array();
				var timeS=sec_to_hh_mm_ss(prev_sec);
				array[0]=timeS;
				array[1]=LastInfo_ts.join(':');
				trip_idling=trip_idling+parseInt(LastInfo[3]);
//				this.VEH_TRIP_REPORT_TABLE("On Going Trip",dur,' ');
//				global_fn_obj.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], 'Trip',dur,"On Going", array, p_latlng, n_latlng, trip_idling);
			}
		}
		catch(err){
			console.log("Error in trip fn " + err.message);
		}

	};
	
	this.SET_MARKER_ICONS=function(){

		try{
			var veh_fn=this;
			if(lisener_zoom){
				lisener_zoom.remove();
			}
//			veh_fn.POLYLINE_VISIBLE=VISIBLE_DATA();
			var i=0;
			var iconImg;
			var html_content;


			dest_name_obj={};
			geo_address_obj={'GC':[]};
			global_fn_obj.SET_STEP_LEVEL();
//			var Replay_CtrlDiv = document.createElement('div');
//			var Replay_Ctrl = new Replay_HomeCtrl(Replay_CtrlDiv, map);
//			map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(Replay_CtrlDiv);

			/*********poly line visible button*******/

//			if(buslist.USERDETAILS.hasOwnProperty('POLYLINE')){
//				if((buslist.USERDETAILS.POLYLINE==true || global_ref_obj.POLYLINE_STATUS==true )&& global_ref_obj.POLYLINE_STATUS!=false){
//					var homeControlDiv = document.createElement('div');
//					var homeControl = new HomeControl(homeControlDiv, map);
//					map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
//				}
//			}


			/**************Polyline button name changing**************/
//			if(buslist.USERDETAILS.hasOwnProperty('POLYLINE')){
//				if((buslist.USERDETAILS.POLYLINE==true || global_ref_obj.POLYLINE_STATUS==true )&& global_ref_obj.POLYLINE_STATUS!=false){
//
//					$("#visible").text('Hide Line');
//
//				}
//			}


			var status_img;
			var tool_tip_data="";
			var History_data=this.HISTORY_DATA;
			var LastInfo  = History_data.LOCATIONS[History_data.LOCATIONS.length-1].I.split(' ');

//			if(buslist.USERDETAILS.POLYLINE=false){
//			$("#visible").text('Visible Line');
//			}
//			else{
//			$("#visible").text('Hide Line');
//			}


			if(LastInfo[0]=='NORMAL' || LastInfo[0]=='NORMAL:'){
				status_img=base_url+"/images/volty/busmoving_2.min.png";
				tool_tip_data="RUNNING";
			}else if(LastInfo[0]=='STOPPED:'){
				tool_tip_data="STOPPED";
				status_img=base_url+"/images/volty/busparked_2.min.png";
			}
//			if(veh_index_obj[list_index]['WATER_TANKER']==true){
//			if(History_data.LOCATIONS[History_data.LOCATIONS.length-1].hasOwnProperty('ATTACHED')){
//			status_img=base_url+"/images/volty/arrows.png";
//			this.MARKER_ATTACH_ID[this.MARKERS.length]=History_data.LOCATIONS[i-1].ATTACHED;
//			}
//			}
//			if(veh_list_click==true){
//				veh_list_click=false;
				if(History_data.hasOwnProperty('RECENT')){
					if(History_data.hasOwnProperty('CMD')){
						History_data.LOCATIONS[History_data.LOCATIONS.length-1].CMD=History_data.CMD;
					}
					if(History_data.hasOwnProperty('MAINS')){
						History_data.LOCATIONS[History_data.LOCATIONS.length-1].MAINS=History_data.MAINS;
					}
					if(History_data.hasOwnProperty('GSM')){
						History_data.LOCATIONS[History_data.LOCATIONS.length-1].GSM=History_data.GSM;
					}
				}
//				global_fn_obj.UPDATE_STATUS_IMAGE(History_data.LOCATIONS[History_data.LOCATIONS.length-1], tool_tip_data);
//			}
			var marker_visible;
			var marker_on_top;
			var prev_latlng= History_data.LOCATIONS[0].XY.split(",");                //0th points
			prev_time_stamp=History_data.LOCATIONS[0].T;
			try{
				var prev_info= History_data.LOCATIONS[0].I.split(' ');}catch(err){var prev_info=['NORMAL:','0'];}
				var marker_delay_time=100/(History_data.LOCATIONS.length/step);
				veh_fn.MARKER_WORKER=new Worker(base_url+"/js/volty/js/thread.js");
				veh_fn.MARKER_WORKER.postMessage({'cmd': 'start', 'msg':1,'msg1':parseInt(marker_delay_time)});
				veh_fn.MARKER_WORKER.addEventListener('message', function(e) {
					i=e.data;
					if(i<=History_data.LOCATIONS.length-1)                             // 1 to length-1
					{
						var next_latlng= History_data.LOCATIONS[i].XY.split(",");
						var next_time_stamp=History_data.LOCATIONS[i].T;
						try{
							var next_info= History_data.LOCATIONS[i].I.split(' '); }catch(err){var next_info=['NORMAL:','0'];}
							LatLng = new google.maps.LatLng(prev_latlng[0], prev_latlng[1]);

							/**********Poly Line*************/
//							if(buslist.USERDETAILS.hasOwnProperty('POLYLINE')){
//								if((buslist.USERDETAILS.POLYLINE==true || global_ref_obj.POLYLINE_STATUS==true )&& global_ref_obj.POLYLINE_STATUS!=false){
//									veh_fn.MYTRIP.push(LatLng);
//									veh_fn.ADD_POLYLINE(veh_fn.MYTRIP);
//								}
//							}
							var LatLng_next_pos=new google.maps.LatLng(next_latlng[0], next_latlng[1]);

							if(prev_info[0]=='NORMAL' || prev_info[0]=='NORMAL:'){
//								global_reports_fn_obj.SHOW_HISTORY(History_data.LOCATIONS[i-1],prev_info[1],'ON',next_time_stamp);
								if(prev_info[2]=='MISS'){
									var user_first_latlng=History_data.LOCATIONS[i-2].XY.split(',');
									var user_last_latlng=History_data.LOCATIONS[i-1].XY.split(',');

									var request = {
											origin: new google.maps.LatLng(user_first_latlng[0], user_first_latlng[1]),
											destination: new google.maps.LatLng(user_last_latlng[0], user_last_latlng[1]),
											travelMode: google.maps.TravelMode.DRIVING
									};
									directionsService.route(request, function(response, status) {
										if (status == google.maps.DirectionsStatus.OK){
											directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true,preserveViewport: true,polylineOptions: {
												strokeColor: "red",strokeWidth: '5' }});
											/*directionsDisplay.setMap(map);
                                    directionsDisplay.setDirections(response);
                                    route_object[route_count]=directionsDisplay;
                                    route_count++;*/


											var given_points=response.routes[0].overview_path;
											var path_array=[];
											for(var l_p_count=0;l_p_count<given_points.length;l_p_count++){
												if (l_p_count%2 != 0){
													path_array.push(given_points[l_p_count]);
													var flightPath = new google.maps.Polyline({
														path: path_array,
														geodesic: true,
														strokeColor: "#055144",
//														strokeColor: '#00FF00',
														strokeOpacity: 1.0,
														strokeWeight: 3
													});

													flightPath.setMap(map);
													// veh_route_array.push(flightPath);
													veh_fn.ROUTE_ARRAY.push(flightPath);
												}else{
													path_array=[];
													path_array.push(given_points[l_p_count]);
												}
											}
										}
										else{
											console.log('Error : '+status);
										}
									});
									html_content= "<div  id='infobox'>" + 'SAT signal missing '+ "</div>";
									if((document.URL).indexOf('test.voltysoft.com')>-1){
										iconImg=base_url+'/images/volty/sat_sig.png';
									}else{
										html_content=global_fn_obj.INFOBOX_CONTENT('NORMAL', History_data.LOCATIONS[i-1], prev_time_stamp, prev_info[1]);
										iconImg=global_fn_obj.SET_NORMAL_ICON(prev_info[1],LatLng,LatLng_next_pos);
									}
									veh_fn.ADD_MARKER(LatLng,html_content,iconImg,prev_time_stamp,true);
									google.maps.event.addListener(marker, "click", function() {
										infobox.setContent(this.html);
										infobox.open(map, this);
										map.panTo(this.position);
									});
								}
								else{
									if(i==1){
										iconImg = base_url+'/images/volty/daychange.png';
										marker_visible=true;
										marker_on_top=true;
									}
									else{
										try{
											if(History_data.LOCATIONS[i-1].MILLS>History_data.LOCATIONS[i].MILLS){
												iconImg=global_fn_obj.SET_NORMAL_ICON(prev_info[1],'CIRCLE');
											}else{
												iconImg=global_fn_obj.SET_NORMAL_ICON(prev_info[1],LatLng,LatLng_next_pos);
											}
										}catch (e) {
											iconImg=global_fn_obj.SET_NORMAL_ICON(prev_info[1],LatLng,LatLng_next_pos);
										}
										marker_on_top=false;
										if(i%step==0){
											marker_visible=true;
										}else{
											marker_visible=false;
										}
										if(prev_info[2]=='MUST'){
											marker_visible=true;
										}
										if(History_data.LOCATIONS[i-1].hasOwnProperty('A')){
											var dest_reach_count=1;
											var alert_content=History_data.LOCATIONS[i-1].A;
											if(alert_content.indexOf('REACHED DESTINATION')>-1){
												var alert_details=alert_content.split(' ');
												var dest_reach_name=alert_details[2];
												if(dest_name_obj.hasOwnProperty(dest_reach_name)){
													var point_latlng=dest_name_obj[dest_reach_name][0].XY.split(',');
													LatLng = new google.maps.LatLng(point_latlng[0], point_latlng[1]);
													dest_name_obj[dest_reach_name].push(History_data.LOCATIONS[i-1]);
													dest_reach_count=dest_name_obj[dest_reach_name].length;
												}else{
													dest_name_obj[dest_reach_name]=[];
													dest_name_obj[dest_reach_name].push(History_data.LOCATIONS[i-1]);
													var alert_latlng=History_data.LOCATIONS[i-1].XY.split(',');
													LatLng = new google.maps.LatLng(alert_latlng[0], alert_latlng[1]);
													dest_reach_count=1;
												}
											}
											if(alert_content.indexOf('GPS DEVICE DISCONNECTED')>-1){
											}else{
												if(alert_content.indexOf('REACHED DESTINATION')>-1){
													iconImg='https://chart.googleapis.com/chart?chst=d_map_spin&chld=0.9|0|FFFF42|13|b|'+dest_reach_count;
												}else{
													if(alert_content.indexOf('FUEL FILLED')>-1){
														if(History_data.LOCATIONS[i-1].hasOwnProperty('FF')){
															if(History_data.LOCATIONS[i-1].FF>0){
																iconImg=base_url+'/images/volty/alerts.png';
															}
														}
													}
													if(global_ref_obj.FUEL_ALERTS==true){
														if(alert_content.indexOf('FUEL THEFT')>-1){
															if(History_data.LOCATIONS[i-1].hasOwnProperty('TF')){
																if(History_data.LOCATIONS[i-1].TF>0){
																	iconImg=base_url+'/images/volty/alerts.png';
																}
															}else{
																iconImg=base_url+'/images/volty/alerts.png';
															}
														}
													}
													/* if(alert_content.indexOf('FUEL THEFT')>-1 || alert_content.indexOf('FUEL FILLED')>-1){
                                                if(History_data.LOCATIONS[i-1].hasOwnProperty('L')){
                                                    if(History_data.LOCATIONS[i-1].L>0){
                                                        iconImg=base_url+'/images/volty/alerts.png';
                                                    }
                                                }else{
                                                    iconImg=base_url+'/images/volty/alerts.png';
                                                }
                                            }
                                            else{
                                                iconImg=base_url+'/images/volty/alerts.png';
                                            }*/
												}
												marker_visible=true;
												marker_on_top=true;
											}

										}
									}
//									if(veh_index_obj[list_index]['WATER_TANKER']==true){
//										if(History_data.LOCATIONS[i-1].hasOwnProperty('ATTACHED')){
////											iconImg=base_url+"/images/volty/arrows.png";
//											iconImg=base_url+"/images/volty/water_tank.png";
//											veh_fn.MARKER_ATTACH_ID[veh_fn.MARKERS.length]=History_data.LOCATIONS[i-1].ATTACHED;
//										}
//									}
									html_content=global_fn_obj.INFOBOX_CONTENT('NORMAL', History_data.LOCATIONS[i-1], prev_time_stamp, prev_info[1]);
									veh_fn.ADD_MARKER(LatLng,html_content,iconImg,prev_time_stamp,marker_visible,marker_on_top);
									google.maps.event.addListener(marker, "click", function() {
										infobox.setContent(this.html);
										infobox.open(map, this);
										map.panTo(this.position);
										if(veh_fn.MARKER_ATTACH_ID.hasOwnProperty(this.id)){
											var s_data=veh_fn.MARKER_ATTACH_ID[this.id];
											var req_obj={};
											var secret={};
											secret.MARKER_ID=this.id;
											req_obj.SECRET=secret;
											req_obj.REGNO=s_data.REG_NO;
											req_obj.DATE=s_data.DATE;
											req_obj.TIME=s_data.TIME;
											Publish_subscribe.REQ_ATTACH(req_obj);
											Publish_subscribe.UPDATE_POINT_UNSUB();
										}
									});
								}
							}
							else if(prev_info[0]=='STOPPED:'){
//								global_reports_fn_obj.SHOW_HISTORY(History_data.LOCATIONS[i-1],0,'OFF',History_data.LOCATIONS[i].T);
								marker_visible=true;
								marker_on_top=true;
								var return_obj=minutes(prev_info[1],prev_time_stamp);
								var latest_timestamp=return_obj.TIME_STAMP;
								if(parseFloat(prev_info[1]) >global_ref_obj.PT){
									html_content=global_fn_obj.INFOBOX_CONTENT('PARKED',History_data.LOCATIONS[i-1], latest_timestamp, return_obj.STOPPED_TIME, prev_info[3]);
									if(i==1){
										iconImg = base_url+'/images/volty/daychange.png';
									}
									else{
										iconImg = base_url+'/images/volty/parkingmarker.png';
									}
								}
								else{
									html_content=global_fn_obj.INFOBOX_CONTENT('STOPPED', History_data.LOCATIONS[i-1], latest_timestamp, return_obj.STOPPED_TIME, prev_info[3]);
									if(i==1){
										iconImg = base_url+'/images/volty/daychange.png';
									}
									else{
										iconImg = base_url+'/images/volty/redmarker.png';
									}
								}
//								if(veh_index_obj[list_index]['WATER_TANKER']==true){
//									if(History_data.LOCATIONS[i-1].hasOwnProperty('ATTACHED')){
////										iconImg=base_url+"/images/volty/arrows.png";
//										iconImg=base_url+"/images/volty/water_tank.png";
//										veh_fn.MARKER_ATTACH_ID[veh_fn.MARKERS.length]=History_data.LOCATIONS[i-1].ATTACHED;
//									}
//								}
								veh_fn.ADD_MARKER(LatLng,html_content,iconImg,prev_time_stamp,marker_visible,marker_on_top);
								google.maps.event.addListener(marker, "click", function() {
									infobox.setContent(this.html);
									veh_fn.GET_ADDRESS(this);
									infobox.open(map, this);
									map.panTo(this.position);
									if(veh_fn.MARKER_ATTACH_ID.hasOwnProperty(this.id)){
										var s_data=veh_fn.MARKER_ATTACH_ID[this.id];
										var req_obj={};
										var secret={};
										secret.MARKER_ID=this.id;
										req_obj.SECRET=secret;
										req_obj.REGNO=s_data.REG_NO;
										req_obj.DATE=s_data.DATE;
										req_obj.TIME=s_data.TIME;
										Publish_subscribe.REQ_ATTACH(req_obj);
										Publish_subscribe.UPDATE_POINT_UNSUB();
									}
								});
							}
							prev_latlng=next_latlng;
							prev_time_stamp=next_time_stamp;
							prev_info=next_info;
							veh_fn.MARKER_WORKER.postMessage({'cmd': 'start', 'msg':i+1,'msg1':parseInt(marker_delay_time)});
					}

					if(History_data.LOCATIONS.length==1){
						i=1;
					}
					//last info latlng
					if(i==History_data.LOCATIONS.length){
						try{
							marker_on_top=true;
							var LastInfo  = History_data.LOCATIONS[i-1].I.split(' ');}catch(err){var LastInfo=['NORMAL:','0'];}                        //last point
							var last_latlng= History_data.LOCATIONS[i-1].XY.split(",");
							var LatLng_last_pos = new google.maps.LatLng(last_latlng[0], last_latlng[1]);
							var Last_time_stamp=History_data.LOCATIONS[i-1].T;
							if(LastInfo[0]=='NORMAL' || LastInfo[0]=='NORMAL:'){
//								global_reports_fn_obj.SHOW_HISTORY(History_data.LOCATIONS[i-1],LastInfo[1],'ON',Last_time_stamp);
								status_img=base_url+"/images/volty/busmoving_2.min.png";
								iconImg = base_url+'/images/volty/busmoving_2.png';
								html_content=global_fn_obj.INFOBOX_CONTENT('NORMAL', History_data.LOCATIONS[i-1], Last_time_stamp, LastInfo[1]);
							}
							else if(LastInfo[0]=='STOPPED:'){
//								global_reports_fn_obj.SHOW_HISTORY(History_data.LOCATIONS[i-1],0,LastInfo[3],Last_time_stamp);
								status_img=base_url+"/images/volty/busparked_2.min.png";
								var return_obj=minutes(LastInfo[1],Last_time_stamp);
								var latest_timestamp=return_obj.TIME_STAMP;
								if(parseFloat(LastInfo[1]) >global_ref_obj.ST){
//									iconImg = base_url+'/images/volty/busparked_2.png';
									iconImg = base_url+'/images/volty/parkingmarker.png';
									html_content=global_fn_obj.INFOBOX_CONTENT('PARKED',History_data.LOCATIONS[i-1], latest_timestamp, return_obj.STOPPED_TIME, LastInfo[3]);
								}
								else{
//									iconImg = base_url+'/images/volty/busstopped_2.png';
									iconImg = base_url+'/images/volty/redmarker.png';
									html_content=global_fn_obj.INFOBOX_CONTENT('STOPPED', History_data.LOCATIONS[i-1], latest_timestamp, return_obj.STOPPED_TIME, LastInfo[3]);
								}
							}
//							if(veh_fn.HISTORY_DATA.hasOwnProperty('RECENT')){
//								Publish_subscribe.UPDATE_POINT_SUB();
//							}
//							if(veh_index_obj[list_index]['WATER_TANKER']==true){
//								if(History_data.LOCATIONS[i-1].hasOwnProperty('ATTACHED')){
////									iconImg=base_url+"/images/volty/arrows.png";
//									iconImg=base_url+"/images/volty/water_tank.png";
//									veh_fn.MARKER_ATTACH_ID[veh_fn.MARKERS.length]=History_data.LOCATIONS[i-1].ATTACHED;
//								}
//							}
							LatLng= LatLng_last_pos;

							veh_fn.ADD_MARKER(LatLng,html_content,iconImg,prev_time_stamp,true,marker_on_top);
							google.maps.event.addListener(marker, "click", function() {
								infobox.setContent(this.html);
								veh_fn.GET_ADDRESS(this);
								infobox.open(map, this);
								map.panTo(this.position);
								if(veh_fn.MARKER_ATTACH_ID.hasOwnProperty(this.id)){
									var s_data=veh_fn.MARKER_ATTACH_ID[this.id];
									var req_obj={};
									var secret={};
									secret.MARKER_ID=this.id;
									req_obj.SECRET=secret;
									req_obj.REGNO=s_data.REG_NO;
									req_obj.DATE=s_data.DATE;
									req_obj.TIME=s_data.TIME;
									Publish_subscribe.REQ_ATTACH(req_obj);
									Publish_subscribe.UPDATE_POINT_UNSUB();
								}
							});
							infobox.setContent(global_ref_obj.SINGLE_HISTORY.MARKERS[global_ref_obj.SINGLE_HISTORY.MARKERS.length-1].html);
							infobox.open(map,global_ref_obj.SINGLE_HISTORY.MARKERS[global_ref_obj.SINGLE_HISTORY.MARKERS.length-1]);
							//map.setZoom(map.getZoom()+1);
							global_ref_obj.SINGLE_HISTORY.MARKERS[global_ref_obj.SINGLE_HISTORY.MARKERS.length-1].setAnimation(google.maps.Animation.BOUNCE);
							if(global_ref_obj.SINGLE_HISTORY.MARKERS.length==1 || global_ref_obj.SINGLE_HISTORY.MARKERS.length==2){
								global_ref_obj.SINGLE_HISTORY.MARKERS[0].setVisible(true);
								map.setZoom(14);
							}
							lisener_zoom=google.maps.event.addListener(map, 'zoom_changed', function() {
								global_ref_obj.SINGLE_HISTORY.SHOW_POINTS_IN_DELAY();
							});
					}
				}, false);
//				console.log('Total Markers : '+History_data.LOCATIONS.length);
		}
		catch(err){
			console.log("Error in SET_MARKER_ICONS fn " + err.message );
		}
//		var myTrip=[telangana,stavanger,amsterdam,london];
//		console.log(myTrip);
//		var polyline = new google.maps.Polyline({
//		path: myTrip,
//		geodesic: true,
//		strokeColor: "black",
////		strokeColor: '#00FF00',
//		strokeOpacity: 1.0,
//		strokeWeight: 2
//		});

//		polyline.setMap(map);


	};
	this.ADD_MARKER=function(LatLng,html_content,iconImg,time_stamp,mark_visible,onTop){
		marker = new google.maps.Marker({
			position: LatLng,
			map: map,
			clickable: true,
			visible: mark_visible,
			title: time_stamp,
			html:html_content,
			icon:iconImg,
			id:this.MARKERS.length,
			optimized: false
		});
		this.MARKER_CONTENT_OBJ[this.MARKERS.length]=html_content;
		if(onTop==true){
			marker.setZIndex(google.maps.Marker.MAX_ZINDEX + (1000*this.MARKERS.length));
		}
		this.MARKERS[this.MARKERS.length]=marker;
	};

	this.SHOW_POINTS_IN_DELAY=function(){
//		console.log(map.getZoom());
		global_fn_obj.SET_STEP_LEVEL();
		if(replay_s_f==true){
			if(rply_slow==true){
				if(tripview==true){
					this.TRIP_DELAY_TIME=10000/(this.TRIP_MARKER_LIST.length/step);
				}
				else{
					this.DELAY_TIME=10000/(this.MARKERS.length/step);
				}
			}
			else{
				if(tripview==true){
					this.TRIP_DELAY_TIME=(100/(this.TRIP_MARKER_LIST.length/step))/2;
				}
				else{
					this.DELAY_TIME=(1000/(this.MARKERS.length/step))/2;
				}
			}
		}
		else{
			if(tripview==true){
				this.TRIP_DELAY_TIME=100/(this.TRIP_MARKER_LIST.length/step);
			}
			else{
				this.DELAY_TIME=1000/(this.MARKERS.length/step);
			}
		}
		if(tripview==true){
			this.TRIP_GRAPHICAL_FLOW();
			return;
		}
		/*if(this.PREV_STEP==step){
         return;
         }
         this.PREV_STEP=step;*/
		var new_veh_list=this;
		var myTrip=[];
		var LatLng;
		if(new_veh_list.hasOwnProperty('WORKER'))
			new_veh_list.WORKER.terminate();
		new_veh_list.WORKER=new Worker(base_url+"/js/volty/js/thread.js");
		var NormalMarkerIndex=0;
		new_veh_list.WORKER.postMessage({'cmd': 'start', 'msg':'first','msg1':parseInt(new_veh_list.DELAY_TIME)});
		new_veh_list.WORKER.addEventListener('message', function(e) {
			if('first' != e.data){
				try{
					var markers = new_veh_list.MARKERS[e.data];
					if(markers.getVisible()==false)
						markers.setVisible(true);
				}
				catch(err){
					console.log("Error in SHOW_POINTS_IN_DELAY function " + err.message);
				}
			}
			if(NormalMarkerIndex <new_veh_list.MARKERS.length){
				var few_list_marker =  new_veh_list.MARKERS[NormalMarkerIndex];

				if(few_list_marker.getVisible()==false)
					few_list_marker.setVisible(true);

				new_veh_list.WORKER.postMessage({'cmd': 'start', 'msg':NormalMarkerIndex,'msg1':parseInt(new_veh_list.DELAY_TIME)});
				NormalMarkerIndex += step;
				try{
					for(var MarkerIndex=NormalMarkerIndex + 1; MarkerIndex < NormalMarkerIndex + step; MarkerIndex++){
						if(MarkerIndex < new_veh_list.MARKERS.length){
							var few_marker =  new_veh_list.MARKERS[MarkerIndex];
							LatLng=new google.maps.LatLng(few_marker.position.lat(),few_marker.position.lng());


							/**********Draw Polyline***********/
							if($("#visible").text()=='Hide Line'){
								myTrip.push(LatLng);
								new_veh_list.ADD_POLYLINE(myTrip);
//								$("#visible").text('Hide Line');
							}

							if(few_marker.getVisible()==true)
								few_marker.setVisible(false);
						}
					}

				}
				catch(err){
					console.log("Error in delay points for loop" + err.message);
				}
			}
			try{
				for(var z=0;z<new_veh_list.PARKED_ARRAY_LIST.length;z++){
					new_veh_list.MARKERS[new_veh_list.PARKED_ARRAY_LIST[z]].setVisible(true);
				}
			}
			catch(err){
				console.log("Error in delay points for loop " + err.message);
			}
			try{
				for(var a=0;a<new_veh_list.STOPPED_ARRAY_LIST.length;a++){
					new_veh_list.MARKERS[new_veh_list.STOPPED_ARRAY_LIST[a]].setVisible(true);
				}
				for(var m_count=0;m_count<new_veh_list.MUST_ARRAY_LIST.length;m_count++){
					new_veh_list.MARKERS[new_veh_list.MUST_ARRAY_LIST[m_count]].setVisible(true);
				}
				if(new_veh_list.MARKERS[new_veh_list.MARKERS.length-1].getVisible()==false){
					new_veh_list.MARKERS[new_veh_list.MARKERS.length-1].setVisible(true);
					new_veh_list.MARKERS[new_veh_list.MARKERS.length-1].setAnimation(google.maps.Animation.BOUNCE);
				}
			}
			catch(err){
				console.log("Error in delay points for loop " + err.message);
			}
		}, false);
	};
	this.REMOVE_ALL_MARKERS=function(){
		for (var remove_mar = 0; remove_mar < this.MARKERS.length; remove_mar++){
			this.MARKERS[remove_mar].setMap(null); //Remove the marker from the map
//			polyline.setMap(null);
		}
	};
	this.ADD_POLYLINE=function(myTrip){
		if(this.POLYLINE){
			this.POLYLINE.setMap(null);
		}
//		$("#visible").text('Hide Line');
		this.POLYLINE = new google.maps.Polyline({
			path: myTrip,
			geodesic: true,
			strokeColor: "black",
//			strokeColor: '#00FF00',
			strokeOpacity: 1.0,
			strokeWeight: 1
		});

		this.POLYLINE.setMap(map);
	};
	this.REMOVE_ALL_POLYLINE=function(){
		if(this.POLYLINE){
			this.POLYLINE.setMap(null);
		}
	};
	this.VEH_UPDATE_POINT=function(message){
		var veh_data=this;
		var Veh_update_data=message;
		var html_content;
		var iconImg;
//		var myTrip=[];
		var LOCATIONS=this.HISTORY_DATA.LOCATIONS;
		var prev_data_Lasttime = LOCATIONS[LOCATIONS.length-1]["T"];
		var prev_data_LastInfo = LOCATIONS[LOCATIONS.length-1]["I"].split(' ');
		var prev_data_latlng=LOCATIONS[LOCATIONS.length-1]["XY"].split(',');
		var user_last_point=(Veh_update_data.XY).split(',');
		var user_LatLng=new google.maps.LatLng(user_last_point[0], user_last_point[1]);
		if(Veh_update_data.D==global_ref_obj.DATE){                                            //comparing present date and latest point date
			var latest_info=(Veh_update_data.I).split(' ');
			prev_time_stamp=prev_data_Lasttime;   //title time
			if(latest_info[0]=="NORMAL:"){
//				global_fn_obj.UPDATE_STATUS_IMAGE(Veh_update_data, 'RUNNING');
//				global_reports_fn_obj.SHOW_HISTORY(Veh_update_data,latest_info[1],'ON',Veh_update_data.T);
				if(prev_data_LastInfo[0]=="NORMAL:"){
//					global_fn_obj.UPDATE_REPORTS(Veh_update_data);
					global_ref_obj.BOTTOM_C_BUTTON.innerHTML='Total Idling Time : '+ parseInt(Veh_update_data.IT) +' min'+'<br>'+'Total Distance &nbsp;&nbsp;&nbsp; : '+ parseFloat(Veh_update_data.TD).toFixed(2) +' km';
					LatLng=new google.maps.LatLng(prev_data_latlng[0], prev_data_latlng[1]);

					/**********Poly Line*************/
//					if(buslist.USERDETAILS.hasOwnProperty('POLYLINE')){
////						if(veh_data.POLYLINE_VISIBLE==true){
//						if((buslist.USERDETAILS.POLYLINE==true || global_ref_obj.POLYLINE_STATUS==true )&& global_ref_obj.POLYLINE_STATUS!=false){
//							veh_data.MYTRIP.push(LatLng);
//
//							veh_data.ADD_POLYLINE(veh_data.MYTRIP);
//
//						}
//					}


					html_content=global_fn_obj.INFOBOX_CONTENT('NORMAL', this.HISTORY_DATA.LOCATIONS[this.HISTORY_DATA.LOCATIONS.length-1], prev_data_Lasttime, prev_data_LastInfo[1]);

					this.MARKERS[this.MARKERS.length-1].setVisible(false);
					this.MARKERS.splice(this.MARKERS.length-1, 1);
					iconImg=global_fn_obj.SET_NORMAL_ICON(prev_data_LastInfo[1], LatLng, user_LatLng);
					this.ADD_MARKER(user_LatLng,html_content,iconImg,prev_time_stamp,false,false);
					google.maps.event.addListener(marker, "click", function() {
						infobox.setContent(this.html);
						infobox.open(map, this);
						map.panTo(this.position);
					});
					this.HISTORY_DATA.LOCATIONS.push(Veh_update_data);
					if(prev_data_LastInfo[2]=='MUST')
						this.MUST_ARRAY_LIST[this.MUST_ARRAY_LIST.length]=this.HISTORY_DATA.LOCATIONS.length-1;
					if(step==this.LATEST_STEP){
						this.MARKERS[this.MARKERS.length-1].setVisible(true);
						this.LATEST_STEP=0;
					}
					else{
						if(this.LATEST_STEP>step)
							this.LATEST_STEP=0;
						this.LATEST_STEP++;
					}

				}
				else if(prev_data_LastInfo[0]=="STOPPED:"){
					$('#new_bus > li').click();								// latest point changed form normal to stopped then requesting for recent data. if any problem in getting data below code will work.
				}
				prev_time_stamp=Veh_update_data.T;   //title time
				iconImg = base_url+'/images/volty/busmoving_2.png';
				html_content=global_fn_obj.INFOBOX_CONTENT('NORMAL', Veh_update_data, Veh_update_data.T, latest_info[1]);

				LatLng=user_LatLng;
				this.ADD_MARKER(user_LatLng,html_content,iconImg,prev_time_stamp,true,true);
				this.PARKED_ARRAY_LIST.splice(this.PARKED_ARRAY_LIST.length-1,1);
				this.PARKED_ARRAY_LIST[this.PARKED_ARRAY_LIST.length]=this.HISTORY_DATA.LOCATIONS.length-1;
				this.MARKERS[this.MARKERS.length-1].setVisible(true);
				this.MARKERS[this.MARKERS.length-1].setAnimation(google.maps.Animation.BOUNCE);
				infobox.setContent(this.MARKERS[this.MARKERS.length-1].html);
				infobox.open(map, this.MARKERS[this.MARKERS.length-1]);
				var veh_fn=this;
				google.maps.event.addListener(this.MARKERS[this.MARKERS.length-1], "click", function() {
					infobox.setContent(this.html);
					veh_fn.GET_ADDRESS(this);
					infobox.open(map, this);
					map.panTo(this.position);
				});
			}
			else if(latest_info[0]=="STOPPED:"){
//				global_fn_obj.UPDATE_STATUS_IMAGE(Veh_update_data, 'STOPPED');
				if(prev_data_LastInfo[0]=="NORMAL:"){
					$('#new_bus > li').click();                // latest point changed form normal to stopped then requesting for recent data.if any problem in getting data below code will work.
				}
				else if(prev_data_LastInfo[0]=="STOPPED:"){
					global_ref_obj.BOTTOM_C_BUTTON.innerHTML='Total Idling Time : '+ parseInt(Veh_update_data.IT) +' min'+'<br>'+'Total Distance &nbsp;&nbsp;&nbsp; : '+ parseFloat(Veh_update_data.TD).toFixed(2) +' km';
					var present_time=Veh_update_data.T;
					Veh_update_data.T=this.HISTORY_DATA.LOCATIONS[this.HISTORY_DATA.LOCATIONS.length-1]['T'];
					this.HISTORY_DATA.LOCATIONS.splice(this.HISTORY_DATA.LOCATIONS.length-1, 1);
					var return_obj=minutes(latest_info[1],prev_time_stamp);
					var latest_timestamp=return_obj.TIME_STAMP;
					if(parseFloat(latest_info[1])<=global_ref_obj.ST){
						this.MARKERS[this.MARKERS.length-1].setIcon(base_url+'/images/volty/redmarker.png');
						this.MARKERS[this.MARKERS.length-1].html=global_fn_obj.INFOBOX_CONTENT('STOPPED',Veh_update_data, present_time, return_obj.STOPPED_TIME, latest_info[3]);

					}
					else if(parseFloat(latest_info[1])>global_ref_obj.ST){
						this.MARKERS[this.MARKERS.length-1].setIcon(base_url+'/images/volty/parkingmarker.png');
						this.MARKERS[this.MARKERS.length-1].html=global_fn_obj.INFOBOX_CONTENT('PARKED',Veh_update_data, present_time, return_obj.STOPPED_TIME, latest_info[3]);

					}
					if(this.MARKER_GEO_OBJ.hasOwnProperty(this.MARKERS[this.MARKERS.length-1].id)){
						delete this.MARKER_GEO_OBJ[this.MARKERS[this.MARKERS.length-1].id];
					}
					this.MARKERS[this.MARKERS.length-1].setAnimation(google.maps.Animation.BOUNCE);
					infobox.setContent(this.MARKERS[this.MARKERS.length-1].html);
					infobox.open(map, this.MARKERS[this.MARKERS.length-1]);
					this.HISTORY_DATA.LOCATIONS.push(Veh_update_data);
				}
			}
		}
		else{
			$('#new_bus > li').click();        //if latest point is not todays then requesting for latest date data
		}

	};
	this.SHOW_REPORTS=function(){
		var History_data=this.HISTORY_DATA;
		var rpts_table = document.getElementById("reports");
		var rpts_rows = rpts_table.rows;
		if(rpts_rows.length==1){
			return;
		}
		/*if GC exist get values from it.validate present stopped_list index and GC  'I'(index) value.   */
		if(History_data.hasOwnProperty('GC')){
			if(History_data.GC.length==rpts_rows.length){
				if(this.PARKED_ARRAY_LIST[0]==History_data.GC[0]['I'])
					rpts_rows[1].cells[1].innerHTML=History_data.GC[0]['N'];
				for (var address_count = 1; address_count < History_data.GC.length; address_count++) {
					if(this.PARKED_ARRAY_LIST[address_count]==History_data.GC[address_count]['I'])
						rpts_rows[address_count].cells[2].innerHTML=History_data.GC[address_count]['N'];
					if(rpts_rows[address_count+1]){
						if(this.PARKED_ARRAY_LIST[address_count+1]==History_data.GC[address_count+1]['I'])
							rpts_rows[address_count+1].cells[1].innerHTML=History_data.GC[address_count]['N'];
					}
				}
			}
		}
		/*if it is a latlng req for address.*/
//		var cel=2;
//		var add_latlng=(rpts_rows[1].cells[1].innerHTML).split(',');
//		if(!isNaN(add_latlng[0]) && !isNaN(add_latlng[1])){
//			var latlng = new google.maps.LatLng(add_latlng[0],add_latlng[1]);
//			global_fn_obj.REPORT_ADDRESS(latlng,1,1,'single');
//		}
//		for(var r=1;r<rpts_rows.length;r++){
//			add_latlng=(rpts_rows[r].cells[cel].innerHTML).split(',');
//			if(!isNaN(add_latlng[0]) && !isNaN(add_latlng[1])){
//				latlng = new google.maps.LatLng(add_latlng[0],add_latlng[1]);
//				global_fn_obj.REPORT_ADDRESS(latlng,r,cel,'all');
//			}
//		}

	};
	this.VEH_TRIP_REPORT_TABLE=function(distTravel,duration,avgSd){
		var table_row_length=global_fn_obj.GET_ROWS_LENGTH('myTable')+1;
//		avgsd_list[table_row_length]=avgSd;
		duration=global_fn_obj.DURATION(duration);
		if(distTravel<0){
			distTravel="---";
		}
		var tbody = document.getElementById("myTable").getElementsByTagName("tbody")[0];
		var row = document.createElement("tr");                                             // create row
		row.appendChild( document.createElement('td') ).setAttribute("style","text-align:center;vertical-align:middle;");
		row.appendChild( document.createElement('td') ).setAttribute("style","text-align:center;vertical-align:middle;");//.setAttribute("style","height:30px;vertical-align: middle;width:40%;");
		row.appendChild( document.createElement('td') ).setAttribute("style","text-align:center;vertical-align:middle;");//.setAttribute("style","height:30px;vertical-align: middle;width:40%;");
		row.cells[0].appendChild( document.createTextNode(table_row_length) );
		row.cells[1].appendChild( document.createTextNode(distTravel) );                          // create table cell 2
		row.cells[2].appendChild( document.createTextNode(duration) );                             // create table cell 3
		tbody.appendChild(row);
		var this_veh_data=this;
		row.onclick = function(){
			if(this_veh_data.MARKERS.length==this_veh_data.HISTORY_DATA.LOCATIONS.length){
				this_veh_data.SINGLE_TRIP(this);
				hi_light=false;
				highlight(this);
			}
		};
		row.onmouseover = function (){
			this.style.cursor = "pointer";
			if(!this.hilite)
				this.style.background = "#f4f4f4";
		};
		row.onmouseout = function (){
			if(!this.hilite)
				this.style.background = "#f4f4f4";
		};

	};
	this.SINGLE_TRIP=function(g_j){

		if(global_ref_obj.ButtonOnMp=='NO'){
//			var top_Div = document.createElement('div');
//			var top_Cl = new Top_center_Cl(top_Div, map);
//			map.controls[google.maps.ControlPosition.TOP_CENTER].push(top_Div);
			//g_table = document.getElementById("myTable");
			global_ref_obj.ButtonOnMp='YES';
		}
		tripview=true;
		this.SINGLE_TRIP_DATA(g_j.rowIndex);
	};
	this.SINGLE_TRIP_DATA=function(trip_row_value){
		global_ref_obj.SINGLE_HISTORY.REMOVE_ALL_POLYLINE();
		Publish_subscribe.UPDATE_POINT_UNSUB(global_ref_obj.REG_NO);
		map.setZoom(map.getZoom()+1);
		if(this.hasOwnProperty('WORKER'))
			this.WORKER.terminate();
		if(this.hasOwnProperty('TRIP_WORKER'))
			this.TRIP_WORKER.terminate();
		try{
			var trip_list_count=0;
			var trip_slist_count=0;
			var trip_must_count=0;
			this.TRIP_MARKER_LIST=[];
			this.TRIP_MUST_LIST=[];
			this.TRIP_STOPPED_LIST=[];
			for(var f=0;f<this.MARKERS.length;f++){
				this.MARKERS[f].setVisible(false);
			}
			trip_bounds= new google.maps.LatLngBounds();
			for(var m=this.PARKED_ARRAY_LIST[trip_row_value-1];m<=this.PARKED_ARRAY_LIST[trip_row_value];m++){
				this.TRIP_MARKER_LIST[trip_list_count]=this.MARKERS[m];
				trip_bounds.extend(this.MARKERS[m].getPosition());
				trip_list_count++;
			}
			var trip_start=this.PARKED_ARRAY_LIST[trip_row_value-1];
			var trip_end=this.PARKED_ARRAY_LIST[trip_row_value];
			for(var s_m=0;s_m<this.STOPPED_ARRAY_LIST.length;s_m++){
				if(trip_start<=this.STOPPED_ARRAY_LIST[s_m] && this.STOPPED_ARRAY_LIST[s_m]<=trip_end){
					this.TRIP_STOPPED_LIST[trip_slist_count]=this.MARKERS[this.STOPPED_ARRAY_LIST[s_m]];
					trip_slist_count++;
				}
			}
			for(var must_ct=0;must_ct<this.MUST_ARRAY_LIST.length;must_ct++){
				if(trip_start<=this.MUST_ARRAY_LIST[must_ct] && this.MUST_ARRAY_LIST[must_ct]<=trip_end){
					this.TRIP_MUST_LIST[trip_must_count]=this.MARKERS[this.MUST_ARRAY_LIST[must_ct]];
					trip_must_count++;
				}
			}
			map.setZoom(map.getZoom()+1);
			map.fitBounds(trip_bounds);
		}
		catch(err){
			console.log("Error in SINGLE_TRIP_DATA fn " + err.message);
		}
		var g_row = document.getElementById("myTable").rows[trip_row_value];
		var start_area;
		var stop_area;
		//hi_light=false;
		highlight(g_row);
		infobox.close(map,this.MARKERS[this.MARKERS.length-1]);
		//document.getElementById('Top_Text').innerHTML = start_area + '   '+'-> to ->'+'   ' + stop_area;
		infobox.close(map,this.MARKERS[this.MARKERS.length-1]);

		this.MARKERS[this.PARKED_ARRAY_LIST[trip_row_value-1]].position



		geocoder.geocode({'latLng':this.MARKERS[this.PARKED_ARRAY_LIST[trip_row_value-1]].position},function(results,status)                  // address for 1st latlng in trip
				{
			if(status == google.maps.GeocoderStatus.OK){
				if(results[0]){
					var area= results[0].formatted_address.split(',');
					start_area='Trip : ' +'&nbsp&nbsp;'+ area[0]+','+area[1];
				}
			}
			else{
				// alert("Geocoder failed due to: " + status);
			}
				});
		geocoder.geocode({'latLng':this.MARKERS[this.PARKED_ARRAY_LIST[trip_row_value]].position},function(results,status)                     // address for last latlng in trip
				{
			if(status == google.maps.GeocoderStatus.OK){
				if(results[0]){
					var final_area= results[0].formatted_address.split(',');
					stop_area=final_area[0]+','+final_area[1];
					document.getElementById('Top_Text').innerHTML = start_area + ' &nbsp&nbsp;  '+'&rarr;'+' &nbsp&nbsp;  '+' to'+' &nbsp&nbsp;'+' &rarr;'+'&nbsp&nbsp;' + stop_area;
				}
			}
			else
			{
				// alert("Geocoder failed due to: " + status);
			}
				});
	};
	this.TRIP_GRAPHICAL_FLOW=function(){
		if(this.hasOwnProperty('TRIP_WORKER'))
			this.TRIP_WORKER.terminate();
		//console.log('TRIP_GRAPHICAL_FLOW function');
		var trip_veh_data=this;
		var myTrip=[];
		var LatLng;
		trip_veh_data.TRIP_WORKER=new Worker(base_url+"/js/volty/js/thread.js");
		trip_veh_data.TRIP_WORKER.postMessage({'cmd': 'stop'});
		Trip_MarkerIndex=0;
		trip_veh_data.TRIP_WORKER.postMessage({'cmd': 'start', 'msg':'trip_first','msg1':parseInt(trip_veh_data.TRIP_DELAY_TIME)});
		trip_veh_data.TRIP_WORKER.addEventListener('message', function(e) {
			if('trip_first' != e.data){
				try{
					var trip_markers = trip_veh_data.TRIP_MARKER_LIST[e.data];
					if(trip_markers.getVisible()==false){
						trip_markers.setVisible(true);}
					LatLng=new google.maps.LatLng(trip_markers.position.lat(),trip_markers.position.lng());
					/**********Poly Line*************/
					if($("#visible").text()=='Hide Line'){
						myTrip.push(LatLng);
						trip_veh_data.ADD_POLYLINE(myTrip);
					}
//					else if(buslist.USERDETAILS.hasOwnProperty('POLYLINE')){
////					if(trip_veh_data.POLYLINE_VISIBLE==true){
//					if(buslist.USERDETAILS.POLYLINE==true || global_ref_obj.POLYLINE_STATUS==true){
//					myTrip.push(LatLng);
//					trip_veh_data.ADD_POLYLINE(myTrip);
//					}

//					}

				}
				catch(err){
					console.log("Trip zoom TRIP_GRAPHICAL_FLOW fn " + err.message);
				}
			}

			if(Trip_MarkerIndex <trip_veh_data.TRIP_MARKER_LIST.length){
				var trip_few_list_marker =  trip_veh_data.TRIP_MARKER_LIST[Trip_MarkerIndex];
				if(trip_few_list_marker.getVisible()==false)
					trip_few_list_marker.setVisible(true);
				trip_veh_data.TRIP_WORKER.postMessage({'cmd': 'start', 'msg':Trip_MarkerIndex,'msg1':parseInt(trip_veh_data.TRIP_DELAY_TIME)});
				Trip_MarkerIndex += step;
				try{
					for(var trip_M_Index=Trip_MarkerIndex + 1; trip_M_Index < Trip_MarkerIndex + step; trip_M_Index++){
						if(trip_M_Index < trip_veh_data.TRIP_MARKER_LIST.length){
							var few_marker =  trip_veh_data.TRIP_MARKER_LIST[trip_M_Index];
							if(few_marker.getVisible()==true)
								few_marker.setVisible(false);
						}
					}

				}
				catch(err){
					console.log("Trip zoom TRIP_GRAPHICAL_FLOW fn for loop 1" + err.message);
				}
			}
			for(var s_list_index=0;s_list_index<trip_veh_data.TRIP_STOPPED_LIST.length;s_list_index++){
				trip_veh_data.TRIP_STOPPED_LIST[s_list_index].setVisible(true);
			}
			for(var m_list_index=0;m_list_index<trip_veh_data.TRIP_MUST_LIST.length;m_list_index++){
				trip_veh_data.TRIP_MUST_LIST[m_list_index].setVisible(true);
			}
			trip_veh_data.TRIP_MARKER_LIST[trip_veh_data.TRIP_MARKER_LIST.length-1].setVisible(true);
		}, false);
	};
	this.GET_ADDRESS=function(pos){
		var geo_object=this.MARKER_GEO_OBJ;
		/*Fetch marker address and set. If already exist get from geo_object.*/
		if (geo_object.hasOwnProperty(pos.id)) {
			var id=pos.id;
			var cont="<div  id='infobox'>"+geo_object[id]+ "</div>";
			infobox.setContent(cont);
		}
		else{
			geocoder.geocode({'latLng':pos.position},function(results,status)                  // address for 1st latlng in trip
					{
				if(status == google.maps.GeocoderStatus.OK){
					if(results[0]){
						var area1= results[0].formatted_address.split(',');
						var p_area='Location &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ' +global_fn_obj.ADDRESS_FORMAT(area1);
						if(document.getElementById("infobox")){
//							if(veh_index_obj[list_index]['WATER_TANKER']==false){
//								var prev_area=document.getElementById("infobox").innerHTML;
//								document.getElementById("infobox").innerHTML=prev_area+"<br>"+p_area;
//								geo_object[pos.id]=document.getElementById("infobox").innerHTML;
//							}
						}

					}
				}
				else{
					//console.log("Geocoder failed due to: " + status);
				}
					});
		}

	};
	this.SHOW_FUEL_REPORT=function(){
		var form_data='<div class="form-group"><label class="control-label col-sm-5" for="fuel_day_start">DAY START &nbsp;&nbsp;:</label><div class="col-sm-7 padding_top panel-red"><a id="fuel_day_start"></a></div></div>'
			+'<div class="form-group"><label class="control-label col-sm-5" for="fuel_day_end">DAY END &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label><div class="col-sm-7 padding_top panel-red"><a id="fuel_day_end"></a></div></div>'
			+'<div class="form-group"><label class="control-label col-sm-5" for="fuel_refuel">REFUEL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label><div class="col-sm-7 padding_top panel-red"><a id="fuel_refuel"></a></div></div>'
			+'<div class="form-group"><label class="control-label col-sm-5" for="fuel_consume">CONSUMED &nbsp;:</label><div class="col-sm-7 padding_top panel-red"><a id="fuel_consume"></a> </div></div>'
			+'<div class="form-group"><label class="control-label col-sm-5" for="fuel_theft">THEFT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label><div class="col-sm-7 padding_top panel-red"><a id="fuel_theft" ></a></div></div>'
			+'<div class="form-group"><label class="control-label col-sm-5" for="fuel_mileage">MILEAGE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</label><div class="col-sm-7 padding_top panel-red"><a id="fuel_mileage" ></a></div></div>';
		$('#fuel_report_form').append(form_data);
		var History_data=this.HISTORY_DATA;
		var fuel_data=History_data.FUELDATA;
		var consumed=(parseFloat(fuel_data.START)+parseFloat(fuel_data.FILLED)-parseFloat(fuel_data.END)).toFixed(3);
		var mileage=0;
		if(consumed<1){
			consumed=0;
			mileage=0;
		}else{
			mileage=(parseFloat(History_data.TOTDIST)/consumed).toFixed(3);
		}
		$('#fuel_day_start').text(parseFloat(fuel_data.START).toFixed(3)+' Ltr');
		$('#fuel_day_end').text(parseFloat(fuel_data.END).toFixed(3)+' Ltr');
		$('#fuel_refuel').text(parseFloat(fuel_data.FILLED).toFixed(3)+' Ltr');
		$('#fuel_consume').text(consumed+' Ltr');
		var running_theft=0.000;
		if(fuel_data.hasOwnProperty('RTHEFT')){
			running_theft=fuel_data.RTHEFT;
		}
		var theft=parseFloat(running_theft+fuel_data.THEFT).toFixed(3);
		if(theft<1){
			theft=0;
		}
		$('#fuel_theft').text(theft+' Ltr');
		if(History_data.hasOwnProperty('TNKDETAILS')){
			var main_mileage=parseFloat(History_data.TNKDETAILS.MILEAGE);
			if(mileage>main_mileage){
				mileage=main_mileage+'+';
			}
			if(mileage<(main_mileage/2)){
				mileage= '<'+(main_mileage/2);
			}
		}
		$('#fuel_mileage').text(mileage+' Ltr');
	}
};
function MULTIPLE_DAY_REPORT(FROM_DATE,TO_DATE){
	this.FROM_DATE=FROM_DATE;
	this.TO_DATE=TO_DATE;
	this.P_DATE=FROM_DATE;
	this.TRIP_NO=0;
	this.TRIP_DATE='';
	this.UPDATE=true;
	this.BASIC_PDF=function(view_download){
		var download_obj=(global_fn_obj.GET_PDF_HEADER()).HEADER;
		var columns={'columns':[{color:'black','text':'ACCOUNT       :  '+global_ref_obj.USER_COMPANY_NAME+'\nREG NO           :  '+global_ref_obj.REG_NO+'\nVEH NAME     :  '+global_ref_obj.VEH_NAME},{color:'black','text':'FROM DATE          :  '+this.FROM_DATE+'\nTO DATE                :  '+this.TO_DATE}]};
		download_obj.content.push(columns);
		download_obj.content.push(global_fn_obj.GET_LINE());
		var table_obj={};
		table_obj.style='table_style';
		table_obj.layout='headerLineOnly';
		table_obj.table={};
		table_obj.table.headerRows=1;
		table_obj.table.widths= [25,80,80,35,35,50,50,35,50];
		table_obj.table.body=[];
		var table_body_array=[{ text:'S NO', style: 'tableHeader' },{ text:'START POINT', style: 'tableHeader' },{ text:'END POINT', style: 'tableHeader' },{ text:'START TIME', style: 'tableHeader' },{ text:'END TIME', style: 'tableHeader' },{ text:'IDLING TIME', style: 'tableHeader' },{ text:'DURATION', style: 'tableHeader' },{ text:'AVG SPEED', style: 'tableHeader' },{ text:'DISTANCE TRAVELLED', style: 'tableHeader'}];
		table_obj.table.body.push(table_body_array);
		download_obj.content.push(table_obj);
		download_obj.styles={'table_style':{fontSize: 9,alignment:'center',margin:[0,0,0,20]},'tableHeader': {bold: true,fontSize: 9,color: 'white',margin: [0, 3],fillColor:'#585858'},'Header_content':{fillColor: '#FF9900',alignment:'center',color:'white'},'header_table_style':{margin:[0,0,0,0]},'first_color':{fillColor: '#e4e4e4'},};
		this.DOWNLOAD=download_obj;
		this.REQ_SER();
	};
	this.REQ_SER=function(){
		if(this.UPDATE==true){
			if(this.P_DATE==this.FROM_DATE){
				global_fn_obj.REMOVE_TABLE('multiple_day_reports');
			}
			if(this.P_DATE==this.TO_DATE){
				Publish_subscribe.REQ_TYPE('MULTIPLE_HISTORY',this.P_DATE);
				this.UPDATE=false;
				websocketclient.unsubscribe('VOLTY/RES/MULTIHISTORY/'+global_ref_obj.CID);
			}else{
				Publish_subscribe.REQ_TYPE('MULTIPLE_HISTORY',this.P_DATE);
				this.P_DATE=global_fn_obj.GET_DATE_MIN_PLUS(this.P_DATE,'PLUS',1)		
			}
		}
	};
	this.SHOW_DATA=function(DATA){
		this.REQ_SER();
		//global_fn_obj.UI_UNBLOCK();
		if(DATA.hasOwnProperty('ERROR')){
			return;
		}
		this.TRIP_DATE=DATA.DATE;
		this.TRIP_NO=0;
		var s_dist=0;
		var p_sec=0;
		var trip_idling=0;
		var latlng_name='NO';
		var LOCATIONS=DATA.LOCATIONS;
		var p_latlng=LOCATIONS[0].XY.split(',');
		bounds.extend(new google.maps.LatLng(p_latlng[0], p_latlng[1]));
		p_latlng=latlng_rnd(p_latlng);
		if(LOCATIONS[0].hasOwnProperty('DEST')){
			latlng_name=LOCATIONS[0].DEST;
		}
		else if(LOCATIONS[0].hasOwnProperty('GC')){
			latlng_name=LOCATIONS[0].GC;
		}
		var prevTS = LOCATIONS[0].T.split(':');
		var prevInfo;
		try{
			prevInfo= LOCATIONS[0].I.split(' ');
		}catch(err){
			prevInfo=['NORMAL:','0'];
		}
		if(prevInfo[0]=='NORMAL' || prevInfo[0]=='NORMAL:'){
			if(prevInfo[2]=='MISS'){
				/*1st point MISS.*/
				DATA.LOCATIONS[0].I='NORMAL: '+prevInfo[1];
			}
		}
		else if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])>global_ref_obj.PT){
			trip_idling=trip_idling+parseInt(prevInfo[3]);
		}
		else if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])<=global_ref_obj.PT){
			trip_idling=trip_idling+parseInt(prevInfo[3]);
		}
		for(var i=1;i<LOCATIONS.length;i++){
			var currTS =LOCATIONS[i]["T"].split(':');
			var currInfo;
			try{
				currInfo = LOCATIONS[i]["I"].split(' ');
			}catch(err){
				currInfo=['NORMAL:','0'];
			}
			var n_latlng=LOCATIONS[i]["XY"].split(',');
			n_latlng=latlng_rnd(n_latlng);

			if(currInfo[0]=='STOPPED:' && currInfo[1]>global_ref_obj.PT){
				trip_idling=trip_idling+parseInt(currInfo[3]);
				//Calculate trip duration
				var  prevSec = parseInt(prevTS[0]) * 3600 + parseInt(prevTS[1]) * 60 + parseInt(prevTS[2]);
				if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])<=global_ref_obj.PT){
					prevSec +=float_min_toSec(prevInfo[1]);
				}
				else if(prevInfo[0]=='STOPPED:' && parseFloat(prevInfo[1])>global_ref_obj.PT){
					p_sec =float_min_toSec(prevInfo[1]);
				}
				var currSec = parseInt(currTS[0]) * 3600 + parseInt(currTS[1]) * 60 + parseInt(currTS[2]);
				if(currSec<global_ref_obj.DAY_START){
					currSec+=24*60*60;
				}
				prevSec+=p_sec;
				if(prevSec<global_ref_obj.DAY_START){
					prevSec+=24*60*60;
				}
				currSec =  (currSec - prevSec)/ 60;
				var distTravel = (currInfo.length > 2) ? (parseFloat(currInfo[2])+parseFloat(s_dist)) : 0 ;
				distTravel=Math.round(distTravel* 10) / 10;
				var averageSpeed = distTravel / (currSec/60);
				var duration=parseInt(Math.round(currSec* 100) / 100);
				var avgSd=Math.round(averageSpeed* 10) / 10;

				var arr = new Array();
				var timeS=sec_to_hh_mm_ss(prevSec);

				arr[0]=timeS;
				arr[1]=currTS.join(':');
				var time=arr.join(' - ');
				distTravel=Math.round(distTravel * 100) / 100;
				prevTS = currTS;
				prevInfo = currInfo;
				latlng_name=this.SHOW_DEST_NAME(latlng_name, LOCATIONS[i], distTravel, duration, avgSd, arr, p_latlng, n_latlng, trip_idling);
				p_latlng=n_latlng;
				s_dist=0;
				trip_idling=0;
			}
			else if(currInfo[0]=='STOPPED:' && parseFloat(currInfo[1])<=global_ref_obj.PT){
				s_dist+=parseFloat(currInfo[2]);
				trip_idling=trip_idling+parseInt(currInfo[3]);
			}
		}
		var l_latlng=LOCATIONS[LOCATIONS.length-1]["XY"].split(',');
		l_latlng=latlng_rnd(l_latlng);
		var LastInfo;
		try{
			LastInfo  = LOCATIONS[LOCATIONS.length-1]["I"].split(' ');
		}catch(err){
			LastInfo=['NORMAL:','0'];
		}
		var LastInfo_ts  = LOCATIONS[LOCATIONS.length-1]["T"].split(':');
		var prev_sec=parseInt(prevTS[0]) * 3600 + parseInt(prevTS[1]) * 60 + parseInt(prevTS[2]);
		if(prevInfo[0]=='STOPPED:'){
			prev_sec+=float_min_toSec(prevInfo[1]);
		}
		if(LastInfo[0]=='NORMAL' || LastInfo[0]=='NORMAL:'){
			var cur_sec=parseInt(LastInfo_ts[0]) * 3600 + parseInt(LastInfo_ts[1]) * 60 + parseInt(LastInfo_ts[2]);
			if(cur_sec<global_ref_obj.DAY_START){
				cur_sec+=24*60*60;
			}
			if(prev_sec<global_ref_obj.DAY_START){
				prev_sec+=24*60*60;
			}
			cur_sec=(cur_sec-prev_sec)/60;
			var dur=parseInt(Math.round(cur_sec* 100) / 100);
			var array = new Array();
			var timeS=sec_to_hh_mm_ss(prev_sec);
			array[0]=timeS;
			array[1]=LastInfo_ts.join(':');
			if(!isNaN(parseFloat(LastInfo[2]))){
				var distTravel = parseFloat(LastInfo[2])+s_dist;
				distTravel=Math.round(distTravel* 10) / 10;
				var averageSpeed = distTravel / (cur_sec/60);
				var avgSd=Math.round(averageSpeed* 10) / 10;
				this.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], distTravel, dur, avgSd, array, p_latlng, n_latlng, trip_idling);
			}
			else{
				this.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], 'TRIP', dur, "On Going", array, p_latlng, n_latlng, trip_idling);
			}
		}
		else if(LastInfo[0]=='STOPPED:' && parseFloat(LastInfo[1])>global_ref_obj.ST && parseFloat(LastInfo[1])<=global_ref_obj.PT){
			var cur_sec=parseInt(LastInfo_ts[0]) * 3600 + parseInt(LastInfo_ts[1]) * 60 + parseInt(LastInfo_ts[2]);
			if(cur_sec<global_ref_obj.DAY_START){
				cur_sec+=24*60*60;
			}
			if(prev_sec<global_ref_obj.DAY_START){
				prev_sec+=24*60*60;
			}
			cur_sec=(cur_sec-prev_sec)/60;
//			if(this.STOPPED_ARRAY_LIST[this.STOPPED_ARRAY_LIST.length-1]==(LOCATIONS.length-1)){
//			var distTravel=s_dist;
//			}
//			else{
			var distTravel = (LastInfo.length > 2) ? parseFloat(LastInfo[2])+s_dist : 0 ;
//			}
			distTravel=Math.round(distTravel* 10) / 10;
			var averageSpeed = distTravel / (cur_sec/60);
			var avgSd=Math.round(averageSpeed* 10) / 10;
			var dur=parseInt(Math.round(cur_sec* 100) / 100);
			var array = new Array();
			var timeS=sec_to_hh_mm_ss(prev_sec);
			array[0]=timeS;
			array[1]=LastInfo_ts.join(':');
			var time=array.join(' - ');
			if(i!=1){
				trip_idling=trip_idling+parseInt(LastInfo[3]);
				this.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], distTravel, dur, avgSd, array, p_latlng, n_latlng, trip_idling);
			}
		}
		else if(LastInfo[0]=='STOPPED:' && parseFloat(LastInfo[1])<=global_ref_obj.ST){
			var cur_sec=parseInt(LastInfo_ts[0]) * 3600 + parseInt(LastInfo_ts[1]) * 60 + parseInt(LastInfo_ts[2]);
			if(cur_sec<global_ref_obj.DAY_START){
				cur_sec+=24*60*60;
			}
			if(prev_sec<global_ref_obj.DAY_START){
				prev_sec+=24*60*60;
			}
			cur_sec=(cur_sec-prev_sec)/60;
			var dur=parseInt(Math.round(cur_sec* 100) / 100);
			var array = new Array();
			var timeS=sec_to_hh_mm_ss(prev_sec);
			array[0]=timeS;
			array[1]=LastInfo_ts.join(':');
			trip_idling=trip_idling+parseInt(LastInfo[3]);
			this.SHOW_DEST_NAME(latlng_name, LOCATIONS[LOCATIONS.length-1], 'Trip',dur,"On Going", array, p_latlng, n_latlng, trip_idling);
		}
	}
	this.SHOW_DEST_NAME=function(latlng_name,loc_obj,distTravel,duration,avgSd,arr,p_latlng,n_latlng,trip_idling){
		if(latlng_name!='NO' && loc_obj.hasOwnProperty('DEST')){
			this.TRIP_REPORT_TABLE(distTravel,duration,avgSd,arr[0],arr[1],latlng_name,loc_obj['DEST'],trip_idling);
			latlng_name=loc_obj['DEST'];
		}
		else if(loc_obj.hasOwnProperty('DEST')){
			this.TRIP_REPORT_TABLE(distTravel,duration,avgSd,arr[0],arr[1],p_latlng,loc_obj['DEST'],trip_idling);
			latlng_name=loc_obj['DEST'];
		}
		else if(latlng_name!='NO' && loc_obj.hasOwnProperty('GC')){
			this.TRIP_REPORT_TABLE(distTravel,duration,avgSd,arr[0],arr[1],latlng_name,loc_obj['GC'],trip_idling);
			latlng_name=loc_obj['GC'];
		}
		else if(latlng_name=='NO' && loc_obj.hasOwnProperty('GC')){
			this.TRIP_REPORT_TABLE(distTravel,duration,avgSd,arr[0],arr[1],p_latlng,loc_obj['GC'],trip_idling);
			latlng_name=loc_obj['GC'];
		}
		else if(latlng_name!='NO'){
			this.TRIP_REPORT_TABLE(distTravel,duration,avgSd,arr[0],arr[1],latlng_name,n_latlng,trip_idling);
			latlng_name='NO';
		}
		else{
			latlng_name='NO';
			this.TRIP_REPORT_TABLE(distTravel,duration,avgSd,arr[0],arr[1],p_latlng,n_latlng,trip_idling);
		}
		return latlng_name;
	};
	this.TRIP_REPORT_TABLE=function(distTravel,duration,avgSd,start,stop,p_latlng,n_latlng,trip_idling){
		if(this.TRIP_NO==0){
			var alt_color='first_color';
			var tbody = document.getElementById("multiple_day_reports").getElementsByTagName("thead")[0];
			var row = document.createElement("tr"); // create row
			var abc=row.appendChild(document.createElement('td'));
			abc.setAttribute("colSpan",9);
			abc.setAttribute('style','background-color:#e4e4e4');
			row.cells[0].appendChild(document.createTextNode(this.TRIP_DATE));
			var table_body_array=new Array();
			table_body_array.push({text:(this.TRIP_DATE).toString(),colSpan: 9,style:alt_color});
			this.DOWNLOAD.content[4].table.body.push(table_body_array);
			tbody.appendChild(row);
			this.TRIP_NO++;
		}
		var alt_color='sec_color';
		if(duration<0){
			avgSd='---';
		}
		duration=global_fn_obj.DURATION(duration);
		if(distTravel<0){
			distTravel="---";
		}
		var tbody = document.getElementById("multiple_day_reports").getElementsByTagName("thead")[0];
		var row = document.createElement("tr"); // create row
		row.appendChild(document.createElement('td')).setAttribute("style","width:2%;");
		row.appendChild(document.createElement('td'));
		row.appendChild(document.createElement('td'));
		row.appendChild(document.createElement('td'));
		row.appendChild(document.createElement('td'));
		row.appendChild(document.createElement('td'));
		row.appendChild(document.createElement('td')).setAttribute("style","width:12%;");
		row.appendChild(document.createElement('td'));
		row.appendChild(document.createElement('td'));
		row.cells[0].appendChild(document.createTextNode(this.TRIP_NO));
		row.cells[1].appendChild(document.createTextNode(p_latlng));
		row.cells[2].appendChild(document.createTextNode(n_latlng));
		row.cells[3].appendChild(document.createTextNode(start));
		row.cells[4].appendChild(document.createTextNode(stop));
		row.cells[5].appendChild(document.createTextNode(global_fn_obj.DURATION(Math.floor(trip_idling))));
		row.cells[6].appendChild(document.createTextNode(duration));
		row.cells[7].appendChild(document.createTextNode(avgSd));
		row.cells[8].appendChild(document.createTextNode(distTravel));
		tbody.appendChild(row);
		var table_body_array=new Array();
		table_body_array.push({text:(this.TRIP_NO).toString(),style:alt_color});
		table_body_array.push({text:(p_latlng).toString(),style:alt_color});
		table_body_array.push({text:(n_latlng).toString(),style:alt_color});
		table_body_array.push({text:(start).toString(),style:alt_color});
		table_body_array.push({text:(stop).toString(),style:alt_color});
		table_body_array.push({text:(global_fn_obj.DURATION(Math.floor(trip_idling))).toString(),style:alt_color});
		table_body_array.push({text:(duration).toString(),style:alt_color});
		table_body_array.push({text:(avgSd).toString(),style:alt_color});
		table_body_array.push({text:(distTravel).toString(),style:alt_color});
		if(table_body_array.length==9){
			this.DOWNLOAD.content[4].table.body.push(table_body_array);
		}
		this.TRIP_NO++;
	};
	this.BASIC_PDF();
}
var global_fn_obj={
		'SET_STEP_LEVEL':function(){
			if(map.getZoom() >= 16){
				step=1;
			}
			else if(map.getZoom() >= 14){
				step=2;
			}
			else if(map.getZoom() >= 13){
				step=3;
			}
			else if(map.getZoom() >= 12){
				step=4;
			}
			else if(map.getZoom() >= 10){
				step=6;
			}
			else if(map.getZoom() >= 7){
				step=25;
			}
			else{
				step=30;
			}
		},
		'INFOBOX_CONTENT':function(info,p_location,p_latest_timestamp,p_time_stopped,p_idling){
			var p_html_content;
			var image=base_url+'/images/volty/car.png';
			if(info=='PARKED'){
				p_html_content="<div  id='infobox'>" + "  Time &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : "+ p_latest_timestamp + "<br>"+ "PARKED &nbsp;&nbsp;&nbsp;: "+ p_time_stopped  +"<br>"+ "  Idling  Time  : "+ Math.floor(p_idling) +" mins";
			}
			else if(info=='STOPPED'){
				p_html_content="<div  id='infobox'>" + "Time &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  : "+ p_latest_timestamp + "<br>"+ "STOPPED &nbsp;:" + " " + parseInt(p_time_stopped) +" mins"+ "<br>"  + "Idling  Time : "+ Math.floor(p_idling) +" mins";
			}
			else if(info=='NORMAL'){
				p_html_content="<div  id='infobox'>"+"Time &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+ p_latest_timestamp+"<br/>" + "Speed &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+ parseInt(p_time_stopped) +" kmph";
			}
			if(p_location.hasOwnProperty('XY')){
				var point_latlng=(p_location.XY).split(',');
				p_html_content+="<br>"+"LAT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+parseFloat(point_latlng[0]).toFixed(6);
				p_html_content+="<br>"+"LNG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+parseFloat(point_latlng[1]).toFixed(6);
//				p_html_content+="<br>"+"IMAGE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;; : "+"<img width='80' class='expand' src=" + image + ">";
//				p_html_content+="<br>"+"IMAGE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+'<a class="image_popup" href="'+image+'"><image src="'+image+'" style="width:100px;height:100px;"></a>';
				
				
				
				/*$('.image_popup').magnificPopup({
					type: 'image',mainClass: 'mfp-with-zoom', // this class is for CSS animation below

					zoom: {
						enabled: true, // By default it's false, so don't forget to enable it

						duration: 300, // duration of the effect, in milliseconds
						easing: 'ease-in-out', // CSS transition easing function

						// The "opener" function should return the element from which popup will be zoomed in
						// and to which popup will be scaled down
						// By defailt it looks for an image tag:
						opener: function(openerElement) {
							// openerElement is the element on which popup was initialized, in this case its <a> tag
							// you don't need to add "opener" option if this code matches your needs, it's defailt one.
							return openerElement.is('img') ? openerElement : openerElement.find('img');
						}
					},gallery:{
						enabled:true
					}
					// other options
				});*/
			}
			if(p_location.hasOwnProperty('A')){
				if(p_location['A']!='STOPPED'){
					if(p_location['A']=='FUEL FILLED'){
						if(p_location.hasOwnProperty('FF')){
							if(p_location.FF>0){
								p_html_content+="<br>"+"Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+"<span style='font-size:10px;width:30px'>"+p_location['A']+' '+parseInt(p_location['FF'])+' liters'+"</span>";
							}
						}else{
							if(p_location['A']!='GPS DEVICE DISCONNECTED')
								p_html_content+="<br>"+"Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+"<span style='font-size:10px;width:30px'>"+p_location['A']+"</span>";
						}
					}
					else if(p_location['A']=='FUEL THEFT'){
						if(global_ref_obj.FUEL_ALERTS==true){
							if(p_location.hasOwnProperty('TF')){
								if(p_location.TF>0){
									p_html_content+="<br>"+"Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+"<span style='font-size:10px;width:30px'>"+p_location['A']+' '+parseInt(p_location['TF'])+' liters'+"</span>";
								}
							}else{
								if(p_location['A']!='GPS DEVICE DISCONNECTED')
									p_html_content+="<br>"+"Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+"<span style='font-size:10px;width:30px'>"+p_location['A']+"</span>";
							}
						}
					}
					else{
						if(p_location['A']!='GPS DEVICE DISCONNECTED')
							p_html_content+="<br>"+"Alert&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+"<span style='font-size:10px;width:30px'>"+p_location['A']+"</span>";
					}
				}
			}
			if(p_location.hasOwnProperty('TEMPARRAY')){
				p_html_content+="<br>"+"TEMP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+p_location.TEMPARRAY[p_location.TEMPARRAY.length-1].V+' \xB0 C';
			}
			else if(p_location.hasOwnProperty('TEMP')){
				if(Array.isArray(p_location.TEMP)){
					p_html_content+="<br>"+"TEMP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+p_location.TEMP[p_location.TEMP.length-1].V+' \xB0 C';
				}else{
					p_html_content+="<br>"+"TEMP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : "+p_location['TEMP']+' \xB0 C';
				}
			}
			return p_html_content+"</div>";
		},
		'SET_NORMAL_ICON':function(speed,first_latlng,second_latlng){
			var arrow_colour=global_fn_obj.GET_ARROW_COLOR(speed);
			if(first_latlng=='CIRCLE'){
				iconImg = {
						path: google.maps.SymbolPath.CIRCLE,
						fillColor: arrow_colour,
						fillOpacity: 0.8,
						scale: 3,
						strokeColor: arrow_colour,
//						strokeColor: 'red',
						strokeWeight: 2
				};
				return iconImg;
			}
			iconImg = {
					path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
					fillColor: arrow_colour,
					fillOpacity: 0.8,
					scale: 3,
					strokeColor: arrow_colour,
//					strokeColor: 'red',
					strokeWeight: 2,
					rotation:global_fn_obj.GET_ANGLE(first_latlng,second_latlng)
			};
			return iconImg;
		},
		'GET_ANGLE':function(f_point,s_point){
			var angle = 0;
			var rlat1 = toRad(f_point.lat());
			var rlog1 = toRad(f_point.lng());
			var rlat2 = toRad(s_point.lat());
			var rlog2 = toRad(s_point.lng());
			var logDiff = rlog2 - rlog1;

			angle = Math.atan2(Math.sin(logDiff) * Math.cos(rlat2), ((Math.cos(rlat1) * Math.sin(rlat2)) - ((Math.sin(rlat1) * Math.cos(rlat2)) * Math.cos(logDiff))));
			angle = toDeg(angle);// to degrees
			angle = (angle + 360) % 360;
			return angle;
		},
		'GET_ARROW_COLOR':function(given_speed){
			/*Assign color for marker arrows.*/
			if(parseInt(given_speed)>=veh_spd_range[1]){
				arrow_colour="#CC0099";
			}
			else if(parseInt(given_speed)>veh_spd_range[0] && parseInt(given_speed)<veh_spd_range[1]){
				arrow_colour="#FF0000";
			}
			else if(parseInt(given_speed)<=veh_spd_range[0]){
				arrow_colour="#055144";
			}else{
				arrow_colour="#055144";
			}
			return arrow_colour;
		},
		'ADDRESS_FORMAT':function(area){
			var final_address;
			if(area.length>5){
				/*if address contain 'Unnamed Road' remove it.*/
				if(area[0]=='Unnamed Road'){
					final_address= area[1]+','+area[2]+','+area[3];
				}else{
					final_address= area[0]+','+area[1]+','+area[2];
				}
			}else{
				if(area[0]=='Unnamed Road'){
					if(area.length>2){
						final_address= area[1]+','+area[2];
					}else{
						final_address= area[1];
					}
				}else{
					final_address= area[0]+','+area[1];
				}
			}
			return final_address;
		}
}
function Top_center_Cl(b_Div, map){
	b_Div.style.padding = '5px';
	var b_UI = document.createElement('div');
	if(isFirefox==true || isIE==true){
		b_UI.className="home_control_button_mozilla";
	}
	else{
		b_UI.className="home_control_button_chrome";
	}
	b_Div.appendChild(b_UI);
	var b_Text = document.createElement('div');
	b_Text.className="bottom_left";
	b_Text.id='Top_Text';
	b_Text.innerHTML='REG NO : '+global_ref_obj.REG_NO;
	b_UI.appendChild(b_Text);
}
function Bottom_right_Cl(br_Div, map){
	var History_data=global_ref_obj.SINGLE_HISTORY.HISTORY_DATA;
	br_Div.style.padding = '5px';
	var br_UI = document.createElement('div');
	if(isFirefox==true || isIE==true){
		br_UI.className="home_control_button_mozilla";
	}
	else{
		br_UI.className="home_control_button_chrome";
	}
	br_Div.appendChild(br_UI);
	var br_Text = document.createElement('div');
	global_ref_obj.BOTTOM_C_BUTTON=br_Text;
	br_Text.className="bottom_right";
	br_Text.innerHTML = 'Total Idling Time : '+ parseInt(History_data.IDLETIME) +' min'+'<br>'+'Total Distance &nbsp;&nbsp;&nbsp; : '+ parseFloat(History_data.TOTDIST).toFixed(2) +' km';
	br_UI.appendChild(br_Text);
}
/**
 * Rounding latlng to 4 digits.
 * @method latlng_rnd
 * @for trip
 * @static
 * @param p_latlng latlng value
 * @return  rounded latlng of 4 digits
 */
function latlng_rnd(p_latlng){
	p_latlng[0]=(Math.round(parseFloat(p_latlng[0])* 10000) / 10000).toFixed(4);
	p_latlng[1]=(Math.round(parseFloat(p_latlng[1])* 10000) / 10000).toFixed(4);
	return p_latlng.join(',');
}
function sec_to_hh_mm_ss(prevSec){
	var hours   = Math.floor(prevSec / 3600);
	var minutes = Math.floor((prevSec - (hours * 3600)) / 60);
	var seconds = prevSec - (hours * 3600) - (minutes * 60);

	if(hours>=24){
		hours=hours-24;
	}
	if (hours   < 10) {hours   = "0"+hours;}
	if (minutes < 10) {minutes = "0"+minutes;}
	if (seconds < 10) {seconds = "0"+seconds;}

	return  hours+':'+minutes+':'+seconds;
}
/**
 * Converts numeric degrees to radians
 * @method toRad
 * @for angle
 * @static
 * @param Value  lat or lng value
 * @return {Number}
 *
 */
function toRad(Value){
	return Value * Math.PI / 180;                                                /** Converts numeric degrees to radians */
}
/**
 * Converts to numeric degrees.
 * @method toDeg
 * @for angle
 * @static
 * @param Value  number
 * @return {Number}
 *
 */
function toDeg(Value){
	return Value * 180 / Math.PI;
}
/**
 * Addition of minutes and timestamp done here.
 * @method minutes
 * @for SET_MARKER_ICONS
 * @static
 * @param min   no. of minutes
 * @param t_stamp  timestamp format string
 * @return
 *
 */
function minutes(min,t_stamp){
	t_stamp=t_stamp.split(':');
    t_stamp = parseInt(t_stamp[0]) * 3600 + parseInt(t_stamp[1]) * 60 + parseInt(t_stamp[2]);
    var min_float=min.toString().split(".");
    if(min_float[1]){
    	var Sec = min_float[0] * 60+parseInt((min_float[1] * 60)/100) ;
    }
    else{
    	var Sec = min_float[0] * 60;
    }
	t_stamp=Sec+t_stamp;
	var hours   = Math.floor(Sec / 3600);
	var minutes = Math.floor((Sec - (hours * 3600)) / 60);
	var seconds = Sec - (hours * 3600) - (minutes * 60);

	if (hours   < 10) {hours   = "0"+hours;}
	if (minutes < 10) {minutes = "0"+minutes;}
	if (seconds < 10) {seconds = "0"+seconds;}
	if(hours==00){
		time_stopped  = minutes+ ' mins';
	}
	else{
		time_stopped  = hours+ ' hrs '+minutes+ ' mins';
	}
	var obj={};
	obj.STOPPED_TIME=time_stopped;
	obj.TIME_STAMP=sec_to_hh_mm_ss(t_stamp);
	return obj;
}
var infobox;
infobox = new InfoBox({
    disableAutoPan: false,
    maxWidth: 150,
    pixelOffset: new google.maps.Size(-140, 0),
    zIndex: null,
    boxStyle: {
       background: "url(base_url+'/images/volty/tipbox.gif') no-repeat",//'<img  src=base_url+"/images/volty/tipbox.png" />',//"url('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif') no-repeat",
       opacity: 0.75,
       width: "240px",
       textAlign: "left",
       padding:"0px 0px 0px 40px"
   },
   closeBoxMargin: "10px 2px 2px 2px",
   closeBoxURL: base_url+"/images/volty/close.gif",//http://www.google.com/intl/en_us/mapfiles/close.gif",
   infoBoxClearance: new google.maps.Size(1, 1)
});