=== Double Opt-in for Comments===
Contributors: rloes
Donate link: https://github.com/rloes/
Tags: comments, spam, double-opt-in
Requires at least: 6.4
Tested up to: 6.4
Stable tag: 6.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

The Double Opt-In Comments plugin enhances the integrity and authenticity of comments on your WordPress site. This plugin requires non-logged-in users to verify their email address before their comments are published. When a new comment is posted using an email address that has not previously been used for commenting, the comment is held for moderation. An activation link is sent to the provided email address. The comment is only published after the user clicks this link, confirming the authenticity of their email. This process significantly reduces spam and ensures that comments are genuine and from real users.
Features:

- *Double Opt-In for Comments:* Non-logged-in users must verify their email address before their comment is published.
- *Customizable Texts:* All notification texts sent to users are fully customizable to match the tone and style of your website.
- *Email Management:* Option to delete verified email addresses from the system for privacy concerns.
- *Spam Reduction:* Significantly reduces spam and ensures authentic user engagement.

== Installation ==

1. Upload `ws-comments-doi.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Discussion and scroll down to manage the plugins behaviour

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* introduced plugin

== Upgrade Notice ==

= 1.0 =
* protect your posts from spam and only receive authentic comments