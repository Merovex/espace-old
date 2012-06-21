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
Markup('abbrs', 'fulltext','/([A-Z][A-Z]s)/e', "Keep('$1')");
function bburl($k) {
    global $ScriptUrl, $pagename;
    return "[[$k]] ".Keep("<span class='bburl'>[url=$ScriptUrl/$pagename$k]$k [/url]</span>");
}
if ($action == 'markdown') { $LinkPageExistsFmt = "[[\$LinkText]]"; }
else { Markup('quoted', '_end', '/``(.*?)"/', "&#8220;$1&#8221;"); }
Markup('bburl', 'directives', '/\(:bburl\s*(#.*?):\)/e', "bburl('\\1')");
markup('abbreviations', 'directives', '/\(:abbr\s*(\w+)\s(.*?):\)/', "Keep('<abbr title=\'$2\'>$1</abbr>');");

function AECoords($name, $coord) {
    $galaxy = array(
        'A' => 'alpha', 
        'B' => 'beta', 
        'C' => 'ceti',
        'D' => 'delta',
        'E' => 'epsilon',
        'F' => 'fenix',
        'G' => 'gamma',
        'H' => 'helion',
        'I' => 'ixion',
    );
    $url = "http://$galaxy[$name].astroempires.com/map.aspx?loc=";
    return Keep("<a href='$url$coord'>$coord</a>");
}
Markup('AECoords','_begin','/(([A-Z])\d\d:\d\d:\d\d:\d\d)/e', "AECoords('$2','$1')");

include_once("$FarmC/markup/mediacat.php");
include_once("$FarmC/markup/mediatable.php");
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

$UserText = '';

// if ($Author) {
//   $UserText =<<<EOT
//     <div class="btn-group pull-right">
//       <a class="btn btn-primary" href="/Contributers/$Author"><i class="icon-user icon-white"></i> $Author</a>
//       <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
//       <ul class="dropdown-menu">
//         <li><a href="?action=edit"><i class="icon-pencil"></i> Edit</a></li>
//         <li><a href="?action=diff"><i class="icon-film"></i> History</a></li>
//         <li><a href="?action=pageattr"><i class="icon-wrench"></i> Settings</a></li>
//         <li class="divider"></li>
//         <li><a href="?action=signout"><i class="icon-remove"></i> Sign Out</a></li>
//       </ul>
//     </div>
//   </div>
// EOT;
// }
// else {
//   $UserText =<<<EOT
//               <ul class="nav pull-right">
//     <!-- <li><a href="/users/sign_up">Sign Up</a></li> -->
//     <!-- <li class="divider-vertical"></li> -->
//                 <li class="dropdown">
//                   <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
//                   <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
//                     <form action='/Main/Main?action=login' method='post' name='authform'>
//                     <table width='100%'><tr ><td >Name:</td><td ><input type='text' name='authid' class='inputbox' /></td></tr>
//                     <tr ><td >Password:</td><td ><input type='password' name='authpw' class='inputbox' /></td></tr>
//                     </table><p><input class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="submit" name="commit" value="Sign In" />
//                     </p></form>
//                   </div>
//                   </li>
//               </ul>
// EOT;
// }
