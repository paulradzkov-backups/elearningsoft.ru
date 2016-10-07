<?php
/**
* @package   elearningsoft
* @author    Paul Radzkov http://paulradzkov.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get template configuration
include($this['path']->path('layouts:template.config.php'));
	
?>
<!DOCTYPE HTML>
<!--[if lt IE 7]>      <html class="no-js lt-ie10 lt-ie9 lt-ie8 lt-ie7" lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie10 lt-ie9 lt-ie8" lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie10 lt-ie9" lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"> <![endif]-->
<!--[if IE 9]>         <html class="no-js lt-ie10" lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"> <!--<![endif]-->
<head>
<?php echo $this['template']->render('head'); ?>
<meta name="copyright" lang="ru" content="eLearningSoft.ru" />
<meta name="sprypayUrlChecker" content="c14aacb2159afce2cfe2abc37c400b71">
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8056849-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes') . ' ' . $pageclass; ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>' data-spy="scroll" data-target=".on-page-nav">

	<?php if ($this['modules']->count('absolute')) : ?>
	<div id="absolute">
		<?php echo $this['modules']->render('absolute'); ?>
	</div>
	<?php endif; ?>
	
	<div class="wrapper clearfix paper">

		<header id="header" class="page-header clearfix">
            
            <a href="<?php echo $this['config']->get('site_url'); ?>" class="page-logo">
                <img src="<?php echo $this['path']->url('images:logo-big.png');?>" width="260" height="60" alt="eLearningsoft.ru">
                <span class="slogan">Системы дистанционного обучения</span>
            </a>
            
			<nav id="menu" class="mainmenu clearfix">
				
				<?php echo $this['modules']->render('shortmenu'); ?>

                <?php if ($this['modules']->count('sitemap + search')) : ?>
				<label for="sitemap-trigger" class="button sitemap-button">Весь сайт</label>
                <?php endif; ?>
			</nav>
            
            <?php if ($this['modules']->count('sitemap + search')) : ?>
            <input type="checkbox" name="sitemap-trigger" id="sitemap-trigger">
            <nav id="js-sitemap" class="sitemap no-animation">
                <a href="<?php echo $this['config']->get('site_url'); ?>" class="sitemap-logo">
                    <img src="<?php echo $this['config']->get('site_url') . '/templates/elearningsoft/images/logo-sitemap.png';?>" width="180" height="60" alt="eLearningsoft.ru">
                </a>
				<?php echo $this['modules']->render('sitemap'); ?>
				<?php if ($this['modules']->count('search')) : ?>
					<div id="search" class="sitemap-search grid_2 alpha"><?php echo $this['modules']->render('search'); ?></div>
				<?php endif; ?>
				<?php if ($user_logged == false) { ?>
					<div id="login" class="sitemap-login grid_2 omega"><?php echo $this['modules']->render('login'); ?></div>
				<?php } else { ?>
					<div id="logout" class="sitemap-login grid_2 omega"><?php echo $this['modules']->render('logout'); ?></div>
				<?php } ?>
            </nav>
            <?php endif; ?>

		</header>

		<?php if ($this['modules']->count('top-a')) : ?>
		<section id="top-a" class="grid-block"><?php echo $this['modules']->render('top-a', array('layout'=>$this['config']->get('top-a'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('top-b')) : ?>
		<section id="top-b" class="grid-block"><?php echo $this['modules']->render('top-b', array('layout'=>$this['config']->get('top-b'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('innertop + innerbottom + sidebar-a + sidebar-b') || $this['config']->get('system_output')) : ?>
		<div id="main" class="page-body">

			<div id="maininner" class="grid-box">

				<?php if ($this['modules']->count('innertop')) : ?>
				<section id="innertop" class="grid-block"><?php echo $this['modules']->render('innertop', array('layout'=>$this['config']->get('innertop'))); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('breadcrumbs')) : ?>
				<section id="breadcrumbs"><?php echo $this['modules']->render('breadcrumbs'); ?></section>
				<?php endif; ?>

				<?php if ($this['config']->get('system_output')) : ?>
				<section id="content" class="grid-block"><?php echo $this['template']->render('content'); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('innerbottom')) : ?>
				<section id="innerbottom" class="grid-block"><?php echo $this['modules']->render('innerbottom', array('layout'=>$this['config']->get('innerbottom'))); ?></section>
				<?php endif; ?>

			</div>
			<!-- maininner end -->
			
			<?php if ($this['modules']->count('sidebar-a')) : ?>
			<aside id="sidebar-a" class="grid-box"><?php echo $this['modules']->render('sidebar-a', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>
			
			<?php if ($this['modules']->count('sidebar-b')) : ?>
			<aside id="sidebar-b" class="grid-box"><?php echo $this['modules']->render('sidebar-b', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>

		</div>
		<?php endif; ?>
		<!-- main end -->

        <?php if ($this['modules']->count('bottom-a + bottom-b')) : ?>
        <div class="bottom-position clearfix">
            <?php if ($this['modules']->count('bottom-a')) : ?>
            <section id="bottom-a" class="grid-block"><?php echo $this['modules']->render('bottom-a', array('layout'=>$this['config']->get('bottom-a'))); ?></section>
            <?php endif; ?>
            
            <?php if ($this['modules']->count('bottom-b')) : ?>
            <section id="bottom-b" class="grid-block"><?php echo $this['modules']->render('bottom-b', array('layout'=>$this['config']->get('bottom-b'))); ?></section>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
		
		<footer id="footer" class="page-footer clearfix grid_base">

			<div class="footer-a grid_1"><?php echo $this['modules']->render('footer-a'); ?></div>
			<div class="footer-b grid_2"><?php echo $this['modules']->render('footer-b'); ?></div>
			<div class="footer-c grid_1"><?php echo $this['modules']->render('footer-с'); ?></div>
			<div class="footer-line grid_4"><?php echo $this['modules']->render('footerline'); ?></div>
		</footer>

	</div>
	<?php echo $this['modules']->render('debug'); ?>
	<?php echo $this->render('footer'); ?>
	<script>
        /*jQuery.localScroll({
            onAfter: function(target){
                location = '#' + ( target.id || target.name );
            }
        });*/
    </script>
    <!-- Yandex.Metrika counter -->
	<script type="text/javascript">
	(function (d, w, c) {
	    (w[c] = w[c] || []).push(function() {
	        try {
	            w.yaCounter1503023 = new Ya.Metrika({id:1503023,
	                    webvisor:true,
	                    clickmap:true,
	                    trackLinks:true,
	                    accurateTrackBounce:true});
	        } catch(e) { }
	    });

	    var n = d.getElementsByTagName("script")[0],
	        s = d.createElement("script"),
	        f = function () { n.parentNode.insertBefore(s, n); };
	    s.type = "text/javascript";
	    s.async = true;
	    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

	    if (w.opera == "[object Opera]") {
	        d.addEventListener("DOMContentLoaded", f);
	    } else { f(); }
	})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript><div><img src="//mc.yandex.ru/watch/1503023" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
</body>
</html>