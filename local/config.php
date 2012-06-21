<?php if (!defined('PmWiki')) exit();
$DefaultName = 'Main';
$FarmC = "$FarmD/cookbook";

#================================
# Time Configurations
#================================
date_default_timezone_set('America/New_York'); # if you run PHP 5.1 or newer
SDV($TimeFmt, '%B %d, %Y at %H:%M DST (GMT -5)');
$FmtPV['$Year'] = $Year = gmdate('Y', strtotime('now'));
$FmtPV['$PageCreationDate'] = 'strftime("%Y-%m-%d", time())';

#================================
# Navigational Aids
#================================

$EnablePathInfo = 1;
$ScriptUrl = 'http://evening-galaxy-4662.herokuapp.com';

#================================
# Branding
#================================
$WikiTitle = 'EverySpace Society'; # Appears in Browser Title bar.
$WikiSlogan = 'A Collaborative Science Fiction Milieu';
$SkinColor = 'red';
$Skin = 'bootstrap';

$EnableMarkdown = 1;

include_once("$FarmC/security/security.php");

#==================================
# Page Markup Enhancements
#==================================
include_once("$FarmD/cookbook/markup/grouptitle.php");
include_once("$FarmD/cookbook/markup/newpageboxplus.php");
include_once("$FarmD/cookbook/pagetoc.php");
include_once("$FarmD/cookbook/markdown.php");

include_once("eve-related.php");
include_once("social.php");

