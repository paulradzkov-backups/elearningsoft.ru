<?php
/* @package JMod Contact for Joomla 3.0!  
 * @link       http://jmodules.com/ 
 * @copyright (C) 2012- Sean Casco
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html 
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*=================================================================
Getting all param of module
Here all params set by admin for this module are catching in varuables for further use
=================================================================*/

  /* Module suffix params*/
  $mod_class_suffix = $params->get('moduleclass_sfx', '');
  
  /* Fields label params*/
  $emaillabel = $params->get('email_label', 'Email:');
  $subjectlabel = $params->get('subject_label', 'Subject:');
  $messagelabel = $params->get('message_label', 'Message:');
  $recipient_email_id = $params->get('recipient_email_id', '');
  /*==========================================================*/
  
  /* Custom Fields params*/
  $txtfld1label = $params->get('custom_field_1', 'Custom1:');
  $txtfld1enable = $params->get('custom_field_1_enable', false);
  $txtfld2label = $params->get('custom_field_2', 'Custom2:');
  $txtfld2enable = $params->get('custom_field_2_enable', false);
  $txtfld3label = $params->get('custom_field_3', 'Custom3:');
  $txtfld3enable = $params->get('custom_field_3_enable', false);
  /*==========================================================*/
  
  /* message setting param values*/
  $button_text = $params->get('button_text', 'Send Message');
  $thanks_message = $params->get('thanks_message', 'Thank you for your contact.');
  $thanks_message_text_color = $params->get('thanks_message_text_color', '#FF0000');
  $error_message = $params->get('error_message', 'Message could not be sent to recipient. Please your entries and make sure all entries are right and try again.');

  $email_required_message = $params->get('email_required_message', 'Please enter your email');
  $invalid_email_message = $params->get('invalid_email_message', 'Please enter a valid email');
  $subject_required_message = $params->get('subject_required_message', 'Please enter your mail subject');
  $msg_required_message = $params->get('msg_required_message', 'Please enter your mail message');

  $fromName = @$params->get('from_name', 'Contact Form');
  $fromEmail = @$params->get('from_email', 'contact_form@yoursite.com');
  /*==========================================================*/
  
  /* Fields widths param values*/
  $emailwidth = $params->get('email_width', '15');
  $subjectwidth = $params->get('subject_width', '25');
  $messagewidth = $params->get('message_width', '25');
  $buttonwidth = $params->get('button_width', '50');
  /*==========================================================*/
  
  /* mail antispam params*/
  $enable_anti_spam = $params->get('enable_anti_spam', false);
  $anti_spam_question = $params->get('anti_spam_q', 'One + One =(nubmer)?');
  $anti_spam_answer = $params->get('anti_spam_a', '2');
  /*==========================================================*/
  
  /* recaptcha params*/
  $enable_recaptcha   = $params->get('enable_recaptcha', false);
  $recaptcha_public   = $params->get('recaptcha_public_key', false);
  $recaptcha_private  = $params->get('recaptcha_private_key', false);
  
  
  $disable_https = $params->get('disable_https', false);   
  $pre_text = $params->get('pre_text', '');
  /*==========================================================*/
  
  // include the recaptcha library if necessary
  if ($enable_recaptcha)
  {
    require_once('recaptcha_JMod.php');
    $resp = recaptcha_check_answer($recaptcha_private,$_SERVER["REMOTE_ADDR"],@$_POST["recaptcha_challenge_field"],@$_POST["recaptcha_response_field"]);
  }
  $recaptcha_error = null;

  $exact_url = $params->get('exact_url', true);
  if (!$exact_url) {
    $url = JURI::current();
  }
  else {
    if (!$disable_https) {
    $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
    else {
    $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
  }
  
  $err = '';
  /*============= IF isset $_POST====================*/
  if (isset($_POST["email"]))
  {
    if ($enable_anti_spam && $_POST["anti_spam_answer"] != $anti_spam_answer)  /* anti spam question */
    {
      $err = '<span class="cf-error">' . JText::_('Wrong anti-spam answer') . '</span>';
    }
    elseif ($enable_recaptcha && !$resp->is_valid) // reCaptcha spam
    {
      $err = '<span class="cf-error">' . JText::_('The reCAPTCHA wasn\'t entered correctly. Go back and try it again.' . '(reCAPTCHA said: ' . $resp->error . ')') . '</span>';
      $recaptcha_error = $resp->error;
    }
    
    if ($_POST["email"] === "")// validation for empty email
    {
      $err = '<span class="cf-error">' . $email_required_message . '</span>';
    }
    elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $_POST["email"]))//validation for invalide email
    {
      $err = '<span class="cf-error">' . $invalid_email_message . '</span>';
    }
    elseif ($_POST["subject"] === "")// validation for empty subject
    {
      $err = '<span class="cf-error">' . $subject_required_message . '</span>';
    }
    elseif ($_POST["message"] === "")// validation for empty message
    {
      $err = '<span class="cf-error">' . $msg_required_message . '</span>';
    }

    if ($err == '') {
      $mySubject = $_POST["subject"];
      $myMessage = 'You received a message from '. $_POST["email"] ."\n\n". $_POST["message"];
      if(isset($_POST['custome_field1']) && $_POST['custome_field1']!=''){
        $myMessage .= "\n\n".$txtfld1label." : " . $_POST['custome_field1'];    
      }
      if(isset($_POST['custome_field2']) && $_POST['custome_field2']!=''){
        $myMessage .= "\n\n".$txtfld2label." : " . $_POST['custome_field2'];    
      }
      if(isset($_POST['custome_field3']) && $_POST['custome_field3']!=''){
        $myMessage .= "\n\n".$txtfld3label." : " . $_POST['custome_field3'];    
      }
      
      $mailSender = &JFactory::getMailer();
      $mailSender->addRecipient($recipient_email_id);
  
      $mailSender->setSender(array($fromEmail,$fromName));
      $mailSender->addReplyTo(array( $_POST["email"], '' ));
  
      $mailSender->setSubject($mySubject);
      $mailSender->setBody($myMessage);
  
      if (!$mailSender->Send()) {
        $myReplacement = '<span class="cf-error">' . $error_message . '</span>';
        print $myReplacement;
        return true;
      }
      else {
        $myReplacement = '<p class="success-message grid_4">' . $thanks_message . '</p>';
        print $myReplacement;
        return true;
      }
    }
  }
  /*============= end of isset $_POST====================*/
  if ($recipient_email_id === "") {
    $myReplacement = '<span class="contact-form-error">Email recipient not define for contact recieving contact email, Please contact to site admin report to this problem .</span>';
    print $myReplacement;
    return true;
  }
