<?php
echo "Base URL debería ser: http://localhost:8080/<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "DOCKER_ENV: " . getenv('DOCKER_ENV') . "<br>";
