<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostTypeSeeder extends Seeder
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
				'name'             => 'Private individual',
				'lft'              => null,
				'rgt'              => null,
				'depth'            => null,
				'active'           => '1',
			],
			[
				'translation_lang' => 'en',
				'translation_of'   => '2',
				'name'             => 'Professional',
				'lft'              => null,
				'rgt'              => null,
				'depth'            => null,
				'active'           => '1',
			],
		];
		
		foreach ($entries as $entry) {
			$entryId = DB::table('post_types')->insertGetId($entry);
			DB::table('post_types')->where('id', $entryId)->update(['translation_of' => $entryId]);
		}
	}
}
