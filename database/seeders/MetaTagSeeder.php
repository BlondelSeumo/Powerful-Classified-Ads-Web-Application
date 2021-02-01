<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetaTagSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$entries = [
			[
				'translation_lang' => 'en',
				'translation_of'   => '1',
				'page'             => 'home',
				'title'            => '{app_name} - Geo Classified Ads CMS',
				'description'      => 'Sell and Buy products and services on {app_name} in Minutes {country}. Free ads in {country}. Looking for a product or service - {country}',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '2',
				'page'             => 'register',
				'title'            => 'Sign Up - {app_name}',
				'description'      => 'Sign Up on {app_name}',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '3',
				'page'             => 'login',
				'title'            => 'Login - {app_name}',
				'description'      => 'Log in to {app_name}',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '4',
				'page'             => 'create',
				'title'            => 'Post Free Ads',
				'description'      => 'Post Free Ads - {country}.',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '5',
				'page'             => 'countries',
				'title'            => 'Free Local Classified Ads in the World',
				'description'      => 'Welcome to {app_name} : 100% Free Ads Classified. Sell and buy near you. Simple, fast and efficient.',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '6',
				'page'             => 'contact',
				'title'            => 'Contact Us - {app_name}',
				'description'      => 'Contact Us - {app_name}',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '7',
				'page'             => 'sitemap',
				'title'            => 'Sitemap {app_name} - {country}',
				'description'      => 'Sitemap {app_name} - {country}. 100% Free Ads Classified',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '8',
				'page'             => 'password',
				'title'            => 'Lost your password? - {app_name}',
				'description'      => 'Lost your password? - {app_name}',
				'keywords'         => '{app_name}, {country}, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '9',
				'page'             => 'pricing',
				'title'            => 'Pricing - {app_name}',
				'description'      => 'Pricing - {app_name}',
				'keywords'         => '{app_name}, {country}, pricing, free ads, classified, ads, script, app, premium ads',
				'active'           => '1',
			],
		];
		
		foreach ($entries as $entry) {
			$entryId = DB::table('meta_tags')->insertGetId($entry);
			DB::table('meta_tags')->where('id', $entryId)->update(['translation_of' => $entryId]);
		}
	}
}
