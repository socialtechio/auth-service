<?php


namespace Services;


class PasswordGenerator
{
    protected $salt;

    /**
     * PasswordGenerator constructor.
     * @param string $salt
     */
    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param string $password
     * @return string
     */
    public function generate(string $password) : string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['salt' => $this->salt]);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify(string $password, string $hash) : bool
    {
        return password_verify($password, $hash);
    }
}
