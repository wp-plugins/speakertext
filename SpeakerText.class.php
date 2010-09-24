<?php
class SpeakerText 
{ 
	// All of the Regular Expressions we use to match videos
	// /s makes . match newlines
	const YOUTUBE_RE = "/<object(.*?)<embed.*? src=[\"']http:\/\/(www.)?youtube.com\/v\/(.*?)([&\?].*?)[\"'](.*?)<\/object>/s";
	const BRIGHTCOVE_RE = "/brightcove(.*?)@videoPlayer([\"'] *value)?=[\"']?(\d+)(.*?)<\/object>/is"; 
	const BLIP_RE = "/<embed(.*?)src=[\"']http:\/\/blip.tv\/play\/([a-zA-Z0-9]*)(.*?)[\"'](.*?)<\/embed>(.*?<\/object>)?/s";
	const JW_RE = "/<object.*?file=(.*?)[&\"].*?<\/object>/s";
	const OOYALA_RE = "/<script.*?src=\"https?:\/\/player.ooyala.com\/player.js\?(.*?)embedCode=(.*?)[\"&].*?<\/noscript>/s";
	
	const YOUTUBE_PLATFORM = 1;
	const BRIGHTCOVE_PLATFORM = 3;
	const BLIP_PLATFORM = 4;
	const OOYALA_PLATFORM = 5;
	const SELF_PLATFORM = 7;
	
	const OOYALA_JS_INSERT = "callback=st_ooyala_callback&";
	
	function SpeakerText() {
		return true;
	}
	
	function deactivate() {
		unregister_setting('speakertext_options', 'speakertext_public_key');
		unregister_setting('speakertext_options', 'speakertext_player_margin');
	}
	
	
	function filter_the_content($content) {
		$content = $this->filter_videos($content, self::YOUTUBE_RE, self::YOUTUBE_PLATFORM, 3);
		$content = $this->filter_videos($content, self::BRIGHTCOVE_RE, self::BRIGHTCOVE_PLATFORM, 3);
		$content = $this->filter_videos($content, self::BLIP_RE, self::BLIP_PLATFORM, 2);
		$content = $this->filter_videos($content, self::JW_RE, self::SELF_PLATFORM, 1);
		$content = $this->filter_videos($content, self::OOYALA_RE, self::OOYALA_PLATFORM, 2);
		return $content;
	}
	
	function filter_videos($content, $re, $platform, $match_num) {
		$matches = array();
		preg_match_all($re, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		
		$global_offset = 0;
		foreach($matches as $match) {
			$video_id = $match[$match_num][0];
			
			if( $platform == self::SELF_PLATFORM ) {
				$video_id = sha1(basename($video_id));
			}
			
			if( $platform == self::OOYALA_PLATFORM && strpos($match[0][0], "callback") === false ) {
				// Add in javascript callback after all url params
				$offset = $global_offset + $match[1][1];
				$content = substr($content, 0, $offset) . self::OOYALA_JS_INSERT . substr($content, $offset);
				$global_offset += strlen(self::OOYALA_JS_INSERT);
			}
			
			$offset = $global_offset + $match[0][1] + strlen($match[0][0]);

			// Add in SpeakerText text embed right after video
			$embed_code = $this->get_text_embed($platform, $video_id);
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
		echo "<style>div.STplayer { margin-top: -" . get_option('speakertext_player_margin') . "; }</style>\n";
	}
	
	/* BELOW IS TO MANAGE SPEAKERTEXT PLUGIN SETTINGS */
	
	function create_menu() {
		// create settings submenu
		add_options_page('SpeakerText Plugin Settings', 'SpeakerText', 'manage_options', 'speakertext', array($this, 'speakertext_settings_page'));
	}
	
	function register_settings() {
		register_setting("speakertext_options", "speakertext_public_key");
		register_setting("speakertext_options", "speakertext_player_margin");
		
		add_settings_section("speakertext_credentials", "Credentials", array($this, 'credentials_text'), 'speakertext');
		add_settings_field('public_key', 'Public Key', array($this, 'public_key_text'), 'speakertext', 'speakertext_credentials');
		
		add_settings_section("speakertext_options", "Options", array($this, 'options_text'), 'speakertext');
		add_settings_field('player_margin', 'Player Margin Correction', array($this, 'player_margin_text'), 'speakertext', 'speakertext_options');
	}
	
	function credentials_text() {
		echo '<p>Your public API key can be found on your SpeakerText <a href="http://speakertext.com/account" target="_blank">account page</a>.</p>';
	}
	
	function options_text() {
		echo '<p>Wordpress\' default formatting causes there to be a space between the SpeakerText interactive transcript player and the video.</p>';
					
		echo '<p>To fix this, we can move the transcript up using CSS. The value specified below should be approximately equal to
					the bottom margin of your paragraph tags.  Common values are 1em, 1.5em, or 2em.  You can also specify the value in pixels, such as 24px. 
					You may have to play around with this number to get the correct value for your theme.</p>';
	}
	
	function public_key_text() {
		$spk = get_option('speakertext_public_key');
		echo "<input id='public_key' name='speakertext_public_key' size='55' type='text' value='{$spk}' />";
	}
	
	function player_margin_text() {
		$pm = get_option('speakertext_player_margin');
		echo "<input id='player_margin' name='speakertext_player_margin' size='5' type='text' value='{$pm}' />";
	}
	
	function speakertext_settings_page() { ?>
	<div class="wrap">
	<h2>SpeakerText Settings</h2>

	<form method="post" action="options.php">
	    <?php settings_fields( 'speakertext_options' ); ?>
			<?php do_settings_sections('speakertext'); ?>
			
			<p><input name="Submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>

	</form>
	</div><?php
	}
	

}
?>