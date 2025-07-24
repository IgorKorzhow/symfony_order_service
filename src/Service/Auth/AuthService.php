<?php

namespace App\Service\Auth;

use App\Entity\ExternalAuthUser;
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
        'getUser' => '/auth/api/user',
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
                        'Authorization' => $token,
                    ],
                ]
            );

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new AuthServiceUnauthorizedException(
                    message: 'User not found or token is invalid',
                    code: Response::HTTP_UNAUTHORIZED
                );
            }

            $authUser = new ExternalAuthUser($response->toArray());
            $authUser->setToken($token);

            return $authUser;
        } catch (TransportExceptionInterface $e) {
            throw new AuthServiceTransportException(code: Response::HTTP_INTERNAL_SERVER_ERROR, previous: $e);
        }
    }
}
