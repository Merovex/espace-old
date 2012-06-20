<?php if (!defined('PmWiki')) exit();
#
# Markdown  -  A text-to-HTML conversion tool for web writers
#
# Copyright (c) 2004-2005 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#
# Copyright (c) 2004-2005 Michel Fortin - PHP Port  
# <http://www.michelf.com/projects/php-markdown/>
#
# PmWiki Conversion Copyrigh (c) 2006 Benjamin C. Wilson
# <http://www.dausha.net/Markdown/Recipe
#
Markup('MarkdownComment', '<comment', '/<!--.*?-->/si', '');

$BlockMarkup['markdownbq'] = 
       array("<div style='color:#900;'>",
       "</div><div style='color:#900;'>",'</div>',1
       );

#### Block markups ####
#
## Headings
function MarkdownAtxHeading($s, $t) {
    return str_pad('', strlen($s), '!')." $t";
}
Markup('mkatxheading','<preordered', '/(#{1,6})\s*(\w.*?)#+/e', "MarkdownAtxHeading('$1','$2');");
Markup('mkheading', 'fulltext', '/^(\S.+)[ \t]*\n([-=~]){5,}[ \t]*\n+/em', "DoHeadings('$1', '$2');");

## horiz rule
Markup('mkhr1', 'block', '/^[ ]{0,2}([ ]?\*[ ]?){3,}[ \t]*$/mx',"<:block,1><hr />");
Markup('mkhr2', 'block', '/^[ ]{0,2}([ ]?-[ ]?){3,}[ \t]*$/mx',"<:block,1><hr />");
Markup('mkhr3', 'block', '/^[ ]{0,2}([ ]?_[ ]?){3,}[ \t]*$/mx',"<:block,1><hr />");
function DoHeadings($text, $level) {
  global $MarkdownSectionLevel, $MarkdownSubSectionLevel,
  $MarkdownSubSubSectionLevel;
  $key = $level{0};
  $levels = array(
    '=' => $MarkdownSectionLevel, 
    '-' => $MarkdownSubSectionLevel,
    '~' => $MarkdownSubSubSectionLevel, 
  );
  return $levels[$key]."$text";
}
/*
#Markup('markdown indent','block','/^(&gt;\\s?)(\\s*)/','<:markdownbq,$1,$1 $2>$2');
#Markup('markdownIndent','block','/^(&gt;\\s?)(\\s*)/e',"bq('m','$1','$2');");
#Markup('^->','block','/^(?>(-+))&gt;\\s?(\\s*)/e',"bq('b','$1','$2');");
*/

function bq($m, $one,$two) {
    $one = trim($one);
    $one = preg_replace("/&gt;/", '>', $one);
    $s = '';
    if ($m == 'b') {
    $s = "<:indent,$one,$one $two>$two";
    } else {
    $s = "<:markdownbq,$one,$one, $two>$two";
    }

    print "<pre>$s</pre>";
    return $s;
}

