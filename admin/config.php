<?php

$config['admin_lang'] = 'ru'; //язык админпанели

$config['style'] = 'admin/templates';
$config['style'] = 'admin/templates2';

//многи ко многим
$config['depend'] = array(
	//'shop_products'=>array('categories'=>'shop_products-categories'),
);

//зеркальные модули
$config['mirrors'] = array(
	//'articles'=>'news',
	'shop_products_special'=>'shop_products',
	'landing_items1'=>'landing_items',
	'landing_items2'=>'landing_items',
	'landing_items3'=>'landing_items',
);

//перечисление значений boolean массива $table к которым подвязаны классы для иконок
$config['boolean'] = array(
	'boolean','display','market','yandex_index','noindex'
);

//v1.4.87 - hypertext_images_styles
$config['hypertext_images_styles'] = array(
	1=>'Стандартный',
	2=>'Синий',
	3=>'Серый'
);

//icons https://feathericons.com/
/*
 * layers list package settings shopping-cart twitch users map map-in globe
 */
$modules_admin = array(
	array(
		'module'=>'index',
		'icon'=>'bar-chart-2',
	),
	/*array(
		'module'=>'pages',
		'image'=>'sitemap',
		'icon'=>'git-branch', //layers list package
	),
	array(
		'module'=>'news',
		'image'=>'news',
		'icon'=>'tablet', //file
	),
	array(
		'name'=>'gallery',
		'image'=>'gallery',
		'icon'=>'image',
		'module'=>array(
			array('module'=>'gallery'),
			array('module'=>'slider'),
			//'landing'   => 'landing',
			//'о нас'     => 'landing_items1',
			//'услуги'    =>'landing_items2',
			//'стоимости работ' =>'landing_items3'
		)
	),*/
	array(
		'module'=>'languages',
		'name'=>'dictionary',
		'image'=>'dictionary',
		'icon'=>'book-open',
	),
    /*array(
        'module'=>'wallpaper_languages',
        'name'=>'wallpaper_languages',
        'image'=>'dictionary',
        'icon'=>'book-open',
    ),*/
	/*array(
		'module'=>'feedback',
		'image'=>'feedback',
		'icon'=>'twitch',
	),*/
	array(
		'name'=>'catalog',
		'image'=>'catalog',
		'icon'=>'package',
		'module'=>array(
			//array('module'=>'shop_products_special'),
			//array('module'=>'shop_categories'),
			array('module'=>'shop_wallpaper'),
            array('module'=>'shop_wallpaper_categores'),
			array('module'=>'shop_wallpaper_bulk','name'=>'Массовая загрузка обоев'),
			//array('module'=>'styles',),
			//array('module'=>'colors',),
			array('module'=>'user_generation',),
			//array('module'=>'shop_brands'),
			//array('module'=>'shop_parameters'),
			//array('module'=>'shop_reviews'),
			//array('module'=>'shop_branches'),
		)
	),
	/*array(
		'name'=>'geo',
		'image'=>'sitemap',
		'icon'=>'globe',
		'module'=>array(
			array('module'=>'geo_cities'),
			array('module'=>'geo_regions'),
			array('module'=>'geo_countries'),
		),
	),
	array(
		'name'=>'synchronization',
		'image'=>'synchro',
		'icon'=>'download-cloud',
		'module'=>array(
			array(
				'module'=>'shop_export',
				'name'=>'export'
			),
			array(
				'module'=>'shop_import',
				'name'=>'import'
			),
			array(
				'module'=>'shop_upload_images',
				'name'=>'upload_images'
			),
			//'импорт категорий'=>'shop_categories_import',
		),
	),
	array(
		'name'=>'shop',
		'image'=>'shop',
		'icon'=>'shopping-cart',
		'module'=>array(
			array('module'=>'orders'),
			array('module'=>'order_types'),
			array('module'=>'order_deliveries'),
		),
		//'order_payments'	=> 'order_payments',
	),*/

	array(
		'module'=>'users',
		'image'=>'users',
		'icon'=>'users',
	),
	array(
		'module'=>'packages',
		'name'=>'packages',
		'image'=>'package',
		'icon'=>'package',
	),
	array(
		'module'=>'user_payment',
		'name'=>'user_payment',
	),

	/*array(
		'name'=>'users',
		'image'=>'users',
		'icon'=>'users',
		'module'=>array(
			array('module'=>'users',),
			array('module'=>'user_types',),
			array('module'=>'user_fields',),
			array('module'=>'user_socials',),
		),
	),*/

	/*array(
		'name'=>'subscribe',
		'image'=>'subscribe',
		'icon'=>'mail',//at-sign
		'module'=>array(
			array('module'=>'subscribers',),
			array('module'=>'subscribe_letters',),
		),
	),*/

	array(
		'name'=>'config',
		'image'=>'settings',
		'icon'=>'settings',
		'module'=>array(
			array('module'=>'config',),
			//array('module'=>'letter_templates',),
			//array('module'=>'letters',),
			array('module'=>'logs',),
			//array('module'=>'_migrations',),
		),
	),
/*
	array(
		'name'=>'design',
		'image'=>'design',
		'icon'=>'pen-tool',//monitor',
		'module'=>array(
			array('module'=>'template_css',),
			array('module'=>'template_images',),
			array('module'=>'template_includes',),
			array('module'=>'template_scripts',),
		),
	),
*/
	$config['style']=='admin/template' ? array(
		'name'=>'backup',
		'image'=>'archive',
		'icon'=>'archive',//download-cloud',
		'module'=>array(
			array('module'=>'backup',),
			array('module'=>'restore',)
		),
	):
	array(
		'module'=>'dumper',
		'name'=>'backup',
		'image'=>'archive',
		'icon'=>'archive',//download-cloud',
	),
/*
	array(
		'name'=>'SEO',
		'image'=>'seo',
		'icon'=>'',
		'module'=>array(
			array('module'=>'redirects',),
			array(
				'module'=>'seo_robots',
				'name'=>'robots.txt'
			),
			array(
				'module'=>'seo_htaccess',
				'name'=>'.htaccess'
			)
			//'sitemap.xml'	=> 'seo_sitemap',
			//сео модули, по умолчанию закоментированы (используются в основном в проектах Пестрякова)
			//'links'		=> 'seo_links',
			//'pages'		=> 'seo_pages',
			//'import'		=> 'seo_links_import',
			//'export'		=> 'seo_links_export',
		),
	),*/

);

