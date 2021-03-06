<?php
	include('db_conn.php');
	
	$usr_id = 2;
	
	$dtProjectBg = array();
	$dtProjectNode = array();
	
	$strSql = "
	select pro_id, pro_bg_rule
	from gn_project
	where usr_id = ".$usr_id."
	and pro_status = 'A'
	and pro_start_project = 'Y'
	";
	$dtProjectSt = getDatatable($strSql);
	
	if(count($dtProjectSt) > 0)
	{
		if($dtProjectSt[0]['pro_bg_rule']=='S')
		{
			$strSql = "
			select probg_path, probg_time
			from gn_project_bg
			where pro_id = ".$dtProjectSt[0]['pro_id']."
			order by probg_slno
			";
			$dtProjectBg = getDatatable($strSql);
		}
		else
		{
			$strSql = "
			select probg_path, probg_time
			from gn_project_bg
			where pro_id = ".$dtProjectSt[0]['pro_id']."
			order by rand()
			";
			$dtProjectBg = getDatatable($strSql);
		}
		
		$strSql = "
		select btn.btnd_id, btn.pro_id, btn.mtype_id, ifnull(btn.btnd_parent_id,0) btnd_parent_id, 
		btn.btnd_transfer_id, btn.btnd_name, btn.btnd_file_path, 
		btn.btnd_link, btn.btnd_button_width, btn.btnd_bg_img_path, btn.btnd_auto_play, 0 node_display
		from gn_bot_node btn
		inner join gn_project pro on btn.pro_id = pro.pro_id
		and pro.usr_id = ".$usr_id."
		and pro.pro_status = 'A'
		order by btn.pro_id, btn.btnd_sl_no
		";
		$dtProjectNode = getDatatable($strSql);
		
	}
	


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Chat project</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">

	<style type="text/css" media="screen">
		:root{
			--body-color: #434a54;
			--chat-bg-image: url(../admin/uploads/project_bg/<?php echo $dtProjectBg[0]['probg_path'];?>);
			--font-family: 'Lato', sans-serif;

            --text-color1: #4A4A4A;
            --highlight-colour: #ff0076;
            --light-bg-colour: #f3f3f3;


            --header-bg-color: #ffffff;
            --footer-bg-color: #ffffff;
            --footer-border: 1px solid #d8d8d8;

            --message-bot-bg-color: #f1f1f1;
            --message-bot-text-color: #333;
            --message-user-bg-color: #ff0076;
            --message-user-text-color: #ffffff !important;
            --message-font-size: 16px;
            --message-text-line-height: 22px;
            --message-text-font-weight: 700;
            --message-text-letter-spacing: 0.4px;


            --quick-reply-color1: #ff0076;
            --quick-reply-color2: #ffffff;
            --quick-border: 1px solid #3498db;
            

		}	
		
		.message_scroll_gap{
			height:50px;
		}
	</style>

	<link rel="stylesheet" href="css/chat.css">
</head>
<body>

	<div class="chat-content-wrapper">

		
        <div class="chat-main-content">

            <!---conversation--->
            <div class="conversation-container" id="massage-scoll-block">
            	<!---chatArea--->
                <div class="chat-area">
                    <ul class="messages" id="lstMessage">
                        <li class="message bot message_loader">
                            <div class="message-body">
                                <div class="text">
                                    <div class="text-loader">
                                        <div class="text-loader-animation">
                                            <span class="dot"></span>
                                            <span class="dot"></span>
                                            <span class="dot"></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </li>
						
						
            
                    </ul>
                </div>
                <!---chatArea---end--->
            </div>
            <!---conversation---end--->


            <!---create-newmessage--->
            <div class="create-newmessage">

                

            	<div class="input-containter">
                   <input class="input-message" placeholder="Type your message..." type="custom">
                    <button class="send-button">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </button>
                    <a href="" class="attachment-button">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                </div>

                
            	
            </div>
            <!---create-newmessage---end--->

        </div>

	</div>

	<audio controls loop autoplay height="" width="" style="display: none;" id="bg_audio" >
      <source src="bg-sound.mp3" type="audio/ogg">
      <source src="bg-sound.ogg" type="audio/mpeg">
      Your browser does not support the audio element.
    </audio>
	



<link rel="stylesheet" href="css/font-awesome.min.css">

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<link rel="stylesheet" href="css/owl.carousel.min.css">
<script src="js/owl.carousel.min.js"></script>

