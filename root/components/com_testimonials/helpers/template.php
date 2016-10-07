<?php
/**
* Testimonials Component for Joomla 3
* @package Testimonials
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');

class TemplateHelper
{
    protected $document;
    protected $template;
    protected $config;
    protected $width;
    protected $live_site_url;
    protected $template_name;
    protected $nophoto;
    
    function __construct($template='default'){
	$this->document = JFactory::getDocument();
	$this->template = $template;
	if(is_dir(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_testimonials' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR .$this->template)){
	    $this->document->addStyleSheet(JURI::root().'components/com_testimonials/templates/'.$this->template.'/css/style.css');
	}
	$this->config = JComponentHelper::getParams('com_testimonials');
	$this->width  = 300;
	$this->live_site_url=JURI::base();
	$this->template_name=$template;
	$this->config->set('template',$this->template_name);
	$this->config->set('addingbyuser', JFactory::getUser()->authorise('core.create', 'com_testimonials'));
	$this->nophoto = false;

	$this->addJQuery();
    }
    
    static function addFancyBox(){
	JHtml::stylesheet(JURI::root().'components/com_testimonials/assets/jquery.fancybox-1.3.4.css');
	JHtml::script(JURI::base() . 'components/com_testimonials/assets/jquery.fancybox-1.3.4.js');
    }
    
    static function addJQuery(){
	JHtml::script(JURI::base() . 'components/com_testimonials/assets/jplace.jquery.js');
    }
    
    static function addRating(){
	JHtml::stylesheet(JURI::base() . 'components/com_testimonials/assets/jquery.rating.css');
	//JHtml::script(JURI::base() . 'components/com_testimonials/assets/jquery.rating.js');
    }
    
    static function addHeaderScript($script){
	
    }
    
    function showTextTitle($title="")
	{
		?>
		<h1><?php echo $title;?></h1>
		<?php
	}

	function showBackLink()
	{
		$Itemid=(int) JFactory::getApplication()->input->getInt('Itemid',0);

		?>
			<a class="backlink" href="<?php echo JRoute::_("index.php?option=com_testimonials&Itemid=".$Itemid); ?>" ><?php echo Jtext::_('COM_TESTIMONIALS_SHOW_ALL'); ?></a>
		<?php
	}

	function showTestimonialLink(){
	    $Itemid=(int) JFactory::getApplication()->input->getInt('Itemid',0);
		if($this->config->get('addingbyuser') )
		{	?>
				<a href="<?php echo 'index.php?option=com_testimonials&view=form&tmpl=component&Itemid='.$Itemid ?>" class="comtm_iframe tm_add_button" onclick="return false;"><?php echo (JText::_('COM_TESTIMONIALS_ADD')); ?></a><br />
				<?php
			}
	}

	function _pluginProcess($text){
		$text = JHTML::_( 'content.prepare', $text );
		return $text;
	}

	function showTestimonial( &$row, $html, $custom_array, $isLast, $odd, $plugin=false )
	{
		$image = '';
		$avatar ='';

		$row->t_author = nl2br($row->t_author);
		$html = $this->_pluginProcess($html);

		$avatar_trigger = 'avatar_on';
		if ($this->config->get('show_avatar') == 1) {
			$avatar = $this->getAvatar($row->photo, isset($row->avatar)?$row->avatar:'');
		}else{
			$avatar = "";
			$avatar_trigger = 'avatar_off';
		}

		if ($this->nophoto==true) {$avatar = JURI::root()."components/com_testimonials/templates/".$this->template_name."/images/tnnophoto.jpg";}

		if(!empty($avatar)){
			$avatar = "<img class=\"avatar\" src=\"$avatar\" alt=\"\"/>";
		}else{
			$avatar = '';
		}

		$return_str = $html;
		$replaseFrom = $replaseTo = array();
		$replaseFrom[] 	= '[caption]';
		if($row->t_caption == '_') $row->t_caption = '&nbsp;';
		$row->t_caption = $this->_pluginProcess($row->t_caption);
		$replaseTo[] 	= $this->config->get('show_caption')?$row->t_caption:'';

		$replaseFrom[] 	= '[avatar]';
		$replaseTo[] 	= $this->config->get('show_avatar')?$avatar:'';

		$replaseFrom[] 	= '[testimonial]';
		$row->testimonial = $this->_pluginProcess($row->testimonial);
		$replaseTo[] 	= $row->testimonial;

		$replaseFrom[] 	= '[author]';
		$row->t_author = $this->_pluginProcess($row->t_author);
		$replaseTo[] 	= $this->config->get('show_authorname')?$row->t_author:'';

		$replaseFrom[] 	= '[author_descr]';
		$row->author_description = $this->_pluginProcess($row->author_description);
		$replaseTo[] 	= $this->config->get('show_authordesc')?$row->author_description:'';

		$replaseFrom[] 	= '[rating]';
		$replaseTo[] 	= '';

		$replaseFrom[] 	= '[date]';
		$replaseTo[] 	= (!empty($row->date_added)) ? JHtml::_('date', $row->date_added, JText::_('DATE_FORMAT_LC1')) : '';

		/*images*/
		if (!empty($row->images)) {
			$list = explode("|", $row->images);
			$str = '<ul class="imagelist">';
			foreach ($list as $i=>$item) {
				if(!empty($item)) $str .= '<li><a class="gallery" rel="gal'.$row->id.'" href="'.JURI::base().'images/testimonials/'.$item.'"><img src="'.JURI::base().'index.php?option=com_testimonials&task=showpicture&image=images/testimonials/'.$item.'&width=100" alt="" /></a></li>';
			}
			$str .= '</ul>

		<script>
			jQuery.noConflict();
			(function($) {
				$(function() {
					$(\'a.gallery\').fancybox({
						\'transitionIn\'	:	\'elastic\',
						\'transitionOut\'	:	\'elastic\',
						\'speedIn\'		:	600,
						\'speedOut\'		:	200,
						\'overlayShow\'	:	false
					});
				});
			})(jplace.jQuery);
		</script>';

		} else {
			$str = '';
		}

		$replaseFrom[] 	= '[imagelist]';
		$replaseTo[] 	= (!empty($str)) ? $str : '';

		if (sizeof($row->customs)>0)
		{
			for ( $i = 0, $n = sizeof( $row->customs ); $i < $n; $i++ )
			{
			    
				$custom = $row->customs[$i];
				$custom->label = trim($custom->key, "[]");
                $custom->key = strtolower($custom->key);
				switch ( $custom->type )
				{
					case 'text':
						$replaseFrom[] 	= $custom->key;
						$replaseTo[] 	= $custom->value;
						break;
					case 'rating':
						$replaseFrom[] 	= $custom->key;
						$replace = '';
						for($a=1;$a<6;$a++){
						    $replace .= '<i class="star-rating-i-'.((isset($custom->value) && $custom->value >= $a) ? 'on' : 'off').'">&nbsp;&nbsp;&nbsp;&nbsp;</i>';
						}
						$replaseTo[] 	= '<span class="tm_stars">'.$replace.'</span>';
						break;
					case 'url':
						$url = explode("|", $custom->value);
						if(!empty($url[0])){
						    if(!preg_match('|^https?://|i', $url[0])) $url[0] = 'http://'.$url[0];
						}
						$replaseFrom[] 	= $custom->key;
						$replaseTo[] 	= '<a href="'.(isset($url[0])?$url[0]:(isset($url[1])?$url[1]:'#')).'" name="testimonial_link" target="_blank">'.(isset($url[1])?$url[1]:(isset($url[0])?$url[0]:'')).'</a>';
						break;
					case 'textemail':
						$replaseFrom[] 	= $custom->key;
						$replaseTo[] 	= '<a href="mailto:'.$custom->value.'" name="testimonial_email">'.$custom->value.'</a>';
						break;
				}
			}
		}

		$return_str = str_ireplace($replaseFrom,$replaseTo,$return_str);


		/*clear custom fields*/
		$replaseFrom = $replaseTo = array();
		if (is_array($custom_array)&& sizeof($custom_array)>0)
		{
			foreach ( $custom_array as $ca )
			{
				$replaseFrom[] 	= $ca;
				$replaseTo[] 	= '';
			}
		}
		$return_str = str_ireplace($replaseFrom,$replaseTo,$return_str);

		$user = JFactory::getUser();
		$iCan = new stdClass();
		$iCan->comment = $user->authorise('core.comment', 'com_testimonials');

		$tags =($this->config->get('show_tags')&& isset($row->tags))?'<div class="tags">'.Jtext::_('COM_TESTIMONIALS_TAGS').$row->tags.'</div>':' ';
		$return_str = '<a name="anc_'.@$row->id.'"></a>
			<div class="testimonial '.$avatar_trigger.' '.$odd.'">
				'.$return_str.$tags;
		if (!empty($row->comment)) {
			$comment = explode('|',$row->comment);
			$user 	 = JFactory::getUser($comment[0]);
			$return_str .= '<div class="owner_comment"><span class="comment_user_name"><strong>'.JText::_('COM_TESTIMONIALS_OWNER_REPLY').'</strong></span>';
			$return_str .= ($iCan->comment && !$plugin) ? '<span class="dlt-comment"><img src="'.JURI::root().'components/com_testimonials/assets/images/stop.png" alt="'.JText::_('COM_TESTIMONIALS_COMMENT_DELETE').'" title="'.JText::_('COM_TESTIMONIALS_COMMENT_DELETE').'" onclick="javascript:deleteComment('.@$row->id.');"/></span>' : '';
			$return_str .= '<br/><span class="comment_text">'.$comment[1].'</span></div>';
		}
		if ($iCan->comment && !$plugin) {

			$return_str .= '<div id="add_comment'.@$row->id.'" style="display: none;"><textarea name="comment" class="comment-textarea">'.((!empty($row->comment)) ? $comment[1] : '').'</textarea><br /><input type="button" name="add_comment" onclick="storeComment(\''.@$row->id.'\');" value="'.JText::_('COM_TESTIMONIALS_CAN_COMMENT').'" class="submit-tstl-comment" /></div>';
		}
		$return_str .= '</div>';
		if(!$isLast) {$return_str .= "<span class=\"tm_separator\"><!--x--></span>";}

		if (!$plugin) echo $return_str;

		return $return_str;
	}


	function getAvatar($photo='', $cb_avatar='')
	{
		$this->nophoto=false;
		$avatar = '';
			if ($photo != ''){
				if (file_exists(JPATH_SITE."/".$photo)) {
					$path = explode('/', $photo);
					$fname = array_pop($path);
					$path = implode('/', $path);
					if(file_exists(JPATH_SITE.'/'.$path."/thumb_".$fname)){
					    list($width, $height, $type) = getimagesize(JPATH_SITE.'/'.$path."/thumb_".$fname);
					    if ($width == $this->config->get('thumb_width')) {
						$avatar = $path."/thumb_".$fname;
					    } else {
						jimport( 'joomla.filesystem.file' );
						JFile::delete(JPATH_SITE.'/'.$path."/thumb_".$fname);
						$TimgHelper = new TimgHelper();
						$thumb = $TimgHelper->captureImage($TimgHelper->resize(JPATH_SITE."/".$photo, $this->config->get('thumb_width', '110'), $this->config->get('thumb_width', '110')), $photo);
						JFile::write(JPATH_SITE.'/'.$path."/thumb_".$fname, $thumb);
						if(file_exists(JPATH_SITE.'/'.$path."/thumb_".$fname)) $avatar = $path."/thumb_".$fname;
					    }
					}else{
					    jimport( 'joomla.filesystem.file' );
					    $TimgHelper = new TimgHelper();
					    $thumb = $TimgHelper->captureImage($TimgHelper->resize(JPATH_SITE."/".$photo, $this->config->get('thumb_width', '110'), $this->config->get('thumb_width', '110')), $photo);
					    JFile::write(JPATH_SITE.'/'.$path."/thumb_".$fname, $thumb);
					    if(file_exists(JPATH_SITE.'/'.$path."/thumb_".$fname)) $avatar = $path."/thumb_".$fname;
					}
				}else {
					$this->nophoto=true;
				}
			}else{
				if ($this->config->get('use_cb') == 1) {
					$check = explode('/',@$cb_avatar);
					$check = $check[0];

					if (isset($cb_avatar) && $check != 'gallery') {
						if (file_exists(JPATH_SITE."/images/comprofiler/tn".$cb_avatar)) {
							$avatar = $this->live_site_url."images/comprofiler/tn".$cb_avatar;
						}else{
							$this->nophoto=true;
						}
					}elseif (isset($cb_avatar) && $check == 'gallery') {
						if (file_exists(JPATH_SITE."/images/comprofiler/".$cb_avatar)) {
							$avatar = $this->live_site_url."images/comprofiler/".$cb_avatar;
						}else{
							$this->nophoto=true;
						}
					}else{
						$this->nophoto=true;
					}
				}
				if ($this->config->get('use_jsoc') == 1 && $cb_avatar) {
					if (file_exists(JPATH_SITE."/".$cb_avatar)) {
							$avatar = $this->live_site_url."/".$cb_avatar;
							$this->nophoto=false;
						}else{
							$this->nophoto=true;
						}
				}
				if (!$cb_avatar) $this->nophoto=true;
			}
		return $avatar;
	}

	function getUserAvatar($uId)
	{
	    $settings = JComponentHelper::getParams ("com_testimonials");
	    $avatar = '';
	    if($uId>0 && ($settings->get('use_cb') == 1 || $settings->get('use_jsoc') == 1 )){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		if($this->config->get('use_cb') == 1){
		    $query->select('CONCAT("images/comprofiler/",compr.avatar) as `avatar`');
		    $query->from('`#__comprofiler` AS `compr`');
		    $query->where('compr.user_id = '.$uId);
		}
		if($this->config->get('use_jsoc') == 1){
		    $query->select('jsoc.thumb as `avatar`');
		    $query->from('`#__community_users` AS `jsoc`');
		    $query->where('jsoc.userid = '.$uId);
		}
		$db->setQuery($query);
		$data = $db->loadObject();
		if(!empty($data->avatar) && file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . $data->avatar)) $avatar = $data->avatar;
	    }
	    return $avatar;

	}

    function showForm($form = null, $item=null, $custom_fields=null, $tags=null) {
	$user		= JFactory::getUser();
	if ($this->config->get('addingbyuser')){
	    $settings = JComponentHelper::getParams ("com_testimonials");
	    $error = JFactory::getApplication()->input->getBool('error', false);
	    if($error){
		$form_data = JFactory::getApplication()->getUserState('com_testimonials.edit.form.data', array());
		$item->photo = $form_data['photo'];
		$item->images = $form_data['exist_images'];
		if(!empty($form_data['tags'])) $tags->selected = $form_data['tags'];
		if(!empty($form_data['remove_image'])){
		    $remove_images = explode('|', $form_data['remove_image']);
		    foreach($remove_images as $remove_image){
			$item->images = str_replace($remove_image, '', $item->images);
		    }
		    $item->images = str_replace('||', '|', $item->images);
		}
	    }else $form_data['remove_image'] = '';
	    if (($settings->get('use_cb') == 1 || $settings->get('use_jsoc') == 1 ) && ($user->id || $item->id) && empty($item->avatar)){
		if($item->id) $item->avatar = $this->getUserAvatar($item->user_id_t);
		else $item->avatar = $this->getUserAvatar($user->id);
	    }else $item->avatar = '';
	    
	    ?>
<script type='text/javascript'>
	(function($) {
     $(document).ready(function () {
     if ($("[rel=tooltip]").length) {
	$("[rel=tooltip]").tooltip();
     }
     $("[rel=rating]").click(function (){
	var block_name = $(this).attr('name');
	$(this).parent().children().removeClass('hover');
	$('#customs_'+$(this).attr('field_id')).val($(this).attr('rating'));
	$(this).toggleClass('hover');
     });
     deleteImage = function(el){
	$($(el).parent()).remove();
	$('#remove_image').val($('#remove_image').val()+'|'+jQuery(el).attr('image'));
     };
     deleteAvatar = function(el){
	$('#avatarUploadButton').css('display','block');
	$('#avatarUploadImage').css('display','none');
	$('#jform_photo').val(''); 
     };
     <?php if($settings->get('show_addimage') == 1) : ?>
     $('#imageUpload').fileupload({
	sequentialUploads: false,
        dataType: 'json',
	formData: {task: 'new_image', id: '<?php echo $item->id?>'},
	xhrFields: {
        withCredentials: true
    },
	submit: function (e, data) {
		$('#imageProgressContainer').css('display','block');
	    },
        done: function (e, data) {
	    if(data.result.status == 'ok'){
		$('#uploadedImages').append('<a href="javascript:void(0)" class="testim-img"><img src="<?php echo JURI::root().'/images/testimonials/';?>'+data.result.image+'" alt="'+data.result.image+'"/><span class="testim-del-img" image="'+data.result.image+'" onclick="deleteImage(this);"></span></a>');
		$('#uploadedImages a:last-child > img').load(function(){
		    if($('#uploadedImages a:last-child > img').height() < $('#uploadedImages a:last-child > img').width()){
			$('#uploadedImages a:last-child > img').css('height','100%');
			$('#uploadedImages a:last-child > img').css('width','auto');
		    }
		});
		$('#jform_exist_images').val($('#jform_exist_images').val()+'|'+data.result.image);
		$('#imageProgressContainer').css('display','none');
	    }
	    if(data.result.status == 'bad' && data.result.message != ''){
		alert(data.result.message);
		$('#imageProgressContainer').css('display','none');
	    }
        } 
    });
    <?php endif; ?>
    <?php if($settings->get('allow_photo') == 1) : ?>
     $('#avatarUpload').fileupload({
	sequentialUploads: false,
        dataType: 'json',
	formData: {task: 'new_avatar', id: '<?php echo $item->id?>' },
	submit: function (e, data) {
		$('#avatarUploadButton').css('display','none');
		$('#avatarProgressContainer').css('display','block');
	},
        done: function (e, data) { 
	    if(data.result.status == 'ok'){
		$('#avatarUploadImage').html('<img src="<?php echo JURI::root().'/images/testimonials/';?>'+data.result.image+'" alt="'+data.result.image+'"/><span class="testim-del-img" image="'+data.result.image+'" onclick="deleteAvatar(this);"></span>');
		$('#avatarProgressContainer').css('display','none');
		$('#avatarUploadImage').css('display','block');
		$('#jform_photo').val('images/testimonials/'+data.result.image);
		$('#avatarUploadImage > img').load(function(){
		    if($('#avatarUploadImage > img').height() < $('#avatarUploadImage > img').width()){
			$('#avatarUploadImage > img').css('height','100%');
			$('#avatarUploadImage > img').css('width','auto');
		    }
		});
	    }
	    if(data.result.status == 'bad'){
		if(data.result.message != '') alert(data.result.message);
		$('#avatarProgressContainer').css('display','none');
		$('#avatarUploadButton').css('display','block');
	    }
        } 
    });
    <?php endif; ?>
   });
   })(jplace.jQuery);
  </script>
	<input type="hidden" name="jform[date_added]" value="<?php echo date('Y-m-d H:i:s', time());?>" />
	<?php if($item->id) : ?>
	    <input type="hidden" name="jform[user_id_t]" value="<?php echo($item->user_id_t);?>" />
	<?php endif; ?>
	    <fieldset class="testim-required">
                    <hr class="testim-line testim-top-line"/>
		    <?php if ($settings->get('show_caption')) : ?>
		    <div class="testim-field-group">
                    <label class="testim-label testim-required" for="jform_t_caption" rel="tooltip" title="<?php echo JText::_($form->getFieldAttribute ('t_caption', 'description')); ?>" ><?php echo JText::_($form->getFieldAttribute ('t_caption', 'label')); ?>:</label>
			    <?php echo $form->getInput('t_caption'); ?>
                    </div>
		    <?php else : ?>
			<input type="hidden" name="jform[t_caption]" value="<?php echo((!empty($item->t_caption) ? $item->t_caption : '_')); ?>" />
		    <?php endif; ?>
                    <div class="testim-field-group">
                    <label class="testim-label" for="jform_testimonial" rel="tooltip" title="<?php echo JText::_($form->getFieldAttribute ('testimonial', 'description')); ?>" ><?php echo JText::_($form->getFieldAttribute ('testimonial', 'label')); ?>:</label>
                        <div class="testim-texeditor-container">
			    <div id="jform_testimonial_toolbar" style="display: none;" class="texteditor-toolbar">
			      <div class="btn-group">
				  <a class="btn" data-wysihtml5-command="bold" title="CTRL+B"><i class="icon-bold"></i></a>
				  <a class="btn" data-wysihtml5-command="italic" title="CTRL+I"><i class="icon-italic"></i></a>
				  <a class="btn" data-wysihtml5-command="underline" title="CTRL+U"><i class="icon-underline"></i></a>
				  <a class="btn" data-wysihtml5-command="createLink"><i class="icon-link"></i></a>
			      </div>
			      <div class="btn-group">
				  <a class="btn" data-wysihtml5-command="insertOrderedList"><i class="icon-list-ol"></i></a>
				  <a class="btn" data-wysihtml5-command="insertUnorderedList"><i class="icon-list-ul"></i></a>
			      </div>
			      <div data-wysihtml5-dialog="createLink" style="display: none; padding-top: 5px">
				  <div class="input-append">
				      <input type="text" class="input-large" data-wysihtml5-dialog-field="href" value="http://">
				      <a class="btn" data-wysihtml5-dialog-action="save"><i class="icon-ok"></i> <?php echo JText::_('COM_TESTIMONIALS_SAVE'); ?></a>
				      <a class="btn" data-wysihtml5-dialog-action="cancel"><i class="icon-remove"></i> <?php echo JText::_('COM_TESTIMONIALS_CANCEL'); ?></a>
				  </div>
			      </div>
			    </div>
			    <?php echo $form->getInput('testimonial'); ?>
			</div>
                    </div>               
		
		    <?php
			$unrequiredFields = 0;
			foreach ($custom_fields as $i => $custom_field) {
			    if($custom_field->required) {
				echo($this->showCustomField($custom_field, $i));
			    }else $unrequiredFields++;
			}
		    ?>
		    
		    <?php if($settings->get('allow_photo')) : ?>
			<!--Block .testim-field-group is for avatar.-->
			<div class="testim-field-group testim-images-container">
			    <div class="testim-label">&nbsp;</div>
                            <div class="testim-add-image clearfix" id="avatarUploadContainer">
				    <?php if((!empty($item->photo) && file_exists(JPATH_SITE . DIRECTORY_SEPARATOR  . $item->photo)) || !empty($item->avatar)) : ?>
				    <?php $image = (!empty($item->photo) ? $item->photo : $item->avatar); ?>
					<div class="imageProgressContainer" style="display: none;"><div id="imageProgress" class="imageProgress"></div></div>
					<a href="javascript:void(0)" class="testim-img" id="avatarUploadImage"><img src="<?php echo JURI::root().'/'.$image;?>" alt=" "/><span class="testim-del-img" image="<?php echo $image?>" onclick="deleteAvatar(this);"></span></a>
					<div class="testim-add-img2" id="avatarUploadButton" style="display:none"><span class="testim-add-img-label"><?php echo JText::_('COM_TESTIMONIALS_ADD_AVATAR'); ?></span><input type="file" name="avatar" id="avatarUpload" data-url="<?php echo JRoute::_('index.php?option=com_testimonials&task=new_avatar'); ?>" class="file-input-button" /></div>
				    <?php else : ?>
					<div id="avatarProgressContainer"  class="imageProgressContainer" style="display: none;"><div id="imageProgress" class="imageProgress"></div></div>
					<a href="javascript:void(0)" class="testim-img" id="avatarUploadImage" style="display:none"></a>
					<div class="testim-add-img2" id="avatarUploadButton" onclick="document.getElementById('avatarUpload').click(); "><span class="testim-add-img-label"><?php echo JText::_('COM_TESTIMONIALS_ADD_AVATAR'); ?></span><input type="file" onclick="event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);" name="avatar" id="avatarUpload" data-url="<?php echo JRoute::_('index.php?option=com_testimonials&task=new_avatar'); ?>" class="file-input-button" /></div>
				    <?php endif; ?>
			    </div>

			    <input type="hidden" name="jform[photo]" id="jform_photo" value="<?php echo (!empty($item->photo) ? $item->photo : '');?>" />

			</div> 
		    <?php endif; ?>
			
		    <?php if ($settings->get('show_authorname')) : ?>
		    <div class="testim-field-group">
                    <label class="testim-label testim-required" for="jform_t_author" rel="tooltip" title="<?php echo JText::_($form->getFieldAttribute ('t_author', 'description')); ?>" ><?php echo JText::_($form->getFieldAttribute ('t_author', 'label')); ?>:</label>
			    <?php echo $form->getInput('t_author'); ?>
                    </div>
		    <?php endif; ?>
                    
                    </fieldset>
                    <hr class="testim-line"/>
		    <?php if($unrequiredFields>0 || $settings->get('allow_photo') || $settings->get('show_authordesc')) : ?>
                    <div class="testim-field-group testim-field-more">
                        <div class="testim-label"></div>
                        <div class="testim-add-image">                
                            <i class="icon-caret-right" id="testim-more-label"></i><a href="" class="testim-more"><?php echo JText::_('COM_TESTIMONIALS_TELL_MORE');?></a>
                        </div>
                    </div>  
                    <fieldset>
                        <div class="testim-notrequired testim-hide">
			<?php if ($settings->get('show_authordesc')) : ?>
			<div class="testim-field-group">
			<label class="testim-label testim-required" for="jform_author_description" rel="tooltip" title="<?php echo JText::_($form->getFieldAttribute ('author_description', 'description')); ?>" ><?php echo JText::_($form->getFieldAttribute ('author_description', 'label')); ?>:</label>
			<div class="testim-texeditor-container">
			    <div id="jform_author_description_toolbar" style="display: none;" class="texteditor-toolbar">
			      <div class="btn-group">
				  <a class="btn" data-wysihtml5-command="bold" title="CTRL+B"><i class="icon-bold"></i></a>
				  <a class="btn" data-wysihtml5-command="italic" title="CTRL+I"><i class="icon-italic"></i></a>
				  <a class="btn" data-wysihtml5-command="underline" title="CTRL+U"><i class="icon-underline"></i></a>
				  <a class="btn" data-wysihtml5-command="createLink"><i class="icon-link"></i></a>
			      </div>
			      <div class="btn-group">
				  <a class="btn" data-wysihtml5-command="insertOrderedList"><i class="icon-list-ol"></i></a>
				  <a class="btn" data-wysihtml5-command="insertUnorderedList"><i class="icon-list-ul"></i></a>
			      </div>
			      <div data-wysihtml5-dialog="createLink" style="display: none; padding-top: 5px">
				  <div class="input-append">
				      <input type="text" class="input-large" data-wysihtml5-dialog-field="href" value="http://">
				      <a class="btn" data-wysihtml5-dialog-action="save"><i class="icon-ok"></i> <?php echo JText::_('COM_TESTIMONIALS_SAVE'); ?></a>
				      <a class="btn" data-wysihtml5-dialog-action="cancel"><i class="icon-remove"></i> <?php echo JText::_('COM_TESTIMONIALS_CANCEL'); ?></a>
				  </div>
			      </div>
			    </div>
			    <?php echo $form->getInput('author_description'); ?>
			</div>
			</div>
			<?php endif; ?>
			<?php if($settings->get('show_addimage')) : ?>
			<!--Block .testim-field-group is for images. When images are added into .for-images .testim-field-group is display:block-->
			<div class="testim-field-group testim-images-container">
			    <div class="testim-label">&nbsp;</div>
			    <div class="testim-add-image clearfix">
				<div id="imageProgressContainer" class="imageProgressContainer"><div id="imageProgress" class="imageProgress"></div></div>
				<span id="uploadedImages" ></span>
				<?php $this->showImages($item->images); ?>
                                <div class="testim-add-img2" onclick="document.getElementById('imageUpload').click();"><span class="testim-add-img-label"><?php echo JText::_('COM_TESTIMONIALS_ADD_IMAGE'); ?></span><input type="file" name="image" onclick="event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);" id="imageUpload" data-url="<?php echo JRoute::_('index.php?option=com_testimonials&task=new_image'); ?>" class="file-input-button"></div>
				<input type="hidden" name="jform[exist_images]" id="jform_exist_images" value="<?php echo $item->images;?>" />
				<input type="hidden" name="remove_image" id="remove_image" value="<?php echo $form_data['remove_image'];?>" />
			    </div>
			</div> 
			<?php endif; ?>
                            <?php
				foreach ($custom_fields as $i => $custom_field) {
				    if($custom_field->required == 0) {
					echo($this->showCustomField($custom_field, $i));
				    }
				}
			    ?>
                        </div>
		    </fieldset>
		    <?php endif; ?>
		<?php if($settings->get('show_tags') && !empty($tags->tags)) : ?>
                <fieldset>                    
                    <hr class="testim-line testim-bottom-line"/>
                    <div class="testim-field-group testim-tags-container">
                        <ul class="testim-tags">
			    <?php foreach($tags->tags as $tag) : ?>
                            <li>
                                <span class="label<?php echo (in_array($tag->value, $tags->selected) ? ' label-success' : '')?>" tag="<?php echo $tag->value?>"><?php echo $tag->text?></span>
                            </li>
			    <?php endforeach; ?>
                        </ul>
                    </div>
		    <select name="jform[tags][]" id="jform_tags" multiple="multiple" style="display: none;">
			<?php foreach($tags->tags as $tag) : ?>
			<option value="<?php echo $tag->value?>"<?php echo (in_array($tag->value, $tags->selected) ? ' selected="selected"' : '')?>><?php echo $tag->text?></option>
			<?php endforeach; ?>
		    </select>
		   <hr class="testim-line testim-bottom-line"/>
                </fieldset>
		<?php endif; ?>
		    <?php
		    if(!JFactory::getUser()->authorise('core.edit', 'com_testimonials')){
			if ($this->config->get('show_recaptcha')) {
			require_once(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'recaptchalib.php');
			?>
		    <fieldset>
			<hr class="testim-line testim-bottom-line"/>
		    <div class="testim-field-group testim-images-container">
			<div class="testim-label">&nbsp;</div>
			<div class="testim-add-image clearfix">
			<?php echo recaptcha_get_html($this->config->get('recaptcha_publickey'));?>
			</div>
		    </div>
		    </fieldset>  
		    <?php
			}elseif ($this->config->get('show_captcha')){
		    ?>
		    <fieldset>  
			<hr class="testim-line testim-bottom-line"/>
		    <div class="testim-field-group">
                    <div class="testim-label testim-images-container"><img src="<?php echo JURI::base();?>index.php?option=com_testimonials&task=captcha.show" alt="<?php echo JText::_('COM_TESTIMONIALS_RELOAD_SECURITY_CODE');?>" title="<?php echo JText::_('COM_TESTIMONIALS_RELOAD_SECURITY_CODE');?>" onclick="javascript: refresh_captcha( captcha_params );" style="cursor:pointer;" id="captcha_code" /></div>
			    <input type="text" name="captcha_value" id="captcha_value" value="" class="inputbox" style="margin-bottom: 10px;" autocomplete="off" />
			
                    </div>
		    </fieldset>  
		    <?php
			}
		    }
		    ?>
	    <?php
	} 
    }
    
    protected function showCustomField($custom_field, $id){
	switch ($custom_field->type){
	    case 'url':
		$url = array();
		$url = explode('|', $custom_field->value);
		?>
		    <div class="testim-field-group">
                    <label class="testim-label" for="customs_link_<?php echo $custom_field->id?>" rel="tooltip" title="<?php echo $custom_field->descr; ?>" ><?php echo $custom_field->name." ";?><?php echo JText::_('COM_TESTIMONIALS_CUSTOMS_URL_LINK');?>:</label>
                        <input type="text" id="customs_link_<?php echo $custom_field->id?>" name="customs_link[<?php echo $custom_field->id?>]" value="<?php echo htmlspecialchars(isset($url[0])?$url[0]:'');?>" />
                    </div>
		    <div class="testim-field-group">
                    <label class="testim-label" for="customs_name_<?php echo $custom_field->id?>" rel="tooltip" title="<?php echo $custom_field->descr; ?>" ><?php echo $custom_field->name." ";?><?php echo JText::_('COM_TESTIMONIALS_CUSTOMS_URL_NAME');?>:</label>
                        <input type="text" id="customs_name_<?php echo $custom_field->id?>" name="customs_name[<?php echo $custom_field->id?>]" value="<?php echo htmlspecialchars(isset($url[1])?$url[1]:'');?>" />
                    </div>
		<?php
		break;
	    case 'textarea':
		?>
		<div class="testim-field-group">
		<label class="testim-label" for="customs_<?php echo $custom_field->id?>" rel="tooltip" title="<?php echo $custom_field->descr; ?>" ><?php echo $custom_field->name;?>:</label>
		    <textarea cols="30" rows="10" id="customs_<?php echo $custom_field->id?>" name="customs[<?php echo $custom_field->id;?>]"><?php echo htmlspecialchars(isset($custom_field->value)?$custom_field->value:'');?></textarea>
		</div>
		<?php
		break;
	    case 'rating':
		?>
		<div class="testim-field-group">
		<label class="testim-label" for="customs_<?php echo $custom_field->id?>" rel="tooltip" title="<?php echo $custom_field->descr; ?>" ><?php echo $custom_field->name;?>:</label>
		<input type="hidden" name="customs[<?php echo $custom_field->id?>]" id="customs_<?php echo $custom_field->id?>" value="<?php echo (isset($custom_field->value) ? $custom_field->value : '')?>" />
		    <div class="rating">
		    <?php for($a=5;$a>0;$a--) : ?>
			<span rating="<?php echo $a;?>" rel="rating" field_id="<?php echo $custom_field->id?>" <?php echo (isset($custom_field->value) && $custom_field->value == $a ? 'class="hover"' : '')?>>&#9734;</span>
		    <?php endfor; ?>
		    </div>
		</div>
		<?php
		break;
	    default:
		?>
		<div class="testim-field-group">
		<label class="testim-label" for="customs_<?php echo $custom_field->id?>" rel="tooltip" title="<?php echo $custom_field->descr; ?>" ><?php echo $custom_field->name;?>:</label>
		    <input type="text" id="customs_<?php echo $custom_field->id?>" name="customs[<?php echo $custom_field->id?>]" value="<?php echo htmlspecialchars(isset($custom_field->value)?$custom_field->value:'');?>" />
		</div>
		<?php
		break;
	}
    }

    protected function showImages($images){
	$images = trim($images, '|');
	$images = explode('|', $images);
	foreach($images as $image){
	    if(!empty($image) && file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'testimonials' . DIRECTORY_SEPARATOR . $image)){
		?>
		    <a href="javascript:void(0)" class="testim-img"><img src="<?php echo JURI::root().'/images/testimonials/'.$image;?>" alt="<?php echo $image?>"/><span class="testim-del-img" image="<?php echo $image?>" onclick="deleteImage(this);"></span></a>
		<?php
	    }
	}
    }
	
}