<?php if (!defined('PmWiki')) exit();
/*  Copyright 2005 Ben Wilson (ameen@dausha.net)
    This file assists the ability to conceal the mechanism of PmWiki. It does
    this by removing the reference to 'index.php' or 'pmwiki.php' from the URL.
    Also includes an option for going SSL. Must be used in conjunction with
    .htaccess or other URL rewriting that will know to redirect non-script
    calls to the script.

    This file extends PmWiki; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  See pmwiki.php for full details.
*/
SDV($EnableOpenUrlSecure, 0);
SDV($EnableOpenUrl, 0);
if ($EnableOpenUrl || $EnableOpenUrlSecure) {
    $ScriptUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
    $ScriptUrl = preg_replace('/\/index.php/','', $ScriptUrl);
    $ScriptUrl = preg_replace('/\/pmwiki.php/','', $ScriptUrl);
}
if ($EnableOpenUrlSecure) {
    $ScriptUrl = preg_replace('/http/','https', $ScriptUrl);
}
