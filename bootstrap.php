<?php

require __DIR__ . '/vendor/autoload.php';

use Domains\User;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Services\Authenticator;
use Services\DataTracker;
use Services\JsonStorage;
use Services\PasswordGenerator;
use Services\Registrator;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use SocialTech\SlowStorage;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$app = AppFactory::create();

/**
 * curl -X POST \
    http://127.0.0.1/registration \
    -H 'Content-Type: application/json' \
    -d '{"firstname": "Timmy", "lastname": "Ataev", "nickname": "timmy1", "age": 24, "password": "123456"}'
 */
$app->group('/registration', static function (RouteCollectorProxy $group) {

    $group->map(['GET', 'DELETE', 'PUT', 'PATCH', 'OPTIONS', 'HEAD'], '', static function (Request $request, Response $response) {
        return $response->withStatus(405);
    });

    $group->post('', static function (Request $request, Response $response, $args) {
        if ($request->getHeaderLine('Content-Type') === 'application/json') {
            $data = json_decode($request->getBody()->getContents(), true);

            $user = new User(
                $data['firstname'],
                $data['lastname'],
                $data['nickname'],
                (int) $data['age'],
                $data['password']
            );

            $storage = new JsonStorage(getenv('USERS_STORAGE_LOCATION'));
            $passwordGenerator = new PasswordGenerator(getenv('PASSWORD_SALT'));
            $registration = new Registrator($storage, $passwordGenerator);

            if ($registration->process($data)) {
                $response = $response->withStatus(201);
                $response->getBody()->write($user->toJSON());
            } else {
                $response = $response->withStatus(400);
            }
        }

        return $response;
    });
});

/**
 * curl -X POST \
    http://127.0.0.1/authentication \
    -H 'Content-Type: application/json' \
    -d '{ "nickname": "timmy1", "password": "123456"}'
 */
$app->post('/authentication', static function (Request $request, Response $response, array $args) {
    if ($request->getHeaderLine('Content-Type') === 'application/json') {
        $data = json_decode($request->getBody()->getContents(), true);

        $storage = new JsonStorage(getenv('USERS_STORAGE_LOCATION'));
        $passwordGenerator = new PasswordGenerator(getenv('PASSWORD_SALT'));

        $authenticator = new Authenticator($storage, $passwordGenerator);
        if ($authenticator->process($data)) {
            $response = $response->withStatus(200);
            $response->getBody()->write(JWT::encode(['id' => $data['nickname']], getenv('JWT_SECRET')));
        } else {
            $response = $response->withStatus(400);
        }
    }

    return $response;
});

/**
 * curl -X POST \
    http://127.0.0.1/tracking \
    -H 'Content-Type: application/json' \
    -H 'X-JWT: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6InRpbW15MSJ9.VuQ7wGWwcVsiQzOmH5lANMeULcfzWr4Ea52iaUQA1K0' \
    -d '{ "source_label": "search_page"}'
 */
$app->post('/tracking', static function (Request $request, Response $response, array $args) {
    if ($request->getHeaderLine('Content-Type') === 'application/json') {
        $data = json_decode($request->getBody()->getContents(), true);
        $headerJWT = $request->getHeaderLine('X-JWT');
        $cookieJWT = $request->getCookieParams()['X-JWT'];
        $authorization = !empty($headerJWT) ? $headerJWT : $cookieJWT;

        if (!$authorization) {
            $authorization = JWT::encode(['id' => uniqid('id', true)], getenv('JWT_SECRET'));
            setcookie('X-JWT', $authorization);
        }

        $jwtInfo = (array) JWT::decode($authorization, getenv('JWT_SECRET'), ['HS256']);
        $data['id_user'] = $jwtInfo['id'];

        $dataTracker = new DataTracker(new SlowStorage());
        $result = $dataTracker->process($data);
        $response = $response->withStatus(200);
        $response->getBody()->write(json_encode($result));
    }

    return $response;
});