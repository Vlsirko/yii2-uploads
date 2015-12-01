<?php

namespace Uploads\validators;

use yii\validators\Validator;
use Uploads\models\UploadedFile;

/**
 * This class validates custom uploaded file
 *
 * @author Sirenko Vlad
 */
class UploadedFileValidator extends Validator {

	public $allowedFileExtentions = [];
	public $maxFileSize = null;
	
	public function validateAttribute($model, $attribute)
	{
		$file = $model->$attribute;
		
		if (!($file instanceof UploadedFile)) {
			$model->addError($attribute, "{$attribute} must be instance of UploadedFile");
			return;
		}
		
		if (!in_array($file->extention, $this->allowedFileExtentions)) {
			$model->addError($attribute, "Неподходящее расширение файла");
		}
		
		if (!is_null($this->maxFileSize) && $this->maxFileSize < $file->getSize()) {
			$model->addError($attribute, "Слишком большой файл");
		}
	}

}
