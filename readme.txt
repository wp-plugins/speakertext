=== SpeakerText ===
Contributors: SpeakerText
Donate link: http://speakertext.com
Tags: video, transcript, seo
Requires at least: 2.7
Tested up to: 3.0
Stable tag: trunk

The SpeakerText plugin automatically loads an interactive transcript beneath each of your videos, improving SEO. SpeakerText account required.

== Description ==

SpeakerText turns your videos into text so that they can be read, found and shared by everyone. The WordPress plugin auto-detects all the videos on your site and instantly loads an interactive transcript beneath each video. 

SpeakerText transcripts show up instantly on your site and are indexed by Google for SEO. 

SpeakerText makes it simple for visitors to share quotes from your videos on Twitter and Facebook. Whenever this happens, SpeakerText automatically includes a time-stamped short URL back to your site that starts the video at that exact quote.

Whenever a visitor copy & pastes a quote from your video transcript, SpeakerText turns that quote into a hyperlink back to your site and starts the video at the matching time cue. 

Once the SpeakerText plugin is installed, transcripts will appear instantly beneath each video that you've chosen to transcribe. You don't need to do anything else!

You must have a SpeakerText account and an API key to activate the SpeakerText plugin.

== Installation ==

1. Upload the `speakertext` folder to the `/wp-content/plugins/` folder on your site. Or install the plugin directly from your admin console.
1. Activate the plugin through the 'Plugins' menu in WordPress. 
1. Log in to your SpeakerText account and retrieve your public API key from http://speakertext.com/wordpress. 
1. Go to the "Settings" section in Wordpress and go to "SpeakerText." Paste the public API key into in the "Public Key" field and click "Save Changes."

== Frequently Asked Questions ==
Q: Do I have to pay for SpeakerText?
A: Yes, we're a business! 

Q: Does SpeakerText use speech-to-text software to transcribe my videos?
A: SpeakerText uses a combination of artificial and human intelligence to create high-quality video transcripts at low cost.

Q: Do I have to copy & paste the transcripts onto my site? 
A: No! SpeakerText loads them automatically in a `<div>` tag beneath each one of your videos.

Q: Why is there a space between my video and the transcript? 
A: WordPress automatically adds a `<p>` tag after each embed and then adds a `</p>` tag before the SpeakerText `<div>`, causing there to be a space. You can specify a margin correction in the SpeakerText settings page.

Q: Do SpeakerText transcripts affect my site SEO? 
A: Yes, SpeakerText transcripts load server-side and thus get indexed by Google for SEO!

== Upgrade Notice ==
None.

== Screenshots ==
None.

== Changelog ==

= 0.1.0 =
Initial Release. Supports YouTube.

= 1.0.0 =
Support for Blip.tv and Brightcove. New configuration parameter: remove space between video and transcript.

== Other Notes ==
To learn more, visit: http://speakertext.com/wordpress

For questions, email support@speakertext.com