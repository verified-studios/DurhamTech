<?php

namespace Drupal\durhamtech_social\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'social' block.
 *
 * @Block(
 *   id = "durhamtech_social_block",
 *   admin_label = @Translation("Durham Tech Social Media Slider"),
 *   category = @Translation("Durham Tech")
 * )
 */
class SocialBlock extends BlockBase
{
    /**
     * {@inheritdoc}
     */

    public function build()
    {
        $output = '';

        $instagram  = Instagram::getPosts(1);
        $twitter    = Twitter::getPosts(1);
        $facebook    =
          ['
              <a href="https://www.facebook.com/durhamtech" target="_blank" class="social-slide social-instagram social-facebook">
                <div class="img"><img src="https://scontent-atl3-1.xx.fbcdn.net/v/t1.0-0/s480x480/37570541_1596727217103906_914967182398455808_n.jpg?_nc_cat=0&oh=83be6adbf78ad83da42377f9a915c931&oe=5C0D273E" alt="Grab your mat and join Durham Tech for Beginning Yoga! This new course begins September 4 at our RTP campus. It emphasizes the principles of yoga, stress management, breathing techniques, and postures. Register here: http://ow.ly/hpuy30l0oCR"/></div>
                <p>Grab your mat and join Durham Tech for Beginning Yoga! This new course begins September 4 at our RTP campus. It emphasizes the principles of yoga, stress management, breathing techniques, and postures. Register here: http://ow.ly/hpuy30l0oCR</p>
                <div class="social-icon">
                    <span class="fa fa-facebook"></span>
                </div>
              </a>'];

        $posts      = array_merge($instagram, $twitter,$facebook);

        shuffle($posts);

        foreach ($posts as $k => $post ) {
            $output .= $post;
        }

        $slick = '<div class="slick slick-social">' . $output . '</div>';

        return array(
            '#type' => 'markup',
            '#attached' => array(
                'library' => array(
                    'durhamtech_social/slick'
                ),
            ),
            '#markup' => $slick,
        );
    }
}

class Instagram {
    public static function getPosts($num) {
        $endpoint = 'https://api.instagram.com/v1/users/self/media/recent/?access_token=1918069938.51b3acd.74343440f35d49fe9d2500404b60a681';
        $json = file_get_contents($endpoint);
        $obj = json_decode($json);
        $count = 0;
        $items = array();
        while ($num > $count) {
            $thumb = $obj->data[$count]->images->standard_resolution->url;
            $caption = $obj->data[$count]->caption->text;
            $link = $obj->data[$count]->link;
            $items[$count] = '
              <a href="' . $link .'" target="_blank" class="social-slide social-instagram">
                <div class="img"><img src="' . $thumb . '" alt="' . $caption .'"/></div>
                <p>' . $caption .'</p>
                <div class="social-icon">
                    <span class="fa fa-instagram"></span>
                </div>
              </a>';
            $count++;
        }
        return $items;
    }
}

class Twitter {

    public static function buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    public static function buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }

    public static function getPosts($num) {

        $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

        $oauth_access_token = "37061935-WVrr4evWBGtaEtwjQe8qB35qL36H0dJAUmKQNJmsA";
        $oauth_access_token_secret = "HbuRLIq2bGFiBhrUnIqEGmGeghcSSxB68w6MSHFLESeEa";
        $consumer_key = "DMJRJjROI4IbVR2JK2PMqlBo3";
        $consumer_secret = "sLnlYxH7gvcgkT7fBjb0WtQsK5Dx74J3cZpEcUeiCOSrSptY76";

        $oauth = array( 'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $oauth_access_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0');

        $base_info = Twitter::buildBaseString($url, 'GET', $oauth);
        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        // Make requests
        $header = array(Twitter::buildAuthorizationHeader($oauth), 'Expect:');
        $options = [
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        $obj = json_decode($json);


        $count = 0;
        $items = array();
        while ($num > $count) {
            $caption = $obj[$count]->text;
            if ($obj[$count]->id) {
              $id = $obj[$count]->id;
              $items[$count] = '<a href="https://twitter.com/durhamtech/status/' . $id .'" target="_blank" class="social-slide social-twitter"><p>' . $caption .'</p><div class="social-icon"><span class="fa fa-twitter"></span></div></a>';
            } else {
              $items[$count] = '<div class="social-slide social-twitter"><p>' . $caption .'</p><div class="social-icon"><span class="fa fa-twitter"></span></div></div>';
            }
            $count++;
        }
        return $items;
    }
}
