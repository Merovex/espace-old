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

function _DoCodeBlocks_callback($matches) {
    $codeblock = $matches[1];

    $codeblock = _EncodeCode(_Outdent($codeblock));
//  $codeblock = _Detab($codeblock);
    # trim leading newlines and trailing whitespace
    $codeblock = preg_replace(array('/\A\n+/', '/\s+\z/'), '', $codeblock);

    $result = "\n\n<pre><code>" . $codeblock . "\n</code></pre>\n\n";

    return $result;
}
function _Outdent($text) {
#
# Remove one level of line-leading tabs or spaces
#
    global $MarkdownTabWidth;
    return preg_replace("/^\s{1,$MarkdownTabWidth})/m", '', $text);
}
function _Detab($text) {
#
# Replace tabs with the appropriate amount of space.
#
    global $MarkdownTabWidth;

    # For each line we separate the line in blocks delemited by
    # tab characters. Then we reconstruct every line by adding the 
    # appropriate number of space between each blocks.
    
    $lines = explode("\n", $text);
    $text = "";
    
    foreach ($lines as $line) {
        # Split in blocks.
        $blocks = explode("\t", $line);
        # Add each blocks to the line.
        $line = $blocks[0];
        unset($blocks[0]); # Do not add first block twice.
        foreach ($blocks as $block) {
            # Calculate amount of space, insert spaces, insert block.
            $amount = $MarkdownTabWidth - strlen($line) % $MarkdownTabWidth;
            $line .= str_repeat(" ", $amount) . $block;
        }
        $text .= "$line\n";
    }
    return $text;
}

$md_escape_table = array(
    "\\" => md5("\\"),
    "`" => md5("`"),
    "*" => md5("*"),
    "_" => md5("_"),
    "{" => md5("{"),
    "}" => md5("}"),
    "[" => md5("["),
    "]" => md5("]"),
    "(" => md5("("),
    ")" => md5(")"),
    ">" => md5(">"),
    "#" => md5("#"),
    "+" => md5("+"),
    "-" => md5("-"),
    "." => md5("."),
    "!" => md5("!")
);
function _EncodeCode($_) {
#
# Encode/escape certain characters inside Markdown code runs.
# The point is that in code, these characters are literals,
# and lose their special Markdown meanings.
#
    global $md_escape_table;

    # Encode all ampersands; HTML entities are not
    # entities within a Markdown code span.
    $_ = str_replace('&', '&amp;', $_);

    # Do the angle bracket song and dance:
    $_ = str_replace(array('<',    '>'), 
                     array('&lt;', '&gt;'), $_);

    # Now, escape characters that are magic in Markdown:
    $_ = str_replace(array_keys($md_escape_table), 
                     array_values($md_escape_table), $_);

    return $_;
}
