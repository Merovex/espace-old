<?php if (!defined('PmWiki')) exit();
$WikiTitle = "Story Bible - Bophendze";
#$PageLogoUrl = "http://example.com/mylogo.gif";
# $DefaultPasswords['admin'] = crypt('onesecret');
#
# $EnableUpload = 1;
# $DefaultPasswords['upload'] = crypt('secrettwo');
#
date_default_timezone_set('America/New_York'); # if you run PHP 5.1 or newer
#
$TimeFmt = '%B %d, %Y, at %I:%M %p EST';
$EnablePathInfo = 1;
$ScriptUrl = 'http://localhost';
$ScriptUrl = 'http://blooming-planet-2890.herokuapp.com/';
// $Skin = 'boira';
$SkinColor = 'red';
$Skin = 'minimous';
$Skin = 'bootstrap';
$EnableMarkdown =1;
include_once("$FarmD/cookbook/pagetoc.php");
include_once("$FarmD/cookbook/markdown.php");
$EditTemplatesFmt = 'Characters.Template';
