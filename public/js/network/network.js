var isFilterEnable;
$(document).ready(function(){
	
	$("#txtFdDtFrm").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        //show_flexible: 1,
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#txtFdDtTo").datepicker(
                "option", "minDate", selectedDate);
        }
    });

    $("#txtFdDtTo").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        //show_flexible: 1,
        minDate: 0,
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#txtFdDtFrm").datepicker("option",
                "maxDate", selectedDate);
        }
    });
    
    // Clearing fromdate & todate on backspace
    $("#txtFdDtFrm, #txtFdDtTo").keypress(function (e)
	{
	  	switch(e.keyCode) { 
			case 46:  // delete
			case 8:  // backspace
				$(this).val('');
				break;
			default:
				e.preventDefault();
				break;
	    }
	});

	$('.network').on('click', '.inner-tabs a', function(){
		var currtab = $(this).attr('href');
		if(currtab == '#feed-menu1'){
			$("#txtNewsDesc").focus();
		}
		else if(currtab == '#feed-menu2'){
			$("#txtJobTitle").focus();
		}else if(currtab == '#feed-menu3'){
			$("#txtArtTitle").focus();
		}
	});

	//Post feed,article, job
	$("#btnPostFeed, #btnPostJob, #btnPublishArticle").click(function(){
		var curTbType = $(this).attr('data-type');
		var tabPrefix = $(this).attr('data-prefix');
		var currURL = $(this).attr('data-url');
		var reqData = {
	        ptitle: $("#txt"+tabPrefix+"Title").val(),
	        pdesc: $("#txt"+tabPrefix+"Desc").val(),
	        feedtype: curTbType
	    }
		$.ajax({
	        type: "POST",
	        url: currURL,
	        data: reqData,  
	        cache: false,
	        dataType: 'json', 
	        success: function(resData){
	        	
	        	if(resData.success){
	        		$.post("./network/ajxshowpost", {feedid:resData.feedId}, function(result){
	        			if(isFilterEnable == ''){
	        				$("#feedDispBlk").prepend(result);
	        				$("#noResults").hide();
	        			}else{
	        				window.location.href = '/network';
	        			}
				    });
	        		$("#frm"+curTbType).trigger('reset');
	            }else if(resData.success == false){
	            	//Checking post type
	            	if(curTbType == 'job'){
	            		$("#postJobTitleErr").html(resData.errors['ptitle']);
	            		$("#postJobDescErr").html(resData.errors['pdesc']);
	            	}else if(curTbType == 'article'){
	            		$("#postArtTitleErr").html(resData.errors['ptitle']);
	            		$("#postArtDescErr").html(resData.errors['pdesc']);
	            	}else{
	            		$("#postFeedErr").html(resData.errors['pdesc']);
	            	}
	            }else{
	            	alert("Please reload the page and try again.");
	            }
	        },
	        error: function(data) {
	            alert("Invalid data");
	        }
	    });
	});

	// Post Comments
	$('#feedDispBlk').on('keydown', '.clsFeedSingleBlk input[type=text]', function(e){
		var feedid = $(this).attr('data-feedid');
		var currURL = $(this).attr('data-url');
		var currComment = $(this).val();
	    var currObj = $(this);
	    if(e.keyCode == 13 && currComment !=''){
	    	var curUser = $(this).attr('data-curuser');
	        $.ajax({
		        type: "POST",
		        url: currURL,
		        data: {feedid:feedid,comment:currComment},  
		        cache: false,
		        dataType: 'json', 
		        success: function(resData){
		        	if(resData.success){
		        		currObj.val('');
		        		var prevComCnt = $("#feedLinksBlk"+feedid+" .comments").attr('data-comcnt');
		        	comHtml = '<div class="col-md-12 padding-none form-control-fld" id="hidecomment'+resData.commInfo["id"]+'">';
  					comHtml += '<div class="user-pic pull-left"><i class="fa fa-user"></i></div>';
  					comHtml += '<span class="user-name pull-left">';
  					comHtml += '<strong>'+curUser+' : </strong><span id="commentid_'+resData.commInfo["id"]+'">'+resData.commInfo['comments']+'</span></span> <div class="pull-right">'
  					+'<a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" onclick="editcomment('+feedid+','+resData.commInfo["id"]+','+resData.commInfo["created_by"]+')" class="edit_this_line edit"><i class="fa fa-edit" title="Edit"></i></a>'
  					+'<a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment('+resData.commInfo["id"]+','+resData.commInfo["created_by"]+')" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;'+resData.commInfo['created_at']+'</div></div></div>';
		        		$("#ajxLoadFeedCom"+feedid).append(comHtml);
		        		$("#feedLinksBlk"+feedid+" .comments span").html(parseInt(prevComCnt)+1);
		        		$("#feedLinksBlk"+feedid+" .comments").attr('data-comcnt',parseInt(prevComCnt)+1);
		            }else{
		            	$("#lblErrComment"+feedid).html(resData.errors['comment']);
		            }
		        },
		        error: function(data) {
		            alert("Invalid data");
		        }
	    	});
	    }
	});
	

	// Post Like/Unlike
	$('#feedDispBlk').on('click', '.feed-links .clsLike', function(){
		var curFeedId = $(this).parent().attr('data-id');
		var currURL = $(this).attr('data-url');
		var currObj = $(this);
		var currLikeObj = $("#feedLinksBlk"+curFeedId+" .likes");
		var currLikeObjLoc = $("#feedLinksBlk"+curFeedId+" .likes span");
		$.ajax({
	        type: "POST",
	        url: currURL,
	        data: {feedid:curFeedId},  
	        cache: false,
	        dataType: 'json', 
	        success: function(resData){
	        	var prevLikeCnt = currLikeObj.attr('data-likecnt');
	        	var likeCount = 0;
	        	if( resData.success && (!resData.actionType)){
	        		currObj.html('Like');
	        		currLikeObj.attr('data-likecnt', parseInt(prevLikeCnt)-1);
	        		currLikeObjLoc.html(parseInt(currLikeObj.attr('data-likecnt')));
	            }else{
	            	currObj.html('Unlike');
	            	currLikeObj.attr('data-likecnt', parseInt(prevLikeCnt)+1);
	            	currLikeObjLoc.html(parseInt(currLikeObj.attr('data-likecnt')));
	            }	
	        },
	        error: function(data) {
	            alert("Invalid data");
	        }
	    });
	});	
	
	// Load selected Share feed on popup
	$('body').on('hidden.bs.modal', '.modal', function () {
	  	$("#popupfeedShare .modal-content").html('');
	});	
	$('#feedDispBlk').on('click', '.clsShare', function(){
		var feedURL = $(this).attr('data-url');
		var data = { feedid: $(this).parent().attr('data-id'),
			feedtype: 'view'
		};	
		$.post(feedURL,data,function(result){
	        $("#popupfeedShare .modal-content").html(result);
	    });
	});	
	$('#popupfeedShare').on('click', '#btnFeedShare', function(event){
		event.preventDefault();
		$.ajax({
	        type: "POST",
	        url: $("#frmShrPost").attr('action'),
	        data: $("#frmShrPost").serializeArray(),  
	        cache: false,
	        dataType: 'json', 
	        success: function(resData){
	        	if(resData.success){
	        		$("button#btnFeedShare").remove();
					$("#popupfeedShare .modal-footer .cancel-btn").html("Ok");
	        		$("#popupfeedShare .modal-body").html('<p class="text-center success">'+resData.statusMsg+'</div>');
	            }else{
	            	$("#txtFeedShare").after('<p class="text-left error">'+resData.errors['txtFeedShare']+'</p>');
	            }	
	        },
	        error: function(data) {
	            alert("Invalid data");
	        }
	    });
	});

	// Set Comment focus
	$('#news-feed, .feed-links').on('click', '.clsComments', function(){
		var feedid = $(this).parent().attr('data-id');
		$("#txtFeedComment"+feedid).focus();
	});


	$("body").on('click', '.load_more_comments', function() {
		var loadCommurl = $(this).attr("data-url");
        var feed_id = $(this).attr("data-feedid");
        var data = {
            'feed_id': feed_id,
            'page': $(this).attr("data-pageno"),
        }
        $.ajax({
            type: "POST",
            url: loadCommurl,
            data: data,
            dataType: 'json',
            success: function(resData) {
                if (resData.success == true) {
                    $("#ajxLoadFeedCom"+feed_id).prepend(resData.html);
                    if(resData.more == false){
                    	$("#loadMoreComments"+feed_id).hide();
                    }
                }
            },
            error: function(request, status, error) {},
        });
        $(this).attr("data-pageno",parseInt($(this).attr("data-pageno"))+1);
    });
});
