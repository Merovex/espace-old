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

Markup('mkbq1','block', '/^([ \t]?((?>&gt;)[ \t])+.*)+/me', "_DoBlockQuotes_callback1('$0');");
Markup('mkbq','fulltext', '/^([ \t]?((((?>&gt;)[ \t])+).*?)<:vspace>)/mes', "_DoBlockQuotes_callback('$0','$2');");

function _DoBlockQuotes_callback1($bq) {
    $bq_R = '/^((((&gt;[ \t])+).*))/mes';
    $bq = preg_replace(array('/^[ \t]*&gt;[ \t]?/m', '/^[ \t]+$/m'), '', $bq);
    $bq = preg_replace($bq_R, "_DoBlockQuotes_callback1('$0','$1');", $bq);
    return "<blockquote>$bq</blockquote>";
    die ($bq);
}
function _DoBlockQuotes_callback($all,$bq) {
    $bq_R = '/^((((&gt;[ \t])+).*))/mes';
    # trim one level of quoting - trim whitespace-only lines
    $bq = preg_replace(array('/^[ \t]*&gt;[ \t]?/m', '/^[ \t]+$/m'), '', $bq);
    $paras = preg_split("/\n\n/", $bq);
    for ($i = 0; $i < count($paras); $i++) {
        $paras[$i] = preg_replace($bq_R, "_DoBlockQuotes_callback('$0','$1');", $paras[$i]);
        $paras[$i] = preg_replace("/\n/", '', $paras[$i]);
        $paras[$i] = "<blockquote>".$paras[$i]."</blockquote>";
    }
    $bq = join("\n\n", $paras);
    return $bq."<:vspace>";

}
