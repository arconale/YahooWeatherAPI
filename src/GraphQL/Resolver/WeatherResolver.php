<?php

namespace App\GraphQL\Resolver;


use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use \DateTime;
class WeatherResolver implements ResolverInterface, AliasedInterface
{

    private $consumer_secret;
    private $consumer_key;

    public function __construct(String $consumer_key,String $consumer_secret)
    { 
        $this->consumer_secret = $consumer_secret;
        $this->consumer_key = $consumer_key;
    }

    public function resolve(Argument $args)
    {
        $url = 'https://weather-ydn-yql.media.yahoo.com/forecastrss';
        $app_id = 'm0oCgo7i';
        $consumer_key = $this->consumer_key;
        $consumer_secret = $this->consumer_secret;
        $query = array(
            'location' => $args["location"],
            'format' => 'json',
        );
        
        $oauth = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => uniqid(mt_rand(1, 1000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );
        $base_info = $this->buildBaseString($url, 'GET', array_merge($query, $oauth));
        $composite_key = rawurlencode($consumer_secret) . '&';
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;
        $header = array(
            $this->buildAuthorizationHeader($oauth),
            'X-Yahoo-App-Id: ' . $app_id
        );
        $options = array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $url . '?' . http_build_query($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
        curl_close($ch);
        $return_data = json_decode($response,true);
        $data['city'] = $return_data['location']['city'];
        $data['day'] = $return_data["forecasts"][0]["day"];
        date_default_timezone_set('Europe/Paris');
        $date = new DateTime();
        $date = $date->setTimestamp($return_data["forecasts"][0]["date"]);
        $data['date'] = $date->format('Y-m-d');
        $data['low'] = $return_data["forecasts"][0]["low"];
        $data['high'] = $return_data["forecasts"][0]["high"];
        $data['text'] = $return_data["forecasts"][0]['text'] ;
        return $data;
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Weather'
        ];
    }
    private function buildBaseString($baseURI, $method, $params)
    {
        $r = array();
        ksort($params);
        foreach ($params as $key => $value) {
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }
    private function buildAuthorizationHeader($oauth)
    {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        $r .= implode(', ', $values);
        return $r;
    }
}
