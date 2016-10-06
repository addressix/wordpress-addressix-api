<?php
require('Response.php');

class AddressixAPI
{
  private static $initiated = false;
  private static $instance;
  private $access_token;
  
  public static function init() {
    if ( ! self::$initiated ) {
      self::$instance = new AddressixAPI();
    }
    return self::$instance;
  }

  /**
   * Initializes WordPress hooks
   */
  function __construct() {
    self::$initiated = true;
    
    $tokens = get_user_meta(get_current_user_id(),'addressix_access');
    if (is_array($tokens)) {
      $this->access_token = $tokens[0];
    }
  }

  function fetch($uri, $parameters = array(), $http_method = 'GET', array $http_headers = array(), $form_content_type = 1)
  {
    $http_headers['Accept'] = 'application/json';
    if ($this->access_token) {
      $http_headers['Authorization'] = 'Bearer ' . $this->access_token;
    }
    $url = 'https://www.addressix.com/api' . $uri;

    return $this->executeRequest($url, $parameters, $http_method, $http_headers, $form_content_type);
  }
 
  public function getFormattedHeaders($headers)
  {
    $formattedHeaders = array();

    $combinedHeaders = array_change_key_case((array) $headers);

    foreach ($combinedHeaders as $key => $val) {
      $key = trim(strtolower($key));
      $fmh = $key . ': ' . $val;

      $formattedHeaders[] = $fmh;
    }

    if (!array_key_exists('user-agent', $combinedHeaders)) {
      $formattedHeaders[] = 'user-agent: addressix-wp/1.0';
    }

    if (!array_key_exists('expect', $combinedHeaders)) {
      $formattedHeaders[] = 'expect:';
    }

    return $formattedHeaders;
  }

  function executeRequest($url, $parameters = array(), $http_method = 'GET', array $http_headers = null, $form_content_type = 1)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HEADER, true);
    $http_headers['Accept'] = 'application/json';

    curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getFormattedHeaders($http_headers));

    if ($http_method!='GET') {
      if ($http_method=='POST') {
	curl_setopt($curl, CURLOPT_POST, true);
      } else {
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
      }      
    }

    switch($http_method) {
    case 'GET':
      if (is_array($parameters) && count($parameters) > 0) {
        $url .= '?' . http_build_query($parameters, null, '&');
      } elseif ($parameters) {
        $url .= '?' . $parameters;
      }
      break;
    case 'PUT':
    case 'POST':
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parameters));
      break;
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    $error = curl_error($curl);
    $info = curl_getinfo($curl);

    $header_size = $info['header_size'];
    $header      = substr($response, 0, $header_size);
    $body        = substr($response, $header_size);
    $httpCode    = $info['http_code'];

    $resp = new Addressix_UnirestResponse($httpCode, $body, $header, array());
    return $resp;
  }
}