<?php
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
if (file_exists(dirname(__DIR__) . '/.env')) $dotenv->safeLoad();
require dirname(__DIR__) . '/routes.php';
