<?php
class SpeakerText 
{ 
	// All of the Regular Expressions we use to match videos
	const YOUTUBE_RE = "/<object(.*?)<embed.*? src=[\"']http:\/\/(www.)?youtube.com\/v\/(.*?)([&\?].*?)[\"'](.*?)<\/object>/";
	
	const YOUTUBE_PLATFORM = 1;

	function SpeakerText() {
		return true;
	}
	
	function deactivate() {
		unregister_setting('speakertext_credentials', 'speakertext_public_key');
	}
	
	function filter_the_content($content) {
		return $this->filter_youtube($content);
	}
	
	function filter_youtube($content) {
		$matches = array();
		preg_match_all(self::YOUTUBE_RE, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		
		$global_offset = 0;
		foreach($matches as $match) {
			$video_id = $match[3][0];
			$offset = $global_offset + $match[0][1] + strlen($match[0][0]);

			// Add in SpeakerText text embed right after video
			$embed_code = $this->get_text_embed(self::YOUTUBE_PLATFORM, $video_id);
			$content = substr($content, 0, $offset) . $embed_code . substr($content, $offset);
			$global_offset += strlen($embed_code);
		}
		
		return $content;
	}
	
	function get_text_embed($platform_id, $video_id) {
		$public_key = get_option('speakertext_public_key');
		$transcript_id = $public_key . "-" . $platform_id . "-" . $video_id;
		$response = wp_remote_get("http://jb.speakertext.com/transcripts/" . $transcript_id . ".html");
		
		if( ! is_wp_error( $response ) ) {
			if( wp_remote_retrieve_response_code( $response ) == 200 ) {
				return wp_remote_retrieve_body( $response );
			}
		}
	}
	
	function add_speakerbar_scripts() {
		wp_enqueue_script('st_player', 'http://jb.speakertext.com/player/jquery.speakertext.js', array('jquery'), "1.0");
		echo "<script>var STapiKey = 'STEMBEDAPIKEY';</script>\n";
	}
	
	function add_speakerbar_styles() {
		wp_enqueue_style('st_player_style', 'http://jb.speakertext.com/player/speakertext.css');
	}
	
	/* BELOW IS TO MANAGE SPEAKERTEXT PLUGIN SETTINGS */
	
	function create_menu() {
		// create settings submenu
		add_options_page('SpeakerText Plugin Settings', 'SpeakerText', 'manage_options', 'speakertext_options', array($this, 'speakertext_settings_page'));
	}
	
	function register_settings() {
		register_setting("speakertext_credentials", "speakertext_public_key");
	}
	
	function speakertext_settings_page() { ?>
	<div class="wrap">
	<h2>SpeakerText Settings</h2>

	<form method="post" action="options.php">
	    <?php settings_fields( 'speakertext_credentials' ); ?>
			<h3>Credentials</h3>
	    <table class="form-table">
        <tr valign="top">
        	<th scope="row">Public Key</th>
        	<td><input type="text" name="speakertext_public_key" style="width: 320px;" value="<?php echo get_option('speakertext_public_key'); ?>" /></td>
        </tr>
	    </table>
	
			<p>What is my <a href="http://speakertext.com/account">public key?</a></p>

	    <p class="submit">
	    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>

	</form>
	</div><?php
	}
}
?>