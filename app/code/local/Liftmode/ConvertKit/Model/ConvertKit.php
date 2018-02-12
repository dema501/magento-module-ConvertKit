<?php
/**
 *
 * @category   Mage
 * @package    Liftmode_ConvertKit
 * @copyright  Copyright (c)  Dmitry Bashlov, contributors.
 */

class Liftmode_ConvertKit_Model_ConvertKit {

    const API_URL = 'https://api.convertkit.com/v3/';
    const MESSAGE_SUCCESS = 'Success';


    /**
     * API Request
     */
    public function doRequest($uri, $http_method, $params = array()) {
        $return = array(
            'success' => false,
            'error_msg' => '',
            'http_code' => 200,
            'response' => array()
        );

        $url = self::API_URL.$uri;

        $return['url'] = $url;

        try {
            $response_string = $this->curl($url, $http_method, $params);
        } catch(\Exception $e) {
            $return['error_msg'] = $e->getMessage();
            if ($e->getCode()) {
                $return['http_code'] = $e->getCode();
            }
            return $return;
        }

        try {
            $response = $this->jsonDecode($response_string);
        } catch(\Exception $e) {
            $return['error_msg'] = $e->getMessage();
            return $return;
        }

        if (!is_array($response) || !$response) {
            $return['error_msg'] = 'Empty response from API.';
        } else {
            $return['success'] = true;
            $return['response'] = $response;
        }

        return $return;
    }


    /**
     * Retrieve data
     */
    private function curl($url, $http_method = 'get', $params = array()) {
        if (!function_exists('curl_init')) {
            throw new \Exception("curl is not enabled in your PHP", 0);
        }

        $ch = curl_init();

        if ($http_method == 'post') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } elseif ($http_method == 'put') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url.http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if ($result === false) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error_msg = curl_error($ch);
            curl_close($ch);

            throw new \Exception($error_msg, $http_code);
        }

        return $result;
    }

    /**
     * Decode JSON
     */
    private function jsonDecode($jsong_string) {
        if (!is_string($jsong_string)) {
            return array();
        }
        if (!function_exists('json_decode')) {
            throw new \Exception("json_decode function is not enabled in your PHP", 1);
        }

        return json_decode($jsong_string, true);
    }
}
