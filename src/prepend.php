<?php

if (($_ENV['HTTP_FLY_CLIENT_IP'] ?? '') !== '' && ($_ENV['FLY_APP_NAME'] ?? '') !== '') {
    $_ENV['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] = $_ENV['HTTP_FLY_CLIENT_IP'];
    $_ENV['REMOTE_HOST'] = $_SERVER['REMOTE_HOST'] = $_ENV['HTTP_FLY_CLIENT_IP'];
    unset($_ENV['HTTP_FLY_CLIENT_IP'], $_SERVER['HTTP_FLY_CLIENT_IP']);
}
