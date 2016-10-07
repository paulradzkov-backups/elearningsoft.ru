<?php
/**
* Testimonials Module for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
if ($modal) {
    JHtml::stylesheet(JURI::root().'components/com_testimonials/assets/jquery.fancybox-1.3.4.css');
    JHtml::script(JURI::base() . 'components/com_testimonials/assets/jquery.fancybox-1.3.4.js');
}
?>
	<script>
            
	    var <?php echo $moduleclass_sfx ?>interval;
	    (function ($){
		$(document).ready(function(){
		    <?php if(($show_add_new && JFactory::getUser()->authorise('core.create', 'com_testimonials')) || $modal) : ?>
		    $("a.modtm_iframe").fancybox({'type':'iframe', 'width':820, 'height':750});
		    <?php endif; ?>
		    $('.<?php echo $moduleclass_sfx ?>testimModItem').each(function(index){
			if($(this).height() > $('#<?php echo $moduleclass_sfx ?>testimonials').height()){
			    var height = $(this).height();
			    var diff = $(this).height() - $('#<?php echo $moduleclass_sfx ?>testimonials').height();
                            var text_height = $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).height();
			    var line_height = $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text_line', $( this ) ).height();
                            var new_height = $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).height() - diff;
			    var lines_count = Math.floor(($(  '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).height() - diff) / line_height);
			    var crop_top = Math.ceil(diff/line_height)*line_height;
			    $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).height(lines_count*line_height);
			    $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).css('overflow', 'hidden');
			    $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).css('text-overflow', 'ellipsis');
			    $("head").append($('<style type="text/css">.crop'+index+':after { content: "\u00a0\u00a0\u00a0\u00a0\u2026"; float: right; margin-left: -6em; padding-right: 5px; position: relative; text-align: right; top: -'+crop_top+'px; box-sizing: content-box; -webkit-box-sizing: content-box; -moz-box-sizing: content-box; background-size: 100% 100%; background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABqoAAAABCAMAAACRxg/eAAAABGdBTUEAALGPC/xhBQAAAwBQTFRF////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////AAAA////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wDWRdwAAAP90Uk5Tfet1sDrNWZIeSRDcocCFLWj1CbhSfuMl1EGoYJkYM8Zui/rwBk15FefYKrxGtKydZQzfIdDJNj1VpFyVcY6BBBwxw2yI/Pfy7RInQ09ie5B0eIMDD+kb5eEk2tbSMMs5QL66tkyyrqpYpl+fm5drAf0H+AoN8+4TFhkf3SIoKwDONDfHO8Q+wURHSlBTVlqiXWNmaZNvcox2iYZ/fAL+9vsFCPkL9A4R8e/sFOjqFxrm5B3i4CDeIybb2SnX1SzT0S/PMsw1yjjIxTzCP79CvbtFuUi3tUuzTrGvUa1Uq6lXp6Vbo16gYZ6cZJpnmJZqlG2RcI9zjYp6d4eChIAu7+D8pQAAASFJREFUOMtjqK+fG2y/UKVwkVDxEsOSZSFcyz3LV6tXrJOtXB/msImjaqtxzfZI8R1etbu16vbx/zsY63hYrvGoWdPxeJaWU+ZtZxIkz3l3XNTpvMzZdS1F9YZP922RnrsWvQ8y2PoeW014miX93HfiK4NJbwQmv89z/qgw9bPNtK8BTDN+Bqr98JvDIDr7r93voD98s365zPyuOP2b7Rf/T+xTPli/y30r8zrnpf6L7Gc8/U8sH2U+lLqffk/vTtot5pup13WvJl8RvpR0Qft84lne9tNOrSflm0+YHos7wtpwyORAzH6JvdF7NHdF7eSu3ma0JWKz2MbwDRprQtcyrvIoW6m8wr10qeB8t6LFSgtcC+b9HwWjYBSMglEw2AEA3ckkQdBniOMAAAAASUVORK5CYII=); background: -webkit-gradient(linear, left top, right top, from(rgba(255, 255, 255, 0)), to(white), color-stop(15%, white)); background: -moz-linear-gradient(to right, rgba(255, 255, 255, 0), white 15%, white); background: -o-linear-gradient(to right, rgba(255, 255, 255, 0), white 15%, white); background: -ms-linear-gradient(to right, rgba(255, 255, 255, 0), white 15%, white); background: linear-gradient(to right, rgba(255, 255, 255, 0), white 15%, white); }</style>'));
			    $( '.<?php echo $moduleclass_sfx ?>mod_testimonial_text', $( this ) ).addClass('crop'+index);
			}
			if(index>0){
			    $(this).css('display', 'none');
			    $(this).removeClass('fade');
			}
			$(this).css('width', $('#<?php echo $moduleclass_sfx ?>testimonials').width() );
		    });
		    <?php if(!$params->get('isstatic') && count($list)>1) : ?>
		    $('.<?php echo $moduleclass_sfx ?>testimonials_module').mouseover(function(){
			clearInterval(<?php echo $moduleclass_sfx ?>interval);
		    });
		    $('.<?php echo $moduleclass_sfx ?>testimonials_module').mouseout(function(){
			<?php echo $moduleclass_sfx ?>interval = setInterval(slideShow, <?php echo $params->get('timeout', 5); ?>000);
		    });
		    <?php endif; ?>
		});
		slideShow = function() {
		    var selectedEffect = '<?php echo $params->get('slideshow_effect', 'slide'); ?>';
		    var displayToggled = false;
		    var current1 = $('.<?php echo $moduleclass_sfx ?>testimModItem:visible');
		    var nextSlide = current1.next('.<?php echo $moduleclass_sfx ?>testimModItem');
		    if(selectedEffect == 'slide'){
			var hideoptions = {
			    "direction": "left",
			    "mode": "hide"
			};
			var showoptions = {
			    "direction": "right",
			    "mode": "show"
			};
		    }else{
			var hideoptions = {
			    "mode": "hide"
			};
			var showoptions = {
			    "mode": "show"
			};
		    }
		    if (current1.is(':last-child')) {
			current1.effect(selectedEffect, hideoptions, 1000);
			$("#<?php echo $moduleclass_sfx ?>testimModItem_0").effect(selectedEffect, showoptions, 1000);
		    }
		    else {
			current1.effect(selectedEffect, hideoptions, 1000);
			nextSlide.effect(selectedEffect, showoptions, 1000);
		    }
		}
		<?php if(!$params->get('isstatic') && count($list)>1) : ?>
		<?php echo $moduleclass_sfx ?>interval = setInterval(slideShow, <?php echo $params->get('timeout', 5); ?>000);
		<?php endif; ?>
	    })(jplace.jQuery);
	</script>
<div class="<?php echo $moduleclass_sfx ?>testimonials_module" style="width: 100%;">
    <div id="<?php echo $moduleclass_sfx ?>testimonials" style="width: 100%; height: <?php echo trim($params->get('height'), 'px '); ?>px; overflow: hidden; position: relative; ">
<?php


if ($isStatic) {
    $list = array($list[rand(0,count($list)-1)]);
}
//ob_start();
foreach ($list as $key => $value) {
	?>
    <div class="<?php echo $moduleclass_sfx ?>testimModItem<?php if($key>0) : ?> fade<?php endif; ?>" id="<?php echo $moduleclass_sfx ?>testimModItem_<?php echo $key; ?>" style="float:left; position: absolute; ">
					<?php require(JModuleHelper::getLayoutPath('mod_testimonials',$params->get('layout', 'default_body')));?>
    </div>
	<?php
}
//file_put_contents('html.html', ob_get_clean());
?>
</div>
</div>

<?php modTestimonialsHelper::$count_modules++; ?>