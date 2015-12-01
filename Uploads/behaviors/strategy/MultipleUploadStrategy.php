<?php

namespace Uploads\behaviors\strategy;

use Uploads\validators\MultipleUploadFileValidator;
use Uploads\models\UploadsCollection;
use Uploads\models\UploadedFile;

/**
 * Description of MultipleUploadStrategy
 *
 * @author vlad
 */
class MultipleUploadStrategy extends UploadStrategy {

	protected $ownersCollection = [];
	
	/**
	 * Loads object of Uploads\models\UploadsCollection 
	 * in $fieldName on afterFind event
	 * 
	 * @param type $fieldName
	 */
	public function hydrateFieldAfterFind($fieldName)
	{
		if (!is_null($this->behavior->owner->$fieldName) && (int) $this->behavior->owner->$fieldName > 0) {
			$this->behavior->owner->$fieldName = $this->getOwnersCollectionByFieldName($fieldName);
			
		}
	}
	
	/**
	 * Adds to collection
	 * @param type $fieldName
	 */
	public function hydrateFieldBeforeValidation($fieldName)
	{
		if ($this->isNewFileUploaded($fieldName)) {
			$collection = $this->getOwnersCollectionByFieldName($fieldName);
			
			foreach ($this->behavior->owner->{$fieldName} as $file) {
				$hydratedFile = $this->hydrateOneObject($file);
				$hydratedFile->setIdentityField($fieldName);
				$hydratedFile->setOwner($collection);
				$collection->addUploadedFiles($hydratedFile);
			}
			
			$this->behavior->owner->{$fieldName} = $collection;
		}
	}

	/**
	 * 
	 * @param type $fieldName
	 * @return Uploads\models\UploadsCollection
	 */
	protected function getOwnersCollectionByFieldName($fieldName)
	{
		if (!isset($this->ownersCollection[$fieldName])) {
			
			$colection = new UploadsCollection;
			
			if ($this->ownerCollectionExists($fieldName)) {
				$collectionId = (int) $this->behavior->owner->getOldAttribute($fieldName);
				$colection->setAttribute('id', $collectionId);
				$colection->loadDefaultValues();
				$colection->isNewRecord = false;
			} 
			
			$this->ownersCollection[$fieldName] = $colection;
		}
		
		return $this->ownersCollection[$fieldName];
	}

	/**
	 * 
	 * @param type $fieldName
	 * @return boolean if collection by this field exists return true else return false
	 */
	protected function ownerCollectionExists($fieldName)
	{
		$oldAttr = $this->behavior->owner->getOldAttribute($fieldName);
		return !is_null($oldAttr) && (int)$oldAttr > 0 ;
	}

	protected function isNewFileUploaded($fieldName)
	{
		return is_array($this->behavior->owner->$fieldName);
	}

	public function deleteField($fieldName)
	{
		return $this->behavior->owner->$fieldName->delete();
	}

	public function saveFields($fieldsNamesArray)
	{
		$toUpdate = [];
		foreach($fieldsNamesArray as $field){
			$collection = $this->behavior->owner->$field;
			
			if(!is_null($collection)){
				$collection->save();
				UploadedFile::saveNewFilesFromCollection($collection);
				$toUpdate[$field] = (int) $collection->id;
			}
		}
		$this->behavior->owner->updateAttributes($toUpdate);
	}

	protected function getValidatorObject()
	{
		return new MultipleUploadFileValidator();
	}

	public function handleBeforeUpdate($field)
	{
		return;
	}

}
