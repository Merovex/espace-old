<?php if (!defined('PmWiki')) exit();
#
# Markdown  -  
#
# Copyright (c) 2006 Benjamin C. Wilson
# <http://www.dausha.net/Markdown/Recipe>
#

#$EnableMarkdownInline = 0;


include_once("$FarmD/scripts/diag.php");
SDV($EnableMarkdown,0); # Off by default.
SDV($MarkdownSectionLevel, '!!'); # Creates <h2> for === sections.
SDV($MarkdownSubSectionLevel, '!!!'); # Creates <h3> for --- sections.
SDV($MarkdownSubSubSectionLevel, '!!!!'); # Creates <h4> for --- sections.
if ($EnableMarkdown) {
    $EnableStdMarkup = 0; # Turn off PmWiki's markup behavior.
    SDV($MarkdownTabWidth, 4);
    SDV($MarkdownTabLessOne, $MarkdownTabWidth - 1);
    SDV($EnableMarkdownInline, 1);
    SDV($EnableMarkdownLinks, 1);
    SDV($EnableMarkdownBlock, 1);
    SDV($EnableMarkdownPrecode, 1);
    SDV($EnableMarkdownLists, 1);
    SDV($EnableMarkdownBlockquotes, 1);
    include_once("markdown/pmwiki-directives.php");
    include_once("markdown/pmwiki-links.php");
    include_once("markdown/pmwiki-advtables.php");
    include_once("markdown/pmwiki-block.php");
    include_once("markdown/pmwiki-inline.php"); # Added 2006-05-07 BCWI
    $HTMLVSpace = '';
}
if ($EnableMarkdownInline) include_once("markdown/markdown-inline.php");
if ($EnableMarkdownLinks)  include_once("markdown/markdown-links.php");
if ($EnableMarkdownBlock)  include_once("markdown/markdown-block.php");
if ($EnableMarkdownPrecode)  include_once("markdown/markdown-precode.php");
if ($EnableMarkdownLists)  include_once("markdown/markdown-lists-0.2.php");
if ($EnableMarkdownBlockquotes)  include_once("markdown/markdown-blockquotes.php");

/*
#Markup("prebullet", "<bullet", "/^(\s+)\\*\s/e", "deindent('$1','*');");
#Markup("preordered", "<orderedlists", "/^(\s+)(\\#|[0-9]+\.)\s/e", "deindent('$1','#');");
function deindent($stuff,$type) {
    $level = (int) strlen($stuff) / 3;
    return str_pad('',$level,$type);
}
## bullet lists
Markup('bullets','block','/^(\\*+)\\s?(\\s*)/','<:ul,$1,$0>$2');
Markup('orderedlists','<bullets','/^(#+)\\s?(\\s*)/','<:ol,$1,$0>$2');
*/
