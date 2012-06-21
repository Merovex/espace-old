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
// $ScriptUrl = 'http://evening-galaxy-4662.herokuapp.com';
$ScriptUrl = '';

#================================
# Branding
#================================
$WikiTitle = 'EverySpace Society'; # Appears in Browser Title bar.
$WikiSlogan = 'A Collaborative Science Fiction Milieu';
$SkinColor = 'red';
$Skin = 'bootstrap';

$EnableMarkdown = 1;

include_once("$FarmC/security/security.php");

#=================================
# Social Stuff
#=================================
include_once("$FarmC/social/blogcalendardate.php");
include_once("$FarmC/social/rss-config.php");
include_once("$FarmC/social/sociallinks.php");
include_once("$FarmC/social/blogsimple.php");

#==================================
# Page Markup Enhancements
#==================================
$FmtPV['$GroupTitle'] = '(($t = PageVar("$group.GroupAttributes", \'$Title\')) == "GroupAttributes") ? $AsSpacedFunction($group) : $t';

include_once("$FarmC/mediacat.php");
include_once("$FarmC/mediatable.php");
include_once("$FarmC/translate_this.php");
include_once("$FarmC/markup/grouptitle.php");
include_once("$FarmC/markup/newpageboxplus.php");
include_once("$FarmC/pagetoc.php");
include_once("$FarmC/markdown.php");
include_once("$FarmC/Footnotes/Footnotes.php");
$EditTemplatesFmt = array('$Group.Template');

# ! == H2, not H1
Markup('^!', 'block', '/^(!{2,6})\\s?(.*)$/e', "'<:block,1><h'.strlen('$1').PSS('>$2</h').strlen('$1').'>'");

include_once("eve-related.php");
include_once("social.php");

