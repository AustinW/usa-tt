<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Coda
{
    protected $http;

    protected $doc;

    protected $table;

    protected $token;

    public function __construct()
    {
        $this->http = new Client();

        $this->setDoc(config('app.coda.doc'));
        $this->setTable(config('app.coda.table'));
        $this->setToken(config('app.coda.token'));
    }

    /**
     * @param        $endpoint
     * @param string $method
     * @param null   $data
     * @return mixed|ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($endpoint, $method = 'get', $data = null)
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken()
            ],
            'debug' => true
        ];

        if ($data) {
            $options[RequestOptions::JSON] = $data;
        }

        return $this->http->request($method, $this->makeUrl() . '/' . $endpoint, $options);
    }

    public function jsonContents(ResponseInterface $request)
    {
        return collect(
            json_decode(
                $request->getBody()->getContents(),
                true
            )
        );
    }

    protected function makeUrl()
    {
        return implode('/', [
            config('app.coda.api'),
            'docs',
            $this->getDoc(),
            'tables',
            $this->getTable()
        ]);
    }

    /**
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param mixed $doc
     * @return Coda
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     * @return Coda
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return Coda
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
}