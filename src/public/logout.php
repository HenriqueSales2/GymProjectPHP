<?php
session_start();
session_destroy();
header("Location: /GymProjectPHP/src/public/login.php"); // redireciona novamente a tela de login
exit();