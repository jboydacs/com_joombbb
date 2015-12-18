 <?php
/**
* @version		$Id:default.php 1 2015-03-05 16:31:34Z Jibon $
* @copyright	Copyright (C) 2015, Jibon Lawrence Costa. All rights reserved.
* @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
JFactory::getDocument()->addScript(JURI::base().'components/com_bbb/assets/jquery-1.11.2.min.js');
JFactory::getDocument()->addStyleSheet(JURI::base().'components/com_bbb/assets/bootstrap-forms.css');
require_once JPATH_ROOT.'/administrator/components/com_bbb/helpers/bbb.php';
$bbb = new BBBHelper();

?>
<script type="text/javascript">
jQuery("document").ready(function(){
	var $ = jQuery;
			
	$("#meeting").change(function (){
		var url = '<?php echo Juri::base(); ?>index.php?option=com_bbb&task=getRecordings&meetingId='+ $("#meeting").val();
		var result;
		$("#getdata").html("");
		$("#load").show();
		$("#load").html("loading.....");
		jQuery.getJSON(url, function(data){
			jQuery.each(data, function(i, rep){
				if (! rep.recordId == "") {
					var publishurl = '<?php echo Juri::base(); ?>index.php?option=com_bbb&task=publishRecordings&recordId='+rep.recordId;
					var deleteurl = '<?php echo Juri::base(); ?>index.php?option=com_bbb&task=deleteRecordings&recordId='+rep.recordId;
					result += '<tr><td>'+rep.recordId+'</td><td><a href="'+rep.playbackFormatUrl+'" target="_blank">Play Now</a></td><td><a href="'+publishurl+'">Publish Video</a>|<a href="'+deleteurl+'">Delete Video</a></td></tr>';	
				$("#load").hide();
				$("#getdata").html(result);
				}
				else {
					$("#load").show();
					$("#load").html("<span style='color: red'>No recording found. If you have finished this meeting just now then wait few moment because the server will need some time to process the video</span>");
				}
				
			});
		});		
	});
});
</script>
<?php
$params = JComponentHelper::getParams('com_bbb');

if (empty($params->get('salt')) || empty($params->get('url'))) {
	echo '<div class="alert alert-danger"><p class="bg-danger" style="color: red;">Please add server url & salt. You can add those information by click on "Options" button.</p></div>';
}

if(JRequest::getVar('message')){
	echo "<div class='alert alert-success'>".JRequest::getVar('message')."</div>";
}
?>
<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container table-responsive">
		<?php endif;?>
		<table class="table table-hover">
			<tr>
				<td colspan="3" style="text-align: center;">
					<select class="form-control" id="meeting" name="meeting">
						<option>Select One Meeting</option>
						<?php 
						foreach ($this->items as $key => $item) {
							echo "<option value='".$item['id']."'>".$item['meetingName']."</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Record Id</th>
				<th>Play Back URL</th>
				<th>Action</th>
			</tr>
			<tbody id="getdata">
			</tbody>			
		</table>
		<div id="load">Please select one meeting.</div>
		
	</div>
</div>
