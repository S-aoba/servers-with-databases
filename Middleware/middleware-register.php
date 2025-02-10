<?php

return [
  'global'=>[
      \Middleware\SessionsSetupMiddleware::class,
      \Middleware\MiddlewareA::class,
      \Middleware\MiddlewareB::class,
      \Middleware\MiddlewareC::class,
      \Middleware\HttpLoggingMiddleware::class
  ],
  'aliases'=>[
        'auth'=>\Middleware\AuthenticatedMiddleware::class,
        'guest'=>\Middleware\GuestMiddleware::class,
    ]
];