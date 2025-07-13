<?php

namespace App\Service\Auth;

use App\Exception\AuthService\AuthServiceTransportException;
use App\Exception\AuthService\AuthServiceUnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AuthService
{
    private const URLS = [
        'getUser' => '/user',
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function getUserByToken(string $token): ExternalAuthUser
    {
        try {
            $response = $this->httpClient->request(
                method: 'GET',
                url: self::URLS['getUser'],
                options: [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                    ],
                ]
            );

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new AuthServiceUnauthorizedException(code: Response::HTTP_UNAUTHORIZED);
            }

            return new ExternalAuthUser($response->toArray());
        } catch (TransportExceptionInterface $e) {
            throw new AuthServiceTransportException(code: Response::HTTP_INTERNAL_SERVER_ERROR, previous: $e);
        }
    }
}
