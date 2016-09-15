<div role="dialog" class="modal fade" id="mapmodel">
							<div class="modal-dialog">

								<!-- Modal content-->
								<div class="modal-content registeration">
									<div class="modal-header">
										<button data-dismiss="modal" class="close" type="button">×</button>
										<link rel="stylesheet" href="{{ asset('/css/volty/style.css') }}" type='text/css' />
										<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBnRy371oDNcVsh4uSMYfBpA-8BJ5anB5s&libraries=geometry&libraries=places"></script>
										{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}										
										@if($routeName != 'consignment_pickup')
										<!-- script src="{{ asset('/js/volty/jquery-1.11.0.min.js') }}"></script-->
										@endif
										<script type="text/javascript" src="{{ asset('/js/volty/infobox.js') }}"></script>
										<script src="{{ asset('/js/volty/veh_history.js') }}"></script>
										<script src="{{ asset('/js/volty/onload.js') }}" type="text/javascript"></script>
										<h4 class="modal-title"></h4>
									</div>
									<div class="modal-body">
										<div id="gmap" class="gmap" style="position: relative; width:100%; height:500px;"> </div>
									</div>
									<div class="modal-footer">
									</div>
								</div>

							</div>
						</div>