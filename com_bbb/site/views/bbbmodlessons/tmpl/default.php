<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

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



<script src="/media/jui/js/chosen.jquery.js" type="text/javascript"></script>
<script src="/templates/gantry/js/jquery-ui.1.11.4.js"></script>
<link rel="stylesheet" href="/media/jui/css/chosen.css" type="text/css"></link>
<link rel="stylesheet" href="/templates/gantry/css/jquery-ui.1.11.4.css"></link>
<link rel="stylesheet" href="/components/com_bbb/assets/css/style.css" type="text/css">
<style>
	.deletebutton{
		cursor:pointer;color: #fff;
		background: #A0C843;display: inline-block;
		padding: 10px 25px;width: 200px;
	}
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
		margin-bottom: 20px;height: 45px;display: inline-block;
		width: 100%;
	}
	.bbbevent-cont {
		border: 1px solid #91D04F;
		padding: 15px 0% 15px 2%;
		text-align: left;
		display: inline-block;
		width: 98%;
	}
	@media(max-width: 1199px){
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
</style>
<script>
	jQuery(document).ready(function(){
		
		
			jQuery('body .lessondetails_cont .datepicker_cls_new').datepicker();
		
		
		
		//Make all select tags within the form transformed to chosen select.
		jQuery('body .bbb-container select').chosen();
		
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
			
			$this.html(timeConvertFE($timestamp, $type));
		});
	});	

	var popUpTimerId = 0;
	//Dynamic Date.
	//timestamp_millisecond: unix timestamp in millisecond
	//type will be, day, hour, or min. type accepts String.
	function timeConvertFE(timestamp_millisecond, type){
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
	

	function timePopUpHtml(displayTs, startTs, isStarted, title, description, sl6fI7Cn1S){
		var returnHtml = '';
		var now = new Date();
		var tsDiff = (startTs - now.getTime())/1000;
		if(tsDiff < 0){
			if(isInIFrame == true){
				window.top.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S;
			}else{
				window.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S;
			}
			return false;
		}
		<?php if($user->id){ ?>
		//Commented popup dosnt work
		/* if(isStarted == 0){ */
			/* description = description.replace(/(['"])/g, "\\$1");
			title = title.replace(/(['"])/g, "\\$1"); */
			
			var isInIFrame = (window.location != window.parent.location) ? true : false;
			
			jQuery.colorbox({html:'<div class="time-popup-cont"><div class="countdown-cont"><span>This lesson starts in: &nbsp;</span><div id="enternowbutton">Enter Now</div><div id="countdown-tolessonstarts-cont"></div></div><div class="titledesc-cont"><div class="eventtitle">'+title+'</div><div class="eventdesc">'+description+'</div></div><div class="timeclock-cont"><ul id="clock"><li id="sec"></li><li id="hour"></li><li id="min"></li></ul><div class="dttext">Date Today:</div><div id="date-today"></div><span class="lesson-startstxt">Lesson Starts:</span><div id="lesson-starts-cont"></div><span>Lesson can be entered:</span><div id="lesson-enter-cont"></div></div></div>'});
		
			jQuery('#date-today').html(timeConvertFE(now.getTime(), 'monthstring') +" "+ timeConvertFE(now.getTime(), 'day') +", "+timeConvertFE(now.getTime(), 'year'));
			
			jQuery('#lesson-starts-cont').html(timeConvertFE(displayTs, 'monthstring') +" "+ timeConvertFE(displayTs, 'day') +", "+timeConvertFE(displayTs, 'year')+" "+timeConvertFE(displayTs, 'hour')+":"+timeConvertFE(displayTs, 'minutes'));	

			jQuery('#lesson-enter-cont').html(timeConvertFE(startTs, 'monthstring') +" "+ timeConvertFE(startTs, 'day') +", "+timeConvertFE(startTs, 'year')+" "+timeConvertFE(startTs, 'hour')+":"+timeConvertFE(startTs, 'minutes'));	
			
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
				
					if(isInIFrame == true){
						/* window.top.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S; */
					}else{
						/* window.location.href = '<?php echo 'index.php?option=com_bbb&view=bbb&tmpl=component&meetingID=' ?>'+sl6fI7Cn1S; */
					}
				}
			}, 1000); 
		/* } */
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
			<div class="cat_sidebar_linkcont"><a class="int-sidebar-links" href="/index.php?option=com_comprofiler&amp;task=userdetails&amp;Itemid=223">My Profile</a></div>
			<div class="cat_sidebar_linkcont"><a class="int-sidebar-links" href="/moderator-lessons">My Lessons</a></div>
			<div class="cat_sidebar_linkcont"><a class="int-sidebar-links" href="/private-lesson">Private Lesson</a></div>
			<div class="cat_sidebar_linkcont"><a class="int-sidebar-links" href="/index.php?option=com_briefcasefactory&amp;view=briefcase&amp;Itemid=287">My Files</a></div>
			<div class="cat_sidebar_linkcont"><a class="int-sidebar-links" href="/teacher-files/private">Teacher Files</a></div>
		</div>
	</div>
