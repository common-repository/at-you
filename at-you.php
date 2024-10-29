<?php
/*

**************************************************************************

Plugin Name:  @You
Plugin URI:   http://www.arefly.com/at-you/
Description:  Allow visitors @ others in the comments.
Version:      1.1.7
Author:       Arefly
Author URI:   http://www.arefly.com/
Text Domain:  at-you
Domain Path:  /lang/

**************************************************************************

	Copyright 2014  Arefly  (email : eflyjason@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

**************************************************************************/

define("AT_YOU_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("AT_YOU_FULL_DIR", plugin_dir_path( __FILE__ ));
define("AT_YOU_TEXT_DOMAIN", "at-you");

/* Plugin Localize */
function at_you_load_plugin_textdomain() {
	load_plugin_textdomain(AT_YOU_TEXT_DOMAIN, false, dirname(plugin_basename( __FILE__ )).'/lang/');
}
add_action('plugins_loaded', 'at_you_load_plugin_textdomain');

include_once AT_YOU_FULL_DIR."options.php";

/* Add Links to Plugins Management Page */
function at_you_action_links($links){
	$links[] = '<a href="'.get_admin_url(null, 'options-general.php?page='.AT_YOU_TEXT_DOMAIN.'-options').'">'.__("Settings", AT_YOU_TEXT_DOMAIN).'</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'at_you_action_links');

function at_you_register_session() {
	if(!session_id()){
		session_start();
	}
}
add_action('init', 'at_you_register_session');

function at_you_get_comments_author($count = 60){
	if(isset($_SESSION['at_you_comments_author'])){
		$member = $_SESSION['at_you_comments_author'];
	}else{
		$comments = get_comments("status=approve&number=" . $count );
		$member = array();
		foreach($comments as $comment){
			if( ($count > 0) && (count($member) >= $count) ){
				break;
			}
			$email = trim($comment->comment_author_email);
			$author = trim($comment->comment_author);		
			if(is_numeric($author)){
				$author = $author." ";
			}
			if( (!array_key_exists($author, $member)) && ($email != "") ){
				$member[$author] = $email;
			}
		}
		$_SESSION['at_you_comments_author'] = $member;
	}
	return $member;
}

function at_you_enqueue_script() {
	if(is_singular()){
		$comments_author = array_values(array_flip(at_you_get_comments_author()));
		wp_enqueue_style('at-you', AT_YOU_PLUGIN_URL.'at-you.min.css', false, false, 'screen');
		wp_enqueue_script('jquery.atyou', AT_YOU_PLUGIN_URL.'jquery.atwho.min.js', array('jquery'));
		wp_enqueue_script('at-you', AT_YOU_PLUGIN_URL.'at-you.min.js', array('jquery'));
		wp_localize_script('at-you', 'atyou', array("comments_author" => $comments_author));
	}
}
add_action('wp_enqueue_scripts', 'at_you_enqueue_script');

function at_you_comment_notify($comment_id){
	$comment = get_comment($comment_id);
	$spam_confirmed = $comment->comment_approved;	
	if($spam_confirmed != "spam"){
		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";		

		$text = $comment->comment_content;
		$names = array();

		if(strpos($text, "@") !== false){
			$text = str_replace("@", " @", $text);
			$array = preg_split("/[\s]+/", trim($text) );

			foreach($array as $key){
				if(strpos($key, "@") !== false){
					array_push($names, trim(str_replace("@", "", $key)));
				}
			}
			$names = array_unique($names);

			$subject = get_option("at_you_comments_title");
			$message = nl2br(get_option("at_you_comments_content"));

			$atyou = at_you_get_comments_author();

			foreach($names as $key){
				$email = $atyou[$key];
				$message_tmp = $message;
				if($email){
					$message_tmp = str_replace("%BLOG_NAME%", get_option("blogname"), $message_tmp);
					$message_tmp = str_replace("%HOME_URL%", get_option('home'), $message_tmp);

					$message_tmp = str_replace("%BEEN_AT_AUTHOR%", $key, $message_tmp);
					$message_tmp = str_replace("%POST_NAME%", get_the_title($comment->comment_post_ID), $message_tmp);
					$message_tmp = str_replace("%COMMENT_AUTHOR%", get_the_title($comment->comment_author), $message_tmp);
					$message_tmp = str_replace("%COMMENT_CONTENT%", trim($text), $message_tmp);
					$message_tmp = str_replace("%COMMENT_LINK%", htmlspecialchars(get_comment_link($comment_id)), $message_tmp);

					wp_mail($email, $subject, $message_tmp, $headers);
				}
			}

			$message = null;
			$message_tmp = null;
			$names = null;
			$atyou = null;
			$text = null;
			$comment = null;
			$array = null;
		}
	}
}
add_action('comment_post', 'at_you_comment_notify');

function at_you_post_notify($comment_id){
	$comment = get_comment($comment_id);
	$spam_confirmed = $comment->comment_approved;	
	if($spam_confirmed != "spam"){
		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";		

		$text = $post->post_content;
		$names = array();

		if(strpos($text, "@") !== false){
			$text = str_replace("@", " @", $text);
			$array = preg_split("/[\s]+/", trim($text) );

			foreach($array as $key){
				if(strpos($key, "@") !== false){
					array_push($names, trim(str_replace("@", "", $key)));
				}
			}
			$names = array_unique($names);

			$subject = get_option("at_you_post_title");
			$message = nl2br(get_option("at_you_post_content"));

			$atyou = at_you_get_comments_author();

			foreach($names as $key){
				$email = $atyou[$key];
				$message_tmp = $message;
				if($email){
					$message_tmp = str_replace("%BLOG_NAME%", get_option("blogname"), $message_tmp);
					$message_tmp = str_replace("%HOME_URL%", get_option('home'), $message_tmp);

					$message_tmp = str_replace("%BEEN_AT_AUTHOR%", $key, $message_tmp);
					$message_tmp = str_replace("%POST_TITLE%", $post->post_title, $message_tmp);
					$message_tmp = str_replace("%POST_EXCERPT%", mb_strimwidth(strip_tags($text) , 0, 380, "..."), $message_tmp);
					$message_tmp = str_replace("%POST_LINK%", get_permalink($post_ID), $message_tmp);

					wp_mail($email, $subject, $message_tmp, $headers);
				}
			}

			$message = null;
			$message_tmp = null;
			$names = null;
			$atyou = null;
			$text = null;
			$comment = null;
			$array = null;
		}
	}
}
add_action("publish_post", "at_you_post_notify", 10, 2);
