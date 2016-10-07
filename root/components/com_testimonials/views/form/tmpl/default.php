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

?>
  <link rel="stylesheet" href="<?php echo(JUri::root()); ?>components/com_testimonials/assets/bootstrap/css/font-awesome.css" type="text/css" />
<?php if($this->params->get('use_editor') == 1) : ?>
<script type='text/javascript'>
    (function($) {
     $(document).ready(function () {
	 var editor = new wysihtml5.Editor("jform_testimonial", { // id of textarea element
	    toolbar:      "jform_testimonial_toolbar", // id of toolbar element
	    parserRules:  wysihtml5ParserRules // defined in parser rules set 
	 });
	 <?php if($this->params->get('show_authordesc') == 1) : ?>
	 var editor2 = new wysihtml5.Editor("jform_author_description", { // id of textarea element
	    toolbar:      "jform_author_description_toolbar", // id of toolbar element
	    parserRules:  wysihtml5ParserRules // defined in parser rules set 
	 });
	 <?php endif; ?>
     });
     })(jplace.jQuery);
  </script>
<?php endif; ?>
  
  
	<script language="JavaScript" type="text/javascript">
		var show_captcha = '<?php echo $params->get('show_captcha');?>';
		var show_recaptcha = '<?php echo $params->get('show_recaptcha');?>';
	    (function($) {
		submit_form = function(task) {
		    if (validateFields(true)) {
                        if (show_captcha && !show_recaptcha) {
                            check_and_submit(captcha_params);
                        }else {
			    $('input[name=task]', $('#adminForm')).val(task);
			    $('#adminForm').submit();
			}
		    }
		}
				
		validateFields = function(msg) {
			var error_message = [];
			var error = false;
			var form = document.adminForm;
			<?php if ($params->get('show_caption')) : ?>
                                $('#jform_t_caption').removeClass('invalid');
                                $('[for=jform_t_caption]').removeClass('invalid');
                                if (document.getElementById('jform_t_caption').value == '') {
                                        $('#jform_t_caption').addClass('invalid');
                                        $('[for=jform_t_caption]').addClass('invalid');
					error_message.push('<?php echo (JText::_('COM_TESTIMONIALS_EDIT_CAPTION_ERROR')); ?>');
                                        error = true;
                                }
                        <?php endif; ?>
                        <?php if ($params->get('show_authorname')) : ?>
                                $('#jform_t_author').removeClass('invalid');
                                $('[for=jform_t_author]').removeClass('invalid');
                                if (document.getElementById('jform_t_author').value == '') {
                                        $('#jform_t_author').addClass('invalid');
                                        $('[for=jform_t_author]').addClass('invalid');
					error_message.push('<?php echo (JText::_('COM_TESTIMONIALS_EDIT_AUTHOR_NAME_ERROR')); ?>');
                                        error = true;
                                }
                        <?php endif; ?>
                                $('#jform_testimonial').removeClass('invalid');
                                $('[for=jform_testimonial]').removeClass('invalid');
                                if (document.getElementById('jform_testimonial').value == '') {
                                        $('#jform_testimonial').addClass('invalid');
                                        $('[for=jform_testimonial]').addClass('invalid');
					error_message.push('<?php echo (JText::_('COM_TESTIMONIALS_EDIT_TESTIMONIAL_ERROR')); ?>');
                                        error = true;
                                }
			<?php foreach($this->custom_fields as $field){					
					if ($field->required){
						if ($field->type == 'url') {?>
							$('#customs_link_<?php echo $field->id;?>').removeClass('invalid');
                                                        $('[for=customs_link_<?php echo $field->id;?>]').removeClass('invalid');
                                                        $('#customs_name_<?php echo $field->id;?>').removeClass('invalid');
                                                        $('[for=customs_name_<?php echo $field->id;?>]').removeClass('invalid');
							if (document.getElementById('customs_link_<?php echo $field->id;?>').value == '') {
								$('#customs_link_<?php echo $field->id;?>').addClass('invalid');
								$('[for=customs_link_<?php echo $field->id;?>]').addClass('invalid');
								error_message.push('<?php echo (JText::sprintf('COM_TESTIMONIALS_EDIT_URL_LINK_ERROR_EMPTY', $field->name)); ?>');
								error = true;
							}
							if (document.getElementById('customs_name_<?php echo $field->id;?>').value == ''){
								$('#customs_name_<?php echo $field->id;?>').addClass('invalid');
								$('[for=customs_name_<?php echo $field->id;?>]').addClass('invalid');
								error_message.push('<?php echo (JText::sprintf('COM_TESTIMONIALS_EDIT_URL_NAME_ERROR_EMPTY', $field->name)); ?>');
								error = true;
							}
							
						<?php }else {?>
							$('#customs_<?php echo $field->id;?>').removeClass('invalid');
                                                        $('[for=customs_<?php echo $field->id;?>]').removeClass('invalid');
							if (document.getElementById('customs_<?php echo $field->id;?>').value == '') {
								$('#customs_<?php echo $field->id;?>').addClass('invalid');
								$('[for=customs_<?php echo $field->id;?>]').addClass('invalid');
								error_message.push('<?php echo (JText::sprintf('COM_TESTIMONIALS_EDIT_CUSTOM_ERROR_EMPTY', $field->name)); ?>');
								error = true;
							}
						<?php }?>
					<?php }
					if ($field->type == 'textemail'){
					?>
					
							if (document.getElementById('customs_<?php echo $field->id;?>').value !== ''){
								$('#customs_<?php echo $field->id;?>').removeClass('invalid');
                                                                $('[for=customs_<?php echo $field->id;?>]').removeClass('invalid');
								var email = document.getElementById('customs_<?php echo $field->id;?>').value;
								if (!email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/)){
									$('#customs_<?php echo $field->id;?>').addClass('invalid');
									$('[for=customs_<?php echo $field->id;?>]').addClass('invalid');
									error_message.push('<?php echo (JText::sprintf('COM_TESTIMONIALS_EDIT_CUSTOM_ERROR_INCORRECT', $field->name)); ?>');
									error = true;
								}
							}
				<?php 
					}
					if ($field->type == 'url'){
					?>
							if (document.getElementById('customs_link_<?php echo $field->id;?>').value !== ''){
								$('#customs_link_<?php echo $field->id;?>').removeClass('invalid');
                                                                $('[for=customs_link_<?php echo $field->id;?>]').removeClass('invalid');
								var url = document.getElementById('customs_link_<?php echo $field->id;?>').value;
								if (!url.match(/(ftp:\/\/|http:\/\/|https:\/\/|www)(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/)){
									$('#customs_link_<?php echo $field->id;?>').addClass('invalid');
									$('[for=customs_link_<?php echo $field->id;?>]').addClass('invalid');
									error_message.push('<?php echo (JText::sprintf('COM_TESTIMONIALS_EDIT_CUSTOM_ERROR_INCORRECT', $field->name)); ?>');
									error = true;
								}
							}
				<?php 
					}
					
					
				} ?>
				<?php if(!JFactory::getUser()->authorise('core.edit', 'com_testimonials')) : ?>
				<?php if ($params->get('show_captcha') && !$params->get('show_recaptcha')) : ?>
                                    $('#captcha_value').removeClass('invalid');
                                    if (document.getElementById('captcha_value').value == '') {
                                            $('#captcha_value').addClass('invalid');
					    error_message.push('<?php echo (JText::_('COM_TESTIMONIALS_EDIT_CAPTCHA_ERROR_EMPTY')); ?>');
                                            error = true;
                                    }
                                <?php endif; ?>
				<?php endif; ?>
				if(!error) return true;
				else{
				    if(msg) showErrorMsg(error_message);
				    return false;
				}
		}
		
		showErrorMsg = function(error_message){
		    if(typeof(error_message) != 'undefined'){
			error_message.reverse();
			error_message.push('<?php echo JText::_('COM_TESTIMONIALS_EDIT_FORM_ERROR');?>');
			error_message.reverse();
			alert(error_message.join("\n"));
		    }else{
			alert('<?php echo JText::_('COM_TESTIMONIALS_EDIT_REQUIRED_ERROR');?>');
		    }
		}
		
		hideErrorMsg = function(){
			document.getElementById('error_msg').style.visibility = 'hidden';
			document.getElementById('error_msg').style.color = '';
			document.getElementById('error_msg').innerHTML = '';
		}
		
	    })(jplace.jQuery);
