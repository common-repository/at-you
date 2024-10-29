<?php
function at_you_register_settings() {
	add_option('at_you_comments_title', __('You have been mentioned on %BLOG_NAME% by %COMMENT_AUTHOR%!', AT_YOU_TEXT_DOMAIN));
	add_option('at_you_comments_content', __('Hello, <b>%BEEN_AT_AUTHOR%</b>!

You have been mentioned by <b>%COMMENT_AUTHOR%</b> in <i>%POST_NAME%</i> of our website: 

<blockquote>%COMMENT_CONTENT%</blockquote>

You can <a href="%COMMENT_LINK%" target="_blank">click here</a> to see full comments.
Welcome to <i><a href="%HOME_URL%" target="_blank">%BLOG_NAME%</a></i> again!', AT_YOU_TEXT_DOMAIN));
	add_option('at_you_post_title', __('You have been mentioned in %POST_TITLE%!', AT_YOU_TEXT_DOMAIN));
	add_option('at_you_post_content', __('Hello, <b>%BEEN_AT_AUTHOR%</b>!

You have been mentioned in <i>%POST_TITLE%</i> of <b>%BLOG_NAME%</b>: 

<blockquote>%POST_EXCERPT%</blockquote>

You can <a href="%POST_LINK%" target="_blank">click here</a> to see full post.
Welcome to <i><a href="%HOME_URL%" target="_blank">%BLOG_NAME%</a></i> again!', AT_YOU_TEXT_DOMAIN));

	register_setting('at_you_options', 'at_you_comments_title');
	register_setting('at_you_options', 'at_you_comments_content');
	register_setting('at_you_options', 'at_you_post_title');
	register_setting('at_you_options', 'at_you_post_content');
}
add_action('admin_init', 'at_you_register_settings');

function at_you_register_options_page() {
	add_options_page(__('@You Options Page', AT_YOU_TEXT_DOMAIN), __('@You', AT_YOU_TEXT_DOMAIN), 'manage_options', AT_YOU_TEXT_DOMAIN.'-options', 'at_you_options_page');
}
add_action('admin_menu', 'at_you_register_options_page');

function at_you_get_tags($place, $type) {
	if($place == "title"){
		if($type == "post"){
			?><p><?php printf(__("You may use %s for blog title and %s for title of post.", AT_YOU_TEXT_DOMAIN), "<code>%BLOG_NAME%</code>", "<code>%POST_TITLE%</code>"); ?></p><?php
		}else{
			?><p><?php printf(__("You may use %s for blog title and %s for author of comments.", AT_YOU_TEXT_DOMAIN), "<code>%BLOG_NAME%</code>", "<code>%COMMENT_AUTHOR%</code>"); ?></p><?php
		}
	}else{
		?>
		<p><?php _e('Tags you may use: ', AT_YOU_TEXT_DOMAIN); ?></p>
		<p><?php printf(__("%s: Blog name.", AT_YOU_TEXT_DOMAIN), "<code>%BLOG_NAME%</code>"); ?></p>
		<p><?php printf(__("%s: Home URL.", AT_YOU_TEXT_DOMAIN), "<code>%HOME_URL%</code>"); ?></p>
		<p><?php printf(__("%s: The one who have been @'s name.", AT_YOU_TEXT_DOMAIN), "<code>%BEEN_AT_AUTHOR%</code>"); ?></p>
		<?php
		if($type == "post"){
			?>
			<p><?php printf(__("%s: The post's title.", AT_YOU_TEXT_DOMAIN), "<code>%POST_TITLE%</code>"); ?></p>
			<p><?php printf(__("%s: The post's content. (Less then 380 characters)", AT_YOU_TEXT_DOMAIN), "<code>%POST_EXCERPT%</code>"); ?></p>
			<p><?php printf(__("%s: The post's link.", AT_YOU_TEXT_DOMAIN), "<code>%POST_LINK%</code>"); ?></p>
			<?php
		}else{
			?>
			<p><?php printf(__("%s: The post name of the comment.", AT_YOU_TEXT_DOMAIN), "<code>%POST_NAME%</code>"); ?></p>
			<p><?php printf(__("%s: The author of the comment.", AT_YOU_TEXT_DOMAIN), "<code>%COMMENT_AUTHOR%</code>"); ?></p>
			<p><?php printf(__("%s: The content of the comment.", AT_YOU_TEXT_DOMAIN), "<code>%COMMENT_CONTENT%</code>"); ?></p>
			<p><?php printf(__("%s: The links of the comment.", AT_YOU_TEXT_DOMAIN), "<code>%COMMENT_LINK%</code>"); ?></p>
			<?php
		}
	}
}

function at_you_options_page() {
?>
<div class="wrap">
	<h2><?php _e("@You Options Page", AT_YOU_TEXT_DOMAIN); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields('at_you_options'); ?>
			<table class="form-table">
				<tr valign="top"><td><h3><?php _e("Been @ in comments: ", AT_YOU_TEXT_DOMAIN); ?></h3></td></tr>
				<tr valign="top">
					<th scope="row"><label for="at_you_comments_title"><?php _e("Title of the E-mail: ", AT_YOU_TEXT_DOMAIN); ?></label></th>
					<td>
						<input type="text" name="at_you_comments_title" id="at_you_comments_title" value="<?php echo get_option('at_you_comments_title'); ?>" size="70" />
						<?php at_you_get_tags("title", "comments") ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="at_you_comments_content"><?php _e("Content of the E-mail: ", AT_YOU_TEXT_DOMAIN); ?></label></th>
					<td>
						<?php wp_editor(get_option("at_you_comments_content"), "at_you_comments_content", array('textarea_rows' => 8)); ?>
						<?php at_you_get_tags("content", "comments") ?>
					</td>
				</tr>
				<tr valign="top"><td><h3><?php _e("Been @ in post: ", AT_YOU_TEXT_DOMAIN); ?></h3></td></tr>
				<tr valign="top">
					<th scope="row"><label for="at_you_post_title"><?php _e("Title of the E-mail: ", AT_YOU_TEXT_DOMAIN); ?></label></th>
					<td>
						<input type="text" name="at_you_post_title" id="at_you_post_title" value="<?php echo get_option('at_you_post_title'); ?>" size="70" />
						<?php at_you_get_tags("title", "post") ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="at_you_post_content"><?php _e("Content of the E-mail: ", AT_YOU_TEXT_DOMAIN); ?></label></th>
					<td>
						<?php wp_editor(get_option("at_you_post_content"), "at_you_post_content", array('textarea_rows' => 8)); ?>
						<?php at_you_get_tags("content", "post") ?>
					</td>
				</tr>
			</table>
		<?php submit_button(); ?>
	</form>
</div>
<?php
}
?>