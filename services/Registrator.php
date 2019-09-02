<?php


namespace Services;


use Domains\User;

class Registrator
{
    protected $storage;
    protected $passwordGenerator;

    /**
     * Registrator constructor.
     * @param JsonStorage $storage
     * @param PasswordGenerator $passwordGenerator
     */
    public function __construct(JsonStorage $storage, PasswordGenerator $passwordGenerator)
    {
        $this->storage = $storage;
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function process(array $data): bool
    {
        // TODO needs data validation

        if ($user = $this->storage->loadById($data['nickname'])) {
            return false;
        }

        $user = new User(
            $data['firstname'],
            $data['lastname'],
            $data['nickname'],
            $data['age'],
            $this->passwordGenerator->generate($data['password'])
        );

        return $this->storage->save($user);
    }

}