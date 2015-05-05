=== JM Twit This Comment ===
Contributors: jmlapam
Tags: twitter, comments, reply, comment form
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jmlapam%40gmail%2ecom&item_name=JM%20Twit%20This%20Comment&no_shipping=0&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8
Requires at least: 3.0
Tested up to: 3.6
License: GPLv2 or later
Stable tag: trunk
License URI: http://www.gnu.org/licenses/gpl-2.0.html

No big deal. It's just that sometimes you read amazing comments so tweet it ^^. 

== Description ==

Once activated the plugin adds another link beside your reply link for each comment and a field in comment form for your commentators if they want to display their Twitter account.

<a href="http://twitter.com/tweetpressfr">Follow me on Twitter</a> - <a href="http://tweetpress.fr/en/plugin/jm-twit-this-comment">my blog</a>

––––
En Français 
–––––––––––––––––––––––––––––––––––

Une fois activé le plugin s'occupe d'ajouter un lien pour tweeter les commentaires faits sur votre blog ! Il ajoute aussi un champ dans le formulaire de commentaire pour que vos commentateurs puissent indiquer leur compte Twitter s'ils le désirent.

<a href="http://twitter.com/tweetpressfr">Me suivre sur Twitter</a> - <a href="http://tweetpress.fr/plugin/jm-twit-this-comment">Le blog</a>


== Installation ==

1. Upload plugin files to the /wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. It's possible to moderate twitter accounts given by commentators through comment edit screen

––––
En Français 
–––––––––––––––––––––––––––––––––––

1. Chargez les fichiers de l'archive dans le dossier /wp-content/plugins/ 
2. Activez le plugin dans le menu extensions de WordPress
3. Vous pouvez modérer le champ compte Twitter que vos commentateurs remplissent grâce à l'édit des commentaires

== Frequently asked questions ==

Not Yet.

== Screenshots ==

1. edit screen
2. front end links
3. demo of tweet

== Changelog ==

= 2.0 =
* 05 May 2015
* code was too old, refactoring

= 1.3.4 =
* 7 jun 2013
* removed tinyurl function and replace it with comment permalink according to some user's feedback
* update with new limit for tweets with URL >> 117 characters

= 1.3.3 =
* 27 Apr 2013
* add support in profiles for registered users

= 1.3.2 =
* 27 Apr 2013
* add rel noffolow to Twitter links in comment
* remove unecessary spaces (POSIX) in comment excerpt 

= 1.3.1 =
* 26 Apr 2013
* fix wrong count for comment excerpt

= 1.3.0 =
* 25 Apr 2013
* minor correction </p> line 83

= 1.2.0 =
* 24 Apr 2013
* Bugfix tinyurl

= 1.1.0 =
* 24 Apr 2013
* Correction for uninstall. Thanks juliobox for reporting it. Now the plugin uses $wpdb instead of delete_comment_meta which allow us to avoid 1 request per comment. Actually we want only 1 request for all.

= 1.0.0 =
* 24 Apr 2013
* Initial release

== Upgrade notice ==
Nothing
= 1.0 =