<?php } ?>

	<?php // if($user->id == 2207 || $user->id == 928){ 
		$uri = & JFactory::getURI(); 
		$pageURL = $uri->toString();
 	?>
	<div class="bbb-container" id="bbb-container"> 	
		<h2 class="icomponent_title">My Lessons</h2>
	
		<div class="newlesson_btn">New Lesson</div>

		<form action="<?php echo $pageURL; ?>" method="post" name="saveform" id="saveform" class="lessonform">
			
			<div class="title_desc_cont">
				<input aria-invalid="false" required="" aria-required="true" type="text" name="lesson_title" id="lesson_title" placeholder="Title" />
				<textarea aria-invalid="false" required="" aria-required="true" name="lesson_description" id="lesson_description" placeholder="Description"></textarea>
			</div>
			<div class="lessondetails_cont">
				<div class="datepicker-cont">
					<span>Date (mm/dd/yyyy): </span>
					<input aria-invalid="false" required="" aria-required="true" name="bbb_fe_date" type="text" id="datepicker_cls_new" class="datepicker_cls_new" value="" />
				</div>
				<div class="timehrmin-cont">
					<span>Time (24 Hr Format): </span>
					<div>
						<input aria-invalid="false" required="" aria-required="true" name="bbb_display_hour" id="bbb_display_hour" type="number" min=0 max=24 class="event_datehr_cls" placeholder="Hours" value="" />&nbsp;:&nbsp;<input aria-invalid="false" required="" aria-required="true" name="bbb_display_minutes" id="bbb_display_minutes" type="number" min=0 max=60 class="event_datemin_cls" placeholder="Minutes" value="" />
					</div>
				</div>
				<div class="timehrmin-cont">
					<span>Category:</span>
					<div><?php echo $this->categoriesDropdownList; ?></div>
				</div>
				<div class="timehrmin-cont">
					<span>Type:</span>
					<div><?php echo $this->typesDropdownList; ?></div>
				</div>
				<div><input type="submit" value="save" id="submitbutton" /></div>
			</div>
			
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="bbb_fe_timestamp" id="bbb_fe_timestamp" value="" />
		</form>
		
		<?php foreach( $this->bbbEvents as $event ){ 
			$teachersAttr = json_decode($event->event_moderators);
			$teacherCount = count(array_filter($teachersAttr)); 
			$displayTime = $event->display_time;
			$displayTimeSecs = $event->display_time/1000;			
			$isStarted = ( time() > $displayTimeSecs || !($this->user->id) ) ? 1: 0;
			$meetingLink = ($user->id) ? '' : 'href="index.php?option=com_bbb&view=bbb&tmpl=component&meetingID='.$event->id.'"';
			$meetingName = $event->meetingName;
			$meetingDesc = $event->meeting_description;
			
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
		<div class="deleteditcont">
		<form action="<?php echo $pageURL; ?>" method="post" class="deleteform" name="deleteform<?php echo $event->id ?>" id="deleteform<?php echo $event->id ?>">
			<input type="hidden" name="event_id" value="<?php echo $event->id ?>" />
			<input type="hidden" name="task" value="delete" />
			<div class="deletebutton" id="deletebutton<?php echo $event->id ?>" >Delete</div>
		</form>
		<span class="editlessonbtn">Edit Lesson</span>
			<form action="<?php echo $pageURL; ?>" method="post" name="updateform" class="updateform" id="updateform<?php echo $event->id; ?>" class="lessonform">
				<script>
					jQuery(document).ready(function(){
						jQuery('.datepick<?php echo $event->id; ?>').datepicker();
						var thiseventdate<?php echo $event->id; ?> = <?php echo ($event->display_time) ? $event->display_time: 0; ?>;
						if(thiseventdate<?php echo $event->id; ?>){
							jQuery('#datepick<?php echo $event->id; ?>').val(timeConvert(thiseventdate<?php echo $event->id; ?>, 'day'));
							jQuery('#bbb_display_hour<?php echo $event->id; ?>').val(timeConvert(thiseventdate<?php echo $event->id; ?>, 'hour'));
							jQuery('#bbb_display_minutes<?php echo $event->id; ?>').val(timeConvert(thiseventdate<?php echo $event->id; ?>, 'minutes'));
						}
						
						
						jQuery( '#updateform<?php echo $event->id; ?>' ).submit(function(){
							//Start Date
							startDateDayValS = String(jQuery( "#datepick<?php echo $event->id; ?>" ).val());
							startDateDayValS = (startDateDayValS) ? startDateDayValS : '02/02/1990';
							startDateHrValS = String(jQuery( "#bbb_display_hour<?php echo $event->id; ?>" ).val());
							startDateHrValS = (startDateHrValS) ? startDateHrValS : '00';
							startDateMinValS = String(jQuery( "#bbb_display_minutes<?php echo $event->id; ?>" ).val());
							startDateMinValS = (startDateMinValS) ? startDateMinValS : '00';
							
							startTimeS =  startDateHrValS+':'+startDateMinValS+':00';
							
							startDate = new Date(startDateDayValS + ' '+ startTimeS);
							startDateTimeStamp = startDate.getTime();
							jQuery( "#bbb_fe_timestamp<?php echo $event->id; ?>" ).val(startDateTimeStamp);
							
							console.log(startDateTimeStamp);
						});
						
						
					});
				</script>
				
				<div class="title_desc_cont">
					<input aria-invalid="false" required="" aria-required="true" type="text" name="lesson_title" id="lesson_title" value="<?php echo $meetingName; ?>" />
					<textarea aria-invalid="false" required="" aria-required="true" name="lesson_description" id="lesson_description"><?php echo $meetingDesc; ?></textarea>
				</div>
				<div class="lessondetails_cont">
					<div class="datepicker-cont">
						<span>Date (mm/dd/yyyy): </span>
						<div><input aria-invalid="false" required="" aria-required="true" name="bbb_fe_date" type="text" id="datepick<?php echo $event->id; ?>" class="datepicker_cls datepick<?php echo $event->id; ?>" value="" /></div>
					</div>
					
					<div class="timehrmin-cont">
						<span>Time (24 Hr Format): </span>
						<div><input aria-invalid="false" required="" aria-required="true" name="bbb_display_hour" id="bbb_display_hour<?php echo $event->id; ?>" type="number" min=0 max=24 class="event_datehr_cls" placeholder="Hours" value="" />&nbsp;:&nbsp;<input aria-invalid="false" required="" aria-required="true" name="bbb_display_minutes" id="bbb_display_minutes<?php echo $event->id; ?>" type="number" min=0 max=60 class="event_datemin_cls" placeholder="Minutes" value="" /></div>
					</div>
					<div>	
						<span>Category:</span>	
						<div><?php echo JHTML::_('select.genericlist', $this->categoriesOptions, 'category_id', 'class="inputbox" required="" aria-required="true"', 'value', 'text', $event->cat_id); ?>
						</div>
					</div>
					<div>
						<span>Type:</span>
						<div><?php echo JHTML::_('select.genericlist', $this->typesOptions, 'type_id', 'class="inputbox" required="" aria-required="true"', 'value', 'text', $event->type_id); ?></div>
					</div>
					<div><input type="submit" value="update" id="submitupdatebutton" /></div>
				</div>
				
				
				
				<input type="hidden" name="event_id" value="<?php echo $event->id; ?>" />
				<input type="hidden" name="task" value="update" />
				<input type="hidden" name="bbb_fe_timestamp" id="bbb_fe_timestamp<?php echo $event->id; ?>" value="" />
				
			</form>
		</div>
			
		<div id="dialog<?php echo $event->id ?>" title="Delete a Lesson">Are you sure that you want to delete this lesson? <div>"<?php echo $event->meetingName; ?>"</div></div>
		
		<script type="text/javascript">
		  jQuery(document).ready(function() {
			jQuery("#dialog<?php echo $event->id ?>").dialog({
			  autoOpen: false,
			  modal: true
			});
		  });

		  jQuery("#deletebutton<?php echo $event->id ?>").click(function(e) {
			e.preventDefault();

			jQuery("#dialog<?php echo $event->id ?>").dialog({
			  buttons : {
				"Confirm" : function() {
				  jQuery("#deleteform<?php echo $event->id ?>").submit();
				},
				"Cancel" : function() {
				  jQuery(this).dialog("close");
				}
			  }
			});

			jQuery("#dialog<?php echo $event->id ?>").dialog("open");
		  });
		</script>
		
		
		
		
			
			<div class="bbbevent-cont">
				<div class="teacher_pic_cont"><?php echo $teacherProfilePic; ?></div>
				
				<div class="eventdesc-cont">
					<a onclick="timePopUpHtml(<?php echo $event->display_time ?>, <?php echo $event->start_time ?>, <?php echo $isStarted ?>, '<?php echo addslashes(htmlspecialchars($meetingName)); ?>', '<?php echo addslashes(htmlspecialchars($lesson_descr)); ?>', '<?php echo $event->id ?>')" class="meeting_link" id="meeting_link" <?php echo $meetingLink; ?> <?php echo ($sidebariframe)?'target="_parent"':''; ?> ><h2 id="meeting_title" class="meeting_title"><?php echo $event->meetingName ?></h2></a>
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
					<a onclick="timePopUpHtml(<?php echo $event->display_time ?>, <?php echo $event->start_time ?>, <?php echo $isStarted ?>, '<?php echo addslashes(htmlspecialchars($meetingName)); ?>', '<?php echo addslashes(htmlspecialchars($lesson_descr)); ?>', '<?php echo $event->id ?>')" class="moreinfolink" id="moreinfolink" style="cursor:pointer;">More Info</a>
				<?php } else { ?>
					<a class="registerloginlink" href="http://www.intenga.com/index.php?option=com_oneclickregistration&view=oneclickregistration&Itemid=180">Register/Login</a>
				<?php } ?>
				
				
				<a onclick="timePopUpHtml(<?php echo $event->display_time ?>, <?php echo $event->start_time ?>, <?php echo $isStarted ?>, '<?php echo addslashes(htmlspecialchars($meetingName)); ?>', '<?php echo addslashes(htmlspecialchars($lesson_descr)); ?>', '<?php echo $event->id ?>')" class="enternowlink" id="enternowlink" <?php echo $meetingLink; ?> <?php echo ($sidebariframe)?'target="_parent"':''; ?> >Enter Here</a>
				
				
				
				
				</div>
			</div>
		<?php } ?>
	</div>
	<?php // } ?>
	
