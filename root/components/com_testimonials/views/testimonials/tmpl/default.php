<?php
/**
* Testimonials Component for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

$params 	= $this->params;
$template 	= $this->template;
$pagination	= $this->pagination;

$template->addJQuery();
$template->addFancyBox();
$template->addRating();

if (JFactory::getApplication()->input->getVar('anc',0,'get')) {

    foreach ($this->items as $item) {
        if ($item->id == JFactory::getApplication()->input->getVar('anc', 0, 'get')) {
            $template->showTextTitle(JText::_('COM_TESTIMONIALS_CURRENT'));
	    $item->customs = $this->getCustomFields($item->id);
            $template->showTestimonial($item, $this->html, $this->custom_array, 0, '');
            echo '<hr />';
        }
    }

    $template->showTextTitle(JText::_('COM_TESTIMONIALS_OTHER'));
} else {
    if ($params->get('show_title')!=0 ){
	if($params->get('texttitle')) $template->showTextTitle($this->escape($params->get('texttitle')));
	else $template->showTextTitle(JText::_('COM_TESTIMONIALS_LIST'));
    }
}

 $id = (int)JFactory::getApplication()->input->getInt('id');
 $tmpl = JFactory::getApplication()->input->getVar('tmpl');
 if ($tmpl!="component")
		{
 			if ($id)  $template->showBackLink();
		}
if ($this->intro_text) echo '<div class="intro_text" style="border-bottom: solid 1px #DDD;margin-bottom: 10px;">'
 .$this->intro_text.'</div>';
 $template->showTestimonialLink();

 ?>
<script type="text/javascript">
	(function($) {
	    $(document).ready(function () {
		
		$("a.comtm_iframe").fancybox({'type':'iframe', 'centerOnScroll':true, 'enableEscapeButton':true, 'enableEscapeButton':true, 'width':820, 'height':750});
	    });
	    storeComment = function(id) {
		    $.ajax({
			    url: '<?php echo JURI::base();?>index.php?option=com_testimonials&task=rating.addcomment&tmpl=component&no_html=1',
			    type: "POST",
			    data: {text: $('#add_comment'+id+' > textarea').val(), id:id},
			    dataType: 'xml',
			    success: function (xml) {
				    var text = $(xml).find('text').text();
				    var html = $(xml).find('html_exists').text();
				    $('#add_comment' + id).slideToggle();
				    if(html){
					    $('#add_comment' + id).before(html);
				    } else if(text){
					    $('#add_comment' + id).prev().find('span.comment_text').html(text);
				    }
			    }
		    });
	    }
	    deleteComment = function(id){
		    $.ajax({
			    url: '<?php echo JURI::base();?>index.php?option=com_testimonials&task=rating.deletecomment&tmpl=component&no_html=1',
			    type: "POST",
			    data: {id:id},
			    dataType: 'html',
			    success: function (data) {
				    if(data){
					    $('#add_comment' + id).prev().fadeOut('slow');
				    }
			    }
		    });
	    };
	})(jplace.jQuery);
</script>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<div id="testimonials-list">
<?php
$n = count($this->items);
	for ($i=0; $i < $n; $i++)
	{
		$item = &$this->items[$i];
		$item->customs = $this->getCustomFields($item->id);

		$this->adminForm($item->id, $item->published);
		$template->showTestimonial($item, $this->html, $this->custom_array, (( $i<$n-1 )?0:1), (($i+1)%2 == 0)?'odd':'');
	}
	if ($n==0) echo '<div><h2>Testimonial(s) not found</h2></div>';
	else if (!JFactory::getApplication()->input->getInt('id'))
	{
?>
		<div class="pagination">
			<div class="tmpagination" align="center" style="text-align:center;"><?php echo $this->pagination->getListFooter();?></div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php
	}
			if(JFactory::getApplication()->input->getVar('tmpl')=='component')
			{
				?>
				<input type="hidden" name="tmpl" value="component" />
				<?php
			}
		?>
</div>
</form>