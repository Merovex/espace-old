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


## Markdown Nested Lists.
Markup('mkul','_begin','  *','return DoLists($x);');
## bullet lists
Markup('^*','block','/^(\\*+)\\s?(\\s*)/','<:ul,$1,$0>$2');
## numbered lists
Markup('^#','block','/^(#+)\\s?(\\s*)/','<:ol,$1,$0>$2');

function SplitBlocks($t) {
   return split("<:vspace>", $t);
}

function DoLists($text) {
  $blocks = SplitBlocks($text);
  $output = array();
  foreach($blocks as $b) {
    $output[] =_DoLists($b);
  }
  DisableMarkup('mkul'); # Otherwise, we will never get finished.
  return join('<:vspace>',$output);
  return $text;
}

$md_list_level = 0;
function _DoLists($text) {
    global $MarkdownTabLessOne;
    $space = $MarkdownTabLessOne; # one less than tab.

    # Re-usable patterns to match list item bullets and number markers:
    $marker_ul  = '[*+-]';
    $marker_ol  = '\d+[.]';
    $marker_any = "(?:$marker_ul|$marker_ol)";

    $markers = array($marker_ul, $marker_ol);

    foreach ($markers as $marker) {
        # Re-usable pattern to match any entirel ul or ol list:
        $whole_list = '
            (                               # $1 = whole list
              (                             # $2
                [ ]{0,'.$space.'}
                ('.$marker.')               # $3 = first list item marker
                [ \t]+
              )
              (?s:.+?)
              (                             # $4
                  \z
                |
                  \n{2,}
                  (?=\S)
                  (?!                       # Negative lookahead for another list item marker
                    [ \t]*
                    '.$marker.'[ \t]+
                  )
              )
            )
        '; // mx
        
        # We use a different prefix before nested lists than top-level lists.
        # See extended comment in _ProcessListItems().
    
        if ($md_list_level) {
            $text = preg_replace_callback('{
                    ^
                    '.$whole_list.'
                }mx',
                '_DoLists_callback_top', $text);
        }
        else {
            $text = preg_replace_callback('{
                    (?:(?<=\n\n)|\A\n?)
                    '.$whole_list.'
                }mx',
                '_DoLists_callback_nested', $text);
        }
    }

    return $text;
}
function _DoLists_callback_top($matches) {
    # Re-usable patterns to match list item bullets and number markers:
    $marker_ul  = '[*+-]';
    $marker_ol  = '\d+[.]';
    $marker_any = "(?:$marker_ul|$marker_ol)";
    $start = _DoLists_ordered_start($matches[2]);
    
    $list = $matches[1];
    $list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";
    
    $marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );
    
    # Turn double returns into triple returns, so that we can make a
    # paragraph for the last item in a list, if necessary:
    $list = preg_replace("/\n{2,}/", "\n\n\n", $list);
    $result = _ProcessListItems($list, $marker_any);
    
    # Trim any trailing whitespace, to put the closing `</$list_type>`
    # up on the preceding line, to get it past the current stupid
    # HTML block parser. This is a hack to work around the terrible
    # hack that is the HTML block parser.
    $result = rtrim($result);
    $result = "<$list_type$start>" . $result . "</$list_type>\n";
    return $result;
}
function _DoLists_ordered_start($m) {
    $count = trim($m,'. ');
    return ($count > 1) ? " start=$count" : '';
}
function _DoLists_callback_nested($matches) {
    # Re-usable patterns to match list item bullets and number markers:
    $start = _DoLists_ordered_start($matches[2]);

    $marker_ul  = '[*+-]';
    $marker_ol  = '\d+[.]';
    $marker_any = "(?:$marker_ul|$marker_ol)";
    
    $list = $matches[1];
    $list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";
    
    $marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );
    
    # Turn double returns into triple returns, so that we can make a
    # paragraph for the last item in a list, if necessary:
    $list = preg_replace("/\n{2,}/", "\n\n\n", $list);
    $result = _ProcessListItems($list, $marker_any);
    $result = "<$list_type$start>\n" . $result . "</$list_type>\n";
    return $result;
}


function _ProcessListItems($list_str, $marker_any) {
#
#   Process the contents of a single ordered or unordered list, splitting it
#   into individual list items.
#
    global $md_list_level;
    
    # The $md_list_level global keeps track of when we're inside a list.
    # Each time we enter a list, we increment it; when we leave a list,
    # we decrement. If it's zero, we're not in a list anymore.
    #
    # We do this because when we're not inside a list, we want to treat
    # something like this:
    #
    #       I recommend upgrading to version
    #       8. Oops, now this line is treated
    #       as a sub-list.
    #
    # As a single paragraph, despite the fact that the second line starts
    # with a digit-period-space sequence.
    #
    # Whereas when we're inside a list (or sub-list), that line will be
    # treated as the start of a sub-list. What a kludge, huh? This is
    # an aspect of Markdown's syntax that's hard to parse perfectly
    # without resorting to mind-reading. Perhaps the solution is to
    # change the syntax rules such that sub-lists must start with a
    # starting cardinal number; e.g. "1." or "a.".
    
    $md_list_level++;

    # trim trailing blank lines:
    $list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

    $list_str = preg_replace_callback('{
        (\n)?                           # leading line = $1
        (^[ \t]*)                       # leading whitespace = $2
        ('.$marker_any.') [ \t]+        # list marker = $3
        ((?s:.+?)                       # list item text   = $4
        (\n{1,2}))
        (?= \n* (\z | \2 ('.$marker_any.') [ \t]+))
        }xm',
        '_ProcessListItems_callback', $list_str);

    $md_list_level--;
    return $list_str;
}
function _ProcessListItems_callback($matches) {
    $item = $matches[4];
    $leading_line =& $matches[1];
    $leading_space =& $matches[2];

    if ($leading_line || preg_match('/\n{2,}/', $item)) {
        #$item = _Outdent($item);
    }
    else {
        # Recursion for sub-lists:
        $item = _DoLists(_Outdent($item));
        $item = preg_replace('/\n+$/', '', $item);
        #$item = _RunSpanGamut($item);
    }

    return "<li>" . $item . "</li>\n";
}

