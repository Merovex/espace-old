<?php if (!defined('PmWiki')) exit();
/*
    commentboxstyled.php
    Copyright 2005, 2006 Hans Bracker, an adaptation of commentbox.php by 
    John Rankin, copyright 2004, 2005 John Rankin john.rankin@affinity.co.nz
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    
    Adds (:commentbox:) and (:commentboxchrono:) markups.
    Put (:commentbox:) at the top of a page, or a GroupHeader,
    latest entries appear at top, underneat the commentbox,
    i.e. in reverse chronological order.
    
    Put (:commentboxchrono:) at the bottom of a page, or a GroupFooter,
    latest entries appear at bottom, above the commentbox, 
    i.e. in chronological order.   
    
    Adds commentbox with chronologfical entries automatically to pages 
    which contain 'Journal' or 'Diary' in their name.
    
    If you use forms markup instead of (:commentbox:) or
    (:commentboxchrono:) markup, add name=cboxform to (:input form ....:),
    which functions as an identifier for the internal authentication check.
    
    You can hide the commentbox for users who have no page edit permissions
    with conditional markup of the EXACT form:
    (:if auth edit:)(:commentbox:)(:if:)     or
    (:if auth edit:)(:commentboxchrono:)(:if:)
    
    Otherwise users can post comments even if they don't have page edit permission.

*/
//define the commentboxstyled version number
define(COMMENTBOXSTYLED_VERSION, '2006-04-24');

# you may wish to put the style declarations into pub/css/local.css
# and set $CommentStyles = 0; or delete $HTMLStylesFmt definitions below.
SDV($CommentStyles, 1);

# The form check will display a warning message if user has not provided content and name.
# Set to 0 if no javascript form check is required.
SDV($EnableCommentFormCheck, 1);
SDV($NoCommentMessage, '$[Please enter a comment to post]');
SDV($NoAuthorMessage, '$[Please enter your name as author]');

