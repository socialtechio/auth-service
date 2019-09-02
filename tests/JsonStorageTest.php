<?php


namespace Tests;


use Domains\User;
use PHPUnit\Framework\TestCase;
use Services\JsonStorage;

class JsonStorageTest extends TestCase
{
    public function testSave()
    {
        $nickname = uniqid('test', false);

        $data = [
            'firstname' => 'Timur',
            'lastname' => 'Ataev',
            'nickname' => $nickname,
            'age' => 29,
            'password' => '123456',
        ];

        $user = new User(
            $data['firstname'],
            $data['lastname'],
            $data['nickname'],
            $data['age'],
            $data['password']
        );

        $location = getenv('USERS_STORAGE_LOCATION');
        $storage = new JsonStorage($location);
        $storage->save($user);

        $path = $location . DIRECTORY_SEPARATOR . "$nickname.json";

        $this->assertFileExists($path);
        $this->assertJsonStringEqualsJsonFile($path, json_encode($data));
    }

    public function testLoad()
    {
        $data = [
            'firstname' => 'Timur',
            'lastname' => 'Ataev',
            'nickname' => 'timmy',
            'age' => 29,
            'password' => '123456',
        ];

        $user = new User(
            $data['firstname'],
            $data['lastname'],
            $data['nickname'],
            $data['age'],
            $data['password']
        );

        $location = getenv('USERS_STORAGE_LOCATION');
        $storage = new JsonStorage($location);
        $storage->save($user);

        $loadedUser = $storage->loadById('timmy');

        $this->assertEquals($user, $loadedUser);
    }
}