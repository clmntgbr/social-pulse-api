<?php

namespace App\Resolver;

use App\Dto\TwitterCallback;
use App\Service\ValidatorError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class TwitterCallbackResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface  $validator,
        private ValidatorError $validatorError
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== TwitterCallback::class) {
            return;
        }

        $twitterCallback = new TwitterCallback();
        $twitterCallback->oauth_token = $request->query->get('oauth_token');
        $twitterCallback->oauth_verifier = $request->query->get('oauth_verifier');
        $twitterCallback->state = $request->query->get('state');

        $errors = $this->validator->validate($twitterCallback);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }

        yield $twitterCallback;
    }
}
