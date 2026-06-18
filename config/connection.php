<?php
 
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dayasatu";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Mengambil nama folder proyek secara otomatis
$project_folder = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));

// Jika file config ini ada di dalam folder config/, kita bersihkan agar kembali ke root
$project_folder = str_replace('/config', '', $project_folder); 

$base_url = $protocol . $host . $project_folder . "/";
?> 
 