<?php

// именуйте миграции по правилу YYYYMMDDHis_name, что бы корректно откатывать миграции

return [
    // сюда пишем массив запросов которые необходимо накатить
    MIGRATION_UP => [
        "TRUNCATE `feedback`","
		TRUNCATE `gallery`","
		TRUNCATE `landing`","
		TRUNCATE `landing_items`","
		TRUNCATE `letters`","
		TRUNCATE `logs`","
		TRUNCATE `news`","
		TRUNCATE `orders`","
		TRUNCATE `order_deliveries`","
		TRUNCATE `order_payments`","
		TRUNCATE `order_types`","
		TRUNCATE `redirects`","
		TRUNCATE `seo_links`","
		TRUNCATE `seo_links-pages`","
		TRUNCATE `seo_pages`","
		TRUNCATE `shop_branches`","
		TRUNCATE `shop_brands`","
		TRUNCATE `shop_categories`","
		TRUNCATE `shop_items`","
		TRUNCATE `shop_parameters`","
		TRUNCATE `shop_products`","
		TRUNCATE `shop_products-categories`","
		TRUNCATE `shop_reviews`","
		TRUNCATE `slider`","
		TRUNCATE `subscribers`","
		TRUNCATE `subscribe_letters`","
		TRUNCATE `user_fields`","
		TRUNCATE `user_socials`"
    ],
    // сюда пишем массив запросов которые необходимо выполнить когда потребуется отменить эту миграцию
    MIGRATION_DOWN => [
        //"",
    ]
];
