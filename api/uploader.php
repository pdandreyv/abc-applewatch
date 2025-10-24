<?php

//not_visible
// Загрузчик для HTML5 uploader в админке: возвращает plain temp-id
/*
 * v1.0.0
 */

// Ответ всегда текстом, без JSON-обёртки
header('Content-Type: text/plain; charset=UTF-8');

// Приходящий файл во временное хранилище
$file = @$_FILES['temp'];
if ($file && is_array($file) && isset($file['tmp_name'])) {
    // Генерируем временную папку
    $tempId = rand(1000000, 9999999);
    // Приводим имя файла к безопасному виду
    $name = isset($file['name']) ? strtolower(preg_replace('~[^-a-z0-9_.]+~u', '-', iconv('UTF-8','ASCII//TRANSLIT//IGNORE', $file['name']))) : 'file';
    $name = trim(preg_replace('~[-]+~u','-',$name),'-');
    $name = trim($name,'.');

    $path = ROOT_DIR.'files/temp/'.$tempId.'/';
    if (is_dir($path) || mkdir($path,0755,true)) {
        if (@move_uploaded_file($file['tmp_name'], $path.$name)) {
            echo $tempId;
            exit;
        }
    }
}

// Фоллбек: ошибка
http_response_code(500);
echo '0';
exit;


