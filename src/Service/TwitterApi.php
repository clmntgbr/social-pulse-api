<?php

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use App\Dto\AccessToken\TwitterAccessToken;
use App\Dto\AccessToken\TwitterBearerToken;
use App\Dto\SocialNetworksAccount\TwitterAccount;
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
        private string $twitterApiKey,
        private string $twitterApiSecret,
        private string $callbackUrl,
        private string $twitterApiUrl,
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
    public function getAccessToken(string ...$params): ?TwitterAccessToken
    {
        $url = sprintf('%s/oauth/access_token?oauth_token=%s&oauth_verifier=%s',
            $this->twitterApiUrl,
            $params[0],
            $params[1]
        );

        try {
            $response = $this->client->request('POST', $url);
            parse_str($response->getContent(), $decoded);

            $twitterAccessToken = $this->serializer->deserialize(json_encode($decoded), TwitterAccessToken::class, 'json');

            $errors = $this->validator->validate($twitterAccessToken);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $twitterAccessToken;
        } catch (ClientExceptionInterface $exception) {
            return null;
        }
    }

    public function getBearerToken(): ?TwitterBearerToken
    {
        $url = sprintf('%s/oauth2/token?grant_type=client_credentials',
            $this->twitterApiUrl
        );

        try {
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => sprintf('Basic %s', base64_encode(sprintf('%s:%s', $this->twitterApiKey, $this->twitterApiSecret))),
                ],
            ]);

            $twitterBearerToken = $this->serializer->deserialize($response->getContent(), TwitterBearerToken::class, 'json');

            $errors = $this->validator->validate($twitterBearerToken);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $twitterBearerToken;
        } catch (ClientExceptionInterface $exception) {
            return null;
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TwitterOAuthException
     */
    public function getAccounts(TwitterAccessToken $token): ?TwitterAccount
    {
        try {
            $client = new TwitterOAuth($this->twitterApiKey, $this->twitterApiSecret, $token->oauthToken, $token->oauthTokenSecret);
            $client->setApiVersion('2');

            $response = $client->get('users/me', [
                'expansions' => ['pinned_tweet_id'],
                'user.fields' => $this->getScopes(),
            ]);

            $response = $response->data ?? $response;
            $twitterAccount = $this->serializer->deserialize(json_encode($response), TwitterAccount::class, 'json');

            $errors = $this->validator->validate($twitterAccount);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $twitterAccount;
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getScopes(): string
    {
        return 'created_at,description,entities,id,location,most_recent_tweet_id,name,pinned_tweet_id,profile_image_url,protected,public_metrics,url,username,verified,verified_type,withheld';
    }
}
