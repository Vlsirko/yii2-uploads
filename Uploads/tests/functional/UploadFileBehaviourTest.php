<?php

namespace Uploads;

use yii;
use Umcms\models\User;
use yii\codeception\DbTestCase;
use Uploads\validators\UploadedFileValidator;
use Uploads\models\UploadsCollection;

class UploadFileBehaviourTest extends DbTestCase {

	/**
	 * @var \Uploads\UnitTester
	 */
	protected $tester;
	protected $model;
	protected $pathToImage = '/upload/files/test.jpg';
	protected $filePath = '';
	protected $user;

	protected function _before()
	{
		$this->filepath = implode(DIRECTORY_SEPARATOR, [yii::getAlias('@backend'), 'web', 'upload', 'files', 'test.jpg']);
		mkdir(dirname($this->filepath), 0777, true);
		file_put_contents($this->filepath, '');
	}

	protected function _after()
	{
		$this->delTree(yii::getAlias("@backend/web"));
	}

	public function testHydrateNewAndOldUploadedFilesAfterFind()
	{

		$user = User::findIdentity(8);
		$this->assertInstanceOf('Uploads\models\UploadedFile', $user->image);
		$this->assertInstanceOf('Uploads\models\UploadedFile', $user->getOldAttribute('image'));
	}

	public function testPrepareFilesToValidation()
	{
		$user = User::findIdentity(8);
		$user->trigger(\yii\db\ActiveRecord::EVENT_BEFORE_VALIDATE);
		$validatorExists = false;

		foreach ($user->activeValidators as $validator) {

			if ($validator instanceof UploadedFileValidator) {
				$validatorExists = true;
				break;
			}
		}
		$this->assertTrue($validatorExists);
	}