if($CommentStyles == 1) {
$HTMLStylesFmt[commentbox] = "
/* styling of commentbox entries */
.messagehead, .journalhead { 
            margin:0;
            padding:0 0 0 3px;
            border:1px solid #999;
            }
.messageitem, .journalitem {
            margin:0;
            padding:3px;
            border-left:1px solid #999;
            border-right:1px solid #999;
            border-bottom:1px solid #999;
            }
.messagehead { background:#e5e5ff; }
/*use the following if message head should be same as message item */
/* .messagehead { background:#eef; border-bottom:none; } */
.messageitem { background:#eef; }
.journalhead { background:#ffb; }
.journalitem { background:#ffc; }

.diaryhead h4 { border-bottom:1px solid #999; 
            margin-bottom:1px; }
* html .diaryhead h4 { margin-bottom:0; }
.diaryitem {background:#ffc;
            margin:0;
            padding:3px;
            border-left:1px solid #999;
            border-right:1px solid #999;
            border-bottom:1px solid #999;
            }
.messagehead h5, .messagedate h4, .journalhead h5, .journaldate h4,
.diaryhead h4 { margin:0.25em 0 0 0; }

.commentbutton { margin:0 0 0 5px; 
                padding:0 3px }
.commenttext { width:100%; 
               margin:0 0 3px 0 }
";}

if($EnableCommentFormCheck==1) {
$HTMLHeaderFmt['checkform'] = "
<script type='text/javascript' language='JavaScript1.2'>
  function checkform ( form ) {
        if (form.text.value == \"\") {
        window.alert( '$NoCommentMessage' );
        form.author.focus();
        return false ;
      }   
      if (form.author.value == \"\") {
        window.alert( '$NoAuthorMessage' );
        form.author.focus();
        return false ;
      }
      return true ;
  }
</script>
";}

SDV($DiaryBoxFmt,"<div id='diary'><form action='\$PageUrl' method='post'>
    <input type='hidden' name='n' value='\$FullName' />
    <input type='hidden' name='action' value='comment' />
    <input type='hidden' name='author' value='\$Author' />
    <table width='90%'><tr>
    <th class='prompt' align='right' valign='top'>$[New entry]&nbsp;</th>
    <td><textarea class='inputtext commenttext' name='text' rows='6' cols='50'></textarea><br />
    <input class='inputbutton commentbutton' type='submit' name='post' value=' $[Post] ' />
    <input class='inputbutton commentbutton' type='reset' value='$[Reset]' /></td></tr></table></form></div>");
SDV($CommentBoxFmt,"
    <div id='message'><form name='cbox' class='inputform' action='\$PageUrl' method='post' onsubmit='return checkform(this);'>
    <input type='hidden' name='n' value='\$FullName' />
    <input type='hidden' name='action' value='comment' />
    <input type='hidden' name='order' value='\$Chrono' />
    <input type='hidden' name='csum' value='$[Comment added]' />
    <table width='90%'><tr>
    <th class='prompt' align='right' valign='top'>$[Comment]&nbsp;</th>
    <td><textarea class='inputtext commenttext' name='text' rows=6 cols=40></textarea>
    </td></tr><tr><th class='prompt' align='right' valign='top'>$[Author]&nbsp;</th>
    <td><input class='inputbox commentauthorbox' type='text' name='author' value='\$Author' size='32' />
    <input class='inputbutton commentbutton' type='submit' name='post' value=' $[Post] ' />
    <input class='inputbutton commentbutton' type='reset' value='$[Reset]' /></td></tr></table><br /></form></div>");

# date and time formats
SDV($JournalDateFmt,'%d %B %Y');
SDV($JournalTimeFmt,'%H:%M');

# journal and diary patterns as part of page name
SDV($JournalPattern,'/Journal$/');
SDV($DiaryPattern,'/Diary$/');


if ($action == 'comment') 
    SDV($HandleActions['comment'],'HandleCommentPost');
else if ($action=='print' || $action=='publish') 
    Markup('cbox','<block','/\(:commentbox(chrono)?:\)/','');
else {  
    Markup('cbox','<block','/\(:commentbox(chrono)?:\)/e',
        "'<:block>'.str_replace('\$Chrono','$1',
        FmtPageName(\$GLOBALS['CommentBoxFmt'],\$pagename))");
        
        
    Markup('dbox','<block','/\(:diarybox:\)/e',
        "'<:block>'.FmtPageName(\$GLOBALS['DiaryBoxFmt'],\$pagename)");
    if (preg_match($JournalPattern,$pagename) ||
        preg_match($DiaryPattern,$pagename)) {
            $GroupHeaderFmt .= '(:if auth edit:)(:diarybox:)(:if:)(:nl:)';
            if (!PageExists($pagename)) $DefaultPageTextFmt = '';
    }
}

function HandleCommentPost($pagename) {
  global $_GET,$_POST,$JournalPattern,$DiaryPattern,$Author;
  global $AuthFunction, $oAuthFunction;
  if (!@$_POST['post'] || @$_POST['text']=='') Redirect($pagename);
  if (@$_POST['author']=='') $Author = 'anon';
  if (isset($_GET['message'])) { $message = $_GET['message']; echo $message; }
  SDV($AuthFunction,'PmWikiAuth');
  $oAuthFunction = $AuthFunction;
  $AuthFunction = 'BypassAuth'; 
  $page = RetrieveAuthPage($pagename, "read");
  if(get_magic_quotes_gpc()==1) $page['text'] = addslashes($page['text']);
  $HandleCommentFunction = (preg_match($JournalPattern,$pagename)) ? 'Journal' : 
    ((preg_match($DiaryPattern,$pagename)) ? 'Diary'   : 'Message');
  $HandleCommentFunction = 'Handle' . $HandleCommentFunction . 'Post';
  $HandleCommentFunction($pagename, $page['text']);
  HandleEdit($pagename);
  exit;
}

function BypassAuth($pagename,$level,$authprompt=true) {
    global $AuthFunction,$oAuthFunction;
    if ($level=='edit') $AuthFunction = $oAuthFunction;
    return $oAuthFunction($pagename,"read",$authprompt);
}

function FormatDateHeading($txt,$datefmt,$fmt) {
  return str_replace($txt,strftime($datefmt,time()),$fmt);
}

## Journal entry
function HandleJournalPost($pagename,$pagetext) {
   global $_POST,$JournalDateFmt,$JournalTimeFmt,$JPItemStartFmt,$JPItemEndFmt,$JPDateFmt,$JPTimeFmt,
            $Author;
   SDV($JPDateFmt,'>>journaldate<<(:nl:)!!!!$Date');
   SDV($JPTimeFmt,"\n>>journalhead<<\n!!!!!&ndash; \$Time &ndash;\n");
   SDV($JPItemStartFmt,">>journalitem<<\n");
   SDV($JPItemEndFmt,"");
   $date = FormatDateHeading('$Date',$JournalDateFmt,$JPDateFmt);
   $time = $date . FormatDateHeading('$Time',$JournalTimeFmt,$JPTimeFmt);
   $entry = $time.$JPItemStartFmt.$_POST['text'].$JPItemEndFmt;
   $_POST['text'] = (strstr($pagetext, $date)) ?
        str_replace($date, $entry, $pagetext) :
        "$entry\n>><<\n\n" . $pagetext;
}

## Diary entry
function HandleDiaryPost($pagename,$pagetext) {
   global $_POST,$JournalDateFmt,$DPDateFmt,$DPItemStartFmt,$DPItemEndFmt,$DPItemFmt,$Author;
   SDV($DPDateFmt,">>diaryhead<<\n!!!!\$Date "); 
   SDV($DPItemStartFmt,"\n>>diaryitem<<\n");
   SDV($DPItemEndFmt,"");
   $date = FormatDateHeading('$Date',$JournalDateFmt,$DPDateFmt);
   $entry = $date.$DPItemStartFmt.$_POST['text'].$DPItemEndFmt;
   $_POST['text'] = (strstr($pagetext, $date)) ?
        str_replace($date, $entry, $pagetext) :
        "$entry\n>><<\n\n" . $pagetext;
}

##  Comment entry
function HandleMessagePost($pagename,$pagetext) {
   global $_POST,$JournalDateFmt,$JournalTimeFmt,$MPDateFmt,$MPTimeFmt,$MPAuthorLink,
        $MPItemFmt,$MPItemStartFmt,$MPItemEndFmt,$MPDateTimeFmt,$MultipleItemsPerDay,$Author,
        $EnablePostAuthorRequired, $CommentboxMessageFmt,$PageUrl;
    $id = StringCount($pagename,">>messagehead<<")+1;
# code to automatically insert page breaks using break_page.php script
#   SDV($PostsPerPage,20);
#   $r = fmod($id-1,$PostsPerPage);
#   if($r==0)  $MPItemEndFmt = "\n>><<\n____";
#   else $MPItemEndFmt = "\n>><<";
   SDV($MPDateFmt,'>>messagedate<<(:nl:)!!!!$Date');
   SDV($MPTimeFmt,"(:nl:)>>messagehead<<\n!!!!!\$Author  &mdash; [-at \$Time-] \n");
   SDV($MPItemStartFmt,">>messageitem<<\n");
   SDV($MPItemEndFmt,"\n>><<");
   SDV($MPDateTimeFmt,"(:nl:)>>messagehead<<\n!!!!!\$Author  &mdash;  [-\$Date, \$Time-] \n");
   SDV($MultipleItemsPerDay,0); # set to 1 to have date above for multiple entries per day
   SDV($MPAuthorLink, 1); # set to 0 to disable author name as link
   $name = @$_POST['author'];
   if (@$_POST['author']=='') $_POST['author'] = 'anon';
  # disable anonymous posts, but this looses also any message content:
  # if($EnablePostAuthorRequired == 1 && $name=='') Redirect($pagename); 
   if($name=='') $name = 'anonymous';
   else $name = ($MPAuthorLink==1) ? '[[~' . $name . ']]' : $name;
   if ($MultipleItemsPerDay) {
        $date = FormatDateHeading('$Date',$JournalDateFmt,$MPDateFmt);
        $entry = '[[#comment'.$id.']]';
        $entry .= str_replace('$Author',$name,
            FormatDateHeading('$Time',$JournalTimeFmt,$MPTimeFmt));
   } else {
        $date = '';
        $entry = '[[#comment'.$id.']]';
        $entry .= FormatDateHeading('$Date',$JournalDateFmt,
            str_replace('$Author',$name,
            FormatDateHeading('$Time',$JournalTimeFmt,$MPDateTimeFmt)));
   }
   $entry.= $MPItemStartFmt.$_POST['text'].$MPItemEndFmt;
   $order= @$_POST['order'];
   if ($order=='') {
       if (strstr($pagetext,'(:commentbox:)(:if:)')) {
         $pos = strpos($pagetext,'(:commentbox:)(:if:)');
         $len = strlen('(:commentbox:)(:if:)');
         $before = substr($pagetext,0,$pos+$len)."\n";
         $after  = substr($pagetext,$pos+$len);
      }      
       else if (strstr($pagetext,'(:commentbox:)')) {
         $pos = strpos($pagetext,'(:commentbox:)');
         $len = strlen('(:commentbox:)');
         $before = substr($pagetext,0,$pos+$len)."\n";
         $after  = substr($pagetext,$pos+$len);
      }     
      else {
         $before = '';
         $after  = $pagetext;
      }
      $entry = "$date$entry";
      $after = ($MultipleItemsPerDay && strstr($after, $date)) ? 
            str_replace($date, $entry, $after) : "$entry$after";
   } else {
      $entry .= "\n";
      if (strstr($pagetext,'(:if auth edit:)(:commentboxchrono:)')) {
         $pos = strpos($pagetext,'(:if auth edit:)(:commentboxchrono:)');
         $before = substr($pagetext,0,$pos);
         $after  = substr($pagetext,$pos);
      }
      else if (strstr($pagetext,'(:commentboxchrono:)')) {
         $pos = strpos($pagetext,'(:commentboxchrono:)');
         $before = substr($pagetext,0,$pos);
         $after  = substr($pagetext,$pos);
      } 
      else {
         $before = $pagetext;
         if ($before[strlen($before)-1]!='\n') $before .="\n";
         $after  = '';
      }
      $before .= ($MultipleItemsPerDay && strstr($before, $date)) ? 
            substr($entry,1) : "$date$entry";
   }
   $_POST['text'] = "$before\n$after";
}

# add page variable {$PostCount}, 
# counts message items per page
$FmtPV['$PostCount'] = 'StringCount($pn,">>messagehead<<")';
function StringCount($pagename,$find) {
   $page = ReadPage($pagename, READPAGE_CURRENT);
   $n = substr_count($page['text'], $find);
   if ($n==0) return '';  #suppressing 0
   return $n;
}

