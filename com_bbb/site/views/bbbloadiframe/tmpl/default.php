<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if($this->meetingID){
	$iframeSrc = '/index.php?option=com_bbb&view=bbb&tmpl=component&meetingID='.$this->meetingID;
}
?>
<style>header#rt-top-surround{height:auto;}body{overflow:hidden;}</style>
<div class="event-container" style="position: relative;display: inline-block;vertical-align: top;width:100%;margin: 0 auto;min-height:350px;text-align:center;">
	<div class="bbb-container" id="bbb-container"> 
		<iframe id="bbb-iframe" src="" frameborder="0" style="margin-bottom: -7px;position: relative;"></iframe>
	</div>
</div>
<script>
jQuery(document).ready(function(){
	 setTimeout(function(){jQuery("#bbb-iframe").attr('src', '<?php echo $iframeSrc; ?>')}, 100);
	   var headerHeight = jQuery('header#rt-top-surround').height();
	   jQuery('#bbb-iframe').height( jQuery(window).height() - headerHeight);
	   jQuery('#bbb-iframe').width( jQuery(window).width() );
      jQuery(window).resize(function(){
		 var headerHeight = jQuery('header#rt-top-surround').height();
         jQuery('#bbb-iframe').height( jQuery(window).height() - headerHeight);
		 jQuery('#bbb-iframe').width( jQuery(window).width() );
      });
	  setInterval(function(){
		   var headerHeight = jQuery('header#rt-top-surround').height();
		   jQuery('#bbb-iframe').height( jQuery(window).height() - headerHeight);
		   jQuery('#bbb-iframe').width( jQuery(window).width() ); 
	  }, 1000);
});
</script>