	public function testSaveOneImageInOneField()
	{
		$_POST['User'] = [
			'username' => 'test',
			'email' => 'test@test.loc',
			'password' => '123456789',
			'image' => $this->pathToImage,
		];

		$user = new User();
		$user->load($_POST);

		$user->save();


		$pathToImage = "/models/User/{$user->id}/test.jpg";

		$user->trigger(\yii\db\ActiveRecord::EVENT_AFTER_FIND);
		$newImageWebPath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice(explode(DIRECTORY_SEPARATOR, (string) $user->image), -4));
		$this->assertEquals($pathToImage, $newImageWebPath);
		$this->assertFileExists($user->image->fullPath);
	}

	public function testSaveTwoSameImagesInDifferentFields()
	{
		$user = new User();
		$user->username = 'test';
		$user->email = 'test@test.loc';
		$user->password = '123456789';
		$user->image = $this->pathToImage;
		$user->image_path = $this->pathToImage;
		$user->getBehavior('uploadFile')->fields[] = 'image_path';
		$user->save();

		$compareUser = User::findByEmail('test@test.loc');
		$this->assertNotEquals((string) $compareUser->image, (string) $compareUser->image_path);
	}

	public function testSaveUploadsForTwoModels()
	{
		$user1 = new User();
		$user1->username = 'testSaveUploadsForTwoModels_1';
		$user1->email = 'testSaveUploadsForTwoModels_1@test.loc';
		$user1->password = '123456789';
		$user1->image = $this->pathToImage;
		$this->assertTrue($user1->save());

		$user2 = new User();
		$user2->username = 'testSaveUploadsForTwoModels_2';
		$user2->email = 'testSaveUploadsForTwoModels_2@te22st.loc';
		$user2->password = '123456789';
		$user2->image = $this->pathToImage;
		$this->assertTrue($user2->save());

		$compareUser1 = User::findIdentity($user1->id);
		$compareUser2 = User::findIdentity($user2->id);

		$this->assertFileExists($compareUser1->image->fullPath);
		$this->assertFileExists($compareUser2->image->fullPath);
		$this->assertNotEquals((string) $compareUser1->image, (string) $compareUser2->image);
	}

	public function testExtentionValidation()
	{
		$this->filepath = implode(DIRECTORY_SEPARATOR, [yii::getAlias('@backend'), 'web', 'upload', 'files', 'test.txt']);
		$user1 = new User();
		$user1->username = 'test';
		$user1->email = 'test@test.loc';
		$user1->password = '123456789';
		$user1->image = $this->pathToImage;
		$behavior = $user1->getBehavior('uploadFile');
		$behavior->allowedFileExtentions = ['png'];

		$this->assertFalse($user1->validate());
	}

	public function testTooBigFileValidation()
	{
		$filepath = implode(DIRECTORY_SEPARATOR, [yii::getAlias('@backend'), 'web', 'upload', 'files', 'test.jpg']);
		$content = '';
		for ($i = 0; $i < 10000; $i++) {
			$content .='1';
		}

		file_put_contents($filepath, $content);

		$user1 = new User();
		$user1->username = 'test';
		$user1->email = 'test@test.loc';
		$user1->password = '123456789';
		$user1->image = $this->pathToImage;
		$behavior = $user1->getBehavior('uploadFile');
		$behavior->maxFileSize = 5000;


		$user1->validate();
		$this->assertFalse($user1->validate());
	}

	public function testResaveUpload()
	{

		$newImageFullPath = implode(DIRECTORY_SEPARATOR, [yii::getAlias('@backend'), 'web', 'upload', 'files', 'test2.jpg']);
		$newImageWebPath = implode(DIRECTORY_SEPARATOR, ['', 'upload', 'files', 'test2.jpg']);

		file_put_contents($newImageFullPath, '');

		$user1 = new User();
		$user1->username = 'testResaveUpload';
		$user1->email = 'testResaveUpload@as.loc';
		$user1->password = '123456789';
		$user1->image = $this->pathToImage;
		$user1->save();

		$user1->image = $newImageWebPath;
		$user1->save();
		$user1->trigger(\yii\db\ActiveRecord::EVENT_AFTER_FIND);
		$this->assertfileExists($user1->image->fullPath);

		$this->assertEquals((string) $user1->image, "/upload/models/User/{$user1->id}/test2.jpg");
		$this->assertFileExists($user1->image->fullPath);
	}

	public function testDeleteUserUploadedFilesBeforeUserDelete()
	{
		$user = User::findIdentity(8);
		$imageFullPath = $user->image->fullPath;
		$user->delete();
		$this->assertFileNotExists($imageFullPath);
	}

	public function testMultipleUpload()
	{
		$user = new User();
		$user->username = 'test';
		$user->email = 'test@test.loc';
		$user->password = '123456789';
		$user->image_path = [$this->pathToImage, $this->pathToImage];

		$user->attachBehavior('multipleUpload', [
			'class' => behaviors\UploadBehaviour::className(),
			'multiple' => true,
			'fields' => ['image_path']
		]);

		$user->save();
		$user->trigger(yii\db\ActiveRecord::EVENT_AFTER_FIND);
		$this->assertInstanceOf('Uploads\models\UploadsCollection', $user->image_path);

		$files = $user->image_path->getUploads();
		$this->assertCount(2, $files);

		$this->assertNotEquals((string) $files[0], (string) $files[1]);
		$this->assertEquals((string) $files[0], "/upload/models/UploadsCollection/{$user->image_path->id}/test.jpg");
		$this->assertEquals((string) $files[1], "/upload/models/UploadsCollection/{$user->image_path->id}/test-1.jpg");

		$this->assertFileExists($files[0]->fullPath);
		$this->assertFileExists($files[1]->fullPath);
	}

	public function testDeleteMultipleUpload()
	{
		$user = new User();
		$user->username = 'test';
		$user->email = 'test@test.loc';
		$user->password = '123456789';
		$user->image_path = [$this->pathToImage, $this->pathToImage];

		$user->attachBehavior('multipleUpload', [
			'class' => behaviors\UploadBehaviour::className(),
			'multiple' => true,
			'fields' => ['image_path']
		]);
		
		$user->save();
		
		$compareUser = User::findOne($user->id);
		
		$compareUser->attachBehavior('multipleUpload', [
			'class' => behaviors\UploadBehaviour::className(),
			'multiple' => true,
			'fields' => ['image_path']
		]);
		$compareUser->trigger(\yii\db\ActiveRecord::EVENT_AFTER_FIND);
		
		$images = $compareUser->image_path->getUploads();
		
		$collectionID = $compareUser->image_path->id;
		
		
		$path1 = $images[0]->fullPath;
		$path2 = $images[1]->fullPath;
		
		$compareUser->delete();
		
		$this->assertFileNotExists($path1);
		$this->assertFileNotExists($path2);
		
		$this->tester->DontSeeInDatabase('uploads_collection', ['id'=>$collectionID]);
		$this->tester->DontSeeInDatabase('uploads', ['collection_id'=>$collectionID]);
	}
	
	public function testUpdateExistingUploadCollection()
	{

		$user = new User();
		$user->username = 'test';
		$user->email = 'test@test.loc';
		$user->password = '123456789';
		$user->image_path = [$this->pathToImage, $this->pathToImage];

		$user->attachBehavior('multipleUpload', [
			'class' => behaviors\UploadBehaviour::className(),
			'multiple' => true,
			'fields' => ['image_path']
		]);

		$user->save();
		$user->trigger(yii\db\ActiveRecord::EVENT_AFTER_FIND);
		$collectionId = $user->image_path->id;

		$compareUser = User::findOne($user->id);
		$compareUser->image_path = [$this->pathToImage];


		$compareUser->attachBehavior('multipleUpload', [
			'class' => behaviors\UploadBehaviour::className(),
			'multiple' => true,
			'fields' => ['image_path']
		]);

		$compareUser->save();

		$compareUser->trigger(yii\db\ActiveRecord::EVENT_AFTER_FIND);

		$compareUserCollectionId = $compareUser->image_path->id;

		$files = $user->image_path->getUploads();
		$this->assertCount(3, $files);
		$this->assertEquals($collectionId, $compareUserCollectionId);
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
