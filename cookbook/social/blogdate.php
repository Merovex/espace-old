<?php if (!defined('PmWiki')) exit();
/*  Copyright 2005 Hans Bracker
    This file is blogdate.php; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  

    To use this script, simply place it into the cookbook/ folder
    and add the line

        include_once('cookbook/blogdate.php');

    to a local customization file.
*/

# add page variable {$Today}, formats today's date as yyyy-mm-dd
$FmtPV['$Today'] = 'strftime("%Y-%m-%d", time() )';
$FmtPV['$TodayNodash'] = 'strftime("%Y%m%d", time() )';

# add page variable {$BlogDate} as page creation date
$FmtPV['$BlogDate'] = 'strftime("%d %B %Y", $page["ctime"])'; 

# add page variable {$BlogTitle} as spaced name with date stripped off
$FmtPV['$BlogTitle'] = 'StripDate($pagename)';
function StripDate($pagename) {
    print "$pagename";
    $sd = preg_replace("/[^.]*\.([\d\-]*)(.*)/e","'$2'==''?'$1':'$2'",$pagename); 
    $BlogTitle = AsSpaced($sd);
    return $BlogTitle;
};
