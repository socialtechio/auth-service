<?php


namespace Services;


use Domains\User;

class JsonStorage
{
    protected $location;

    /**
     * JsonStorage constructor.
     * @param string $location
     */
    public function __construct(string $location)
    {
        $this->location = $location;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function save(User $user): bool
    {
        if (!is_dir($this->location)) {
            mkdir($this->location, 0755, true);
        }

        $data = $user->toJSON();
        $path = $this->generatePath($user->getId());

        if (file_put_contents($path, $data)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $id
     * @return null|User
     */
    public function loadById(string $id): ?User
    {
        $path = $this->generatePath($id);

        if (!file_exists($path)) {
            return null;
        }

        $data = json_decode(file_get_contents($path), true);

        return new User(
            $data['firstname'],
            $data['lastname'],
            $data['nickname'],
            $data['age'],
            $data['password']
        );
    }

    /**
     * @param string $id
     * @return string
     */
    protected function generatePath(string $id): string
    {
        return $this->location . DIRECTORY_SEPARATOR . $id . '.json';
    }
}