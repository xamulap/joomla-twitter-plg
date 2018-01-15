<?php
/**
 */

defined('_JEXEC') or die;

/**
 * Twitter plugin.
 *
 * @since  1.5
 */
class PlgContentTwitter extends JPlugin
{
	/**
	 * Displays the voting area if in an article
	 *
	 * @param   string   $context  The context of the content being passed to the plugin
	 * @param   object   &$row     The article object
	 * @param   object   &$params  The article params
	 * @param   integer  $page     The 'page' number
	 *
	 * @return  mixed  html string containing code for the votes if in com_content else boolean false
	 *
	 * @since   1.6
	 */
	public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{

		if(strstr($row->introtext,"{twitter}")) { 
			include(dirname(__FILE__).'/TweetPHP.php');
			include(dirname(__FILE__).'/conf.php');

			$TweetPHP = new TweetPHP(array(
			  'consumer_key'              => $consumer_key,
			  'consumer_secret'           => $consumer_secret,
			  'access_token'              => $access_token,
			  'access_token_secret'       => $access_token_secret,
			  'twitter_screen_name'       => $twitter_screen_name,
			  'ignore_retweets'	      => 'false',
			));

			$tweet_array = $TweetPHP->get_tweet_array();

			//var_dump($tweet_array);

			$html = '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
			foreach($tweet_array as $t) {
				$created = $t['created_at'];
				$text = $t['full_text'];
				$img = $t['extended_entities']['media'][0]['media_url_https'];
				$url = $t['entities']['urls'][0]['expanded_url'];
				$created = substr($created,0,strpos($created,"+"));
				if(!$url) $url="https://twitter.com/".$twitter_screen_name;
				$html .= "
				<div class='row' style='margin-bottom:20px'>
					<div class='col-md-9'>
						<a href='".$url."' style='color: #b2d234' target='_blank'>".$text."</a>	
					</div>	
					<div class='col-md-3'>
				";
				if($img) { 
					$html.="
						<img style='height: 100%; width: 100%; object-fit: contain' src='".$img."'/>
					";
				}
				$html.="	</div>
				</div>
				";
			}

			$r = str_replace("{twitter}",$html,$row->introtext);
			$row->text = $r;
		}

	}
}
