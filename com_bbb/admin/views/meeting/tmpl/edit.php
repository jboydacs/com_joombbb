<?php
/**
* @version		$Id:edit.php 1 2015-03-05 16:31:34Z Jibon $
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Set toolbar items for the page
$edit		= JFactory::getApplication()->input->get('edit', true);
$text = !$edit ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Meeting' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::apply('meeting.apply');
JToolBarHelper::save('meeting.save');
if (!$edit) {
	JToolBarHelper::cancel('meeting.cancel');
} else {
	// for existing items the button is renamed `close`
	JToolBarHelper::cancel( 'meeting.cancel', 'Close' );
}
?>
<script src="/media/jui/js/chosen.jquery.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/media/jui/css/chosen.css" type="text/css"></link>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"></link>
<script language="javascript" type="text/javascript">
	jQuery(document).ready(function(){
		//Run Start date Jquery Date
		jQuery( "#bbb_start_date" ).datepicker();
		//Run Emd date Jquery Date
		jQuery( "#bbb_end_date" ).datepicker();
		//Run Emd date Jquery Date
		jQuery( "#bbb_display_date" ).datepicker();
		
		//Make all select tags within the form transformed to chosen select.
		jQuery('.bbb_edit_form select:not(.displaynone)').chosen();
		
		startDateMS = <?php echo ($this->item->start_time) ? $this->item->start_time: 0; ?>;
		if(startDateMS){
			jQuery('#bbb_start_date').val(timeConvert(startDateMS, 'day'));
			jQuery('#bbb_start_hour').val(timeConvert(startDateMS, 'hour'));
			jQuery('#bbb_start_minutes').val(timeConvert(startDateMS, 'minutes'));
		}
		
		endDateMS = <?php echo ($this->item->end_time) ? $this->item->end_time: 0; ?>;
		if(endDateMS){
			jQuery('#bbb_end_date').val(timeConvert(endDateMS, 'day'));
			jQuery('#bbb_end_hour').val(timeConvert(endDateMS, 'hour'));
			jQuery('#bbb_end_minutes').val(timeConvert(endDateMS, 'minutes'));
		}
		
		displayDateMS = <?php echo ($this->item->display_time) ? $this->item->display_time: 0; ?>;
		if(endDateMS){
			jQuery('#bbb_display_date').val(timeConvert(displayDateMS, 'day'));
			jQuery('#bbb_display_hour').val(timeConvert(displayDateMS, 'hour'));
			jQuery('#bbb_display_minutes').val(timeConvert(displayDateMS, 'minutes'));
		}
		
		
		//Add Moderator button trigger
		jQuery('#addmoderator_button').on('click', function(){
			/* clone1 = jQuery('.moderator_ddcont select').eq(0).clone().val('0').appendTo('.bbb_edit_form .moderator_ddcont').removeClass("chzn-done display_none").css("display", "block").chosen(); */
			clone1 = jQuery('.moderator_ddcont select').eq(0).clone().val('0').appendTo('.bbb_edit_form .moderator_ddcont').removeClass("chzn-done").css("display", "block").chosen();
			jQuery('.bbb_edit_form .moderator_ddcont').append('<span id="remove_select">Remove</span>');
			
			selectModeratorCount = jQuery(".moderator_ddcont select#moderators_id").length; 
			
			jQuery( ".moderator_ddcont #remove_select" ).each(function() {
				jQuery(this).on('click', function(){
					selectModeratorCount = jQuery(".moderator_ddcont select#moderators_id").length; 
					/* if(selectModeratorCount > 1){ */
						jQuery(this).prevAll("#moderators_id_chzn:first").remove();
						jQuery(this).prevAll("select#moderators_id:first").remove();
						jQuery(this).remove();
				/* 	} */
				});	
			});
		});
		
		//Remove Moderator select button
		selectModeratorCount = jQuery(".moderator_ddcont select#moderators_id").length; 
		jQuery( ".moderator_ddcont #remove_select" ).each(function() {
			jQuery(this).on('click', function(){
				selectModeratorCount = jQuery(".moderator_ddcont select#moderators_id").length; 
				/* if(selectModeratorCount > 1){ */
					jQuery(this).prevAll("#moderators_id_chzn:first").remove();
					jQuery(this).prevAll("select#moderators_id:first").remove();
					jQuery(this).remove();
				/* } */
			});	
		});
		
		
		
		//Add Allowed User button trigger
		jQuery('#adduser_button').on('click', function(){
			clone1 = jQuery('.alloweduser_ddcont select').eq(0).clone().val('0').appendTo('.bbb_edit_form .alloweduser_ddcont').removeClass("chzn-done").css("display", "block").chosen();
			jQuery('.bbb_edit_form .alloweduser_ddcont').append('<span id="remove_select">Remove</span>');
			
			selectAuserCount = jQuery(".alloweduser_ddcont select#allowed_users_id").length; 
			
			jQuery( ".alloweduser_ddcont #remove_select" ).each(function() {
				jQuery(this).on('click', function(){
					selectAuserCount = jQuery(".alloweduser_ddcont select#allowed_users_id").length; 
					/* if(selectAuserCount > 1){ */
						jQuery(this).prevAll("#allowed_users_id_chzn:first").remove();
						jQuery(this).prevAll("select#allowed_users_id:first").remove();
						jQuery(this).remove();
					/* } */
				});	
			});
		});
		
		//Remove Allowed user select button
		selectAuserCount = jQuery(".alloweduser_ddcont select#allowed_users_id").length; 
		jQuery( ".alloweduser_ddcont #remove_select" ).each(function() {
			jQuery(this).on('click', function(){
				selectAuserCount = jQuery(".alloweduser_ddcont select#allowed_users_id").length; 
				/* if(selectAuserCount > 1){ */
					jQuery(this).prevAll("#allowed_users_id_chzn:first").remove();
					jQuery(this).prevAll("select#allowed_users_id:first").remove();
					jQuery(this).remove();
				/* } */
			});	
		});
		
		
		
		//Add Allowed User button trigger
		jQuery('#addgroup_button').on('click', function(){
			clone1 = jQuery('.allowedgroup_ddcont select').eq(0).clone().val('0').appendTo('.bbb_edit_form .allowedgroup_ddcont').removeClass("chzn-done").css("display", "block").chosen();
			jQuery('.bbb_edit_form .allowedgroup_ddcont').append('<span id="remove_select">Remove</span>');
			
			selectAgroupCount = jQuery(".allowedgroup_ddcont select#allowed_groups_id").length; 
			
			jQuery( ".allowedgroup_ddcont #remove_select" ).each(function() {
				jQuery(this).on('click', function(){
					selectAgroupCount = jQuery(".allowedgroup_ddcont select#allowed_groups_id").length; 
					/* if(selectAgroupCount > 1){ */
						jQuery(this).prevAll("#allowed_groups_id_chzn:first").remove();
						jQuery(this).prevAll("select#allowed_groups_id:first").remove();
						jQuery(this).remove();
					/* } */
				});	
			});
		});
		
		//Remove Allowed user select button
		selectAgroupCount = jQuery(".allowedgroup_ddcont select#allowed_groups_id").length; 
		jQuery( ".allowedgroup_ddcont #remove_select" ).each(function() {
			jQuery(this).on('click', function(){
				selectAgroupCount = jQuery(".allowedgroup_ddcont select#allowed_groups_id").length; 
				/* if(selectAgroupCount > 1){ */
					jQuery(this).prevAll("#allowed_groups_id_chzn:first").remove();
					jQuery(this).prevAll("select#allowed_groups_id:first").remove();
					jQuery(this).remove();
				/* } */
			});	
		});
		
		
	});
