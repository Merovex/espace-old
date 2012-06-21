<?php if (!defined('PmWiki')) exit();
/*  Copyright 2006 Hans Bracker, modified from newpagebox.php
    Copyright 2005 Patrick R. Michaud (pmichaud@pobox.com) and
    newpagebox3.php thanks to code from DaveG.
    This file is newpageboxplus.php; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.  

    To use this script, simply place it into the cookbook/ folder
    and add the line
        include_once('cookbook/newpageboxplus.php');
    to a local customization file. Use it as an alternative to newpagebox.php.
    
    usage: (:newpagebox [parameter=value] [parameter=value] :)
    
    Possible parameters to use inside the markup:
    
    base=Group.PageName -- create page in the same group as Group.PageName 
                           (PageName does not need to exist). base=Group is NOT enough!
    template=Group.PageTemplateName -- use Group.PageTemplateName as template for new page.
    value="Create New Page" -- label or value for the inside of the field, 
                            which disappears when clicking the box. Default is empty: "".
    size=number -- size of input box, default is 30.
    label="Button Label" -- label for the button, default "Create a new page called:".
    button=position -- use "left" or "right" to position button (default is "left").
    focus=true -- adds onfocus and onblur javascript which will make any intial value 
                    disappear when clicking on the box. Default is "".
    
*/
define(NEWPAGEBOXPLUS_VERSION, '2006-04-28');

# add markup (:newpagebox:)
Markup('newpagebox', 'directives',
  '/\\(:newpagebox\\s*(.*?):\\)/ei',
  "NewPageBox(\$pagename, PSS('$1'))");
  
# add action=new (the form sends this with the other values)
$HandleActions['new'] = 'HandleNew';

# add form function. The values for parameter defaults can be changed here
function NewPageBox($pagename, $opt) {
  global $ScriptUrl;
  $defaults = array(
    'base' => '',
    'template' => '',
    'value' => '',
    'size'   => '30',
    'label' => FmtPageName(' $[Create a new page called:] ', $pagename),
    'button' => 'left',
    'focus' => '',
    'save' => '');
  $opt = array_merge($defaults, ParseArgs($opt));
  $buttonHTML = "<input class='inputbutton newpagebutton' type='submit' value='{$opt['label']}' />";
  $onfocusHTML = "onfocus=\"if(this.value=='{$opt['value']}') {this.value=''}\" 
             onblur=\"if(this.value=='') {this.value='{$opt['value']}'}\" ";
  return "<form ".Keep("class='newpage' action='$PageUrl' method='post'>
    <input type='hidden' name='n' value='$pagename' />
    <input type='hidden' name='action' value='new' />
    <input type='hidden' name='value' value='{$opt['value']}' />
    <input type='hidden' name='focus' value='{$opt['focus']}' />
    <input type='hidden' name='base' value='{$opt['base']}' />
    <input type='hidden' name='save' value='{$opt['save']}' />
    <input type='hidden' name='template' value='{$opt['template']}' />" .
    ($opt['button']=="left" ? $buttonHTML : "") .
    "<input class='inputbox newpagetext' type='text' name='name' value='{$opt['value']}' size='{$opt['size']}'" .
    ($opt['focus']=="true" ? $onfocusHTML : "") . 
    "/>" .
    ($opt['button']=="right" ? $buttonHTML : "") .
    "</form>");
}

# handles action=new, i.e. what the form sends, sends new page to edit
function HandleNew($pagename) {
  global $Author, $Now;
  $name = @$_REQUEST['name'];
  if (!$name) Redirect($pagename);
  if (@$_REQUEST['focus'] && $name==$_REQUEST['value']) Redirect($pagename);
  $base = MakePageName($pagename, $_REQUEST['base']);
  $basegroup = PageVar($base, '$Group');
  if (@$_REQUEST['base']=='') $newpage = MakePageName($pagename, $name);
  else $newpage = MakePageName($base, "$basegroup.$name");
  $urlfmt = '$PageUrl?action=edit';
  if (@$_REQUEST['template']) {
    $urlfmt .= '&template=' . MakePageName($base, $_REQUEST['template']); }
  if (@$_REQUEST['save']) { 
     if(PageExists($newpage)) Redirect($newpage, $urlfmt);
     if (@$_REQUEST['template'] && PageExists($_REQUEST['template'])) {
        $p = RetrieveAuthPage($_REQUEST['template'], 'read', false, READPAGE_CURRENT);
          if ($p['text'] > '') $new['text'] = $p['text']; 
          $new['author'] = $Author;
          $new['ctime'] = $Now; 
          }
        SaveAttributes($newpage, $new, $new);
        PostPage($newpage, $new, $new);
        PostRecentChanges($newpage, $new, $new);
        Redirect($newpage);
       }
  Redirect($newpage, $urlfmt);
}

