$(function() {	
    
    $('[data-toggle="tooltip"]').tooltip();
    
    $(document).on('click', '.group-memeber', function() {
    	var Id = $(this).data("id"); 
        var uid = $(this).data("user_id"); 
        $('#groupid').val(Id);
        $('#userid').val(uid);
    });
    
    
    $(document).on('click', '.exit_group_yes', function() {
    	var Id = $('#groupid').val();  
        var uid = $('#userid').val();   
    	exitGroup(Id,uid);
    });
    
    function exitGroup(id,uid){
            //var id = $(this).data("id");
            //var uid = $(this).data("user_id");
            window.location.href = '/group/deletemember/' + id+'/'+uid;
    }
    $('.group-memeber').hover(function(){
  prev = $(this).html();
      $(this).text("Exit Group");
  }, function(){
      $(this).html(prev)
  });
  
    $("#invite_member").validate({
        ignore: [],
            // Specify the validation rules
            rules : {
                    'message_to' : "required",
                    'message_subject' : "required",
                    'message_body' :"required",                    
            },
            // Specify the validation error messages
            messages : {
                    'message_to' : "Please select atleast one user",
                    'message_subject' : "Plese select the subject",
                    'message_body' : "Please specify message body",                    
            },
            submitHandler : function(form) {
                    form.submit();
            }
    });
    $(document).on('click','#invite_member_btn',function() {
      $('#user_ids').attr('placeholder',"To *");
    });
    $('#invite_members_to').tokenize({
            datas: "/getpartners",
            onAddToken:function(value, text, e){
                var tokens=$("#invite_members_to").val();
                $("#invite_members_to").val(tokens+","+value);
			},
			onRemoveToken:function(value, text, e){
				var tokens = $("#invite_members_to").val();
				tokens = tokens.replace(','+value,'');
				$("#invite_members_to").val(tokens);
			},
	});
        
	$(document).on('click','.member_button',function() {
            var id=$(this).attr('id');
            var data = {
                'groupid': $(this).attr('id'),       
            };
            $.ajax({
                type: "GET",
                url: '/becomeamember',
                beforeSend: function() {
                    $.blockUI({
                        overlayCSS: {
                            backgroundColor: '#000'
                        }
                    });
                },
                complete: function() {
                    $.unblockUI();
                },
                data: data,
                dataType: 'text',
                success: function(data) { 	
                        if (data!='') {
                            if(data=="Request Pending"){
                                $('#'+id).removeClass('member_button').addClass('request_sent');
                                        
                            }else if(data=="Post Message"){
                                var url =$(location).attr('href');
                                var segments = url.split( '/' );
                                //alert(segments[segments.length-2]);
                                if(segments[segments.length-2]=="groupdetails"){
                                    location.reload();
                                }else{
                                    $('#'+id).removeClass('member_button').addClass('post_message');
                                    $('#'+id).attr('href','/community/groupdetails/'+id);
                                }
                            }
                            $('#'+id).html(data);  
                            //$('#community_group_name').val('');
                        }
                },
                error: function(request, status, error) {        	
                    //$('#check_group_exists').html('');            
                },
            });
        });
        $(document).on('click','.post_message',function() {
            
            $(location).attr('href',$(this).attr('href'))
        });
	/***
	 * Create Group form Validation
	 * Date : 6-04-2016
         * 
         * Added new filed for edit validation image logo (srinu - 28-04-2016)
	 */	
	$("#community_create_group_valid").validate({
        ignore: "input[type='text']:hidden",
        rules: {
            "community_group_name": {
                required: true
            },  
            "community_group_logo": {
                required: true,
                accept: 'jpg|png|jpeg|gif|bmp'
            },  
            "community_group_logo1": {               
                accept: 'jpg|png|jpeg|gif|bmp'
            }, 
            "community_description": {
                required: true
            },  
            "community_logo_agree": {
                required: true
            },
            "community_terms_agree": {
                required: true
            }, 
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().parent().append(error);
        },
        messages: {
            "community_group_name": {
                required: 'Group Name is required'
            },
            "community_group_logo": {
                required: 'Group Logo is required',
                accept: "Logo Must be JPG or PNG or GIF or BMP"
            },
             "community_group_logo1": {                
                accept: "Logo Must be JPG or PNG or GIF or BMP"
            },
            "community_description": {
                required: 'Description is required'
            },
            "community_logo_agree": {
               
            },
            "community_terms_agree": {
                //required: 'Terms & Conditions is required'
                required: 'Please Accept Terms of Service'
            },                 
        },
        submitHandler: function(form) {
            form.submit();
        }
    });	    
	
	/***
	 * Conversation form Validation
	 * Date : 07-04-2016
	 */	
	$("#community_conversation_group_valid").validate({
        ignore: "input[type='text']:hidden",
        rules: {
            "community_group_conversation_title": {
                required: true
            },  
            "community_group_conversation_comments": {
                required: true,                
            },              
        },
        errorPlacement: function(error, element) {
            $(element).parent('div').append(error);
        },
        messages: {
            "community_group_conversation_title": {
                required: 'Title is required'
            },
            "community_group_conversation_comments": {
                required: 'Description is required',                
            },                          
        },
        submitHandler: function(form) {
            form.submit();
        }
    });	  
	
	//Checkbox value set 1 or 0 checked.
    $(".community_private_check").change(function() {
        changeCommunityCheckboxValue($(this));
    });
    
    //Check group name exist or not
    $(document).on('change', '#community_group_name', function() {
    	checkGroupNameExists();
    });
	
    //Community Conversation tab messages chat close button functionality    
    $(document).on('click', '.community_close_converse_div', function() {
    	var Id = $(this).attr("close_div_id");    	
        $(".middle_chat_div_hideshow_" + Id).slideToggle("500");       
    });
    
  
    
  //Community Likes for Post Ajax start here
    $(document).on('click', '.post_likes', function() {
    	var Id = $(this).attr("like-id");    	
    	insertPostLikes(Id);
    });
    
  //Community Likes text change like or unlike
    $(document).on('click', '.change_likes_text', function() {
    	var Id = $(this).attr("like-id");    	
    	postLikesTextChange(Id);
    });
    
    //Community Likes for Post group comment delete call Ajax start here
    $(document).on('click', '.delete_post_comment', function() {
    	var Id = $(this).attr("del-id"); 
        $('#postcommetdel').val(Id);
    	//deleteGroupPostComment(Id);
    });
    
    
    $(document).on('click', '.cancel_post_comment_yes', function() {
    	var Id = $('#postcommetdel').val();   	
    	deleteGroupPostComment(Id);
    });
  
    
    //Community comment section focus text box group details section
    $(document).on('click', '.comment_focus_textbox', function() {
    	var Id = $(this).attr("focus_id");    	
    	$('#community_post_comment_'+Id).focus();
    });
    
     //Community comment section focus text box group details section
    $(document).on('click', '.jobs_hide', function() {    	
    	$('.feed-tab-content').hide();
        $('.jobs_hide_div').show();        
    });
    
      //Community comment section focus text box group details section
    $(document).on('click', '.conv_show', function() {    	
    	$('.feed-tab-content').show();
        $('.jobs_hide_div').hide();      
    });
    
    
  //Community Likes for Post group comment delete call Ajax start here
    $(document).on('click', '.edit_cmnt_text', function() {
    	var Id = $(this).attr("edit-id");   	
        var textValue = $(this).attr("edit_tect_val");        
       
    	var postComment = $('#update_cmnt_'+Id).text();  
        $('#community_post_comment_'+textValue).val(postComment);
        $('#community_post_comment_'+textValue).attr('data-commentid',Id);
    });
    
    //Insert AJax Post comment
    //$(".community_post_main_comment").on( "keypress", function(e) {    	
    $(document).on('keypress', '.community_post_main_comment', function(e) {
    	if (e.keyCode == 13 && !e.shiftKey) {            
            var postingId = $(this).attr("post-id");   
            var checkFiledValid = $('#community_post_comment_'+postingId).val();             
            if (checkFiledValid!='') {
               var currObj = $(this);           
               post_comment(postingId,currObj);
               return false; 
            } else {
                $("#erroralertmodal .modal-body").html("Please enter comment");
                $("#erroralertmodal").modal({
                    show: true
                });
               return false;
            }
            
        }
     }); 
     //group search form validation
     $("#group_search").validate({
        ignore: "input[type='text']:hidden",
        rules: {
            "search": {
                required: true
            },          
        },
        errorPlacement: function(error, element) {
            $(element).parent('div').append(error);
        },
        messages: {
            "search": {
                required: 'Search field is required'
            },                     
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    //organization search form validation
    $("#organization_search").validate({
        ignore: "input[type='text']:hidden",
        rules: {
            "search": {
                required: true
            },  
            "category": {
                required: true,                
            },              
        },
        errorPlacement: function(error, element) {
            //$(element).parent('div').append(error);
            $(element).parent().parent().after(error);
        },
        messages: {
            "search": {
                required: 'Search field is required'
            },
            "category": {
                required: 'Category Type is required',                
            },                          
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    //individual search form validation
    $("#individual_search").validate({
        ignore: "input[type='text']:hidden",
        rules: {
            "search": {
                required: true
            },  
            "category": {
                required: true,                
            },              
        },
        errorPlacement: function(error, element) {
            //$(element).parent('div').append(error);
            $(element).parent().parent().after(error);
        },
        messages: {
            "search": {
                required: 'Search field is required'
            },
            "category": {
                required: 'Category Type is required',                
            },                          
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

   // $(".load_more_comments").click(function() {
    $(document).on('click', '.load_more_comments', function() {
        var data = {
            'post_id': $(this).attr("post_id"),
            'group_id': $('#community_group_id').val(),
            'iteration': $(this).attr("iteration"),
        }
        $.ajax({
            type: "POST",
            url: '/loadmorecomments',
            data: data,
            dataType: 'json',
            success: function(responce) {
                if (responce.success==true) {
                    $("#ajxLoadPostCom"+data.post_id).prepend(responce.html);
                    if(responce.more == false){
                        $("#load_more_comments"+data.post_id).hide();
                    }
                }
            },
            error: function(request, status, error) {

            },
        });
        $(this).attr("iteration",parseInt($(this).attr("iteration"))+1);
    });
    
});

function post_comment(postingId,currObj){
      	 var commentid    =   $('#community_post_comment_'+postingId).attr('data-commentid');
         if(commentid){
             var data = {
                           'postcommentId': postingId,    
                           'postCommentDesc': $('#community_post_comment_'+postingId).val(),   
                           'groupId': $('#community_group_id').val(),  
                           'commentId': commentid, 
            };
         }else{
            var data = {
                           'postcommentId': postingId,    
                           'postCommentDesc': $('#community_post_comment_'+postingId).val(),   
                           'groupId': $('#community_group_id').val(),   
            };
        }
       $.ajax({
           type: "GET",
           url: '/insertCommunityPostMainComment',
           beforeSend: function() {
                    $.blockUI({
                        overlayCSS: {
                            backgroundColor: '#000'
                        }
                    });
                },
                complete: function() {
                    $.unblockUI();
                },
           data: data,
           dataType: 'text',
           success: function(data) {	        	
                   if (data!='') {
                       if(commentid){
                           
                            $('#community_post_comment_'+postingId).val('');
                            $("#update_cmnt_"+commentid).text(data);
                            $('#community_post_comment_'+postingId).removeAttr('data-commentid');
                        
                        
                        }else{
                        //var comment=$('#community_post_comment_'+postingId).val(); 
                            var curUser = currObj.attr('data-curuser');
    
                            $('#community_post_comment_'+postingId).val('');
                            var prevComCnt = $("#feed-links_"+postingId+" .comments").attr('data-comCnt');

                            $("#ajxLoadPostCom"+postingId).append(data);
                            $("#feed-links_"+postingId+" .comments").html('<i class="fa fa-comment-o"></i> ');
                            $("#feed-links_"+postingId+" .comments").append( parseInt(prevComCnt)+1);
                            $("#feed-links_"+postingId+" .comments").attr('data-comCnt', parseInt(prevComCnt)+1);
                        }
                   }
           },
           error: function(request, status, error) {	
                   
           },
            });
}

//Checkbox value set 1 or 0 checked.
function changeCommunityCheckboxValue($chekboxDetails) {
    if ($chekboxDetails.is(':checked')) {
        $chekboxDetails.val(1);
    } else {
        $chekboxDetails.val(0);
    }
}

//Check Groupname exists or not in db
function checkGroupNameExists() {
    var data = {
        'groupname': $('#community_group_name').val(),       
    };
    $.ajax({
        type: "GET",
        url: '/checkGroupNameExists',
        data: data,
        dataType: 'text',
        success: function(data) { 	
	        if (data!='') {
	            $("#check_group_exists").html("Group Name Already Exists.");  
	            $('#community_group_name').val('');
	        }
        },
        error: function(request, status, error) {        	
            $('#check_group_exists').html('');            
        },
    });
}

//Insert Post Likes 
function insertPostLikes(Id) {
    var data = {
    		'postId': Id,
    };    
    $.ajax({
        type: "GET",
        url: '/insertPostLikes',
        data: data,
        dataType: 'text',
        success: function(data) { 	
	        if (data!='') {	        	
	        	 $("#post_likes_count_"+Id).html(data); 
	        }
        },
        error: function(request, status, error) {       	
                  
        },
    });
}

//Insert Post Likes 
function postLikesTextChange(Id) {
    var data = {
    		'postId': Id,
    };    
    $.ajax({
        type: "GET",
        url: '/postLikesTextChange',
        data: data,
        dataType: 'text',
        success: function(data) {        	
	        if (data!='') {	 
	        	if(data==1) {
	        		$("#post_like_"+Id).html("Unlike"); 
	        	} else {
	        		$("#post_like_"+Id).html("Like"); 
	        	}
	        }
        },
        error: function(request, status, error) {       	
                  
        },
    });
}

//Delete Post comments delete with ajax
function deleteGroupPostComment(Id) {
	//if (confirm('Are you sure you want to delete this?')) {
	    var data = {
	    		'postId': Id,
	    };    
	    $.ajax({
	        type: "GET",
	        url: '/deleteGroupComment',
	        data: data,
	        dataType: 'text',    
                 beforeSend: function () {
                $("#deletepostcomment").modal('hide');
                
                 },
	        success: function(data) {        	
		        if (data!='') {	 
		        	$("#hide_delete_post_dev_"+Id).hide(); 
		        }
	        },
	        error: function(request, status, error) {       	
	                  
	        },
	    });
	//}
}