Joomla.submitbutton = function(task)
{
	if (task == 'meeting.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

</script>
<style>
	.bbb_edit_form .chzn-container{
		vertical-align: middle;
		margin: 4px 0;
	}
	#remove_select{
	  display: inline-block;
	  border: 1px solid #adadad;
	  padding: 0 8px;
	  vertical-align: middle;
	  cursor: pointer;
	  margin: 0 5px;
	}
	#addmoderator_button{
		display: inline-block;
		padding: 6px 9px;
		border-radius: 4px;
		border: 1px solid #ADADAD;
		background: #EFEFEF;
		margin: 3px 0 3px 125px;
		cursor: pointer;
	}
	#adduser_button{
		display: inline-block;
		padding: 6px 9px;
		border-radius: 4px;
		border: 1px solid #ADADAD;
		background: #EFEFEF;
		margin: 3px 0 3px 125px;
		cursor: pointer;
	}
	#addgroup_button{
		display: inline-block;
		padding: 6px 9px;
		border-radius: 4px;
		border: 1px solid #ADADAD;
		background: #EFEFEF;
		margin: 3px 0 3px 125px;
		cursor: pointer;
	}
	.datepicker_cls{
		width: 85px;
	}
	.event_datehr_cls, .event_datemin_cls{
		width: 65px;
	}
	.datepicker-cont{
		display: inline-block;
		margin-right: 10px;
	}
	.timehrmin-cont{
		display: inline-block;
		margin-right: 10px;
	}
