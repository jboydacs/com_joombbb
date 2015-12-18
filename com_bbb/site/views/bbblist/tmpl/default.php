<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal', 'a.modal');
$doc = JFactory::getDocument();
$lang = JFactory::getLanguage();

$langTag = explode('-', $lang->getTag())[0];

$langTag = ($langTag == 'en') ? '': $langTag;
$user = JFactory::getUser();
$input = JFactory::getApplication()->input;  	
$tmpl = $input->get('tmpl');
$sidebariframe = $input->get('sidebariframe');
$db = JFactory::getDbo();
if( ($sidebariframe) ){
	$doc = JFactory::getDocument();
	 foreach ( $doc->_links as $k => $array ) {
		 if ( $array['relation'] == 'canonical' ) {
			unset($doc->_links[$k]);
		 }
	 }
	
	$doc->addCustomTag( '<link rel="canonical" href="http://www.intenga.com/live-english-lessons"/>' );
}

if($tmpl == 'component'){
	echo '<style>html{overflow-y: scroll;}</style>';
}
?>
<link rel="stylesheet" href="/components/com_bbb/assets/css/style.css" type="text/css">
<style>
	#enternowbutton{
		display: none;cursor:pointer;color: #fff;
		background: #FF6634;
		padding: 10px 25px;
	}
	.enternowlink{
		display: none;cursor:pointer;
	}
	.socialicons-cont{
		float: left;
	}
	.socialicons-cont div{
		display: inline-block;
		margin: 0 8px;
		font-size: 18px;
		border: 1px solid #fff;
		border-radius: 50%;
		width: 30px;
		height: 30px;
	}
	.socialicons-cont div:hover{
		border: 1px solid #F79646;
	}
	.socialicons-cont, .socialicons-cont div{
		display: inline-block;
	}
	.timecountdown-cont{
		display: inline-block;
		font-size: 15px;
		font-weight: bold;
		padding: 9px 0;
	}
	.lessonbutton-cont{
		display: inline-block;float: right;font-size: 18px;
	}
	.moreinfolink{
		background: #92D050;color: #fff;
	}
	.enternowlink{
		background: #F79646;color: #fff;
	}
	.registerloginlink{
		background: #A0C843;color: #fff;
	}
	.socialicons-cont{
		background: #92D050;color: #fff;padding: 7px 10px;font-size: 20px;
	}
	.socialicons-cont a{
		color: #fff;
	}
	body .socialicons-cont a:hover{
		color: #F79646;
	}
	.socialicons-cont i.fa{
		margin-top: 7px;
		margin-left: 4px;
	}
	.moreinfolink, .enternowlink, .registerloginlink{
		float: right;
		padding: 11px 0;
		width: 180px;
	}
	.infobuttons-cont a:hover{
		color: #fff;
	}
	.infobuttons-cont{
		border-bottom: 1px solid #91D04F;
		border-right: 1px solid #91D04F;
		border-left: 1px solid #91D04F;
		margin-bottom: 20px;height: 45px;
	}
	
	
	
	
	@media(max-width: 480px){
		body .infobuttons-cont{
			height: auto;
		}
		body .socialicons-cont {
			display: inline-block;
			float: none;
			width: 100%;padding-left: 0;
			padding-right: 0;
		}
		body .lessonbutton-cont {
			display: inline-block;
			float: none;width: 100%;
			font-size: 18px;
		}
		body .lessonbutton-cont a{
			float: none;width: 100%;
		}
		body .moreinfolink{
			display: inline-block;
		} 
	}
	.subscribe-popup-cont{
		display: inline-block;
		width: 450px;
		text-align: center;
		padding: 15px;
	}
	.subscribe-button, .subscribe-button:hover{
		background: #F79646;
		padding: 11px 0;
		width: 230px;
		display: inline-block;
		text-align: center;
		color: #fff;
		font-size: 20px;
		margin-top: 25px;
		margin-bottom: 30px;
	}
	.freesubscribe-button, .freesubscribe-button:hover{
		background: #92D050;
		padding: 11px 0;
		width: 230px;
		display: inline-block;
		text-align: center;
		color: #fff;
		font-size: 20px;
		margin-top: 25px;
		margin-bottom: 10px;
	}
	.subscribe-text{
		text-align: left;
	}
	.freesubcont{
		border-top: 1px solid #92D050;
		padding-top: 22px;
	}
