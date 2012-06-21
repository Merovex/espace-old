<?php

#Markup('BlogAuthorDate','_begin','/\(:blogauthordate\s*(.*?):\)/se',"BlogAuthorDate('$1');");
Markup('BlogCalendarDate','fulltext','/\(:blogcalendardate\s*(.*?):\)/se',"BlogCalendarDate('$1');");
Markup('BlogCalendarLongDate','fulltext','/\(:blogarticledate:\)/se',"BlogArticleDate();");
#$FmtPV['$BlogCalendarDate'] = 'strftime("%d %m %b %Y", $page["name"])';
$FmtPV['$BlogCalendarDate'] = 'BlogNameToDate($page["name"])';
$FmtPV['$BlogLongCalendarDate'] = 'BlogNameToLongDate($page["name"])';

function BlogAuthorDate() {
  global $pagename, $PageHeader;
  $date = BlogArticleDate();
  if (preg_match('/31 December 1969/',$date)) { return ''; }
  $page = RetrieveAuthPage($pagename, 'read', false);
  $author = $page['author'];
  $string = "<div class=blogbyline><span class='author'>by <a href='/Authors/$author'>".AsSpaced($author)."</a></span> $date<div>";
  $PageHeader = $string;
}
function BlogNameToLongDate($name) {
    preg_match("/\d{4}-\d\d-\d\d/", $name, $date);
    return strftime("%d %B %Y", strtotime($date[0]));
}
function BlogNameToDate($name) {
    preg_match("/\d{4}-\d\d-\d\d/", $name, $date);
    return strftime("%d %m %b %Y", strtotime($date[0]));
}
function BlogArticleDate() {
    global $pagename;
    return BlogNameToLongDate($pagename);
}
function BlogCalendarDate($date, $comments=0) {
    list($day, $mon, $month, $year) = explode(' ', $date);
    $cloud = '';
    if ($comments>0) { $cloud = "<div class='commentscloud'>$comments</div>"; }
    $output =<<<EOT
<div class="blogcalendar xicon-$mon">
  <div class="day"><span>$month</span> $day</div>
  <div class='year'>$year</div>$cloud
</div>
EOT;
    return Keep($output);
}
