<?php


namespace Tests;


use PHPUnit\Framework\TestCase;
use Services\Authenticator;
use Services\JsonStorage;
use Services\PasswordGenerator;
use Services\Registrator;

class AuthenticatorTest extends TestCase
{
    protected $nickname;

    public function setUp(): void
    {
        $location = getenv('USERS_STORAGE_LOCATION');
        $salt = getenv('PASSWORD_SALT');
        $storage = new JsonStorage($location);
        $passwordGenerator = new PasswordGenerator($salt);
        $registrator = new Registrator($storage, $passwordGenerator);
        $nickname = uniqid('test', false);

        $data = [
            'firstname' => 'Timur',
            'lastname' => 'Ataev',
            'nickname' => $nickname,
            'age' => 29,
            'password' => '123456',
        ];

        $registrator->process($data);

        $this->nickname = $nickname;
    }

    public function testSuccessAuth()
    {
        $location = getenv('USERS_STORAGE_LOCATION');
        $salt = getenv('PASSWORD_SALT');
        $storage = new JsonStorage($location);
        $passwordGenerator = new PasswordGenerator($salt);
        $authenticator = new Authenticator($storage, $passwordGenerator);

        $result = $authenticator->process(['nickname' => $this->nickname, 'password' => '123456']);

        $this->assertTrue($result);
    }

    public function testFailAuth()
    {
        $location = getenv('USERS_STORAGE_LOCATION');
        $salt = getenv('PASSWORD_SALT');
        $storage = new JsonStorage($location);
        $passwordGenerator = new PasswordGenerator($salt);
        $authenticator = new Authenticator($storage, $passwordGenerator);

        $result = $authenticator->process(['nickname' => $this->nickname, 'password' => '1234567']);

        $this->assertFalse($result);
    }
}