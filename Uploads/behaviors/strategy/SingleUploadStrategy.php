<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Uploads\behaviors\strategy;

use Uploads\models\UploadedFile;
use Uploads\validators\UploadedFileValidator;

/**
 * Description of SingleUploadStrategy
 *
 * @author vlad
 */
class SingleUploadStrategy extends UploadStrategy {

	protected $notChangedFields = [];
	protected $toUpdate = [];

	public function saveFields($fields)
	{
		foreach ($fields as $field) {
			$file = $this->behavior->owner->{$field};
			
			if ($file instanceof UploadedFile && !in_array($field, $this->notChangedFields)) {
				$file->save();
				$this->toUpdate[$field] = (string) $file;
			}
		}

		return $this->createRelation();
	}

	public function handleBeforeUpdate($field)
	{
		if (!$this->isFieldChanged($field)) {
			$this->notChangedFields[] = $field;
			return;
		}
		return $this->removePreviousUpload($field);
	}

	public function deleteField($fieldName)
	{
		$file = $this->behavior->owner->{$fieldName};
		if ($file instanceof UploadedFile) {
			return $file->delete();
		}
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function hydrateFieldAfterFind($fieldName)
	{
		$this->hydrateNewAttribute($fieldName)
			->hydrateOldAttribute($fieldName);
	}
	
	public function hydrateFieldBeforeValidation($fieldName)
	{
		$this->hydrateFieldAfterFind($fieldName);
	}

	/**
	 * @inheritdoc
	 */
	protected function createRelation()
	{
		return $this->behavior->owner->updateAttributes($this->toUpdate);
	}

	protected function getValidatorObject()
	{
		return new UploadedFileValidator();
	}

	protected function removePreviousUpload($field)
	{
		$previousUploadObject = $this->behavior->owner->getOldAttribute($field);
		if ($previousUploadObject instanceof UploadedFile) {
			$previousUploadObject->delete();
		}
	}

	protected function hydrateNewAttribute($field)
	{

		if ($this->behavior->owner->$field && !($this->behavior->owner->$field instanceof UploadFile)) {
			$uploadObject = $this->hydrateOneObject($this->behavior->owner->$field);
			$uploadObject->setIdentityField($field);
			$uploadObject->setOwner($this->behavior->owner);
			$this->behavior->owner->$field = $uploadObject;
		}

		return $this;
	}

	protected function hydrateOldAttribute($field)
	{

		$oldAttr = $this->behavior->owner->getOldAttribute($field);
		if ($oldAttr && !($oldAttr instanceof UploadFile)) {
			$uploadObject = $this->hydrateOneObject($this->behavior->owner->getOldAttribute($field));
			$uploadObject->setIdentityField($field);
			$this->behavior->owner->setOldAttribute($field, $uploadObject);
		}
		return $this;
	}

	protected function isFieldChanged($field)
	{
		$newField = $this->behavior->getFieldFromStorage($field);
		$oldField = $this->behavior->owner->getOldAttribute($field);
		
		return (string) $newField !== (string) $oldField;
	}

}
