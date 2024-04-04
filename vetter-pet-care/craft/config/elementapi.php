<?php
namespace Craft;

return [
    'endpoints' => [
        'users/<friendlyName:\s+>.json' => [
            'elementType' => 'User',
            'transformer' => function(UserModel $user) {
                return [
                    'email' => $user->email,
                    'phone' => $user->cellPhone,
                    'zip' => $user->zip,
                    'jsonUrl' => UrlHelper::getUrl("users/{$user->id}.json"),
                ];
            },
        ],
    ]
];
