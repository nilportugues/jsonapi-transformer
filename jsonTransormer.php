<?php

use NilPortugues\Api\Mapping\Mapper;
use NilPortugues\Api\Transformer\Json\JsonApiTransformer;
use NilPortugues\Serializer\Serializer;
use NilPortugues\Tests\Api\Dummy\ComplexObject\Comment;
use NilPortugues\Tests\Api\Dummy\ComplexObject\Post;
use NilPortugues\Tests\Api\Dummy\ComplexObject\User;
use NilPortugues\Tests\Api\Dummy\ComplexObject\ValueObject\CommentId;
use NilPortugues\Tests\Api\Dummy\ComplexObject\ValueObject\PostId;
use NilPortugues\Tests\Api\Dummy\ComplexObject\ValueObject\UserId;

include 'vendor/autoload.php';

$post = new Post(
    new PostId(9),
    'Hello World',
    'Your first post',
    new User(new UserId(1), 'Post Author'),
    [
        new Comment(
            new CommentId(1000),
            'Have no fear, sers, your king is safe.',
            new User(new UserId(2), 'Barristan Selmy'),
            [
                'created_at' => (new DateTime('now -35 minutes'))->format('c'),
                'accepted_at' => (new DateTime())->format('c'),
            ]
        ),
    ]
);

$mappings = [
    [
        'class' => Post::class,
        'alias' => 'Message',
        'aliased_properties' => [
            'title' => 'headline',
            'content' => 'body',
        ],
        'hide_properties' => [],
        'id_properties' => [
            'postId',
        ],
        'urls' => [
            'self' => 'http://example.com/posts/{postId}',
            'related' => 'http://example.com/posts/{postId}/author',
            'relationships' => [
                'self' => 'http://example.com/posts/{postId}/relationships/author',
            ],
        ],
    ],
    [
        'class' => PostId::class,
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'postId',
        ],
        'urls' => [
            'self' => 'http://example.com/posts/{postId}',
            'relationships' => [
                Comment::class => 'http://example.com/posts/{postId}/relationships/comments',
            ],
        ],
    ],
    [
        'class' => User::class,
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'userId',
        ],
        'urls' => [
            'self' => 'http://example.com/users/{userId}',
        ],
    ],
    [
        'class' => UserId::class,
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'userId',
        ],
        'urls' => [
            'self' => 'http://example.com/users/{userId}',
        ],
    ],
    [
        'class' => Comment::class,
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'commentId',
        ],
        'urls' => [
            'self' => 'http://example.com/comments/{commentId}',
            'relationships' => [
                Post::class => 'http://example.com/posts/{postId}/relationships/comments',
            ],
        ],
    ],
    [
        'class' => CommentId::class,
        'alias' => '',
        'aliased_properties' => [],
        'hide_properties' => [],
        'id_properties' => [
            'commentId',
        ],
        'urls' => [
            'self' => 'http://example.com/comments/{commentId}',
        ],
    ],
];

$mapper = new Mapper($mappings);

//echo '<pre>'; print_r($apiMappingCollection); die();


$serializer = new JsonApiTransformer($mapper);
$serializer->setApiVersion('1.0');
$serializer->setSelfUrl('http://example.com/posts/9');
$serializer->setNextUrl('http://example.com/posts/10');
$serializer->addMeta('author', [['name' => 'Nil Portugués Calderó', 'email' => 'contact@nilportugues.com']]);

$response = (new Serializer($serializer))->serialize($post);

header('Content-Type: application/vnd.api+json; charset=utf-8');
echo json_encode(json_decode($response, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
