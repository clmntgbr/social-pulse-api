<?php

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use App\Dto\TwitterAccessToken;
use App\Dto\TwitterAccount;
use App\Dto\TwitterBearerToken;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class TwitterApi implements InterfaceApi
{
    public function __construct(
        private readonly string $twitterApiKey,
        private readonly string $twitterApiSecret,
        private readonly string $twitterApiXUrl,
        private readonly string $twitterApiUrl,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private ValidatorError      $validatorError,
        private TwitterLoginUrl $twitterLoginUrl
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAccessToken(string ...$params): TwitterAccessToken
    {
        $url = sprintf('%s/oauth/access_token?oauth_token=%s&oauth_verifier=%s', $this->twitterApiXUrl, $params[0], $params[1]);

        $response = $this->client->request('POST', $url);
        parse_str($response->getContent(), $result);
        $linkedinAccessToken = $this->serializer->deserialize(json_encode($result), TwitterAccessToken::class, 'json');

        $errors = $this->validator->validate($linkedinAccessToken);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        return $linkedinAccessToken;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getBearerToken(): TwitterBearerToken
    {
        $url = sprintf('%s/oauth2/token?grant_type=client_credentials', $this->twitterApiUrl);

        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => sprintf('Basic %s', base64_encode(sprintf('%s:%s', $this->twitterApiKey, $this->twitterApiSecret))),
            ]
        ]);

        $bearerToken = $this->serializer->deserialize($response->getContent(), TwitterBearerToken::class, 'json');

        $errors = $this->validator->validate($bearerToken);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        return $bearerToken;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TwitterOAuthException
     */
    public function getUserProfile(TwitterAccessToken $token): TwitterAccount
    {
        $client = new TwitterOAuth($this->twitterApiKey, $this->twitterApiSecret, $token->oauth_token, $token->oauth_token_secret);
        $client->setApiVersion('2');
        $response = $client->get('users/me', [
            'expansions' => ['pinned_tweet_id'],
            'user.fields' => $this->twitterLoginUrl->getScopes(),
        ]);
        $response = $response->data ?? $response;
        $twitterAccount = $this->serializer->deserialize(json_encode($response), TwitterAccount::class, 'json');

        $errors = $this->validator->validate($twitterAccount);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        return $twitterAccount;
    }
}