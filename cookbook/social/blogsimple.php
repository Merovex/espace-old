<?php if (!defined('PmWiki')) exit();
/*  Copyright 2006 Hans Bracker
    This file is blogsimple.php; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  

    To use this script, simply place it into the cookbook/ folder
    and add the line
        include_once('cookbook/blogsimple.php');
    to a local customization file.
    You also need to install newpageboxplus.php
    and commentboxstyled.php.
*/
define(BLOGSIMPLE_VERSION, '2006-04-28');


# add page variable {$Today}, formats today's date as yyyy-mm-dd
$FmtPV['$Today'] = 'strftime("%Y-%m-%d", time() )';
$FmtPV['$TodaySimple'] = 'strftime("%A %d %B", time() )';

$FmtPV['$Time'] = 'strftime("%H:%M", time() )';

$FmtPV['$CurrentYear'] = 'date("Y")';

# add page variable {$BlogDate} as page creation date
$FmtPV['$BlogDate'] = 'strftime("%d %B %Y", $page["ctime"])'; 

# add pagevariable {$BlogDateSimple} as page creation date
$FmtPV['$BlogDateSimple'] = 'strftime("%a %d %b", $page["ctime"])';

# add page variable {$BlogTitle} as spaced name with date stripped off
$FmtPV['$BlogTitle'] = 'StripDate($pagename)';
function StripDate($pagename) {
    $sd = preg_replace("/[^.]*\.([\d\-]*)(.*)/e","'$2'==''?'$1':'$2'",$pagename); 
    $BlogTitle = AsSpaced($sd);
    return $BlogTitle;
};

# add page variable {$BlogDate2} as date with name stripped off
$FmtPV['$BlogDate2'] = 'StripName($pagename)';
function StripName($pagename) {
    $sn = preg_replace("/[^.]*\.([\d\-]*)(.*)/e","'$1'",$pagename);
    $sn = preg_replace("[\-]","", $sn);
    return $sn;
};

# add page variable {$Today2} in format yyyymmdd
$FmtPV['$Today2'] = 'strftime("%Y%m%d", time() )';

# add pagevariable {$LastModifiedByDate}
$FmtPV['$LastModifiedDate'] = 'strftime("%a %d %b", $page["time"])';

# add pagevariable {$BlogMonth} - returns long month and year from blogpage name
# used by monthly archives with name format yyyy-mm
$FmtPV['$BlogMonth'] = 'TitleToDate($pagename)';
function TitleToDate($pagename) {
    $tname = preg_replace("/[^.]*\.([\d\-]*)(.*)/e","'$1'==''?'$2':'$1'",$pagename);
    preg_match("/([\d]*)[-]?([\d]*)[-]?([\d]*)/e",$tname,$m);
    if($m[1]) $yy = $m[1];
    else return $tname;
    if($m[2]) $mm = $m[2];
    if($m[3]) $dd = $m[3];
    else $dd = '1'; 
    $tdate = mktime(0,0,0,$mm,$dd,$yy);
    $bmonth = strftime("%B %Y",$tdate);
    return $bmonth;
}


# change message for empty new page 
$group = PageVar($pagename, '$Group');
if($group=='BlogComments') {
    $DefaultPageTextFmt = 'Compose message, add author name, and click post';
    }

# exclude pages patterns for list=blog
$SearchPatterns['blog'][] = '!\\.(All)?Recent(Changes|Uploads|Pages)$!';
$SearchPatterns['blog'][] = '!\.RecentChanges$!';
$SearchPatterns['blog'][] = '!\.GroupHeader$!';
$SearchPatterns['blog'][] = '!\.GroupFooter$!';
$SearchPatterns['blog'][] = '!\.GroupAttributes$!';
$SearchPatterns['blog'][] = '!\.SideBar$!';
$SearchPatterns['blog'][] = '!\.RightBar$!';
$SearchPatterns['blog'][] = '!\.PageTopMenu$!';
$SearchPatterns['blog'][] = '!\.PageFootMenu$!';
$SearchPatterns['blog'][] = '!\.HomePage$!';
$SearchPatterns['blog'][] = '!\.BlogPages$!';
$SearchPatterns['blog'][] = '!\.BlogArchive$!';
$SearchPatterns['blog'][] = '!\.BlogCategories$!';
$SearchPatterns['blog'][] = '!\.BlogComments$!';
$SearchPatterns['blog'][] = '!\.BlogArchiveTemplate$!';
$SearchPatterns['blog'][] = '!\.BlogCategoriesTemplate$!';

## add double line horiz rule markup ====
# Markup('^====','>^->','/^====+/','<:block,1>
#  <hr class="hr-double" />');
/*  
# double hor. rule style class
# add to pub/css/local.css:
.hr-double {
    border: none 0; 
    border-top:1px solid #aaa; 
    border-bottom:1px solid #aaa; 
    width:100%; 
    background:#fff; 
    height:3px; 
    }";
*/

## automatic loading of blog default pages
    $where = count($WikiLibDirs);
    if ($where>1) $where--;
    array_splice($WikiLibDirs, $where, 0, 
        array(new PageStore("$FarmC/blogsimple/wikilib.d/\$FullName")));

## add blog feed links to html headers
$gp = FmtPageName('$Group', $pagename);
if ($gp=='Blog'||$gp=='BlogPages'||$gp=='BlogComments'||$gp=='BlogArchive'||$gp=='BlogCategories') {
      # disable further feedlinks introduced with feedlinks.php (must be loaded later)
      $EnableRssLink = 0; 
      $EnableAtomLink = 0;
  $HTMLHeaderFmt['blogrsslink'] =
    "\n <link rel='alternate' title='\$WikiTitle Blog RSS Feed'
      href='\$ScriptUrl?action=rss&group=BlogPages&list=blog&order=-ctime&count=50'
      type='application/rss+xml' />\n  ";
  $HTMLHeaderFmt['blogatomlink'] =
    "<link rel='alternate' title='\$WikiTitle Blog Atom Feed'
      href='\$ScriptUrl?action=atom&group=BlogPages&list=blog&order=-ctime&count=50'
      type='application/atom+xml' />\n  ";
}
