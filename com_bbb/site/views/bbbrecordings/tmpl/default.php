<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<link rel="stylesheet" href="/components/com_bbb/assets/css/style.css" type="text/css">


<script>
	jQuery(document).ready(function(){
		var now = new Date();
		var monthS = (now.getMonth() < 10) ? '0'+(now.getMonth()+1): now.getMonth()+1;
		var dayS = (now.getDate() < 10) ? '0'+now.getDate(): now.getDate();
		var yearS = now.getFullYear();
		
		var todayAttr = monthS+""+dayS+""+yearS;
		
		jQuery('.bbb-recordings .dynamic_time').each(function(){
			var $this = jQuery(this);
			var $timestamp = parseInt($this.attr("attr-unix-timestamp"));
			var $type = $this.attr("attr-timetype");
			
			$this.html(timeConvert($timestamp, $type));
		});
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
</script>


<div>
<?php if($this->bbbCategories){ ?>
	<div class="events_category" style="width: 12%;">
		<div class="cat_sidebar_title">Category</div>
		<div class="cat_sidebar_innercont">
			<div class="cat_sidebar_linkcont"><a href="<?php echo $this->menuActive->alias ?>"><?php echo "All Recordings" ?></a></div>
			<?php foreach($this->bbbCategories as $category){ ?>
				<div class="cat_sidebar_linkcont"><a href="<?php echo $this->menuActive->alias.'?categoryid='.$category->id; ?>"><?php echo $category->title ?></a></div>
			<?php } ?>
		</div>
	</div>
<?php } ?>
	<div class="bbb-container bbb-recordings" id="bbb-container"> 		
		<?php 
		if($this->bbbrecordings){
			$recordingsCount = count($this->bbbrecordings)-1;
			foreach($this->bbbrecordings as $rec){ ?>
				<div class="bbbevent-inner-cont">
					<a href="<?php echo $rec->recording_url ?>" class="modal_link cboxElement" data-modal-iframe="true" data-modal-width="95%" data-modal-height="95%"><img src="/images/site-images/recording.png"></a>
					<div class="eventdetails-cont">
						<a class="meeting_link modal_link cboxElement" id="meeting_link" href="<?php echo $rec->recording_url ?>" data-modal-iframe="true" data-modal-width="95%" data-modal-height="95%"><h2 id="meeting_title" class="meeting_title"><?php echo $rec->recording_title ?></h2></a>
						<p id="meeting_description" class="meeting_description"><?php echo $rec->recording_description ?></p>
						
						<div>
							<span>Teacher: </span>
							<span class="teachers-conts"><?php 
								foreach($rec->event_moderators as $teacherId){
									$user = JFactory::getUser($teacherId);
									echo $user->name.' ';
								} ?></span>
							<span>Time: </span>
							<span id="time_month_cont" class="time_month_cont dynamic_time" attr-timetype="monthstring" attr-unix-timestamp="<?php echo $rec->display_time ?>"></span>
							<span id="time_day_cont" class="time_day_cont dynamic_time" attr-timetype="day" attr-unix-timestamp="<?php echo $rec->display_time ?>"></span>
							<span id="time_year_cont" class="time_year_cont dynamic_time" attr-timetype="year" attr-unix-timestamp="<?php echo $rec->display_time ?>"></span>
						
							<span id="time_hour_cont" class="time_hour_cont dynamic_time" attr-timetype="hour" attr-unix-timestamp="<?php echo $rec->display_time ?>"></span>:<span id="time_min_cont" class="time_min_cont dynamic_time" attr-timetype="minutes" attr-unix-timestamp="<?php echo $rec->display_time ?>"></span>
							<span>&nbsp;</span>
						</div>
						
					</div>
				</div>
			<?php }
		} ?>
	</div>
</div>

<script>
</script>