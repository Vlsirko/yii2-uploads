<?php

namespace Uploads\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use Uploads\behaviors\strategy\UploadStrategyFactory;

/**
 * This behaviour is responsible for uploading files to models
 *
 * @author Sirenko Vlad
 */
class UploadBehaviour extends Behavior {

	public $allowedFileExtentions = [
		'jpg',
		'jpeg',
		'gif',
		'png',
		'ico',
		'txt',
		'doc',
		'docx',
		'xls',
		'xlsx',
		'csv',
		'odt',
		'pdf',
		'zip',
		'rar',
		'xml',
		'csv'
	];
	public $fields = [];
	public $maxFileSize;
	public $multiple = false;
	protected $uploadStrategy;
	protected $fieldsToUploadStorage = [];

	public function init()
	{
		parent::init();
		$mark = $this->multiple ? UploadStrategyFactory::UPLOAD_MULTIPLE : UploadStrategyFactory::UPLOAD_SINGLE;
		$this->uploadStrategy = UploadStrategyFactory::get($mark);
		$this->uploadStrategy->setBehavior($this);
	}

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'prepareToValidation',
			ActiveRecord::EVENT_BEFORE_INSERT => 'handleBeforeInsert',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'handleBeforeUpdate',
			ActiveRecord::EVENT_AFTER_UPDATE => 'saveFields',
			ActiveRecord::EVENT_AFTER_INSERT => 'saveFields',
			ActiveRecord::EVENT_AFTER_FIND => 'hydrateUploadedFiles',
			ActiveRecord::EVENT_BEFORE_DELETE => 'deleteUploadsFiles'
		];
	}

	/**
	 * Convert pathes to objects before validate
	 * @param type $event
	 */
	public function prepareToValidation()
	{
		
		$this->hydrateUploadedFilesBeforeValidation();
		$this->addValidatorToOwner();
	}

	/**
	 * convert array of pathes to UploadedFile Objects array
	 */
	public function hydrateUploadedFiles()
	{
		foreach ($this->fields as $field) {
			$this->uploadStrategy->hydrateFieldAfterFind($field);
		}
		return $this;
	}

	public function hydrateUploadedFilesBeforeValidation()
	{
		foreach ($this->fields as $field) {
			$this->uploadStrategy->hydrateFieldBeforeValidation($field);
		}
		return $this;
	}

	public function saveFields()
	{

		$this->loadStoragedAttributesToOwner();
		$this->uploadStrategy->saveFields($this->fields);
		return $this;
	}

	public function handleBeforeUpdate()
	{
		foreach ($this->fields as $field) {
			$this->saveFieldInStorage($field);
			$this->resetField($field);
			$this->uploadStrategy->handleBeforeUpdate($field);
		}
	}

	public function deleteUploadsFiles()
	{
		foreach ($this->fields as $field) {
			$this->uploadStrategy->deleteField($field);
		}
		return $this;
	}

	public function getFieldFromStorage($fieldName)
	{
		return $this->fieldsToUploadStorage[$fieldName];
	}

	public function handleBeforeInsert()
	{
		foreach ($this->fields as $field) {
			$this->saveFieldInStorage($field);
			$this->resetField($field);
		}
	}

	protected function saveFieldInStorage($fieldName)
	{
		$this->fieldsToUploadStorage[$fieldName] = $this->owner->{$fieldName};
	}

	protected function resetField($fieldName)
	{
		$this->owner->{$fieldName} = null;
	}

	protected function loadStoragedAttributesToOwner()
	{
		return $this->owner->setAttributes($this->fieldsToUploadStorage);
	}

	protected function addValidatorToOwner()
	{
		$this->owner->validators[] = $this->uploadStrategy->getValidator(
			$this->fields, [
			'allowed_extentions' => $this->allowedFileExtentions,
			'max_file_size' => $this->maxFileSize
			]
		);
		return $this;
	}

}
