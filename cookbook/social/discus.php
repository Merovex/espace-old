<?php if (!defined('PmWiki')) exit();

Markup('discus', 'fulltext', '/\(:discus\s*(.*?):\)/e', "Discus('$1')");
Markup('livefyre', 'fulltext', '/\(:livefyre\s*(.*?):\)/e', "LiveFyre('$1')");
SDV($DiscusShortName, 'example');
Markup('discusCommentCount', 'fulltext', '/\(:discuscommentcount:\)/e', "DiscusCommentCount()");
function liveFyre($args='') {
  global $pagename;
  return Keep("<h2>Discussion</h2><div id='lf_comment_stream' livefyre_title='$pagename'></div>");
}
function discusCommentCount() {
    global $DiscusShortName;
    global $HTMLFooterFmt;
    $HTMLFooterFmt['discus'] =<<<FOOTER
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = '$DiscusShortName'; // required: replace example with your forum shortname

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function () {
        var s = document.createElement('script'); s.async = true;
        s.type = 'text/javascript';
        s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
</script>
FOOTER;
    return '';
}

function discus($name='') {
    global $DiscusShortName;
    global $pagename, $ScriptUrl;
    if ($name == '') { $name = $pagename; }
$output =<<<EOT
<div id="disqus_thread"></div>
<script type="text/javascript">
    var disqus_shortname = '$DiscusShortName'; // required: replace example with your forum shortname
    var disqus_identifier = '$pagename';
    var disqus_url = '$ScriptUrl/$name';

    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>
EOT;
return Keep($output);
}
