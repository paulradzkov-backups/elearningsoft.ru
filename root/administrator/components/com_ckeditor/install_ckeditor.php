<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
} 
/**
 * Script file of HelloWorld component
 */
class com_ckeditorInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        public function install($parent) 
        {
          $parent->getParent()->setRedirectURL('index.php?option=com_ckeditor');       
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        public function uninstall($parent) 
        {
        	// $parent is the class calling this method
          //  echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
          jimport('joomla.filesystem.folder');
					jimport('joomla.filesystem.file');
					jimport('joomla.installer.installer');
					$installer = & JInstaller::getInstance();

					if (JFolder::exists(dirname($installer->getPath('extension_site')).DS.'..'.DS.'plugins'.DS.'editors'))
					{
						if(JFolder::delete(dirname($installer->getPath('extension_site')).DS.'..'.DS.'plugins'.DS.'editors'.DS.'ckeditor'))
						{
							$editor_result   = JText::_('Success');
						}else{
							$editor_result = JText::_('Error');
						}
					}
					$editor_result   = JText::_('Success');
					echo '<p>' . $editor_result . '</p>';
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        public function preflight($type, $parent) 
        {
        	// $parent is the class calling this method
          //$parent->getParent()->setRedirectURL('index.php?option=com_ckeditor');
          jimport('joomla.filesystem.folder');
					jimport('joomla.filesystem.file');
					jimport('joomla.installer.installer');
					$installer =  JInstaller::getInstance();
	
					$source  = $installer->getPath('source');
					$packages   = $source.DS.'packages';
					
					// Get editor package	
					if(is_dir($packages)) {
						
						$editor   = JFolder::files($packages, 'plg_ckeditor.zip', false, true);
					}

					if (!empty($editor) && is_file($editor[0])) {
	          $confObject = JFactory::getApplication();
							$packagePath = dirname($editor[0]).DS.'ckeditor';
							if (!JArchive::extract($editor[0], $packagePath)) {
								$editor_result = JText::_('EDITOR EXTRACT ERROR');
							}else{
								$installer =  JInstaller::getInstance();
								$c_manifest   =  $installer->getManifest();
								$c_root     = & $c_manifest->document;
								if(JFolder::copy($packagePath, dirname($installer->getPath('extension_site')).DS.'..'.DS.'plugins'.DS.'editors','',true))
								{
									$editor_result   = JText::_('Success');
								}else{
									$editor_result = JText::_('Error');
								}
							}
				}else{
					$editor_result = JText::_('Error');
				}
				echo '<p>' .$editor_result . '</p>';
         
        }
}