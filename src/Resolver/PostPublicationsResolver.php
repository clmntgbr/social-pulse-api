<?php

namespace App\Resolver;

use App\Dto\Api\PostPublications;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PostPublicationsResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (PostPublications::class !== $argument->getType()) {
            return;
        }

        $content = $request->getContent();

        /** @var PostPublication[] $postPublication */
        $postPublication = $this->serializer->deserialize($content, 'App\Dto\Api\PostPublication[]', 'json');

        usort($postPublication, function ($a, $b) {
            return $a->id - $b->id;
        });

        $postPublications = new PostPublications($postPublication);

        if (count($postPublication) > 0) {
            $postPublications->publicationType = $postPublication[0]->publicationType;
            $postPublications->socialNetworkUuid = $postPublication[0]->socialNetworkUuid;
        }

        $errors = $this->validator->validate($postPublications);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $postPublications;
    }
}
