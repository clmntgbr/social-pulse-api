<?php

namespace App\Dto;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePostsResponse
{
    #[Assert\Type('array')]
    #[Assert\Valid()]
    #[Assert\NotBlank()]
    /** @var CreatePosts[] $posts */
    public array $posts;

    public static function hydrate(SerializerInterface $serializer, array $data): CreatePostsResponse
    {
        $createPostsResponse = new CreatePostsResponse();

        foreach ($data ?? [] as $postData) {
            $post = $serializer->deserialize(json_encode($postData), CreatePosts::class, 'json');
            $createPostsResponse->posts[] = $post;
        }

        return $createPostsResponse;
    }
}
