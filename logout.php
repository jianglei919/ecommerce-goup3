<?php
session_start();
session_unset();    // 清除所有 session 变量
session_destroy();  // 销毁 session

// 可选：清除购物车 cookie（如果你使用了）
// setcookie('cart', '', time() - 3600, '/');

// 返回首页或登录页
header("Location: login.php");
exit();