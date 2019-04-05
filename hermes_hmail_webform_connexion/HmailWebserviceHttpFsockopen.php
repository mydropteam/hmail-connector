<?php

namespace Hmail\Webservice;

use Drupal\Core\Site\Settings;


/**
 * Class HttpFsockopen.
 *
 * @package Hmail\Webservice
 */
class HttpFsockopen {

  protected $url;

  protected $path;

  protected $host;

  protected $query;

  protected $post;

  protected $port;

  protected $headers;

  protected $ssl;

  protected $method;

  protected $timeout;

    protected static $autoload;

  /**
   * HttpFsockopen constructor.
   *
   * @param string $url
   *   The complete url with host and path to call.
   */
  public function __construct($url) {
    /*
     * if (is_null(HttpFsockopen::$autoload) && $use_autoload) {
     * HttpFsockopen::$autoload = TRUE;
     * spl_autoload_register(["HttpFsockopen", "load"]);
     * }
     * */
    $url_array = parse_url($url);
    if (!empty($url_array["scheme"]) && preg_match("#^https|ssl$#i", $url_array["scheme"])) {
      $this->ssl = TRUE;
    }
    else {
      $this->ssl = FALSE;
    }

    if (empty($url_array["port"])) {
      if ($this->ssl) {
        $this->port = 443;
      }
      else {
        $this->port = 80;
      }
    }

    if (array_key_exists("path", $url_array)) {
      $this->path = $url_array["path"];
    }
    else {
      $this->path = FALSE;
    }

    if (array_key_exists("query", $url_array)) {
      $this->query = $url_array["query"];
    }
    else {
      $this->query = FALSE;
    }
    $this->host = !empty($url_array["host"]) ? $url_array["host"] : '';
    $this->method = "GET";
    $this->timeout = 15;
  }

  /**
   * Set query parameters.
   *
   * @param array|string $data
   *   The query data parameters.
   *
   * @return $this
   */
  public function setQueryData($data) {
    if (is_array($data)) {
      $data = http_build_query($data);
    }
    $this->query = $data;
    return $this;
  }

  /**
   * Set body POST data to send.
   *
   * @param array|string $data
   *   The data to send in body.
   * @param string $format
   *   The format of request.
   *
   * @return $this
   *
   * @throws \Exception
   *   Unknown format.
   */
  public function setPostData($data, $format = 'form') {

    $this->method = "POST";
    switch ($format) {
      case 'form':
        if (is_array($data)) {
          $data = http_build_query($data);
        }
        $this->setHeaders("Content-Type", "application/x-www-form-urlencoded");
        break;

      case 'json':
        if (is_array($data)) {
          $data = json_encode($data);
        }
        $this->setHeaders("Content-Type", "application/json");
        break;

      default:
        throw new \Exception('Format unrecognized');

    }

    /*************************************************/
    /************HMAIL CREDENTIALS********************/
    /*************************************************/
//    Retrieve Hmail credentials from setting.php
      $hmailCredentials = Settings::get('hmail_credentials', []);
      $username = isset($hmailCredentials['user']) && !empty($hmailCredentials['user']) ? $hmailCredentials['user'] : '';
      $password = isset($hmailCredentials['password']) && !empty($hmailCredentials['password']) ? $hmailCredentials['password'] : '';
//      Base64 encode
      $authorization = "Basic " . base64_encode($username . ':' . $password);
//      Set header Authorization
      $this->setHeaders('Authorization', $authorization);

      $this->post = $data;
    return $this;
  }


    /**
   * Set method to the request.
   *
   * @param string $method
   *   The method of request.
   *
   * @return $this
   */
  public function setMethod($method) {
    if (preg_match("#^[a-z]+$#i", $method)) {
      $this->method = strtoupper($method);
    }
    return $this;
  }

  /**
   * Set timeout to the request.
   *
   * @param int $timeout
   *   The timeout of request.
   *
   * @return $this
   */
  public function setTimeout($timeout) {
    $this->timeout = $timeout;
    return $this;
  }

  /**
   * Set port to the request.
   *
   * @param int $port
   *   The number of port.
   *
   * @return $this
   */
  public function setPort($port) {
    $this->port = $port;
    return $this;
  }

  /**
   * Set headers to the request.
   *
   * @param string|array $key
   *   The key of header value to set.
   * @param mixed|null $value
   *   The value to set in header.
   *
   * @return $this
   */
  public function setHeaders($key, $value = NULL) {
    if (is_array($key)) {
      foreach ($key as $key => $value) {
        if (is_null($value)) {
          unset($this->headers[$key]);
        }
        else {
          $this->headers[$key] = $value;
        }
      }
    }
    else {
      if (is_null($value)) {
        unset($this->headers[$key]);
      }
      else {
        $this->headers[$key] = $value;
      }
    }
    return $this;
  }

  /**
   * Set user agent to the request.
   *
   * @param string $user_agent
   *   The user agent.
   *
   * @return \Hmail\Webservice\HttpFsockopen
   *  The current http fsockopen.
   */
  public function setUserAgent($user_agent) {
    return $this->setHeaders("User-Agent", $user_agent);
  }

  /**
   * Execution of request.
   *
   * @return string
   *   The result of request.
   */
  public function exec() {
    $socket = fsockopen(($this->ssl ? "ssl://" : "") . $this->host, $this->port, $errno, $errstr, $this->timeout);
    $contents = "";
    if ($socket) {
      $http = $this->method . " " . (strlen($this->path) ? $this->path : "/") .
        (strlen($this->query) > 0 ? "?" . $this->query : "")
        . " HTTP/1.1\r\n";
      $http .= "Host: " . $this->host . "\r\n";
      foreach ($this->headers as $key => $value) {
        $http .= $key . ": " . $value . "\r\n";
      }
      $http .= "Content-length: " . strlen($this->post) . "\r\n";
      $http .= "Connection: close\r\n\r\n";
      if (!is_null($this->post)) {
        $http .= $this->post . "\r\n\r\n";
      }
      fwrite($socket, $http);
      while (!feof($socket)) {
        $contents .= fgetc($socket);
      }
      fclose($socket);
    }

    return $contents;
  }

  /**
   * Parse the complete response.
   *
   * @param string $format
   *   The format of response.
   * @param string $response
   *   The complete response with header.
   *
   * @return array
   *   The array with header and data separate.
   *
   * @throws \Exception
   */
  public function parseResponse($format, $response) {
    $data = [];
    list($header, $body) = preg_split("/\R\R/", $response, 2);
    switch ($format) {

      case 'json':
        if (!empty($body)) {
          $data = json_decode($body, TRUE);
        }
        break;

      case 'text':
        if (!empty($body)) {
          $data = $body;
        }
        break;

      default:
        throw new \Exception('Format unrecognized');

    }
    return ['header' => $header, 'data' => $data];
  }

}