</style>
	 	<form method="post" action="<?php echo JRoute::_('index.php?option=com_bbb&layout=edit&id='.(int) $this->item->id);  ?>" id="adminForm" name="adminForm" class="bbb_edit_form">
	 	<div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-60  <?php endif; ?>span8 form-horizontal fltlft">
		  <fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>
		
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('meetingName'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('meetingName');  ?>
					</div>
				</div>		

				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('meeting_description'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('meeting_description');  ?>
					</div>
				</div>		
				<!--
				<div class="control-group">
					<div class="control-label">					
						<?php // echo $this->form->getLabel('moderatorPW'); ?>
					</div>
					
					<div class="controls">	
						<?php // echo $this->form->getInput('moderatorPW');  ?>
					</div>
				</div>		
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('attendeePW'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('attendeePW');  ?>
					</div>
				</div>		
				-->
				
				
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Select the event category."><?php echo 'Category'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls">	
						<?php echo JHTML::_('select.genericlist', $this->categoriesOptions, 'category_id', 'class="inputbox" required="" aria-required="true"', 'value', 'text', $this->item->cat_id); ?>
					</div>
				</div>
				
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Select the event Type."><?php echo 'Type'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls">	
						<?php echo JHTML::_('select.genericlist', $this->typesOptions, 'type_id', 'class="inputbox" required="" aria-required="true"', 'value', 'text', $this->item->type_id); ?>
					</div>
				</div>
				
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Select the event Access."><?php echo 'Access'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls">	
						<?php echo JHTML::_('select.genericlist', $this->accessOptions, 'access', 'class="inputbox" required="" aria-required="true"', 'value', 'text', $this->item->access); ?>
					</div>
				</div>
				
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="If yes event will be open all the time and the end time will not be used."><?php echo 'Event is open all the time?'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls">	
						<select id="event_isopen" name="event_isopen" class="event_isopen_cls" required="" aria-required="true">
							<option value="0" <?php echo ($this->item->event_isopen == 0)?'selected':''; ?> >No</option>
							<option value="1" <?php echo ($this->item->event_isopen == 1)?'selected':''; ?> >Yes</option>
						</select>
					</div>
				</div>
				
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Display Time of the event"><?php echo 'Display Date'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					
					<div class="controls">	
						<div class="datepicker-cont">
							<span>Date (mm/dd/yyyy): </span>
							<input aria-invalid="false" required="" aria-required="true" name="bbb_display_date" type="text" id="bbb_display_date" class="datepicker_cls" value="" />
						</div>	
						<div class="timehrmin-cont">
							<span>Time (24 Hr Format): </span>
							<input aria-invalid="false" required="" aria-required="true" name="bbb_display_hour" id="bbb_display_hour" type="number" min=0 max=24 class="event_datehr_cls" placeholder="Hours" value="" />
							<input aria-invalid="false" required="" aria-required="true" name="bbb_display_minutes" id="bbb_display_minutes" type="number" min=0 max=60 class="event_datemin_cls" placeholder="Minutes" value="" />
						</div>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Start Time of the event"><?php echo 'Start Date'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					
					<div class="controls">	
						<div class="datepicker-cont">
							<span>Date (mm/dd/yyyy): </span>
							<input aria-invalid="false" required="" aria-required="true" name="bbb_start_date" type="text" id="bbb_start_date" class="datepicker_cls" value="" />
						</div>	
						<div class="timehrmin-cont">
							<span>Time (24 Hr Format): </span>
							<input aria-invalid="false" required="" aria-required="true" name="bbb_start_hour" id="bbb_start_hour" type="number" min=0 max=24 class="event_datehr_cls" placeholder="Hours" value="" />
							<input aria-invalid="false" required="" aria-required="true" name="bbb_start_minutes" id="bbb_start_minutes" type="number" min=0 max=60 class="event_datemin_cls" placeholder="Minutes" value="" />
						</div>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="End Time of the event"><?php echo 'End Date'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					
					<div class="controls">	
						<div class="datepicker-cont">
							<span>Date (mm/dd/yyyy): </span>
							<input aria-invalid="false" required="" aria-required="true" name="bbb_end_date" type="text" id="bbb_end_date" class="datepicker_cls" value="" />
						</div>	
						<div class="timehrmin-cont">
							<span>Time (24 Hr Format): </span>
							<input aria-invalid="false" required="" aria-required="true" name="bbb_end_hour" id="bbb_end_hour" type="number" min=0 max=24 class="event_datehr_cls" placeholder="Hours" value="" />
							<input aria-invalid="false" required="" aria-required="true" name="bbb_end_minutes" id="bbb_end_minutes" type="number" min=0 max=60 class="event_datemin_cls" placeholder="Minutes" value="" />
						</div>
					</div>
				</div>
				
				<div class="control-group" style="margin: 6px 0;">
					<div class="control-label">&nbsp; </div>
					<div class="controls"><span id="addmoderator_button">Add Teacher</span></div>
				</div>
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Select the users to be a teacher"><?php echo 'Teacher(s)'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls moderator_ddcont">	
						<?php 
						echo JHTML::_('select.genericlist', $this->userOptions, 'moderators_id[]', 'class="inputbox displaynone" style="display: none;"', 'value', 'text', 0);
						if($this->isNew){
							echo $this->moderatorDropdownList; echo '<span id="remove_select">Remove</span>';
						} else {
							$count = 0;
							foreach($this->moderators_Arr as $moderator_id){
								echo JHTML::_('select.genericlist', $this->userOptions, 'moderators_id[]', 'class="inputbox moderator'.$count.'"', 'value', 'text', $moderator_id);
								echo '<span id="remove_select">Remove</span>';
								$count++;
							}
						}
						?>
					</div>
				</div>
				
				<div class="control-group" style="margin: 6px 0;">
					<div class="control-label">&nbsp; </div>
					<div class="controls"><span id="adduser_button">Add User</span></div>
				</div>
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Select the allowed user, (if there is no selected allowed users, all the students will be allowed to enter this event.)"><?php echo 'Allowed User(s)'; ?></span>
						<span class="star">&nbsp;</span>
					</div>
					<div class="controls alloweduser_ddcont">	
						<?php 
						echo JHTML::_('select.genericlist', $this->userOptions, 'allowed_users_id[]', 'class="inputbox displaynone" style="display: none;"', 'value', 'text', 0);
						if($this->isNew){
							echo $this->allowedUserDropdownList; echo '<span id="remove_select">Remove</span>';
						} else {
							$count = 0;
							foreach($this->allowedUsers_Arr as $alloweduser_id){
								echo JHTML::_('select.genericlist', $this->userOptions, 'allowed_users_id[]', 'class="inputbox alloweduser'.$count.'"', 'value', 'text', $alloweduser_id);
								echo '<span id="remove_select">Remove</span>';
								$count++;
							}
						}
						?>
					</div>
				</div>
				
				<div class="control-group" style="margin: 6px 0;">
					<div class="control-label">&nbsp; </div>
					<div class="controls"><span id="addgroup_button">Add Group</span></div>
				</div>
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="Select the group that is allowed to enter this room, (if there is no selected allowed groups, all the groups will be allowed to enter this event.)"><?php echo 'Allowed Group(s)'; ?></span>
						<span class="star">&nbsp;</span>
					</div>
					<div class="controls allowedgroup_ddcont">	
						<?php 
						echo JHTML::_('select.genericlist', $this->groupOptions, 'allowed_groups_id[]', 'class="inputbox displaynone" style="display: none;"', 'value', 'text', 0);
						if($this->isNew){
							echo $this->allowedGroupsDropdownList; echo '<span id="remove_select">Remove</span>';
						} else {
							$count = 0;
							foreach($this->allowedGroups_Arr as $allowedgroup_id){
								echo JHTML::_('select.genericlist', $this->groupOptions, 'allowed_groups_id[]', 'class="inputbox allowedgroup'.$count.'"', 'value', 'text', $allowedgroup_id);
								echo '<span id="remove_select">Remove</span>';
								$count++;
							}
						}
						?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">					
						<span class="hasTooltip" title="If yes all users will be a moderator of the event."><?php echo 'Allow all users to be moderators'; ?></span>
						<span class="star">&nbsp;*</span>
					</div>
					<div class="controls">	
						<select id="all_moderator" name="all_moderator" class="event_isopen_cls" required="" aria-required="true">
							<option value="0" <?php echo ($this->item->all_moderator == 0)?'selected':''; ?> >No</option>
							<option value="1" <?php echo ($this->item->all_moderator == 1)?'selected':''; ?> >Yes</option>
						</select>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('maxParticipants'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('maxParticipants');  ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('record'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('record');  ?>
					</div>
				</div>	
				<div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('duration'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('duration');  ?>
					</div>
				</div>		
				<!-- <div class="control-group">
					<div class="control-label">					
						<?php echo $this->form->getLabel('voiceBridge'); ?>
					</div>
					
					<div class="controls">	
						<?php echo $this->form->getInput('voiceBridge');  ?>
					</div>
				</div>	-->
          </fieldset>                      
        </div>
        <div class="col <?php if(version_compare(JVERSION,'3.0','lt')):  ?>width-30  <?php endif; ?>span2 fltrgt">
			        

        </div>                   
		<input type="hidden" name="option" value="com_bbb" />
	    <input type="hidden" name="cid[]" value="<?php echo $this->item->id ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="meeting" />
		<input type="hidden" name="start_date_timestamp" id="start_date_timestamp" value="" />
		<input type="hidden" name="end_date_timestamp" id="end_date_timestamp" value="" />
		<input type="hidden" name="display_date_timestamp" id="display_date_timestamp" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
<script language="javascript" type="text/javascript">
	jQuery(document).ready(function(){
		//Run Start date Jquery Date
		jQuery( "#bbb_start_date" ).datepicker();
		//Run Emd date Jquery Date
		jQuery( "#bbb_end_date" ).datepicker();
		//Run Display date Jquery Date
		jQuery( "#bbb_display_date" ).datepicker();
		
		jQuery( "#toolbar-apply button, #toolbar-save button" ).on('hover', function(){
			//Start Date
			startDateDayValS = String(jQuery( "#bbb_start_date" ).val());
			startDateDayValS = (startDateDayValS) ? startDateDayValS : '02/02/1990';
			startDateHrValS = String(jQuery( "#bbb_start_hour" ).val());
			startDateHrValS = (startDateHrValS) ? startDateHrValS : '00';
			startDateMinValS = String(jQuery( "#bbb_start_minutes" ).val());
			startDateMinValS = (startDateMinValS) ? startDateMinValS : '00';
			
			startTimeS =  startDateHrValS+':'+startDateMinValS+':00';
			
			startDate = new Date(startDateDayValS + ' '+ startTimeS);
			startDateTimeStamp = startDate.getTime();
			
			//End Date
			endDateDayValS = String(jQuery( "#bbb_end_date" ).val());
			endDateDayValS = (endDateDayValS) ? endDateDayValS : '02/02/1990';
			endDateHrValS = String(jQuery( "#bbb_end_hour" ).val());
			endDateHrValS = (endDateHrValS) ? endDateHrValS : '00';
			endDateMinValS = String(jQuery( "#bbb_end_minutes" ).val());
			endDateMinValS = (endDateMinValS) ? endDateMinValS : '00';
			
			endTimeS =  endDateHrValS+':'+endDateMinValS+':00';
			
			endDate = new Date(endDateDayValS + ' '+ endTimeS);
			endDateTimeStamp = endDate.getTime();
			
			//Display Date
			displayDateDayValS = String(jQuery( "#bbb_display_date" ).val());
			displayDateDayValS = (displayDateDayValS) ? displayDateDayValS : '02/02/1990';
			displayDateHrValS = String(jQuery( "#bbb_display_hour" ).val());
			displayDateHrValS = (displayDateHrValS) ? displayDateHrValS : '00';
			displayDateMinValS = String(jQuery( "#bbb_display_minutes" ).val());
			displayDateMinValS = (displayDateMinValS) ? displayDateMinValS : '00';
			
			displayTimeS =  displayDateHrValS+':'+displayDateMinValS+':00';
			
			displayDate = new Date(displayDateDayValS + ' '+ displayTimeS);
			displayDateTimeStamp = displayDate.getTime();
			
			//populate the hidden inputs to pass the start date and end date.
			jQuery( "#start_date_timestamp" ).val(startDateTimeStamp);
			jQuery( "#end_date_timestamp" ).val(endDateTimeStamp);
			jQuery( "#display_date_timestamp" ).val(displayDateTimeStamp);
		});
		
		//Save button
		var saveButtonClickAttr = jQuery( "#toolbar-apply button" ).attr('onclick');
		//Save and Close button
		var saveAndCloseButtonClickAttr = jQuery( "#toolbar-save button" ).attr('onclick');
		
		//Remove onclick attr so we can add restrictions for the input fields
		jQuery( "#toolbar-apply button" ).removeAttr('onclick');
		jQuery( "#toolbar-save button" ).removeAttr('onclick'); 
		
		jQuery( "#toolbar-apply button" ).on('click', function(){
			var isValid = false;
			var validDateFormat = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
			startDate = String(jQuery( "#bbb_start_date" ).val());
			endDate = String(jQuery( "#bbb_end_date" ).val());
			displayDate = String(jQuery( "#bbb_display_date" ).val());
			
			//Valid inputs
			if( (validDateFormat.test(startDate)) &&
			(validDateFormat.test(endDate)) &&
			(validDateFormat.test(displayDate)) &&
			jQuery( "#bbb_start_hour" ).val() <= 24 &&
			jQuery( "#bbb_start_minutes" ).val() <= 60 &&
			jQuery( "#bbb_end_hour" ).val() <= 24 &&
			jQuery( "#bbb_end_minutes" ).val() <= 60 &&
			jQuery( "#bbb_display_hour" ).val() <= 24 &&
			jQuery( "#bbb_display_minutes" ).val() <= 60			) isValid = true;
		
			if(isValid)	Joomla.submitbutton('meeting.apply')
			else alert('Invalid Date Inputs');
		});
		
		jQuery( "#toolbar-save button" ).on('click', function(){
			var isValid = false;
			var validDateFormat = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
			startDate = String(jQuery( "#bbb_start_date" ).val());
			endDate = String(jQuery( "#bbb_end_date" ).val());
			displayDate = String(jQuery( "#bbb_display_date" ).val());
			
			//Valid inputs
			if( (validDateFormat.test(startDate)) &&
			(validDateFormat.test(endDate)) &&
			(validDateFormat.test(displayDate)) &&
			jQuery( "#bbb_start_hour" ).val() <= 24 &&
			jQuery( "#bbb_start_minutes" ).val() <= 60 &&
			jQuery( "#bbb_end_hour" ).val() <= 24 &&
			jQuery( "#bbb_end_minutes" ).val() <= 60 &&
			jQuery( "#bbb_display_hour" ).val() <= 24 &&
			jQuery( "#bbb_display_minutes" ).val() <= 60 ) isValid = true;
		
			if(isValid)	Joomla.submitbutton('meeting.save');
			else alert('Invalid Date Inputs');
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