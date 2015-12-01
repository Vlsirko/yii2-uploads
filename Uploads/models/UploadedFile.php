<?php

namespace Uploads\models;

use yii;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use Uploads\models\UploadsCollection;

/**
 * Description of UploadedFile
 *
 * @author Sirenko Vlad
 */
class UploadedFile {

	const DEFAULT_UPLOAD_PATH = '@backend/web/upload';
	const EVENT_AFTER_FILE_UPLOAD = 'on file upload';
	const TABLE_NAME = 'uploads';

	public $webPath = '';
	public $fullPath = '';
	public $extention = '';
	protected $validator;
	protected $owner;
	protected $filename;
	protected $identityField;
	protected $fileChanged = true;

	public function __construct($filepath)
	{
		$this->fullPath = yii::getAlias('@backend/web') . $filepath;
		$this->webPath = (string) $filepath;
		$this->extention = pathinfo($filepath, PATHINFO_EXTENSION);
		$this->filename = basename($this->fullPath);
	}

	public function __toString()
	{
		return $this->webPath;
	}

	public function setOwner(ActiveRecord $owner)
	{
		$this->owner = $owner;
	}

	/**
	 * Save upload file inctance to regular directory
	 * @return boolean
	 */
	public function save()
	{
		$newPath = $this->createPathFromOwnerClass();
		$this->createDirIfNotExists($newPath);
		$newFileFullPath = $this->handleFileWithSameName(implode(DIRECTORY_SEPARATOR, [$newPath, $this->filename]));
		return $this->copyUploadedFileToRegularDirectory($newFileFullPath);
	}

	/**
	 * Deletes physical file
	 * @return boolean
	 */
	public function delete()
	{

		if (file_exists($this->fullPath)) {
			if (unlink($this->fullPath)) {
				$this->handleEmptyDir();
				return true;
			}
		}
		return false;
	}

	/**
	 * @return int filesize
	 */
	public function getSize()
	{
		return file_exists($this->fullPath) ? filesize($this->fullPath) : 0;
	}

	/**
	 * 
	 * @param string $field attribute name for which UploadedFile belongs to
	 */
	public function setIdentityField($field)
	{
		$this->identityField = $field;
	}

	/**
	 * delete parents dir if it is emptys
	 */
	protected function handleEmptyDir()
	{
		$dirname = dirname($this->fullPath);
		$files = array_diff(scandir($dirname), ['.', '..']);
		if (count($files) === 0) {
			rmdir($dirname);
		}
	}

	/**
	 * Copys uploaded file to constant location
	 * 
	 * @param type $destinationPath
	 * @return boolean
	 */
	protected function copyUploadedFileToRegularDirectory($destinationPath)
	{
		if (copy($this->fullPath, $destinationPath)) {
			$this->webPath = $this->convertFullPathToWeb($destinationPath);
			$this->fullPath = $destinationPath;
			return true;
		}

		return false;
	}

	/**
	 * If this file exists this method create new name to file
	 * for exclude rewrites files
	 * 
	 * @param string $filePath
	 * @return string full file path
	 */
	protected function handleFileWithSameName($filePath)
	{
		if (file_exists($filePath)) {
			$dir = dirname($filePath);
			$newName = $this->recoursiveBuildNewName($dir, $this->filename);
			$filePath = implode(DIRECTORY_SEPARATOR, [$dir, $newName]);
		}
		return $filePath;
	}

	/**
	 * @return string path to file 
	 */
	protected function createPathFromOwnerClass()
	{
		$class = $this->getOwnerClass();
		$id = $this->getOwnerId();
		return implode(DIRECTORY_SEPARATOR, [$this->getDirToUpload(), 'models', $class, $id]);
	}

	protected function getOwnerClass()
	{
		return array_pop(explode('\\', get_class($this->owner)));
	}

	protected function getOwnerId()
	{
		return $this->owner->id;
	}

	/**
	 * Creates directory if not exists
	 * 
	 * @param string $path path to file/directory 
	 */
	protected function createDirIfNotExists($path)
	{
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
	}

	/**
	 * Creates new file name if file with same name exists in current directory, 
	 * if not returns name string without changes 
	 * 
	 * @param type $path path to file directory where current file is
	 * @param type $fileName name of file to handle
	 * @param type $iteration
	 * @return string new file name
	 */
	protected function recoursiveBuildNewName($path, $fileName, $iteration = 1)
	{
		list($basename, $extention) = explode('.', $fileName);
		$newFileName = $basename . '-' . $iteration . '.' . $extention;
		$fullPath = implode(DIRECTORY_SEPARATOR, [$path, $newFileName]);
		if (!file_exists($fullPath)) {
			return $newFileName;
		}
		return $this->recoursiveBuildNewName($path, $fileName, ++$iteration);
	}

	/**
	 * Convert full path to web path
	 * 
	 * @param type $fullFilePath full path to file
	 * @return string web representation of file path
	 */
	protected function convertFullPathToWeb($fullFilePath)
	{
		$pathPart = yii::getAlias('@backend/web');
		return str_replace($pathPart, '', $fullFilePath);
	}

	/**
	 * By default files will be uploaded in @backend\web\uploads, but you
	 * can change this directory by adding "uploadDirectory" field in common 
	 * params config
	 *  
	 * @return string defines in what directory files will be uploaded
	 */
	public function getDirToUpload()
	{
		$path = isset(yii::$app->params['uploadDirectory']) ? yii::$app->params['uploadDirectory'] : self::DEFAULT_UPLOAD_PATH;
		try {
			return yii::getAlias($path);
		} catch (InvalidParamException $e) {
			return $path;
		}
	}
	

	public static function saveNewFilesFromCollection(UploadsCollection $collection)
	{
		$toInsert = [];
		$command = \yii::$app->db->createCommand();
		foreach ($collection->getNewFiles() as $file) {
			$file->save();
			$toInsert[] = [$collection->id, (string) $file];
		}

		return $command->batchInsert(self::TABLE_NAME, ['collection_id', 'filepath'], $toInsert)->execute();
	}

	public static function getFilesByCollectionId($collectionId)
	{
		$query = new \yii\db\Query;
		$resultSet = $query->select('filepath')
			->from(self::TABLE_NAME)
			->where(['collection_id' =>  $collectionId])
			->all();

		return self::hydrateResultSet($resultSet);
	}

	protected static function hydrateResultSet(array $resultSet)
	{
		$toReturn = [];
		foreach ($resultSet as $row) {
			$toReturn[] = new self($row['filepath']);
		}
		return $toReturn;
	}

}