<link href="assets/venobox/venobox.css" rel="stylesheet">
<script src="assets/venobox/venobox.min.js"></script>

<script src="js/script.js"></script>

<script type="text/javascript">
	var strSiteUrl = '<?php echo $strSiteUrl;?>';
	var iPro_id = <?php echo $dtProjectSt[0]['pro_id'];?>;
	var arr_current_bot_flow = [];
	var iTimer;
	var strNodeDesign = '';
	var btnd_parent_id = 0;
	var btnd_transfer_id = 0;
	var arr_bot_node = [
		<?php
		$strComma = '';
		for($iRow=0;$iRow<=count($dtProjectNode)-1;$iRow++)
		{ 
			echo $strComma.'{"btnd_id":"'.$dtProjectNode[$iRow]['btnd_id'].'","pro_id":"'.$dtProjectNode[$iRow]['pro_id'].'","mtype_id":"'.$dtProjectNode[$iRow]['mtype_id'].'","btnd_parent_id":"'.$dtProjectNode[$iRow]['btnd_parent_id'].'","btnd_transfer_id":"'.$dtProjectNode[$iRow]['btnd_transfer_id'].'","btnd_name":"'.$dtProjectNode[$iRow]['btnd_name'].'","btnd_file_path":"'.$dtProjectNode[$iRow]['btnd_file_path'].'","btnd_link":"'.$dtProjectNode[$iRow]['btnd_link'].'","btnd_button_width":"'.$dtProjectNode[$iRow]['btnd_button_width'].'","btnd_bg_img_path":"'.$dtProjectNode[$iRow]['btnd_bg_img_path'].'","btnd_auto_play":"'.$dtProjectNode[$iRow]['btnd_auto_play'].'","node_display":"'.$dtProjectNode[$iRow]['node_display'].'"}';
			$strComma = ',';	
		}
		?>
	];
	
	function getNodes(p_pro_id,p_btnd_parent_id)
	{
		arr_current_bot_flow = [];
		console.log('1',arr_current_bot_flow);
		for(iRow=0;iRow<=arr_bot_node.length-1;iRow++)
		{
			if(
				(
					(parseInt(arr_bot_node[iRow]['btnd_parent_id']) == parseInt(p_btnd_parent_id))
					&&
					(parseInt(p_pro_id)==0)
				)
				||
				(
					(parseInt(arr_bot_node[iRow]['btnd_parent_id']) == parseInt(p_btnd_parent_id))
					&&
					(
						(parseInt(arr_bot_node[iRow]['pro_id'])==parseInt(p_pro_id))
						&&
						(parseInt(p_pro_id)!=0)
					)
				)
			  )
			  {
				arr_current_bot_flow = arr_current_bot_flow.concat(arr_bot_node[iRow]);
			  }
		}
		console.log('2',arr_current_bot_flow);
		
		iTimer = setTimeout(displayNode, 1000);
		//displayNode();
	}
	
	function displayNode()
	{
		clearTimeout(iTimer);
		btnd_transfer_id = 0;
		
		// iTimer
		// node_display
		strNodeDesign = '';
		for(iRow=0;iRow<=arr_current_bot_flow.length-1;iRow++)
		{
			if(parseInt(arr_current_bot_flow[iRow]['node_display'])==0)
			{
				arr_current_bot_flow[iRow]['node_display'] = 1;
				
				if(parseInt(arr_current_bot_flow[iRow]['mtype_id'])==1)
				{
					// For Text
					
					strNodeDesign += '<li class="message bot">';
					strNodeDesign += '	<div class="message-body">';
					strNodeDesign += '		<div class="text">';
					strNodeDesign += '			<span class="text-span">'+arr_current_bot_flow[iRow]['btnd_name']+'</span>';
					strNodeDesign += '		</div>';
					strNodeDesign += '	</div>';
					strNodeDesign += '</li>';
				}
				else if(parseInt(arr_current_bot_flow[iRow]['mtype_id'])==2)
				{
					// For Button
					strNodeDesign += '<li class="quick-reply-item node_button" data-btnd_id="'+arr_current_bot_flow[iRow]['btnd_id']+'">';
					strNodeDesign += '	<div class="quick-massage-body">';
					strNodeDesign += '		<h4 class="massage_button">'+arr_current_bot_flow[iRow]['btnd_name']+'</h4>';
					strNodeDesign += '	</div>';
					strNodeDesign += '</li>';
				}
				else if(parseInt(arr_current_bot_flow[iRow]['mtype_id'])==3)
				{
					// For Image
					strNodeDesign += '<li class="message bot">';
					strNodeDesign += '	<div class="message-body image-box">';
					strNodeDesign += '	   <a href="'+strSiteUrl+'admin/uploads/images/'+arr_current_bot_flow[iRow]['btnd_file_path']+'" class="venobox" title="Image Titel">';
					strNodeDesign += '			<img src="'+strSiteUrl+'admin/uploads/images/'+arr_current_bot_flow[iRow]['btnd_file_path']+'" alt="image">';
					strNodeDesign += '		</a>';
					strNodeDesign += '	</div>';
					strNodeDesign += '</li>';
				}
				else if(parseInt(arr_current_bot_flow[iRow]['mtype_id'])==5)
				{
					btnd_transfer_id = arr_current_bot_flow[iRow]['btnd_transfer_id'];
					strNodeDesign = 'Transfer';
				}
				
				break;
			}
		}
		
		
		if((strNodeDesign!='')&&(strNodeDesign!='Transfer'))
		{
			jQuery('.message_loader').detach();
			strNodeDesign+='<li class="message bot message_scroll_gap"></li>';
			
			jQuery('.message_scroll_gap').detach();
			//jQuery('#lstMessage').append(strNodeDesign+'<li class="message bot message_scroll_gap"></li>');
			jQuery(strNodeDesign).hide().appendTo("#lstMessage").fadeIn('slow');
			jQuery(document).find('a.venobox').venobox();
			iTimer = setTimeout(displayNode, 1000);
			
			autoScroll();
		}
		else if(strNodeDesign=='Transfer')
		{
			getNodes(0,btnd_transfer_id);
		}
		else
		{
			jQuery('.message_loader').detach();
		}
	}
	
	function autoScroll()
	{
		var height = 0;
		$('#massage-scoll-block .messages').each(function(i, value){
			height += parseInt($(this).height());
		});
		height += '';
		$('#massage-scoll-block').animate({scrollTop: height},'slow');
	}
	
	
	
	document.onclick = function(){
	 // your code
	 //alert("clicked");
	 jQuery('#bg_audio')[0].play();
	}
	
	jQuery(document).ready(function(){
		getNodes(iPro_id,btnd_parent_id);
	});
	
	jQuery(document).on('click','.node_button',function(){
		btnd_parent_id = jQuery(this).data('btnd_id');
		jQuery('.node_button').detach();
		
		jQuery('.message_scroll_gap').detach();
		
		strNodeDesign = '';
		for(iRow=0;iRow<=arr_bot_node.length-1;iRow++)
		{
			if(parseInt(arr_bot_node[iRow]['btnd_id']) == parseInt(btnd_parent_id))
			{
				strNodeDesign += '<li class="message user">';
				strNodeDesign += '	<div class="message-body">';
				strNodeDesign += '		<div class="text">';
				strNodeDesign += '			<span class="text-span">'+arr_bot_node[iRow]['btnd_name']+'</span>';
				strNodeDesign += '		</div>';
				strNodeDesign += '	</div>';
				strNodeDesign += '</li>';
			}
		}
		//jQuery('#lstMessage').append(strNodeDesign);
		jQuery(strNodeDesign).hide().appendTo("#lstMessage").fadeIn('slow');
		
		strNodeDesign = '';
		strNodeDesign += '<li class="message bot message_loader">';
		strNodeDesign += '	<div class="message-body">';
		strNodeDesign += '		<div class="text">';
		strNodeDesign += '			<div class="text-loader">';
		strNodeDesign += '				<div class="text-loader-animation">';
		strNodeDesign += '					<span class="dot"></span>';
		strNodeDesign += '					<span class="dot"></span>';
		strNodeDesign += '					<span class="dot"></span>';
		strNodeDesign += '				</div>';
		strNodeDesign += '			</div>';
		strNodeDesign += '		</div>';
		strNodeDesign += '	</div>';
		strNodeDesign += '</li>';
		strNodeDesign += '<li class="message bot message_scroll_gap"></li>';
		
		jQuery('#lstMessage').append(strNodeDesign);
		autoScroll();
		getNodes(0,btnd_parent_id);
	});
	/*
    var height = 0;
    $('#massage-scoll-block .messages').each(function(i, value){
        height += parseInt($(this).height());
    });
    height += '';
    $('#massage-scoll-block').animate({scrollTop: height});
	*/
</script>

</body>
</html>