<?php

namespace App\Service;

use App\Dto\FacebookAccessToken;
use App\Dto\FacebookAccountResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class FacebookApi implements InterfaceApi
{
    public function __construct(
        private string              $facebookClientId,
        private string              $facebookClientSecret,
        private string              $facebookCallbackUrl,
        private string              $facebookApiUrl,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private ValidatorError      $validatorError
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAccessToken(string ...$code): FacebookAccessToken
    {
        $url = sprintf('%s/oauth/access_token?client_id=%s&redirect_uri=%s&client_secret=%s&code=%s', $this->facebookApiUrl, $this->facebookClientId, $this->facebookCallbackUrl, $this->facebookClientSecret, $code[0]);

        $response = $this->client->request('GET', $url);

        $facebookAccessToken = $this->serializer->deserialize($response->getContent(), FacebookAccessToken::class, 'json');

        $errors = $this->validator->validate($facebookAccessToken);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        return $facebookAccessToken;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getLongAccessToken(string $token): FacebookAccessToken
    {
        $url = sprintf('%s/oauth/access_token?grant_type=fb_exchange_token&client_id=%s&redirect_uri=%s&client_secret=%s&fb_exchange_token=%s', $this->facebookApiUrl, $this->facebookClientId, $this->facebookCallbackUrl, $this->facebookClientSecret, $token);

        $response = $this->client->request('GET', $url);

        $facebookAccessToken = $this->serializer->deserialize($response->getContent(), FacebookAccessToken::class, 'json');

        $errors = $this->validator->validate($facebookAccessToken);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        return $facebookAccessToken;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface|DecodingExceptionInterface
     */
    public function getAccounts(FacebookAccessToken $token): FacebookAccountResponse
    {
        $url = sprintf('%s/me?fields=accounts.limit(10){name,access_token,bio,emails,id,link,page_token,picture{url},website},email&access_token=%s', $this->facebookApiUrl, $token->access_token);

        $response = $this->client->request('GET', $url);

        $facebookAccounts = FacebookAccountResponse::hydrate($this->serializer, $response->toArray());

        $errors = $this->validator->validate($facebookAccounts);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        return $facebookAccounts;
    }
}