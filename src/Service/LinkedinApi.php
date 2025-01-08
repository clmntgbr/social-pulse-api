<?php

namespace App\Service;

use App\Dto\AccessToken\LinkedinAccessToken;
use App\Dto\SocialNetworksAccount\LinkedinAccount;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class LinkedinApi implements InterfaceApi
{
    public function __construct(
        private string $linkedinClientId,
        private string $linkedinClientSecret,
        private string $callbackUrl,
        private string $linkedinApiUrl,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAccessToken(string ...$params): ?LinkedinAccessToken
    {
        $url = sprintf('%s/oauth/v2/accessToken', $this->linkedinApiUrl);

        try {
            $response = $this->client->request('POST', $url, [
                'query' => [
                    'grant_type' => 'authorization_code',
                    'code' => $params[0],
                    'redirect_uri' => sprintf($this->callbackUrl, 'linkedin'),
                    'client_id' => $this->linkedinClientId,
                    'client_secret' => $this->linkedinClientSecret,
                ],
            ]);

            $linkedinAccessToken = $this->serializer->deserialize($response->getContent(), LinkedinAccessToken::class, 'json');

            $errors = $this->validator->validate($linkedinAccessToken);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $linkedinAccessToken;
        } catch (ClientExceptionInterface $exception) {
            return null;
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getAccounts(LinkedinAccessToken $token): ?LinkedinAccount
    {
        $url = sprintf('%s/v2/userinfo',
            $this->linkedinApiUrl
        );

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $token->accessToken),
                    'Connection' => 'Keep-Alive',
                    'ContentType' => 'application / json',
                ],
            ]);

            $linkedinAccount = $this->serializer->deserialize($response->getContent(), LinkedinAccount::class, 'json');

            $errors = $this->validator->validate($linkedinAccount);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $linkedinAccount;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
