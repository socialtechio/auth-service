<?php


namespace Tests;


use PHPUnit\Framework\TestCase;
use Services\JsonStorage;
use Services\PasswordGenerator;
use Services\Registrator;

class UserRegistrationTest extends TestCase
{
    public function testSuccessRegistration()
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

        $result = $registrator->process($data);
        $this->assertTrue($result);
        $this->assertFileExists($location . DIRECTORY_SEPARATOR . "$nickname.json");
    }

    public function testFailRegistration()
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

        $firstRegistration = $registrator->process($data);
        $this->assertTrue($firstRegistration);
        $secondRegistration = $registrator->process($data);
        $this->assertFalse($secondRegistration);
    }
}