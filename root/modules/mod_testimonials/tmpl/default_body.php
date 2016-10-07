<?php
/**
* Testimonials Module for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

$link = '';
if ($params->get('show_readmore') == 1) {
	if ($modal) {
		$link .= '<a id="tstmnl_link_cm" class="modtm_iframe" href="'.JRoute::_('index.php?option=com_testimonials&Itemid='.$tm_itemid.'&tmpl=component#anc_'.$value->id).'">';
	}else {
		$link .= "<a id='tstmnl_link_cm' href='".JRoute::_("index.php?option=com_testimonials&Itemid=$tm_itemid&anc=".$value->id)."&limitstart=0'>";
	}
	$link .= JText::_('MOD_TESTIMONIALS_READ'). "</a>";
}

if ($show_add_new && JFactory::getUser()->authorise('core.create', 'com_testimonials')) {
	$addnew = "<a class='modtm_iframe' href='".JRoute::_("index.php?option=com_testimonials&view=form&tmpl=component&Itemid=$tm_itemid")."'>" . JText::_('MOD_TESTIMONIALS_NEW') . "</a>";
}
?>

<div class='<?php echo $moduleclass_sfx ?>mod_testimonial_div'>
<?php

if ($params->get('show_caption')) {

	?>
		<div class='<?php echo $moduleclass_sfx ?>mod_testimonials_caption'>
			<?php echo $value->t_caption;?>
		</div>
            <?php } ?>
		<span class="<?php echo $moduleclass_sfx ?>mod_testimonial_text">
		    <span class='mod_testimonial_text_line' style='line-height:inherit;display:none;'>&nbsp;</span>
		<?php if ($params->get('show_avatar_module')) {
			if(empty($value->avatar)) $value->avatar = '';
			$avatar = $helper->getAvatar($value->photo, $value->avatar);
			if(!empty($avatar)){
			    ?><div class="tstmnl_avatar"><?php echo $avatar;?></div><?php
			}
		}
		?>
		<?php echo strip_tags($value->testimonial); ?>
		</span>
	<?php
	if($params->get('show_first')) {
	echo '<div class="testimonials_buttons">';
		if($params->get('show_readmore')){
			?><div class='mod_testimonial_readmore' ><?php echo $link;?></div><?php
		}
		if($show_add_new){
			?><div class='mod_testimonial_readmore' ><?php echo $addnew;?></div><?php
		}
	echo '</div>';
	}
	?>
	<br style="clear:both;" />
	<?php
	if($params->get('show_author_module')) {
		?><div class="mod_testimonial_author"><?php echo nl2br($value->t_author);?></div><?php
		?><div class="mod_testimonial_author"><?php echo $value->author_description;?></div><?php
	}
	if(!$params->get('show_first')) {
	echo '<div class="testimonials_buttons">';
		if($params->get('show_readmore')){
			?><div class="mod_testimonial_readmore"><?php echo $link;?></div><?php
		}
		if($show_add_new && JFactory::getUser()->authorise('core.create', 'com_testimonials')){
			?><div class="mod_testimonial_readmore"><?php echo $addnew;?></div><?php
		}
	echo '</div>';
	}

?>
</div>