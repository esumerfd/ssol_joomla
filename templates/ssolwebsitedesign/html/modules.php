<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

function modChrome_artblock($module, &$params, &$attribs)
{
if (!empty ($module->content)) : ?>
<div class="Block">
    <div class="Block-body">

<?php if ($module->showtitle != 0) : ?>
<div class="BlockHeader">
    <div class="l"></div>
    <div class="r"></div>
    <div class="header-tag-icon">
        <div class="t">
<?php echo $module->title; ?>
</div>
    </div>
</div>
<?php endif; ?>
<div class="BlockContent">
    <div class="BlockContent-body">

<?php echo $module->content; ?>

    </div>
</div>


    </div>
</div>

<?php endif;
}

function modChrome_artpost($module, &$params, &$attribs)
{
if (!empty ($module->content)) : ?>
<div class="Post">
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

<?php if ($module->showtitle != 0) : ?>
<h2 class="PostHeaderIcon-wrapper"> <span class="PostHeader">
<?php echo $module->title; ?>
</span>
</h2>

<?php endif; ?>
<div class="PostContent">

<?php echo $module->content; ?>

</div>
<div class="cleared"></div>


</div>

    </div>
</div>

<?php endif;
}