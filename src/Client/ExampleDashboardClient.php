<?php

namespace App\Client;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExampleDashboardClient implements NotificationInterface
{
    private string $exampleDashboardKey;
    private string $exampleDashboardApi;
    // HTTP Client Interface here..
    private HttpClientInterface $httpClient;

    public function __construct(string $exampleDashboardKey, string $exampleDashboardApi)
    {
        $this->exampleDashboardKey = $exampleDashboardKey;
        $this->exampleDashboardApi = $exampleDashboardApi;
    }

    public function notify(array $data)
    {
        $url = $this->exampleDashboardApi;
        $key = $this->exampleDashboardKey;
        // example logic for sending http data
        try {
            //            $this->httpClient->request(
            //                Request::METHOD_POST,
            //                $this->exampleDashboardApi,
            //                $data
            //            );
        } catch (TransportExceptionInterface $e) {
            // do nothing
        }
    }
}
