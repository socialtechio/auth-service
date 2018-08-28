<?php
require_once(__DIR__ . '/../vendor/autoload.php');

$s = new \SocialTech\SlowStorage();

$s->store(__DIR__ . '/../storage/test', 'test');

echo 'Vendor is installed & file is writable. <h3>Let\'s Start!)</h3>';
