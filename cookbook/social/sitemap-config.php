<?php

$Conditions['isadmin'] = "\$GLOBALS['IsUserAdmin']==\$condparm";
$Conditions['iseditor'] = "\$GLOBALS['IsUserEditor']==\$condparm";
$FPLByGroupGFmt = "<h2>\$Groupspaced</h2>";
$FPLByGroupIFmt = "<dd><a href='\$PageUrl'>\$Titlespaced</a></dd>\n";
$SearchPatterns['default'][] = '!^([^.]+)\\.\\1$!';
$SearchPatterns['default'][] = '!^(PmWiki|Farm|Main|Site)\.!';
$SearchPatterns['default'][] = '!Template!';
$SearchPatterns['default'][] = '!\\.(All)?Recent(Changes|Uploads)$!';
$SearchPatterns['default'][] = '!^X!';
$SearchPatterns['sitemap'][] = '!^([^.]+)\\.\\1$!';