</div>

<script>
	jQuery(document).ready(function(){
			jQuery( ".newlesson_btn" ).click(function() {
				jQuery( ".lessonform" ).slideToggle();
			});
			jQuery( ".editlessonbtn" ).click(function() {
				jQuery( this ).next( ".updateform" ).slideToggle();
			});
			jQuery( '#saveform' ).submit(function(){
				//Start Date
				startDateDayValS = String(jQuery( "#datepicker_cls_new" ).val());
				startDateDayValS = (startDateDayValS) ? startDateDayValS : '02/02/1990';
				startDateHrValS = String(jQuery( "#bbb_display_hour" ).val());
				startDateHrValS = (startDateHrValS) ? startDateHrValS : '00';
				startDateMinValS = String(jQuery( "#bbb_display_minutes" ).val());
				startDateMinValS = (startDateMinValS) ? startDateMinValS : '00';
				
				startTimeS =  startDateHrValS+':'+startDateMinValS+':00';
				
				startDate = new Date(startDateDayValS + ' '+ startTimeS);
				startDateTimeStamp = startDate.getTime();
				jQuery( "#bbb_fe_timestamp" ).val(startDateTimeStamp);
				
				console.log(startDateTimeStamp);
			});
			
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
		
		if(type == 'day'){
			returnString = mm+'/'+dd+'/'+yyyy;
		} else if(type == 'hour'){
			returnString = hh;
		} else if(type == 'minutes'){
			returnString = minutes;
		}

		return returnString;
	}
	
</script>