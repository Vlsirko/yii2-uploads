<?php

namespace Uploads\tests\unit;

use Codeception\Util\Stub;
use yii;

class UploadedFileTest extends \Codeception\TestCase\Test {

	/**
	 * @var \Uploads\UnitTester
	 */
	protected $tester;
	protected $baseTestFileString = '/upload/files/test.jpg';
	protected $checkFilePath = "/upload/models/Test/1/test.jpg";
	protected $baseDir;

	protected function _before()
	{
		//create test file
		$this->baseDir = yii::getAlias("@backend/web/upload/files/");
		mkdir($this->baseDir, 0777, true);
		file_put_contents($this->baseDir . '/test.jpg', '');
	}

	protected function _after()
	{
		$this->delTree(yii::getAlias("@backend/web"));
	}

	// tests
	public function testMoveToConstantDir()
	{
		$file = Stub::construct('\Uploads\models\UploadedFile', ['filePath' => $this->baseTestFileString], [
				'getOwnerClass' => 'Test',
				'getOwnerId' => 1,
				'getDirToUpload' => yii::getAlias(implode(DIRECTORY_SEPARATOR, ['@backend', 'web', 'upload'])),
		]);

		$file->save();
		$pathToFile = implode(DIRECTORY_SEPARATOR, ['@backend', 'web', $this->checkFilePath]);
		$fileSaves = file_exists(yii::getAlias($pathToFile));
		$this->assertTrue($fileSaves, 'file saves into wrong directory');
	}

	public function testTwoSameNameFiles()
	{
		$file = Stub::construct('\Uploads\models\UploadedFile', ['filePath' => $this->baseTestFileString], [
				'getOwnerClass' => 'Test',
				'getOwnerId' => 2,
				'getDirToUpload' => yii::getAlias(implode(DIRECTORY_SEPARATOR, ['@backend', 'web', 'upload'])),
		]);
		$file->save();

		$file->save();
		
		$this->assertTrue(strval($file) === '/upload/models/Test/2/test-1.jpg');
	}

	public function testDelete()
	{
		$file = Stub::construct('\Uploads\models\UploadedFile', ['filePath' => $this->baseTestFileString], [
				'getOwnerClass' => 'Test',
				'getOwnerId' => 2,
				'getDirToUpload' => yii::getAlias(implode(DIRECTORY_SEPARATOR, ['@backend', 'web', 'upload'])),
		]);
		$file->save();
		$filePath = ($file->fullPath);
		$file->delete();
		$this->assertFalse(file_exists($filePath));
	}
	
	public function delTree($dir)
	{
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

}
