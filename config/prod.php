<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

require __DIR__.'/security.php';

// Parsing request json if needed
$app->before(function (Request $request) use ($app) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array()); 
    }
});

$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SecurityJWTServiceProvider());

$app->post('/login', function(Request $request) use ($app){
    //$vars = json_decode($request->getContent(), true);
    try {
        if ('' === $request->request->get('_username') || '' === $request->request->get('_password')) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $request->request->get('_username')));
        }
        /**
         * @var $user User
         */
        $user = $app['users']->loadUserByUsername($request->request->get('_username'));
        if (! $app['security.encoder.digest']->isPasswordValid($user->getPassword(), $request->request->get('_password'), '')) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $request->request->get('_username')));
        } else {
            $response = [
                'success' => true,
                'token' => $app['security.jwt.encoder']->encode(['name' => $user->getUsername()]),
            ];
        }
    } catch (UsernameNotFoundException $e) {
        $response = [
            'success' => false,
            'error' => 'Invalid credentials : '.$e->getMessage(),
        ];
    }
    return $app->json($response, ($response['success'] == true ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST));
});
