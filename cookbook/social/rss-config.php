<?php if (!defined('PmWiki')) exit();

$FeedFmt['rss']['feed'] = array(
  '_header' => 'Content-type: text/xml; charset="$Charset"',
  '_start' => '<?xml version="1.0" encoding="$Charset"?'.'>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>'."\n",
  '_end' => "</channel>\n</rss>\n",
  'title' => '$WikiTitle :: $Group - $Title',
  'link' => '$PageUrl?action=rss',
  'description' => '$Group.$Title',
  'lastBuildDate' => '$FeedRSSTime');
$FeedFmt['rss']['item'] = array(
  '_start' => "<item>\n",
  '_end' => "</item>\n",
  'title' => '$Group / $Title',
  'link' => '$PageUrl',
  'description' => '$ChangeSummary',
  'dc:contributor' => '$LastModifiedBy',
  'dc:date' => '$ItemISOTime',
  'enclosure' => 'RSSEnclosure');

if ($action == 'rss')  include_once("$FarmD/scripts/feeds.php");  # RSS 2.0
if ($action == 'atom') include_once("$FarmD/scripts/feeds.php");  # Atom 1.0
if ($action == 'dc')   include_once("$FarmD/scripts/feeds.php");  # Dublin Core
if ($action == 'rdf')  include_once("$FarmD/scripts/feeds.php");  # RSS 1.0

# Allows Firefox Live Bookmarks
$HTMLHeaderFmt['rss'] =
  "<link rel='alternate' title='\$WikiTitle RSS' href='\$ScriptUrl/Site/AllRecentChanges?action=rss' type='text/xml' />";
