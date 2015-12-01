<?php

namespace Uploads\models;

use Uploads\models\UploadedFile;
use Yii;

/**
 * This is the model class for table "uploads_stack".
 *
 * @property integer $id
 * @property integer $entity_id
 *
 * @property Uploads[] $uploads
 */
class UploadsCollection extends \yii\db\ActiveRecord
{
	protected $newFiles = [];
	
	protected $files;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uploads_collection';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUploads()
    {
	  if(is_null($this->files)){
		  $this->files = UploadedFile::getFilesByCollectionId($this->id);
	  }
	  return $this->files;
    }
	
	public function getNewFiles(){
		return $this->newFiles;
	}
	
	public function addUploadedFiles(UploadedFile $file){
		$this->newFiles[] = $file;
	}
	
	public function beforeDelete()
	{
		foreach($this->getUploads() as $file){
			$file->delete();
		}
		return parent::beforeDelete();
	}
}
