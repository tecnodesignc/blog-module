<?php

return [
    'blog.posts' => [
        'index' => 'blog::posts.list resource',
        'create' => 'blog::posts.create resource',
        'edit' => 'blog::posts.edit resource',
        'destroy' => 'blog::posts.destroy resource',
        'manage' => 'page::posts.manage resource',
    ],
    'blog.categories' => [
        'index' => 'blog::categories.list resource',
        'create' => 'blog::categories.create resource',
        'edit' => 'blog::categories.edit resource',
        'destroy' => 'blog::categories.destroy resource',
        'manage' => 'blog::categories.manage resource',
    ],
// append


];
