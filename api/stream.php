<?php
$file = isset($_GET['file']) ? basename($_GET['file']) : '';
$path = '../uploads/videos/' . $file;

if (!file_exists($path) || !is_file($path)) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

$mime_type = "video/mp4";
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if ($ext === 'webm') $mime_type = "video/webm";
if ($ext === 'ogg' || $ext === 'ogv') $mime_type = "video/ogg";

$size = filesize($path);
$length = $size;
$start = 0;
$end = $size - 1;

header("Accept-Ranges: bytes");
header("Content-Type: " . $mime_type);

if (isset($_SERVER['HTTP_RANGE'])) {
    $c_end = $end;
    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    
    if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    if ($range == '-') {
        $c_start = $size - substr($range, 1);
    } else {
        $range = explode('-', $range);
        $c_start = $range[0];
        $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
    }
    
    $c_end = ($c_end > $end) ? $end : $c_end;
    
    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    $start = $c_start;
    $end = $c_end;
    $length = $end - $start + 1;
    
    header('HTTP/1.1 206 Partial Content');
    header("Content-Range: bytes $start-$end/$size");
} else {
    header('HTTP/1.1 200 OK');
}

header("Content-Length: " . $length);

$fp = @fopen($path, 'rb');
if (!$fp) {
    header("HTTP/1.1 500 Internal Server Error");
    exit;
}

fseek($fp, $start);
$buffer = 1024 * 8;
while (!feof($fp) && ($p = ftell($fp)) <= $end) {
    if ($p + $buffer > $end) {
        $buffer = $end - $p + 1;
    }
    set_time_limit(0);
    echo fread($fp, $buffer);
    flush();
}
fclose($fp);
?>