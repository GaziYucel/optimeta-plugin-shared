<?php

namespace Optimeta\Shared\GitHub;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GitHubBase
{
    public function __construct()
    {
    }

    /**
     * @desc Adds an issue to a given repository
     * @param string $title
     * @param string $body
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addIssue(string $title, string $body): int
    {
        $issueId = 0;

        if (empty($this->url) || empty($this->token) || empty($title) || empty($body)) {
            return $issueId;
        }

        try {
            $response = $this->client->request(
                'POST',
                $this->url,
                [
                    'headers' => [
                        'User-Agent' => $this->userAgent,
                        'Accept' => 'application/vnd.github.v3+json',
                        'Authorization' => 'token ' . $this->token
                    ],
                    'json' => [
                        'title' => $title,
                        'body' => $body,
                        'labels' => ['Deposit']
                    ]
                ]);

            $issueId = $this->getIssueId($response);

        } catch (\Exception $ex) {
            $this->errors .= $ex;
        }

        return $issueId;
    }

    /**
     * @desc Get issue id from response
     * @param $response
     * @return int
     */
    public function getIssueId($response): int
    {
        $issueId = 0;

        if(empty($response)) return $issueId;

        try{
            foreach((array)$response as $key => $value){

                if(stristr($key, 'stream')){
                    $objValue = json_decode($value, true);

                    if(is_numeric($objValue['number'])){
                        $issueId = (int)$objValue['number'];
                    }
                }
            }
        }
        catch(\Exception $ex){
            $this->errors .= $ex;
        }

        return $issueId;
    }
}