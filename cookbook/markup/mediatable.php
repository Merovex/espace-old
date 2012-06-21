<?php

Markup('{|','fulltext','/{\|(.*?)\|}/se',"MediaTable('$1');");

function MediaTable($table) {
    $rows = preg_split('/\|\-/', $table);
    $header_info = trim(array_shift($rows));
    $table = "\n\n<table $header_info>";
    foreach($rows as $row) {
        $new_row = '';
        $row = trim($row);
        $first_character = $row[0];
        $row = "\n$row";
        $tag = 'td';
        $tag = ($first_character == '!') ? 'th' : 'td';

        $cells = preg_split('/(?<=\\n)[!|\|]/s', $row);
        array_shift($cells);
        foreach ($cells as $cell) {
          if (preg_match('/\|\|/', $cell)) { foreach (preg_split('/\|\|/', $cell) as $c) { $new_row .= "<$tag>$c</$tag>"; } }
          $stuff = preg_split("/\|/", $cell);
          if (preg_match("/\[/", $stuff[0])) {
		$stuff[0] .= "|" . array_pop($stuff);
          }
          $attr='';
          if (count($stuff) > 1) { $attr = ' '.$stuff[0]; $cell = $stuff[1]; }
          $new_row .= "<$tag$attr>".trim($cell)."</$tag>\n";
        }
        $new_row = "<tr>\n$new_row</tr>\n";
        $table .= $new_row;
    }
    $table = preg_replace('/<tr>.?<th.*<\/th>.?<\/tr>/ims',"<thead>$0</thead>\n", $table); 
    $table = preg_replace('/<tr>.?<td.*<\/td>.?<\/tr>/ims',"<tbody>$0</tbody>\n", $table); 
    $table .= '</table>';
   return $table; 
}
