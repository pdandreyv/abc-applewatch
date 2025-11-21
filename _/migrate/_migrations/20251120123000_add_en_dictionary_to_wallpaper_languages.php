<?php

// Словарь в формате JSON для AJAX-редактора (wallpaper_dictionary)
$data = array(
	// General
	"generic_loading" => "Loading...",
	"generic_processing" => "Processing...",
	"generic_success" => "Success",
	"generic_ok" => "OK",
	"generic_cancel" => "Cancel",
	"generic_buy" => "Buy",
	"generic_unlock" => "Unlock",
	"generic_premium" => "Premium",
	"generic_pay" => "Pay",
	"generic_no_products" => "No products available",
	"generic_failed_to_load" => "Failed to load",
	// Watchface overlays
	"watchface_mock_temperature" => "26°",
	// Home / ContentView
	"home_faces_title" => "Faces \\nLibrary",
	"home_ai_title" => "AI Watch \\nFaces",
	"home_store_title" => "Store",
	"home_store_subtitle" => "· Unlock All Faces\\n· AI gens",
	"home_credits_single" => "1 gen available",
	"home_credits_multi" => "%d gens available",
	"home_free_count" => "%d avail",
	"home_total_count" => "%d total",
	// Generation Screen
	"generation_create_title" => "Create",
	"generation_gens_available" => "Gens Available",
	"generation_my_faces" => "My AI Faces",
	"generation_nav_title" => "AI Watch Faces",
	"generation_describe_title" => "Describe Face",
	"generation_prompt_placeholder" => "Describe your watch face using dictation...",
	"generation_generate_cta" => "Generate",
	"generation_no_credits_title" => "No Credits",
	"generation_no_credits_message" => "You need credits to generate wallpapers.",
	"generation_buy_credits" => "Buy Credits",
	"generation_network_error" => "Network error",
	// Store
	"store_nav_title" => "Store",
	"store_buy_gens" => "Buy Gens",
	"store_unlock_faces_title" => "Unlock\\nFaces\\nLibrary",
	"store_min_price" => "From %@",
	"store_unlock_price" => "%@ for all",
	"store_unlock_price_fallback" => "$4.99 for all",
	// Buy Gens
	"buy_gens_success_title" => "Purchase Successful!",
	"buy_gens_success_message" => "Credits added to your account",
	"buy_gens_package_count" => "%d Gens",
	// Unlock Library
	"unlock_nav_title" => "Unlock Library",
	"unlock_cta" => "Unlock",
	"unlock_price_fallback_single" => "$3.99",
	// Faces Library
	"faces_nav_title" => "Faces Library",
	// Settings
	"settings_nav_title" => "Account",
	"settings_user_placeholder" => "User",
	"settings_language_fallback" => "English",
	"settings_language_title" => "Language",
	"settings_purchases_header" => "Purchases",
	"settings_gens_available" => "Gens Available",
	"settings_faces_library" => "Faces Library",
	"settings_language_nav" => "Language",
	// My Generations
	"my_generations_nav_title" => "My Generations",
	"my_generations_empty_title" => "No generations yet",
	"my_generations_empty_subtitle" => "Create your first AI wallpaper",
	"my_generations_failed_to_load" => "Failed to load",
	"my_generations_delete_title" => "Delete?",
	// Downloads
	"download_instruction_message" => "Watch Face Saved to Photos\\nSet it on your Apple Watch via iPhone",
	"download_alert_title" => "Wallpaper Saved!",
	"generic_deleted" => "Deleted",
	"generic_downloaded" => "Downloaded",
	"generic_delete" => "Delete",
	"generation_result_nav_title" => "Result",
	"wallpapers_empty_title" => "No wallpapers",
	"wallpapers_premium_label" => "Premium",
	"download_instruction_short" => "File saved in app. To set as watch face, open Watch app on iPhone.",
	// Paywall
	"paywall_title" => "Premium Wallpaper",
	"paywall_description" => "Unlock this wallpaper",
	"paywall_buy_single" => "Buy for %@",
	"paywall_unlock_all" => "Unlock All Wallpapers",
	"paywall_unlock_all_price" => "One-time $4.99",
);

$dict = json_encode($data, JSON_UNESCAPED_UNICODE);

return [
	MIGRATION_UP => [
		// создать запись en при отсутствии
		"INSERT INTO `wallpaper_languages` (`created_at`,`updated_at`,`sort`,`display`,`name`,`localization`,`dictionary`)
		 SELECT NOW(), NOW(), 0, 1, 'English', 'en', '".mysql_res($dict)."'
		 FROM DUAL
		 WHERE NOT EXISTS (SELECT 1 FROM `wallpaper_languages` WHERE `localization`='en');",
		// обновить словарь en (JSON)
		"UPDATE `wallpaper_languages` SET `dictionary`='".mysql_res($dict)."', `updated_at`=NOW() WHERE `localization`='en';",
	],
	MIGRATION_DOWN => [
		"UPDATE `wallpaper_languages` SET `dictionary`=NULL WHERE `localization`='en';",
	],
];