</script>
<div class="edit item-page">
<form action="<?php echo JRoute::_('index.php?option=com_testimonials&view=form&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="testim-adminform"  enctype="multipart/form-data">
    
    <div class="testim-buttoms">
	<button class="btn" type="button" onclick="submit_form('topic.save');"> 
	    <?php echo JText::_('COM_TESTIMONIALS_SAVE') ;?>
	</button>
	<button class="btn" type="button" onclick='window.parent.document.getElementById("fancybox-close").click();'>
	    <?php echo JText::_('COM_TESTIMONIALS_CLOSE') ;?>
	</button>
    </div>

		<?php
			$template->showForm($this->form, $this->item, $this->custom_fields, $this->tags);
			
			?>
		<input type="hidden" name="task" value="" />
		<?php
			if(JFactory::getApplication()->input->getVar('tmpl')=='component')
			{
				?>
				<input type="hidden" name="tmpl" value="component" />
				<?php
			}
		?>
    <div class="testim-buttoms">
	<button class="btn" type="button" onclick="submit_form('topic.save');"> 
	    <?php echo JText::_('COM_TESTIMONIALS_SAVE') ; ?>
	</button>
	<button class="btn" type="button" onclick='window.parent.document.getElementById("fancybox-close").click();'>
	    <?php echo JText::_('COM_TESTIMONIALS_CLOSE') ;?>
	</button>
    </div>
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
</body>
</html>
