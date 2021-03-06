<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
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
				'translation_lang'      => 'en',
				'translation_of'        => '1',
				'name'                  => 'Regular List',
				'short_name'            => 'Free',
				'ribbon'                => 'red',
				'has_badge'             => '1',
				'price'                 => '0.00',
				'currency_code'         => 'USD',
				'promo_duration'        => null,
				'duration'              => null,
				'pictures_limit'        => null,
				'description'           => null,
				'facebook_ads_duration' => '0',
				'google_ads_duration'   => '0',
				'twitter_ads_duration'  => '0',
				'linkedin_ads_duration' => '0',
				'recommended'           => '0',
				'parent_id'             => null,
				'lft'                   => '2',
				'rgt'                   => '3',
				'depth'                 => '0',
				'active'                => '1',
			],
			[
				'translation_lang'      => 'en',
				'translation_of'        => '2',
				'name'                  => 'Top page Ad',
				'short_name'            => 'Premium',
				'ribbon'                => 'orange',
				'has_badge'             => '1',
				'price'                 => '7.50',
				'currency_code'         => 'USD',
				'promo_duration'        => '7',
				'duration'              => '60',
				'pictures_limit'        => '10',
				'description'           => 'Featured on the homepage
Featured in the category',
				'facebook_ads_duration' => '0',
				'google_ads_duration'   => '0',
				'twitter_ads_duration'  => '0',
				'linkedin_ads_duration' => '0',
				'recommended'           => '1',
				'parent_id'             => null,
				'lft'                   => '4',
				'rgt'                   => '5',
				'depth'                 => '0',
				'active'                => '1',
			],
			[
				'translation_lang'      => 'en',
				'translation_of'        => '3',
				'name'                  => 'Top page Ad+',
				'short_name'            => 'Premium+',
				'ribbon'                => 'green',
				'has_badge'             => '1',
				'price'                 => '9.00',
				'currency_code'         => 'USD',
				'promo_duration'        => '30',
				'duration'              => '120',
				'pictures_limit'        => '15',
				'description'           => 'Featured on the homepage
Featured in the category',
				'facebook_ads_duration' => '0',
				'google_ads_duration'   => '0',
				'twitter_ads_duration'  => '0',
				'linkedin_ads_duration' => '0',
				'recommended'           => '0',
				'parent_id'             => null,
				'lft'                   => '6',
				'rgt'                   => '7',
				'depth'                 => '0',
				'active'                => '1',
			],
		];
		
		foreach ($entries as $entry) {
			$entryId = DB::table('packages')->insertGetId($entry);
			DB::table('packages')->where('id', $entryId)->update(['translation_of' => $entryId]);
		}
	}
}
