<?php
/**
 * LaraClassified - Classified Ads Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Helpers\Lang\Traits;

use App\Models\Language;
use Illuminate\Support\Str;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

trait LangTablesTrait
{
	/**
	 * Translated models with their relations
	 *
	 * @var array
	 */
	private $translatedModels = [
		'PostType'    => [
			['model' => 'Post', 'key' => 'post_type_id'],
		],
		'Category'    => [
			['model' => 'Category', 'key' => 'parent_id'],
			['model' => 'Post', 'key' => 'category_id'],
			['model' => 'CategoryField', 'key' => 'category_id'],
		],
		'Gender'      => [
			['model' => 'User', 'key' => 'gender_id'],
		],
		'Package'     => [
			['model' => 'Payment', 'key' => 'package_id'],
		],
		'ReportType',
		'Page',
		'MetaTag',
		'Field'       => [
			['model' => 'FieldOption', 'key' => 'field_id'],
			['model' => 'CategoryField', 'key' => 'field_id'],
			['model' => 'PostValue', 'key' => 'field_id'],
		],
		'FieldOption' => [
			['model' => 'PostValue', 'key' => 'option_id'],
		],
	];
	
	/**
	 * Get models namespace
	 *
	 * @var string
	 */
	private $namespace = '\\App\Models\\';
	
	/**
	 * @return array
	 */
	public function getTranslatedModels()
	{
		// Core translated models
		$models = $this->translatedModels;
		
		// Domain Mapping plugin translated models
		if (config('plugins.domainmapping.installed')) {
			$models[] = '\\extras\plugins\domainmapping\app\Models\DomainMetaTag';
		}
		
		return $models;
	}
	
	/**
	 * CREATING - Copy translated entries
	 *
	 * @param $defaultLangAbbr
	 * @param $abbr
	 */
	public function copyTranslatedEntries($defaultLangAbbr, $abbr)
	{
		// (If exist...)
		// Delete all the translations related to the new language
		$this->destroyTranslatedEntries($abbr);
		
		$models = $this->getTranslatedModels();
		
		// Create Translated Models entries
		foreach ($models as $model => $relations) {
			// Models without relations
			if (is_numeric($model) && is_string($relations)) {
				$model = $relations;
			}
			
			// Get model full name (with the namespace)
			if (!Str::contains($model, '\\')) {
				$model = $this->namespace . $model;
			}
			
			// Get the model's main entries
			$mainEntries = $model::where('translation_lang', strtolower($defaultLangAbbr))->get();
			if ($mainEntries->count() > 0) {
				foreach ($mainEntries as $entry) {
					$newEntryInfo = $entry->toArray();
					$newEntryInfo['translation_lang'] = strtolower($abbr);
					
					// If the current Model is 'Category', Then ...
					// Make the 'slug' column unique using the new language code (abbr)
					if (class_basename($model) == 'Category') {
						$newEntryInfo['slug'] = $newEntryInfo['slug'] . '-' . strtolower($abbr);
					}
					
					// Save newEntry to database
					$newEntry = new $model($newEntryInfo);
					$newEntry->save();
				}
			}
		}
	}
	
	/**
	 * UPDATING - Update translated entries
	 *
	 * @param $abbr
	 */
	public function updateTranslatedEntries($abbr)
	{
		$models = $this->getTranslatedModels();
		
		// Update Translated Models entries
		foreach ($models as $model => $relations) {
			// Models without relations
			if (is_numeric($model) && is_string($relations)) {
				$model = $relations;
			}
			
			// Get model full name (with the namespace)
			if (!Str::contains($model, '\\')) {
				$model = $this->namespace . $model;
			}
			
			// Get new "translation_of" value with old entries
			$tmpEntries = $model::where('translation_lang', strtolower($abbr))->get();
			$newTid = [];
			if ($tmpEntries->count() > 0) {
				foreach ($tmpEntries as $tmp) {
					$newTid[$tmp->translation_of] = $tmp->id;
				}
			}
			
			// Change "translation_of" value with new Default Language
			$entries = $model::query();
			if ($entries->count() > 0) {
				foreach ($entries->cursor() as $entry) {
					if (isset($newTid[$entry->translation_of])) {
						$entry->translation_of = $newTid[$entry->translation_of];
						$entry->save();
					}
				}
			}
			
			// If relation exists, change its foreign key value
			if (isset($relations) && is_array($relations) && !empty($relations)) {
				foreach ($relations as $relation) {
					if (!isset($relation) || !isset($relation['key']) || !isset($relation['model'])) {
						continue;
					}
					$relModel = $this->namespace . $relation['model'];
					$relEntries = $relModel::query();
					if ($relEntries->count() > 0) {
						foreach ($relEntries->cursor() as $relEntry) {
							if (isset($newTid[$relEntry->{$relation['key']}])) {
								// Update the relation entry
								$relEntry->{$relation['key']} = $newTid[$relEntry->{$relation['key']}];
								$relEntry->save();
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * DELETING - Delete translated entries
	 *
	 * @param $abbr
	 */
	public function destroyTranslatedEntries($abbr)
	{
		$models = $this->getTranslatedModels();
		
		// Remove Translated Models entries
		foreach ($models as $model => $relations) {
			// Models without relations
			if (is_numeric($model) && is_string($relations)) {
				$model = $relations;
			}
			
			// Get model full name (with the namespace)
			if (!Str::contains($model, '\\')) {
				$model = $this->namespace . $model;
			}
			
			// Get the model's main entries
			$translatedEntries = $model::where('translation_lang', strtolower($abbr));
			if ($translatedEntries->count() > 0) {
				foreach ($translatedEntries->cursor() as $entry) {
					// Delete
					$entry->delete();
				}
			}
		}
	}
	
	/**
	 * UPDATING - Set default language (Call this method at last)
	 *
	 * @param $abbr
	 */
	public function setDefaultLanguage($abbr)
	{
		// Unset the old default language
		Language::whereIn('active', [0, 1])->update(['default' => 0]);
		
		// Set the new default language
		Language::where('abbr', $abbr)->update(['default' => 1]);
		
		// Update the Default App Locale
		$this->updateDefaultAppLocale($abbr);
	}
	
	// PRIVATE METHODS
	
	/**
	 * Update the Default App Locale
	 *
	 * @param $locale
	 */
	private function updateDefaultAppLocale($locale)
	{
		if (!DotenvEditor::keyExists('APP_LOCALE')) {
			DotenvEditor::addEmpty();
		}
		DotenvEditor::setKey('APP_LOCALE', $locale);
		DotenvEditor::save();
	}
}
