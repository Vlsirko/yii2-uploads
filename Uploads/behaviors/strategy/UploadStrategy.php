<?php

namespace Uploads\behaviors\strategy;

use Uploads\models\UploadedFile;
use yii\base\Behavior;

/**
 *
 * @author vlad
 */
abstract class UploadStrategy {

	protected $behavior;

	/**
	 * Convert field data to UploadedFile Object
	 * @param yii\db\ActiveRecord $model Field Owner
	 * @param string $fieldName 
	 * @return bool
	 */
	abstract public function hydrateFieldAfterFind($fieldName);

	/**
	 * remove files wich was saved in field
	 * @param $fieldName string
	 * @return boolean
	 */
	abstract public function deleteField($fieldName);

	/**
	 * Save field 
	 * @param yii\db\ActiveRecord $model Field Owner
	 * @param string $fieldName
	 * @return boolean
	 * 
	 */
	abstract public function saveFields($fieldName);

	/**
	 * @return yii\base\Validator
	 */
	abstract protected function getValidatorObject();

	abstract public function handleBeforeUpdate($field);

	abstract function hydrateFieldBeforeValidation($fieldName);

	public function setBehavior(Behavior $behaviour)
	{
		$this->behavior = $behaviour;
	}

	/**
	 * @param $fields Fields which needs in validation
	 * @param $params the array of validation parameters
	 * sample:
	 * 		$params = [
	 * 			'allowed_extentions' => ['jpg', 'png'] //Allowed extentions to upload
	 * 			'max_file_size' => 10000 //max file size in bytes
	 * 		]
	 * @return yii\validators\Validator Validator wich validates uploaded field 
	 */
	public function getValidator($fields, $params)
	{
		$validator = $this->getValidatorObject();
		$validator->allowedFileExtentions = $params['allowed_extentions'];
		$validator->maxFileSize = $params['max_file_size'];
		$validator->attributes = $fields;
		return $validator;
	}

	/**
	 * Creates object by path to file
	 * @return Uploads\models\UploadedFile 
	 */
	protected function hydrateOneObject($filePath)
	{
		return new UploadedFile($filePath);
	}

}