?>
  
  <div class="contact_form <?php echo $mod_class_suffix; ?>" id="contactform">
    <form action="<?php echo $url; ?>#contactform" method="post" data-validate="parsley" data-trigger="focusin focusout">
      <div class="contact_form intro_text <?php echo $mod_class_suffix; ?>"><?php echo $pre_text; ?></div>
   
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_1"><?php echo $subjectlabel;?></div>
            <div class="contact-form-input grid_2">
                <input class="contact_form inputbox <?php echo $mod_class_suffix;?>" type="text" name="subject" size="<?php echo $subjectwidth;?>" value="<?php if ($err != '' && isset($_POST['subject'])) { echo $_POST['subject'];}?>" data-required="true" />
            </div>
        </div>
      <?php if($txtfld1enable) { ?>
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_1"><?php echo $txtfld1label;?></div>
            <div class="contact-form-input grid_2"><input class="contact_form inputbox <?php echo $mod_class_suffix;?>" type="text" name="custome_field1" size="<?php echo $emailwidth;?>" value="<?php if ($err != '' && isset($_POST['custome_field1'])) { echo $_POST['custome_field1'];}?>" /></div>
            <div class="field-tip grid_1"></div>
        </div>
      <?php }
      if($txtfld2enable) { ?>
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_1"><?php echo $txtfld2label;?></div>
            <div class="contact-form-input grid_2"><input class="contact_form inputbox <?php echo $mod_class_suffix;?>" type="text" name="custome_field2" size="<?php echo $emailwidth;?>" value="<?php if ($err != '' && isset($_POST['custome_field2'])) { echo $_POST['custome_field2'];}?>" /></div>
        </div>
      <?php }
      if($txtfld3enable) { ?>
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_1"><?php echo $txtfld3label;?></div>
            <div class="contact-form-input grid_2"><input class="contact_form inputbox <?php echo $mod_class_suffix;?>" type="text" name="custome_field3" size="<?php echo $emailwidth;?>" value="<?php if ($err != '' && isset($_POST['custome_field3'])) { echo $_POST['custome_field3'];}?>" /></div>
        </div>
      <?php } ?>
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_1"><?php echo $messagelabel;?></div>
            <div class="contact-form-input grid_2"><textarea class="contact_form textarea <?php echo $mod_class_suffix;?>" type="text" name="message" cols="<?php echo $messagewidth;?>" rows="4" data-required="true" data-minwords="3"><?php if ($err != '' && isset($_POST['message'])) { echo $_POST['message'];}?></textarea></div>
            <div class="field-tip grid_1"><p>Пожалуйста, укажите:</p>
			  <ul>
				<li>Ф.И.О.</li>
				<li>должность,</li>
				<li>название организации,</li>
				<li>контактный телефон с кодом в международном формате (например: +7&nbsp;499&nbsp;123-45-67),</li>
				<li>удобное время для звонка.</li>
			  </ul>
			</div>
        </div>
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_1">
                <?php echo $emaillabel;?>
            </div>
            <div class="contact-form-input grid_2">
                <input class="contact_form inputbox <?php echo $mod_class_suffix;?>" type="text" name="email" size="<?php echo $emailwidth;?>" value="<?php if ($err != '' && isset($_POST['email'])) { echo $_POST['email'];}?>" data-required="true" data-type="email" />
            </div>
        <!--    <div class="field-tip grid_1">Мы отправим на ваш адрес копию вашего сообщения</div> -->
        </div>
        <?php if($enable_anti_spam) { ?>
        <div class="contact-form-row clearfix">
            <div class="contact-form-label grid_4"><?php echo $anti_spam_question; ?></div>
            <div class="contact-form-label grid_1">Answer :</div>
            <div class="contact-form-input grid_2"><input class="contact_form inputbox <?php echo $mod_class_suffix;?>" type="text" name="anti_spam_answer" size="<?php echo $emailwidth; ?>" value="<?php if ($err != '' && isset($_POST['anti_spam_answer'])) { echo $_POST['anti_spam_answer'];} ?>"/></div>
        </div>
      <?php } ?>
      <?php if ($enable_recaptcha)
      { ?>
        <div><?php echo recaptcha_get_html($recaptcha_public, $recaptcha_error);?></div>
      <?php } ?>
        <div class="contact-form-row clearfix">
            <div class="common-instructions grid_1 prefix_1"><?php if ($err != '') { print $err; } else { ?><span class="field-tip">Заполните все поля</span><?php } ?></div>
            <div class="contact-form-action-panel grid_1"><input class="contact_form action-button <?php echo $mod_class_suffix;?>" type="submit" value="<?php echo $button_text;?>"/></div>
            <div class="field-tip grid_1"></div>
        </div>
    </form>
  </div>
  <?php  return true; ?>