</style>
<script>
	jQuery(document).ready(function(){
		var isInIFrame = (window.location != window.parent.location) ? true : false;
		
		if(isInIFrame == true){
			jQuery('body').addClass('loadediniframe');
			jQuery('#meeting_link').attr('target', '_parent');
		}
		
		var now = new Date();
		var monthS = (now.getMonth() < 10) ? '0'+(now.getMonth()+1): now.getMonth()+1;
		var dayS = (now.getDate() < 10) ? '0'+now.getDate(): now.getDate();
		var yearS = now.getFullYear();
		
		var todayAttr = monthS+""+dayS+""+yearS;

		jQuery('.today_link').attr('href', '/<?php echo $this->menuActive->alias.'?today='; ?>'+todayAttr);
		jQuery('body .dynamic_time').each(function(){
			var $this = jQuery(this);
			var $timestamp = parseInt($this.attr("attr-unix-timestamp"));
			var $type = $this.attr("attr-timetype");
			
			$this.html(timeConvert($timestamp, $type));
		});
	});	

	var popUpTimerId = 0;
	//Dynamic Date.
	//timestamp_millisecond: unix timestamp in millisecond
	//type will be, day, hour, or min. type accepts String.
	function timeConvert(timestamp_millisecond, type){
		//Return string either date, hr, or minutes.
		var returnString = '';
		//Instantiate Date
		var ts = new Date(timestamp_millisecond),
		yyyy = ts.getFullYear(),
		mm = ('0' + (ts.getMonth() + 1)).slice(-2),	// Months are zero based. Add leading 0.
		dd = ('0' + ts.getDate()).slice(-2),			// Add leading 0.
		minutes = (ts.getMinutes()<10)? '0'+ts.getMinutes() : ts.getMinutes(),		// Add leading 0.
		hh = (ts.getHours()<10)? '0'+ts.getHours() : ts.getHours();		// Add leading 0.
		var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
		var months = ['Jan','Feb','March','April','May','June','July','Aug','Sept','Oct','Nov','Dec'];
		
		if(type == 'day'){
			returnString = dd;
		} else if(type == 'hour'){
			returnString = hh;
		} else if(type == 'minutes'){
			returnString = minutes;
		} else if(type == 'month'){
			returnString = mm;
		} else if(type == 'year'){
			returnString = yyyy;
		} else if(type == 'dayofweek'){
			returnString = days[ts.getDay()];
		} else if(type == 'monthstring'){
			returnString = months[ts.getMonth()];
		} 
		
		return returnString;
	}
	

	function timePopUpHtml(displayTs, startTs, isStarted, title, description, sl6fI7Cn1S, subscribePopup){
		var returnHtml = '';
		var now = new Date();
		var tsDiff = (startTs - now.getTime())/1000;
		
		if( (subscribePopup == 1) && (tsDiff < 0) ){
				var subscribeButtonText = "Elite Membership";
				var freeSubscribeButtonText = "Free Elite Membership";
				var subscribeDescriptionText = "We have two free spaces for each class. Unfortunately for this class there are already two free users in this class. If you become an Elite member for only $12.99 you can always enter classes.";
				var freeSubscribeDescriptionText = "I don't have any money or I can't use paypal is there any way I can get Elite membership for free? Yes there is. If you are intermediate and above you could become a moderator. There are also many other options have a look.";
				
				jQuery.colorbox({html:"<div class='subscribe-popup-cont'><div><div class='subscribe-text'>"+subscribeDescriptionText+"</div><a class='subscribe-button' href='/intenga-subscription'>"+subscribeButtonText+"</a></div><div class='freesubcont'><div class='subscribe-text'>"+freeSubscribeDescriptionText+"</div><a class='freesubscribe-button' href='/free-subscription'>"+freeSubscribeButtonText+"</a></div></div>"});
				
				return false;
		}
		
		
		
		if(tsDiff < 0){
			if(isInIFrame == true){
				window.top.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S;
			}else{
				window.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S;
			}
			return false;
		}
		
		
		
		<?php if($user->id){ ?>
			var isInIFrame = (window.location != window.parent.location) ? true : false;
			
			jQuery.colorbox({html:'<div class="time-popup-cont"><div class="countdown-cont"><span>This lesson starts in: &nbsp;</span><div id="enternowbutton">Enter Now</div><div id="countdown-tolessonstarts-cont"></div></div><div class="titledesc-cont"><div class="eventtitle">'+title+'</div><div class="eventdesc">'+description+'</div></div><div class="timeclock-cont"><ul id="clock"><li id="sec"></li><li id="hour"></li><li id="min"></li></ul><div class="dttext">Date Today:</div><div id="date-today"></div><span class="lesson-startstxt">Lesson Starts:</span><div id="lesson-starts-cont"></div><span>Lesson can be entered:</span><div id="lesson-enter-cont"></div></div></div>'});
		
			jQuery('#date-today').html(timeConvert(now.getTime(), 'monthstring') +" "+ timeConvert(now.getTime(), 'day') +", "+timeConvert(now.getTime(), 'year'));
			
			jQuery('#lesson-starts-cont').html(timeConvert(displayTs, 'monthstring') +" "+ timeConvert(displayTs, 'day') +", "+timeConvert(displayTs, 'year')+" "+timeConvert(displayTs, 'hour')+":"+timeConvert(displayTs, 'minutes'));	

			jQuery('#lesson-enter-cont').html(timeConvert(startTs, 'monthstring') +" "+ timeConvert(startTs, 'day') +", "+timeConvert(startTs, 'year')+" "+timeConvert(startTs, 'hour')+":"+timeConvert(startTs, 'minutes'));	
			
			if(popUpTimerId){
				clearInterval(popUpTimerId); 
			}
			
			jQuery('#enternowbutton').click(function(){
				if(isInIFrame == true){
						window.top.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S;
				}else{
						window.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S; 
				}
			});
			
			//Countdown
			popUpTimerId = setInterval(function(){
				var now = new Date();
				
				var totalTime = ((startTs - now.getTime())/1000);
				var hours = Math.floor(totalTime / (60 * 60));
				var divisor_for_minutes = totalTime % (60 * 60);
				var minutes = Math.floor(divisor_for_minutes / 60);
				var divisor_for_seconds = divisor_for_minutes % 60;
				var seconds = Math.ceil(divisor_for_seconds);
				var remainingTimeS = hours+"h:"+minutes+"m:"+seconds+"s";
				jQuery('#countdown-tolessonstarts-cont').html(remainingTimeS);
				
				if((seconds == 1) && (minutes == 0) && (hours == 0)){
					jQuery('#enternowbutton').css('display', 'inline-block');
					jQuery('#countdown-tolessonstarts-cont').css('display', 'none');
				}
			}, 1000); 
		<?php } ?>
		
		
		return false;
	}
	
	
	jQuery(document).ready(function(){
		
		
		
			//Countdown
			setInterval(function(){
				jQuery("body .eventcountdown").each(function(){
					var now2 = new Date();
					
					var totalTime2 = ((jQuery(this).attr('attr-starttime') - now2.getTime())/1000);
					var hours2 = Math.floor(totalTime2 / (60 * 60));
					var divisor_for_minutes2 = totalTime2 % (60 * 60);
					var minutes2 = Math.floor(divisor_for_minutes2 / 60);
					var divisor_for_seconds2 = divisor_for_minutes2 % 60;
					var seconds2 = Math.ceil(divisor_for_seconds2);
					var remainingTimeS2 = hours2+"h:"+minutes2+"m:"+seconds2+"s";
					jQuery(this).html(remainingTimeS2);
					 
					if((seconds2 <= 1) && (minutes2 <= 0) && (hours2 <= 0)){
						jQuery(this).css('display', 'none');
						jQuery(this).parents( ".infobuttons-cont" ).find('.enternowlink').css('display', 'inline-block');
						jQuery(this).parents( ".infobuttons-cont" ).find('.registerloginlink').css('display', 'none');
						jQuery(this).parents( ".infobuttons-cont" ).find('.moreinfolink').css('display', 'none');
					}
				});
			}, 1000); 
		
		
		
		jQuery('#cboxOverlay, #cboxClose').click(function(){
			if(popUpTimerId){
				clearInterval(popUpTimerId); 
			}
		});
	});	
	
	
