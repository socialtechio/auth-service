<?php


namespace Domains;


class User
{
    protected $firstname;
    protected $lastname;
    protected $nickname;
    protected $age;
    protected $password;

    /**
     * User constructor.
     * @param string $firstname
     * @param string $lastname
     * @param string $nickname
     * @param int $age
     * @param string $password
     */
    public function __construct(string $firstname, string $lastname, string $nickname, int $age, string $password)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->nickname = $nickname;
        $this->age = $age;
        $this->password = $password;
    }

    public function getId() : string
    {
        return $this->nickname;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'nickname' => $this->nickname,
            'age' => $this->age,
            'password' => $this->password
        ];
    }

    /**
     * @return string
     */
    public function toJSON() : string
    {
        return json_encode($this->toArray());
    }

}