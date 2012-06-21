<?php if (!defined('PmWiki')) exit();
/*  Copyright 2006 Ben Wilson (ameen@dausha.net)
This file adds a page rename capability to pmwiki 2.
This file extends PmWiki; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published
by the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.  See pmwiki.php for full details.
*/

Markup("grouptitle", 'directives', "/\(:grouptitle(.*?):/e", 'GroupTitle("$1");');
function GroupTitle($t) {
    global $FmtV;
    $t = trim($t);
    $FmtV['$GroupTitle'] = $t;
}