</script>
<div>
<?php if($this->bbbCategories){ ?>
	<div class="events_category" style="width: 12%;">
		<div class="cat_sidebar_title">Category</div>
		<div class="cat_sidebar_innercont">
			<div class="cat_sidebar_linkcont"><a href="<?php echo $langTag.'/'.$this->menuActive->alias.'?alllessons=1'; ?>"><?php echo 'All Lessons' ?></a></div>
			<?php foreach($this->bbbCategories as $category){ ?>
				<div class="cat_sidebar_linkcont"><a href="<?php echo $langTag.'/'.$this->menuActive->alias.'?categoryid='.$category->id; ?>"><?php echo $category->title ?></a></div>
			<?php } ?>
		</div>
	</div>
<?php } ?>	
	
	<?php 
	$paidId = 13;
	// if($user->id == 2207 || $user->id == 928){ ?>
	<div class="bbb-container" id="bbb-container"> 		
		<?php foreach( $this->bbbEvents as $event ){ 
			
			$teachersAttr = json_decode($event->event_moderators);
			$groupAttr = json_decode($event->allowed_groups);
			$teacherCount = count(array_filter($teachersAttr)); 
			$displayTime = $event->display_time;
			$displayTimeSecs = $event->display_time/1000;			
			$isStarted = ( time() > $displayTimeSecs || !($this->user->id) ) ? 1: 0;
			$meetingLink = ($user->id) ? '' : 'href="index.php?option=com_bbb&view=bbb&tmpl=component&meetingID='.$event->id.'"';
			$meetingName = $event->meetingName;
			$meetingDesc = $event->meeting_description;
			$groupsOfUser = JAccess::getGroupsByUser($user->id);
			$eventAccess = array();
			
			$eventAccess[0] = 'All members';
			$eventAccess[1] = 'Elite members';
			
			$subscribePopup = 0;
			
			if($user->id){
				if(in_array($paidId, $groupAttr)){
					$subscribePopup = 1;
					if(in_array($paidId, $groupsOfUser)){
						$subscribePopup = 0;
					}
				}
			}
			
			$isUserModerator = false;
			$moderatorsids = json_decode($event->event_moderators);
			
			if(in_array($user->id, $moderatorsids)){
				$isUserModerator = true;
			}
			
			//Start Free Users
				$paidId = 13;
				
				$allowedgroupsids = json_decode($event->allowed_groups);
				
				$query = $db->getQuery(true);
				$query = "SELECT * FROM `#__bigbluebutton_freeusers` WHERE `event_id`=".$event->id;
				$db->setQuery($query);
				$eventWFreeUser = $db->loadObject();
				//if user count is equal or greater than 2 or if a user is a free user, and the user is not a returning user.
				//count($a)
				$free_users_id = json_decode($eventWFreeUser->users_id);
				
				$lessonIsPaid = false;
				
				
				$userReEntered = false;
				if(in_array($user->id, $free_users_id)){
					$userReEntered = true;
				}
				
				//if false means user is a paid user.
				$userIsFree = true;
				
				$groupsOfUser = JAccess::getGroupsByUser($user->id);
				
				//If Group is paid, display the lesson.
				if (in_array($paidId, $allowedgroupsids)){
					$subscribePopup = 0;
					
					$lessonIsPaid = true;
					
					if(in_array($paidId, $groupsOfUser)){
						$userIsFree = false;
					}
					
				}
				
				$countFree_users_id = count($free_users_id);
				if ( ($eventWFreeUser->users_count >= 2 ||  $countFree_users_id >= 2) && ($lessonIsPaid == true) && ($userIsFree == true) && ($userReEntered == false) && ($isUserModerator == false) ) {
					$subscribePopup = 1;
				}
			//End Free Users
			
			
			$meetingName = str_replace("‘", "'", $meetingName);
			$meetingDesc = str_replace("‘", "'", $meetingDesc);
			
			$eventCatTitle = '';
			if($event->cat_id){
				$query = $db->getQuery(true)
					->select('title')
					->where('id ='.$event->cat_id)
					->from($db->quoteName('#__bigbluebutton_category'));
				$db->setQuery($query);
				$eventCatTitle = $db->loadResult();
			}
		
			$eventTypeTitle = '';
			if($event->type_id){
				$query2 = $db->getQuery(true)
					->select('title')
					->where('id ='.$event->type_id)
					->from($db->quoteName('#__bigbluebutton_type'));
				$db->setQuery($query2);
				$eventTypeTitle = $db->loadResult();
			}
			
			$teacherName = '';
			$teacherProfilePic = '<img src="/templates/gantry/images/icons/menuicon-admin.png" />';
			if($teachersAttr[0]){
				$teacherName .= JFactory::getUser($teachersAttr[0])->name.' ';
				$query = $db->getQuery(true)
						->select('avatar')
						->where('user_id ='.$teachersAttr[0])
						->from($db->quoteName('#__comprofiler'));
					$db->setQuery($query);
					$imageFilename = $db->loadResult();
				$teacherProfilePic = '<img src="images/comprofiler/'.$imageFilename.'" />';
			}
			
			$lesson_descr = trim(preg_replace('/\s+/', ' ', $meetingDesc));
		?>
			<div class="bbbevent-cont">
				<div class="teacher_pic_cont"><?php echo $teacherProfilePic; ?></div>
				
				<div class="eventdesc-cont">
					<a onclick="timePopUpHtml(<?php echo $event->display_time ?>, <?php echo $event->start_time ?>, <?php echo $isStarted ?>, '<?php echo addslashes(htmlspecialchars($meetingName)); ?>', '<?php echo addslashes(htmlspecialchars($lesson_descr)); ?>', '<?php echo $event->id ?>', <?php echo $subscribePopup ?>)" class="meeting_link" id="meeting_link" <?php echo $meetingLink; ?> <?php echo ($sidebariframe)?'target="_parent"':''; ?> ><h2 id="meeting_title" class="meeting_title"><?php echo $event->meetingName ?></h2></a>
					<p id="meeting_description" class="meeting_description"><?php echo $event->meeting_description ?></p>
				</div>
				
				<div class="evdetail-cont">
					<div>
						Date: <strong>
						<span class="dynamic_time" attr-timetype="day" attr-unix-timestamp="<?php echo $event->display_time ?>"></span>.<span class="dynamic_time" attr-timetype="month" attr-unix-timestamp="<?php echo $event->display_time ?>"></span>.
						<span class="dynamic_time" attr-timetype="year" attr-unix-timestamp="<?php echo $event->display_time ?>"></span></strong>
					</div>
					<div>
						Time: <span class="dynamic_time" attr-timetype="hour" attr-unix-timestamp="<?php echo $event->display_time ?>"></span>.<span class="dynamic_time" attr-timetype="minutes" attr-unix-timestamp="<?php echo $event->display_time ?>"></span>
					</div>
					<div>
						<?php 
							echo 'Teacher: '.$teacherName;	
						?>
					</div>
					<div>Level: <?php echo $eventCatTitle; ?></div>
					<div>Type: <?php echo $eventTypeTitle; ?></div>
					<div>Access: <?php echo $eventAccess[$event->access]; ?></div>
				</div>
			</div>
			<div class="infobuttons-cont">
				<div class="socialicons-cont">
					<div><a href="https://twitter.com/intenga" target="_blank"><i class="fa fa-twitter">&nbsp;</i></a></div>
					<div><a href="https://plus.google.com/u/0/communities/102547939009368970178" target="_blank"><i class="fa fa-google-plus">&nbsp;</i></a></div>
					<div><a href="https://www.facebook.com/groups/intengafriends/" target="_blank"><i class="fa fa-facebook">&nbsp;</i></a></div>
				</div>
				<div class="timecountdown-cont">You can enter the room in <span class="eventcountdown" attr-starttime="<?php echo $event->start_time ?>"></span></div>
				<div class="lessonbutton-cont">
				
				<?php if($user->id){ ?>
					<a onclick="timePopUpHtml(<?php echo $event->display_time ?>, <?php echo $event->start_time ?>, <?php echo $isStarted ?>, '<?php echo addslashes(htmlspecialchars($meetingName)); ?>', '<?php echo addslashes(htmlspecialchars($lesson_descr)); ?>', '<?php echo $event->id ?>', <?php echo $subscribePopup ?>)" class="moreinfolink" id="moreinfolink" style="cursor:pointer;">More Info</a>
				<?php } else { ?>
					<a class="registerloginlink" href="http://www.intenga.com/index.php?option=com_oneclickregistration&view=oneclickregistration&Itemid=180">Register/Login</a>
				<?php } ?>
				
				<a onclick="timePopUpHtml(<?php echo $event->display_time ?>, <?php echo $event->start_time ?>, <?php echo $isStarted ?>, '<?php echo addslashes(htmlspecialchars($meetingName)); ?>', '<?php echo addslashes(htmlspecialchars($lesson_descr)); ?>', '<?php echo $event->id ?>', <?php echo $subscribePopup ?>)" class="enternowlink" id="enternowlink" <?php echo $meetingLink; ?> <?php echo ($sidebariframe)?'target="_parent"':''; ?> >Enter Here</a>
				
				</div>
			</div>
		<?php } ?>
	</div>
	<?php // } ?>
