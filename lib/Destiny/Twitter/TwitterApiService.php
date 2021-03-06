<?php
namespace Destiny\Twitter;

use Destiny\Common\Config;
use Destiny\Common\Service;
use Destiny\Common\Utils\Http;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

/**
 * @method static TwitterApiService instance()
 */
class TwitterApiService extends Service {

    public $oauthBase = 'https://api.twitter.com/oauth';
    public $apiBase = 'https://api.twitter.com/1.1';

    /**
     * @param array $conf
     * @return Client
     */
    public function getOAuth1Client(array $conf){
        $stack = HandlerStack::create();
        $stack->push(new Oauth1([
            'consumer_key'    => $conf['client_id'],
            'consumer_secret' => $conf['client_secret'],
            'token'           => '',
            'token_secret'    => ''
        ]));
        $client = new Client([
            'timeout' => 15,
            'connect_timeout' => 10,
            'handler' => $stack,
            'auth' => 'oauth'
        ]);
        return $client;
    }

    /**
     * @return array
     */
    public function getTweets(){
        $conf = Config::$a['oauth_providers']['twitter'];
        $twitterApiService = TwitterApiService::instance();
        $client = $twitterApiService->getOAuth1Client($conf);
        $response = $client->get("$this->apiBase/statuses/user_timeline.json", [
            'headers' => ['User-Agent' => Config::userAgent()],
            'query' => [
                'screen_name' => Config::$a['twitter']['user'],
                'trim_user' => true,
                'count' => 4
            ]
        ]);
        $tweets = [];
        if ($response->getStatusCode() == Http::STATUS_OK) {
            $data = json_decode((string)$response->getBody(), true);
            foreach ($data as $tweet) {
                $html = $tweet['text'];
                if (isset ($tweet['entities']['user_mentions'])) {
                    foreach ($tweet['entities']['user_mentions'] as $mention) {
                        $l = '<a href="http://twitter.com/' . $mention['screen_name'] . '">' . $mention['name'] . '</a>';
                        $html = str_replace('@' . $mention['screen_name'], $l, $html);
                    }
                }
                if (isset ($tweet['entities']) && isset ($tweet['entities']['urls'])) {
                    foreach ($tweet['entities']['urls'] as $url) {
                        $l = '<a href="' . $url['url'] . '" rev="' . $url['expanded_url'] . '">' . $url['display_url'] . '</a>';
                        $html = str_replace($url['url'], $l, $html);
                    }
                }
                $tweet ['user']['screen_name'] = Config::$a['twitter']['user'];
                $tweet ['html'] = $html;
                $tweets [] = $tweet;
            }
        }
        return $tweets;
    }

    /**
     * Extracts and decodes OAuth parameters from the passed string
     *
     * @param string $body the response body from an OAuth flow method
     * @return array the response body safely decoded to an array of key => values
     */
    public function extract_params($body) {
        $kvs = explode('&', $body);
        $decoded = array();
        foreach ($kvs as $kv) {
            $kv = explode('=', $kv, 2);
            $kv[0] = $this->safe_decode($kv[0]);
            $kv[1] = $this->safe_decode($kv[1]);
            $decoded[$kv[0]] = $kv[1];
        }
        return $decoded;
    }

    /**
     * Decodes the string or array from it's URL encoded form
     * If an array is passed each array value will will be decoded.
     *
     * @param mixed $data the scalar or array to decode
     * @return string $data decoded from the URL encoded form
     */
    public function safe_decode($data) {
        if (is_array($data)) {
            return array_map(array($this, 'safe_decode'), $data);
        } else if (is_scalar($data)) {
            return rawurldecode($data);
        } else {
            return '';
        }
    }
}

