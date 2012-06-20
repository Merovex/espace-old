<?php if (!defined('PmWiki')) exit();
$WikiTitle = "Story Bible - Bophendze";
#$PageLogoUrl = "http://example.com/mylogo.gif";
# $DefaultPasswords['admin'] = crypt('onesecret');
#
# $EnableUpload = 1;
# $DefaultPasswords['upload'] = crypt('secrettwo');
#
$DefaultName = 'Main';
$FmtPV['$PageCreationDate'] = 'strftime("%Y-%m-%d", time())';
date_default_timezone_set('America/New_York'); # if you run PHP 5.1 or newer
#
$TimeFmt = '%B %d, %Y, at %I:%M %p EST';
$EnablePathInfo = 1;
// $ScriptUrl = 'http://localhost';
$ScriptUrl = 'http://evening-galaxy-4662.herokuapp.com';
// $Skin = 'boira';
$SkinColor = 'red';
// $Skin = 'minimous';
$Skin = 'bootstrap';
$EnableMarkdown =1;
include_once("$FarmD/cookbook/pagetoc.php");
include_once("$FarmD/cookbook/markdown.php");
// $EditTemplatesFmt = 'Characters.Template';

include_once("eve-related.php")
include_once("social.php");

# Station Identification
$WikiTitle = 'EverySpace Society'; # Appears in Browser Title bar.
$WikiSlogan = 'A Collaborative Science Fiction Milieu';
