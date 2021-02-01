<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryFieldSeeder extends Seeder
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
				'category_id'               => '1',
				'field_id'                  => '1',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '2',
				'rgt'                       => '3',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '2',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '4',
				'rgt'                       => '5',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '3',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '6',
				'rgt'                       => '7',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '4',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '14',
				'rgt'                       => '15',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '5',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '8',
				'rgt'                       => '9',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '6',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '16',
				'rgt'                       => '17',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '7',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '10',
				'rgt'                       => '11',
				'depth'                     => '1',
			],
			[
				'category_id'               => '1',
				'field_id'                  => '8',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '12',
				'rgt'                       => '13',
				'depth'                     => '1',
			],
			[
				'category_id'               => '9',
				'field_id'                  => '14',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '4',
				'rgt'                       => '5',
				'depth'                     => '1',
			],
			[
				'category_id'               => '9',
				'field_id'                  => '15',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '2',
				'rgt'                       => '3',
				'depth'                     => '1',
			],
			
			[
				'category_id'               => '14',
				'field_id'                  => '16',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '4',
				'rgt'                       => '5',
				'depth'                     => '1',
			],
			[
				'category_id'               => '14',
				'field_id'                  => '17',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '2',
				'rgt'                       => '3',
				'depth'                     => '1',
			],
			[
				'category_id'               => '30',
				'field_id'                  => '8',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '37',
				'field_id'                  => '9',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '2',
				'rgt'                       => '3',
				'depth'                     => '1',
			],
			[
				'category_id'               => '37',
				'field_id'                  => '10',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '4',
				'rgt'                       => '5',
				'depth'                     => '1',
			],
			[
				'category_id'               => '37',
				'field_id'                  => '11',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '8',
				'rgt'                       => '9',
				'depth'                     => '1',
			],
			[
				'category_id'               => '37',
				'field_id'                  => '12',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '10',
				'rgt'                       => '11',
				'depth'                     => '1',
			],
			[
				'category_id'               => '37',
				'field_id'                  => '13',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => '6',
				'rgt'                       => '7',
				'depth'                     => '1',
			],
			[
				'category_id'               => '54',
				'field_id'                  => '8',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '73',
				'field_id'                  => '18',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '73',
				'field_id'                  => '19',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '73',
				'field_id'                  => '20',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '122',
				'field_id'                  => '21',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '122',
				'field_id'                  => '22',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
			[
				'category_id'               => '122',
				'field_id'                  => '23',
				'disabled_in_subcategories' => '0',
				'parent_id'                 => null,
				'lft'                       => null,
				'rgt'                       => null,
				'depth'                     => null,
			],
		];
		
		foreach ($entries as $entry) {
			DB::table('category_field')->insert($entry);
		}
	}
}
