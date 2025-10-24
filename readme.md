# abc-cms

## Documentation
смотреть в папке  
/_documentation/

## Installation

**_config2.php**  
Указать соединение с базой:  
$config['mysql_server'] = '';  
$config['mysql_username'] = '';  
$config['mysql_password'] = '';  
$config['mysql_database'] = '';

Дамп лежит в папке /admin/backup/

## Gulp
**установка node.js**  
npm install  

**собрать папку asssets (js и css)**  
npm run build  

**пересобрать картинки asssets/imgs**  
npx gulp imagemin

**обновлять asssets на лету (js и css)**   
npx gulp watch

## Vendor
**Конвертация картинок в webp**  
$ composer require rosell-dk/webp-convert

**Excel**  
$ composer require box/spout

**mpdf**
$ composer require mpdf/mpdf

**архиватор**
$ composer require chamilo/pclzip