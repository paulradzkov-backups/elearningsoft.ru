<?php
/**
* @package   yoo_master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// generate css for layout
//$css[] = sprintf('.wrapper { max-width: %dpx; }', $this['config']->get('template_width'));

// generate css for 3-column-layout
$sidebar_a       = '';
$sidebar_b       = '';
$maininner_width = 100;
$sidebar_a_width = intval($this['config']->get('sidebar-a_width'));
$sidebar_b_width = intval($this['config']->get('sidebar-b_width'));
$sidebar_classes = "";
$rtl             = $this['config']->get('direction') == 'rtl';
$body_config	 = array();

// set widths
if ($this['modules']->count('sidebar-a')) {
	$sidebar_a = $this['config']->get('sidebar-a'); 
	$maininner_width -= $sidebar_a_width;
	$css[] = sprintf('#sidebar-a { width: %d%%; }', $sidebar_a_width);
}

if ($this['modules']->count('sidebar-b')) {
	$sidebar_b = $this['config']->get('sidebar-b'); 
	$maininner_width -= $sidebar_b_width;
	$css[] = sprintf('#sidebar-b { width: %d%%; }', $sidebar_b_width);
}

//$css[] = sprintf('#maininner { width: %d%%; }', $maininner_width);

// all sidebars right
if (($sidebar_a == 'right' || !$sidebar_a) && ($sidebar_b == 'right' || !$sidebar_b)) {
	$sidebar_classes .= ($sidebar_a) ? 'sidebar-a-right ' : '';
	$sidebar_classes .= ($sidebar_b) ? 'sidebar-b-right ' : '';

// all sidebars left
} elseif (($sidebar_a == 'left' || !$sidebar_a) && ($sidebar_b == 'left' || !$sidebar_b)) {
	$sidebar_classes .= ($sidebar_a) ? 'sidebar-a-left ' : '';
	$sidebar_classes .= ($sidebar_b) ? 'sidebar-b-left ' : '';
	$css[] = sprintf('#maininner { float: %s; }', $rtl ? 'left' : 'right');

// sidebar-a left and sidebar-b right
} elseif ($sidebar_a == 'left') {
	$sidebar_classes .= 'sidebar-a-left sidebar-b-right ';
	$css[] = '#maininner, #sidebar-a { position: relative; }';
	$css[] = sprintf('#maininner { %s: %d%%; }', $rtl ? 'right' : 'left', $sidebar_a_width);
	$css[] = sprintf('#sidebar-a { %s: -%d%%; }', $rtl ? 'right' : 'left', $maininner_width);

// sidebar-b left and sidebar-a right
} elseif ($sidebar_b == 'left') {
	$sidebar_classes .= 'sidebar-a-right sidebar-b-left ';
	$css[] = '#maininner, #sidebar-a, #sidebar-b { position: relative; }';
	$css[] = sprintf('#maininner, #sidebar-a { %s: %d%%; }', $rtl ? 'right' : 'left', $sidebar_b_width);
	$css[] = sprintf('#sidebar-b { %s: -%d%%; }', $rtl ? 'right' : 'left', $maininner_width + $sidebar_a_width);
}

// number of sidebars
if ($sidebar_a && $sidebar_b) {
	$sidebar_classes .= 'sidebars-2 ';
} elseif ($sidebar_a || $sidebar_b) {
	$sidebar_classes .= 'sidebars-1 ';
}

// generate css for dropdown menu
foreach (array(1 => '.dropdown', 2 => '.columns2', 3 => '.columns3', 4 => '.columns4') as $i => $class) {
	$css[] = sprintf('#menu %s { width: %dpx; }', $class, $i * intval($this['config']->get('menu_width')));
}

// load css
$this['asset']->addFile('css', 'http://fonts.googleapis.com/css?family=Open+Sans:300,400italic,400,600,700&subset=cyrillic-ext,latin');
$this['asset']->addFile('css', 'css:opensans.css');
$this['asset']->addFile('css', 'css:grid.css');
//$this['asset']->addFile('css', 'css:menus.css');
$this['asset']->addString('css', implode("\n", $css));
//$this['asset']->addFile('css', 'css:modules.css');
$this['asset']->addFile('css', 'css:tools.css');
//$this['asset']->addFile('css', 'css:system.css');
$this['asset']->addFile('css', 'css:extensions.css');
$this['asset']->addFile('css', 'css:icons.css');
$this['asset']->addFile('css', 'css:template.css');
if ($this['config']->get('direction') == 'rtl') $this['asset']->addFile('css', 'css:rtl.css');
$this['asset']->addFile('css', 'css:print.css');

// load fonts
$http  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
//$fonts = array(
//	'droidsans' => 'template:fonts/droidsans.css',
//	'opensans' => 'template:fonts/opensans.css',
//	'yanonekaffeesatz' => 'template:fonts/yanonekaffeesatz.css',
//	'mavenpro' => 'template:fonts/mavenpro.css',
//	'kreon' => 'template:fonts/kreon.css');

//foreach (array_unique(array($this['config']->get('font1'), $this['config']->get('font2'), $this['config']->get('font3'))) as $font) {
//	if (isset($fonts[$font])) {
//        $this['asset']->addFile('css', $fonts[$font]);
//	}
//}

// set body css classes
$option="";
$view="";
$task="";
$option = JRequest::getVar('option');
$view = JRequest::getVar('view');
$task = JRequest::getVar('task');

$pageclass = "";
$pageclass .= $option;
if($view){$pageclass .= ' view-'. $view;}
if($task){$pageclass .= ' task-'. $task;}

$body_classes  = $sidebar_classes.' ';
$body_classes .= $this['system']->isBlog() ? 'isblog ' : 'noblog ';
$body_classes .= $this['config']->get('page_class');

$this['config']->set('body_classes', $body_classes);

// add social buttons
$body_config['twitter'] = (int) $this['config']->get('twitter', 1);
$body_config['plusone'] = (int) $this['config']->get('plusone', 1);
$body_config['facebook'] = (int) $this['config']->get('facebook', 1);

$this['config']->set('body_config', json_encode($body_config));

// add javascripts
JHtml::_('bootstrap.framework');
$this['asset']->addFile('js', 'js:modernizr.custom.78037.js');
$this['asset']->addFile('js', 'js:warp.js');
//$this['asset']->addFile('js', 'js:responsive.js');
//$this['asset']->addFile('js', 'js:accordionmenu.js');
//$this['asset']->addFile('js', 'js:dropdownmenu.js');
//$this['asset']->addFile('js', 'js:jquery.scrollTo-1.4.3.1-min.js');
//$this['asset']->addFile('js', 'js:jquery.localscroll-1.2.7-min.js');

$this['asset']->addFile('js', 'js:messages.ru.js'); //parsley translation
$this['asset']->addFile('js', 'js:parsley.extend.min.js');
$this['asset']->addFile('js', 'js:parsley.min.js'); //form validation


//$this['asset']->addFile('js', 'js:socialite.min.js'); //social buttons plugin
$this['asset']->addFile('js', 'js:template.js');


// internet explorer
if ($this['useragent']->browser() == 'msie') {

	// add conditional comments
	// $head[] = sprintf('<!--[if lte IE 8]><script src="%s"></script><![endif]-->', $this['path']->url('js:html5.js'));
	  $head[] = sprintf('<!--[if IE 8]><link rel="stylesheet" href="%s" /><![endif]-->', $this['path']->url('css:ie8.css'));

}

//page url
$pageurl = JURI::current();
$user_logged = false;
$user = JFactory::getUser();
if ($user->guest) {
	$user_logged = false;
} else {
	$user_logged = true;
}



// add $head
if (isset($head)) {
	$this['template']->set('head', implode("\n", $head));
}
