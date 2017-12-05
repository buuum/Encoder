<?php

namespace Buuum\Encoder;

use Buuum\Encoder\Exception\DelayException;
use Buuum\Encoder\Exception\ExpiresException;

class Encoder
{

    /**
     * @var
     */
    protected $key;

    /**
     * @var string
     */
    protected $method;

    /**
     * Encoder constructor.
     * @param string $key
     * @param string $method
     * @throws \Exception
     */
    public function __construct(string $key = '', $method = 'AES-256-CBC')
    {
        $this->key = $key;
        $this->setMethod($method);
    }

    /**
     * @param array $data
     * @param array $head
     * @param boolean $response_hash_fixed
     * @param string $key
     * @return string
     */
    public function encode(array $data, array $head = [], $response_hash_fixed = false, string $key = '')
    {
        if(empty($key)){
            $key = $this->key;
        }
        
        $header = [
            'expires' => 0,
            'delay'   => 0
        ];

        $header = array_merge($header, $head);

        if ($header['expires'] > 0) {
            $header['expires'] += time();
        }
        if ($header['delay'] > 0) {
            $header['delay'] += time();
        }

        $segments = [];
        $segments[] = json_encode($header);
        $segments[] = json_encode($data);

        return $this->sign(implode('.', $segments), $response_hash_fixed, $key);
    }

    /**
     * @param $data
     * @param string $key
     * @return mixed
     * @throws DelayException
     * @throws ExpiresException
     */
    public function decode($data, string $key = '')
    {
        if(empty($key)){
            $key = $this->key;
        }

        $encrypted = $this->base64_url_decode($data);
        list($data, $iv) = explode(':', $encrypted);

        $decrypted = openssl_decrypt($data, $this->method, $key, 0, $iv);
        list($headers, $data) = explode('.', json_decode($decrypted), 2);
        $headers = json_decode($headers, true);

        if ($headers['expires'] > 0 && $headers['expires'] < time()) {
            $time = date(\DateTime::ISO8601, $headers['expires']);
            throw new ExpiresException('This token expired on ' . $time, $headers['expires']);
        }
        if ($headers['delay'] > 0 && $headers['delay'] > time()) {
            $time = date(\DateTime::ISO8601, $headers['delay']);
            throw new DelayException('Cannot handle token prior to ' . $time, $headers['delay']);
        }

        return json_decode($data);
    }

    /**
     * @param $method
     * @throws \Exception
     */
    public function setMethod($method)
    {
        if (!in_array($method, openssl_get_cipher_methods())) {
            throw new \Exception('Method not allowed');
        }

        $this->method = $method;
    }

    /**
     * @param $data
     * @param $response_hash_fixed
     * @param $key
     * @return string
     */
    private function sign($data, $response_hash_fixed, $key)
    {
        $iv = $this->getIv($response_hash_fixed, $key);
        $encrypted = openssl_encrypt(json_encode($data), $this->method, $key, 0, $iv);
        return $this->base64_url_encode($encrypted . ':' . $iv);
    }

    /**
     * @param $response_hash_fixed
     * @param $key
     * @return string
     */
    private function getIv($response_hash_fixed, $key)
    {
        $size = openssl_cipher_iv_length($this->method);
        $key = (!$response_hash_fixed) ? time() : $key;

        return substr(hash('sha256', $key), 0, $size);
    }

    /**
     *
     * @param $input
     * @return string
     *
     */
    private function base64_url_encode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     *
     * @param $input
     * @return string
     *
     */
    private function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

}