<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ExternalAuthUser;
use App\Exception\AuthService\AuthServiceTransportException;
use App\Exception\AuthService\AuthServiceUnauthorizedException;
use App\Service\Auth\AuthService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthServiceTest extends TestCase
{
    private AuthService $authService;
    private HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->authService = new AuthService($this->httpClient);
    }

    public function testGetUserByTokenSuccess(): void
    {
        $token = 'valid_token';
        $userData = [
            'id' => 1,
            'name' => 'test',
            'email' => 'test@example.com',
            'roles' => ['ROLE_USER'],
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')->willReturn($userData);

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/auth/api/user',
                [
                    'headers' => [
                        'Authorization' => $token,
                    ],
                ]
            )
            ->willReturn($response);

        $result = $this->authService->getUserByToken($token);

        $this->assertInstanceOf(ExternalAuthUser::class, $result);
        $this->assertEquals($userData['id'], $result->getId());
        $this->assertEquals($userData['email'], $result->getEmail());
        $this->assertEquals($userData['roles'], $result->getRoles());
        $this->assertEquals($token, $result->getToken());
    }

    public function testGetUserByTokenUnauthorized(): void
    {
        $token = 'invalid_token';

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_UNAUTHORIZED);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $this->expectException(AuthServiceUnauthorizedException::class);
        $this->expectExceptionMessage('User not found or token is invalid');
        $this->expectExceptionCode(Response::HTTP_UNAUTHORIZED);

        $this->authService->getUserByToken($token);
    }

    public function testGetUserByTokenTransportException(): void
    {
        $token = 'valid_token';

        $this->httpClient
            ->method('request')
            ->willThrowException(
                $this->createMock(TransportExceptionInterface::class)
            );

        $this->expectException(AuthServiceTransportException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->authService->getUserByToken($token);
    }

    public function testGetUserByTokenClientException(): void
    {
        $token = 'valid_token';

        $this->httpClient
            ->method('request')
            ->willThrowException(
                $this->createMock(ClientExceptionInterface::class)
            );

        $this->expectException(ClientExceptionInterface::class);

        $this->authService->getUserByToken($token);
    }

    public function testGetUserByTokenDecodingException(): void
    {
        $token = 'valid_token';

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')
            ->willThrowException(
                $this->createMock(DecodingExceptionInterface::class)
            );

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $this->expectException(DecodingExceptionInterface::class);

        $this->authService->getUserByToken($token);
    }
}
