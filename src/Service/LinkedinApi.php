<?php

namespace App\Service;

use App\Dto\AccessToken\LinkedinAccessToken;
use App\Dto\Linkedin\LinkedinPost;
use App\Dto\Post;
use App\Dto\SocialNetworksAccount\LinkedinAccount;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Entity\SocialNetwork\SocialNetwork;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpClient\HttpOptions;
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
        private HttpClientInterface $httpClient,
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
            $response = $this->httpClient->request('POST', $url, [
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
        } catch (ClientExceptionInterface $clientException) {
            return null;
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getAccounts(LinkedinAccessToken $linkedinAccessToken): ?LinkedinAccount
    {
        $url = sprintf('%s/v2/userinfo',
            $this->linkedinApiUrl
        );

        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $linkedinAccessToken->accessToken),
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

    /**
     * @throws BadRequestHttpException
     * @param LinkedinSocialNetwork $socialNetwork
     */
    public function post(SocialNetwork $socialNetwork, array $payload): Post
    {
        $body = [
            "author" => sprintf("urn:li:person:%s", $socialNetwork->getSocialNetworkId()), 
            "commentary" => $payload['content'], 
            "visibility" => "PUBLIC", 
            "distribution" => [
                  "feedDistribution" => "MAIN_FEED", 
                  "targetEntities" => [
                  ], 
                  "thirdPartyDistributionChannels" => [
                     ] 
               ], 
            "lifecycleState" => "PUBLISHED", 
            "isReshareDisabledByAuthor" => false 
        ];

        try {
            $response = $this->httpClient->request('POST', sprintf('%s/rest/posts', $this->linkedinApiUrl), [
                'body' => json_encode($body),
                'headers' => [
                  'authorization' => sprintf('Bearer %s', $socialNetwork->getToken()),
                  'content-type' => 'application/json',
                  'linkedin-version' => '202411',
                  'x-restli-protocol-version' => '2.0.0',
                ],
            ]);

            $headers = $response->getHeaders();

            $linkedinPost = $this->serializer->deserialize(json_encode($headers), LinkedinPost::class, 'json');

            $errors = $this->validator->validate($linkedinPost);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $linkedinPost;
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}