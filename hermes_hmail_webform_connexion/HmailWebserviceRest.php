<?php

namespace Hmail\Webservice;

use Drupal\Core\Site\Settings;

/**
 * Class hmail webservice socket.
 *
 * @package Hmail\Webservice
 */
class Rest {

//    protected $csrfToken;

    protected $logoutToken;

    protected $host;

    protected $format;

    /**
     * Rest constructor.
     *
     * @param string $host
     *   The url of host.
     * @param string $format
     *   The format of the request.
     */
    public function __construct($host, $format = 'json') {
        $this->host = trim($host);
        $this->format = $format;
    }

    /**
     * Call webservice Subscribe.
     *
     * @param array $data
     *   The array of data to send in request.
     *
     * @return array|bool
     *   The result of webservice.
     *
     * @throws \Exception
     */
    public function subscribe(array $data) {
//    if (empty($this->csrfToken)) {
//      return FALSE;
//    }

        // Create new instance of http fsockopen.
        $fs = new HttpFsockopen($this->host . '/api/v1/subscription?_format=json');
        $fs->setPostData($data, $this->format);
        return $fs->parseResponse('json', $fs->exec());
    }

    public function testStatus(array $data) {
        // Create new instance of http fsockopen.
        $fs = new HttpFsockopen($this->host . '/api/v1/subscription?_format=json');
        $fs->setPostData($data, $this->format);

        $header = $fs->parseResponse('json', $fs->exec())['header'];
        $header = explode(" ", $header)[1];
        return $header;
    }

    /**
     * Call webservice Subscribe.
     *
     * @param string $user_name
     *   The user name to login.
     * @param string $password
     *   The passord of user to login.
     * @param bool $force
     *   If true regen new csrf token.
     *
     * @return array|bool
     *   The result of webservice.
     *
     * @throws \Exception
     */
//  public function authentification($user_name, $password, $force = FALSE) {
//    if (!empty($this->csrfToken) && $force == FALSE) {
//      return TRUE;
//    }
//
//    // Create new instance of http fsockopen.
//    $fs = new HttpFsockopen($this->host . '/user/login');
//    $fs->setQueryData(['_format' => $this->format]);
//    $data = ['name' => $user_name, 'pass' => $password];
//    $fs->setPostData($data, $this->format);
//    $result = $fs->parseResponse('json', $fs->exec());
//
//    // Set csrf token.
//    if (!empty($result['data']['csrf_token'])) {
//      $this->csrfToken = $result['data']['csrf_token'];
//    }
//    if (!empty($result['data']['logout_token'])) {
//      $this->logoutToken = $result['data']['logout_token'];
//    }
//    return $result;
//  }

    /**
     * Call webservice to request csrf Token (used by anonymous).
     *
     * @param bool $force
     *   If true regen new csrf token.
     *
     * @return array|bool
     *   The result of webservice.
     *
     * @throws \Exception
     */
//    public function getToken($force = FALSE) {
//
//        if (empty($this->csrfToken) || $force) {
//            // Create new instance of http fsockopen.
//            $fs = new HttpFsockopen($this->host . '/rest/session/token');
//            $fs->setHeaders("Content-Type", "test/plain");
//            $result = $fs->parseResponse('text', $fs->exec());
//            if (!empty($result['data'])) {
//                $this->csrfToken = $result['data'];
//            }
//        }
//        return $this->csrfToken;
//    }
}