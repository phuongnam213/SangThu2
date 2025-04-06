<?php

require_once 'config.php'; // Gọi file cấu hình để lấy các thông tin kết nối

try {
    // Tạo kết nối với cơ sở dữ liệu
    $dsn = DBDRIVER . ":host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8";
    $db = new PDO($dsn, DBUSER, DBPASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Hàm thực hiện truy vấn trả về nhiều dòng
if (!function_exists('db_query')) {
    function db_query($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Hàm thực hiện truy vấn trả về một dòng
if (!function_exists('db_query_one')) {
    function db_query_one($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Hàm thực hiện các truy vấn không trả về dữ liệu (INSERT, UPDATE, DELETE)
if (!function_exists('db_execute')) {
    function db_execute($query, $params = []) {
        global $db;
        $stmt = $db->prepare($query);
        return $stmt->execute($params);
    }
}
?>
