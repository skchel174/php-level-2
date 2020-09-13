<?php

return [
    'default' => [
        'controller' => 'main',
        'action' => 'index'
    ],
    'main' => [
        'controller' => 'main',
        'action' => ['index']
    ],
    'catalog' => [
        'controller' => 'catalog',
        'action' => ['index', 'append']
    ],
    'product' => [
        'controller' => 'product',
        'action' => ['index', 'add']
    ],
    'user' => [
        'controller' => 'user',
        'action' => ['authorization', 'signin', 'signup', 'signout', 'profile', 'orders']
    ],
    'cart' => [
        'controller' => 'cart',
        'action' => ['index', 'increase', 'decrease', 'remove', 'order']
    ],
    'admin' => [
        'controller' => 'admin',
        'action' => ['index', 'authorization', 'signin', 'orders', 'status']
    ]
];