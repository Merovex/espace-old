<?php

$md_list_level = 0;

function deindent($stuff,$type) {
    global $MarkdownTabWidth, $MarkdownTabLessOne;
    if (!$MarkdownTabLessOne) $MarkdownTabLessOne = $MarkdownTabWidth - 1;
    $level = (int) strlen($stuff) / $MarkdownTabLessOne;
    if ($level == 0) $level = 1;
    return str_pad('',$level,$type);
}

Markup("bullethr", "/\\* \\* \\*/", "<hr />");
Markup("prebullet", "<bullet", "/^(\s+)[*+-]\s/e", "deindent('$1','*');");
Markup("preordered", "<orderedlists", "/^(\s*)(\\#|[0-9]+\.)\s/e", "deindent('$1','#');");
Markup('orderedlists','<bullets','/^(#+)\\s?(\\s*)/','<:ol,$1,$0>$2');
Markup('bullets','block','/^([*-+]+)\\s?(\\s*)/','<:ul,$1,$0>$2');

## Whitespace at the beginning of lines can be used to maintain the
## indent level of a previous list item, or a preformatted text block.
Markup('^ws', '<^img', '/^(\\s+)/e', "WSIndent('$1')");
function WSIndent($i,$w='') {
  global $MarkupFrame, $MarkdownTabLessOne;
  $icol = strlen($i);
  for($depth = count(@$MarkupFrame[0]['cs']); $depth > 0; $depth--) {
    if ((@$MarkupFrame[0]['is'][$depth] * $MarkdownTabLessOne ) <= $icol) {
      $MarkupFrame[0]['idep'] = $depth;
      $MarkupFrame[0]['icol'] = $icol;
      #return $w;
      return '';
    }
  }
  return "<:pre,1>$i";
}


