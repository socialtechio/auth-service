<?php


namespace Services;


class Authenticator
{
    protected $storage;
    protected $passwordGenerator;

    public function __construct(JsonStorage $storage, PasswordGenerator $passwordGenerator)
    {
        $this->storage = $storage;
        $this->passwordGenerator = $passwordGenerator;
    }

    public function process(array $data)
    {
        // TODO needs data validation

        if (!$user = $this->storage->loadById($data['nickname'])) {
            return false;
        }

        $userPassword = $user->toArray()['password'];
        $inputPassword = $data['password'];

        return $this->passwordGenerator->verify($inputPassword, $userPassword);
    }
}