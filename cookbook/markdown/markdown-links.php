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

$md_nested_brackets_depth = 6;
$md_nested_brackets = 
    str_repeat('(?>[^\[\]]+|\[', $md_nested_brackets_depth).
    str_repeat('\])*', $md_nested_brackets_depth);

Markup("MkAn1", '<links',
  "/(\\[($md_nested_brackets)\\]\\([ \\t]*<?(.*?)>?[ \\t]*((['\"])(.*?)\\5)?\\))/xse",
  "DoAnchors('$1','$2','$3','$4')");

Markup("MkAn2",'>fulltext','[','return DoAnchors2($x);');

function DoAnchors2($text) {
    global $md_nested_brackets;
    $text = _StripLinkDefinitions($text);
    $text = preg_replace_callback("{
        (                   # wrap whole match in $1
          \\[
            ($md_nested_brackets)   # link text = $2
          \\]

          [ .,\?!;:]?              # one optional space
          (?:\\n[ ]*)?      # one optional newline followed by spaces

          \\[
            (.*?)       # id = $3
          \\]
        )
        }mxs",
        '_DoAnchors_reference_callback', $text);
    return $text;
}

function DoAnchors($all,$name,$url,$title=''){
    if ($title != '') {
        $title = trim($title,' \"');
        $title = " title='$title'";
    }
    return Keep("<a href='$url'$title>$name</a>");
}

function _DoAnchors_inline_callback($matches) {
    global $md_escape_table;
    $whole_match    = $matches[1];
    $link_text      = $matches[2];
    $url            = $matches[3];
    $title          =& $matches[6];

    $result = "<a href=\"$url\"";
    if (isset($title)) {
        $title = str_replace('"', '&quot;', $title);
        #$title = str_replace(array('*', '_'),
                             #array($md_escape_table['*'], $md_escape_table['_']),
                             #$title);
        $result .=  " title='$title'";
    }
    
    $result .= ">$link_text</a>";
    return $result;
}


# Table of hash values for escaped characters:
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
$md_urls = array();
$md_titles = array();

function _DoAnchors_reference_callback($matches) {
    global $md_urls, $md_titles, $md_escape_table;
    $all = $matches[1];
    $text   = $matches[2];
    $link_id     = strtolower($matches[3]);

    if ($link_id == "") {
        $link_id = strtolower($text); # for shortcut links like [this][].
    }

    if (isset($md_urls[$link_id])) {
        $url = $md_urls[$link_id];
        # We've got to encode these to avoid conflicting with italics/bold.
        $result = "<a href=\"$url\"";
        if ( isset( $md_titles[$link_id] ) ) {
            $title = $md_titles[$link_id];
            $title = str_replace(array('*',     '_'),
                                 array($md_escape_table['*'], 
                                       $md_escape_table['_']), $title);
            $result .=  " title='$title'";
        }
        $result .= ">$text</a>";
    }
    else {
        $result = $all;
    }
    return Keep($result);
}
function _StripLinkDefinitions_callback($matches) {
    global $md_urls, $md_titles;
    $link_id = strtolower($matches[1]);
    $md_urls[$link_id] = _EncodeAmpsAndAngles($matches[2]);
    if (isset($matches[3]))
        $md_titles[$link_id] = str_replace('"', '&quot;', $matches[3]);
    return ''; # String that will replace the block
}
function _EncodeAmpsAndAngles($text) {
# Smart processing for ampersands and angle brackets that need to be encoded.

    # Ampersand-encoding based entirely on Nat Irons's Amputator MT plugin:
    #   http://bumppo.net/projects/amputator/
    $text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', 
                         '&amp;', $text);;

    # Encode naked <'s
    $text = preg_replace('{<(?![a-z/?\$!])}i', '&lt;', $text);

    return $text;
}
function _StripLinkDefinitions($text) {
#
# Strips link definitions from text, stores the URLs and titles in
# hash references.
#
    global $MarkdownTabWidth;
    $less_than_tab = $MarkdownTabWidth - 1;

    # Link defs are in the form: ^[id]: url "optional title"
    $text = preg_replace_callback('{
                        ^[ ]{0,'.$less_than_tab.'}\[(.+)\]: # id = $1
                          [ \t]*
                          \n?               # maybe *one* newline
                          [ \t]*
                        <?(\S+?)>?          # url = $2
                          [ \t]*
                          \n?               # maybe one newline
                          [ \t]*
                        (?:
                            (?<=\s)         # lookbehind for whitespace
                            ["(]
                            (.+?)           # title = $3
                            [")]
                            [ \t]*
                        )?  # title is optional
                        (?:\n+|\Z)
        }xm',
        '_StripLinkDefinitions_callback',
        $text);
    return $text;
}
function _UnescapeSpecialChars($text) {
#
# Swap back in all the special characters we've hidden.
#
    global $md_escape_table;
    return str_replace(array_values($md_escape_table), 
                       array_keys($md_escape_table), $text);
}
