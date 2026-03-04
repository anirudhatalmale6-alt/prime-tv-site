<?php
function loadConfig() {
    $configFile = __DIR__ . '/../data/config.json';
    if (!file_exists($configFile)) {
        die('Configuration file not found.');
    }
    return json_decode(file_get_contents($configFile), true);
}

function saveConfig($config) {
    $configFile = __DIR__ . '/../data/config.json';
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function waLink($number, $text = '') {
    $url = 'https://wa.me/' . $number;
    if ($text) {
        $url .= '?text=' . urlencode($text);
    }
    return $url;
}