</div>
<script>
	jQuery(document).ready(function(){
              setInterval( function() {
				  var seconds = new Date().getSeconds();
				  var sdegree = seconds * 6;
				  var srotate = "rotate(" + sdegree + "deg)";
				  
				  jQuery("#sec").css({"-moz-transform" : srotate, "-webkit-transform" : srotate, "-o-transform" : srotate, "-ms-transform" : srotate});
					  
			  }, 1000 );
				  
			 
			  setInterval( function() {
				  var hours = new Date().getHours();
				  var mins = new Date().getMinutes();
				  var hdegree = hours * 30 + (mins / 2);
				  var hrotate = "rotate(" + hdegree + "deg)";
				  
				  jQuery("#hour").css({"-moz-transform" : hrotate, "-webkit-transform" : hrotate, "-o-transform" : hrotate, "-ms-transform" : hrotate});
					  
			  }, 1000 );
			
			  setInterval( function() {
				  var mins = new Date().getMinutes();
				  var mdegree = mins * 6;
				  var mrotate = "rotate(" + mdegree + "deg)";
				  
				  jQuery("#min").css({"-moz-transform" : mrotate, "-webkit-transform" : mrotate, "-o-transform" : mrotate, "-ms-transform" : mrotate});
                  
              }, 1000 );
	});
</script>