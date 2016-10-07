<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

?>
	<ul class="login-menu">
		<li class="grid_1 alpha"><?php echo JText::_('MOD_LOGIN'); ?></li>
		<?php
		$usersConfig = JComponentHelper::getParams('com_users');
		if ($usersConfig->get('allowUserRegistration')) : ?>
		<li class="grid_1 omega">
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><?php echo JText::_('MOD_LOGIN_REGISTER'); ?></a>
		</li>
		<?php endif; ?>
	</ul>
	<form class="mod_user short style form-inline" action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
	
		<?php if ($params->get('pretext')) : ?>
		<div class="pretext">
			<?php echo $params->get('pretext'); ?>
		</div>
		<?php endif; ?>

		<div class="username">
			<input type="text" class="input-medium" name="username" size="18" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
		</div>

		<div class="password">
			<input type="password" class="input-medium" name="password" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
		</div>

		<div class="button-container">
			<button class="button" value="<?php echo JText::_('JLOGIN') ?>" name="Submit" type="submit"><?php echo JText::_('JLOGIN') ?></button>
		</div>

		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div class="remember">
			<?php $number = rand(); ?>
			<label for="modlgn-remember-<?php echo $number; ?>" class="checkbox">
				<input id="modlgn-remember-<?php echo $number; ?>" type="checkbox" name="remember" value="yes" checked />
				<?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?>
			</label>
		</div>
		<?php endif; ?>
		
		
		<ul class="forgot-something">
			<li class="forgot-login">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
			</li>
			<li class="forgot-password">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
			</li>
		</ul>
		
		<?php if($params->get('posttext')) : ?>
		<div class="posttext">
			<?php echo $params->get('posttext'); ?>
		</div>
		<?php endif; ?>
		
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
	
	<script>
		jQuery(function($){
			$('form.login input[placeholder]').placeholder();
		});
	</script>
