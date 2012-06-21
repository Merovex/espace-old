Blogsimple is an experimental blog bundle built on pagelists.
It has been developed for use with Triad skin, which is a three column skin.
It uses both SideBar and RightBar. 

INSTALLATION:

unzip into the cookbook/ directory which should create  directories 
cookbook/blogsimple/            
         files:blogsimple.php
               newpageboxplus.php
               commentboxstyled.php
          this README.txt file 
cookbook/blogsimple/wikilib.d/  pmwiki page store with blog configuration pages

add to config.php:
    include_once("$FarmD/cookbook/blogsimple/blogsimple.php");
    
if you have not installed it before:
add to config.php:
    include_once($FarmD/cookbook/blogsimple/newpageboxplus.php");

if you have not installed it before:
add to config.php:
    include_once($FarmD/cookbook/blogsimple/commentboxstyled.php");

You can only use commentboxstyled.php or commentbox.php, so if you have commentbox.php
installed uninstall it and install commentboxstyled.php instead.

USE:

open the wiki and go to Blog.Blog, which is the blog homepage.

Configuration pages are in group Blog.
Blog pages are in group BlogPages.
Blog comments are in group BlogComments.
Archive pages are in group BlogArchive.
Category pages are in group BlogCategories.