<?php

define('ROOT_DIR', dirname(__FILE__).'/../../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');
include_once (ROOT_DIR.'functions/array_func.php');
include_once (ROOT_DIR.'functions/file_func.php');

define('MIGRATION_UP', 'up');
define('MIGRATION_DOWN', 'down');

// хелперы
function echo_message($message)
{
    echo $message . '<br>';
}

function echo_error_message($message)
{
    echo '<div style="color: darkred;">'. $message .'</div>' . '<br>';
}

function echo_success_message($message)
{
    echo '<div style="color: darkgreen;">'. $message .'</div>' . '<br>';
}

$request_url = explode('?',$_SERVER['REQUEST_URI'],2); //dd($request_url);
$u = explode('/',$request_url[0]);
// игнорируем _ в имени пути
$u = array_filter($u, function ($v) {
    return !in_array($v, ['_']);
});

mysql_connect_db();

$table = '_migrations';

function checkTableExist($table)
{
    global $config;
    $row = mysql_select('
        SELECT * 
        FROM information_schema.tables
        WHERE table_schema = \'' . $config['mysql_database'] . '\' 
            AND table_name = \'' . $table . '\'
        LIMIT 1
    ', 'row');
    //
    return $row ?: null;
}

if (!checkTableExist($table)) {
    mysql_fn(
        'query',
        'CREATE TABLE `' . $table . '` ( 
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT 
            , `created_at` DATETIME NULL DEFAULT NULL
            , `ip` VARCHAR(32) NOT NULL 
            , `name` VARCHAR(255) NOT NULL 
            , PRIMARY KEY (`id`) 
        ) ENGINE = InnoDB
    ');
}

//
$migrated_items = mysql_select('SELECT `name` FROM `' . $table . '` ORDER BY `name` ASC, `created_at` ASC', 'rows');
$migrated_names = array_values(array_pluck($migrated_items, 'name'));
//
$migration_path = $table;
$migration_files = scandir2($migration_path);
sort($migration_files);

// если надо, откатываем N миграций
if (isset($_GET['rollback'])) {
    require '_rollback.php';
    exit;
}

// если надо, создаем миграции
if (isset($_GET['create'])) {
    require '_create.php';
    exit;
}

require '_migrate.php';
