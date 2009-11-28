<?php
defined('_JEXEC') or die('Restricted access'); // no direct access
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';
$document = isset($this) ? $this : null;
$baseUrl = $this->baseurl;
$templateUrl = $this->baseurl . '/templates/' . $this->template;
artxComponentWrapper($document);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
 <head>
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<jdoc:include type="head" />
  <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/system.css" type="text/css" />
  <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/general.css" type="text/css" />

  <link rel="stylesheet" type="text/css" href="<?php echo $templateUrl; ?>/css/template.css" />
  <!--[if IE 6]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie6.css" type="text/css" media="screen" /><![endif]-->
  <!--[if IE 7]><link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.ie7.css" type="text/css" media="screen" /><![endif]-->
  <script type="text/javascript" src="<?php echo $templateUrl; ?>/script.js"></script>
 </head>
<body>
<div class="PageBackgroundSimpleGradient">
</div>
<div class="Main">
<div class="Sheet">
    <div class="Sheet-tl"></div>
    <div class="Sheet-tr"></div>
    <div class="Sheet-bl"></div>
    <div class="Sheet-br"></div>
    <div class="Sheet-tc"></div>
    <div class="Sheet-bc"></div>
    <div class="Sheet-cl"></div>
    <div class="Sheet-cr"></div>
    <div class="Sheet-cc"></div>
    <div class="Sheet-body">
<div class="Header">
    <div class="Header-jpeg"></div>
<div class="logo">
 <h1 id="name-text" class="logo-name"><a href="<?php echo $baseUrl; ?>/">sewing seeds of literacy</a></h1>
 <div id="slogan-text" class="logo-text">sharing our gifts, touching a life</div>
</div>


</div>
<jdoc:include type="modules" name="user3" />
<jdoc:include type="modules" name="banner1" style="xhtml" />
<?php echo artxPositions($document, array('top1', 'top2', 'top3'), 'artblock'); ?>
<div class="contentLayout">
<div class="content">

<?php
  echo artxModules($document, 'banner2', 'xhtml');
  if (artxCountModules($document, 'breadcrumb'))
    echo artxPost(null, artxModules($document, 'breadcrumb'));
  echo artxPositions($document, array('user1', 'user2'), 'artpost');
  echo artxModules($document, 'banner3', 'xhtml');
?>
<?php if (artxHasMessages()) : ?><div class="Post">
    <div class="Post-tl"></div>
    <div class="Post-tr"></div>
    <div class="Post-bl"></div>
    <div class="Post-br"></div>
    <div class="Post-tc"></div>
    <div class="Post-bc"></div>
    <div class="Post-cl"></div>
    <div class="Post-cr"></div>
    <div class="Post-cc"></div>
    <div class="Post-body">
<div class="Post-inner">
<div class="PostContent">

<jdoc:include type="message" />

</div>
<div class="cleared"></div>

</div>

    </div>
</div>
<?php endif; ?>
<jdoc:include type="component" />

<?php echo artxModules($document, 'banner4', 'xhtml'); ?>
<?php echo artxPositions($document, array('user4', 'user5'), 'artpost'); ?>
<?php echo artxModules($document, 'banner5', 'xhtml'); ?>
</div>

</div>
<div class="cleared"></div>

<?php echo artxPositions($document, array('bottom1', 'bottom2', 'bottom3'), 'artblock'); ?>
<jdoc:include type="modules" name="banner6" style="xhtml" />
<div class="Footer">
 <div class="Footer-inner">
  <?php echo artxModules($document, 'syndicate'); ?>
  <div class="Footer-text">
  <?php if (artxCountModules($document, 'copyright') == 0): ?>
<p>Copyright &copy; 2009 Sewing Seeds of Literacy.<br/>
All Rights Reserved.</p>

  <?php else: ?>
  <?php echo artxModules($document, 'copyright', 'xhtml'); ?>
  <?php endif; ?>
  </div>
 </div>
 <div class="Footer-background"></div>
</div>

    </div>
</div>
<div class="cleared"></div>
</div>

</body> 
</html>