//v1.3.1
$config['sources']['admin']=array(
	'/plugins/jquery/jquery-1.11.3.min.js',
	'/plugins/jquery/jquery.form2.js',
	'/plugins/jquery/jquery.uploader.js',
	'/plugins/jquery/jquery-ui-1.11.4.custom/jquery-ui.min.js',
	'/plugins/jquery/jquery-ui-1.11.4.custom/jquery-ui.min.css',
	'/plugins/jquery/i18n/jquery.ui.datepicker-{localization}.js',
	'/plugins/tinymce_4.3.11/tinymce.min.js',
	//'/plugins/tinymce_5.0.4/tinymce.min.js',
	//'/plugins/tinymce_5.0.4/jquery.tinymce.min.js',
	'/plugins/highslide/highslide-with-gallery.js',
	'/templates/scripts/highslide.js',
	'/plugins/highslide/highslide.css',
	'/admin/templates/css/reset.css',
	'/admin/templates/css/style.css?',
	'/admin/templates/js/dnd.js',
	'/admin/templates/js/script.js?'
);

//v1.4.7
$config['sources']['admin_top' ] = array(
	//Plugin styles
	'/'.$config['style'].'/vendors/bundle.css',
	//Slick
	'/'.$config['style'].'/vendors/slick/slick.css',
	'/'.$config['style'].'/vendors/slick/slick-theme.css',
	//vendors/vmap/jqvmap.min.css
	//App styles
	'/'.$config['style'].'/assets/css/app.min.css',
	'/'.$config['style'].'/assets/css/modify.css?'
);
$config['sources']['admin_bottom'] = array(
	//App scripts -->
	'/'.$config['style'].'/assets/js/app.min.js',

	'/'.$config['style'].'/vendors/lightbox/magnific-popup.css',
	'/'.$config['style'].'/vendors/lightbox/jquery.magnific-popup.min.js',

	'/'.$config['style'].'/vendors/select2/css/select2.min.css',
	'/'.$config['style'].'/vendors/select2/js/select2.min.js',

	'/'.$config['style'].'/vendors/clockpicker/bootstrap-clockpicker.min.css',
	'/'.$config['style'].'/vendors/clockpicker/bootstrap-clockpicker.min.js',

	'/'.$config['style'].'/vendors/datepicker/daterangepicker.css',
	'/'.$config['style'].'/vendors/datepicker/daterangepicker.js',

	'/'.$config['style'].'/vendors/select2/js/select2.min.js',
	'/'.$config['style'].'/vendors/select2/js/i18n/ru.js',
	'/'.$config['style'].'/vendors/select2/css/select2.min.css',

	'/plugins/jquery/jquery.form2.js',
	'/plugins/jquery/jquery.uploader.js',
	'/plugins/tinymce_4.3.11/tinymce.min.js',
	'/'.$config['style'].'/js/script.js?',
	'/'.$config['style'].'/js/file.js?'
);