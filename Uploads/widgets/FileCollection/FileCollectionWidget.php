<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Uploads\widgets\FileCollection;

use yii\jui\InputWidget;


/**
 * Description of FileCollectionWidget
 *
 * @author vlad
 */
class FileCollectionWidget extends InputWidget{
	
	public function run()
    {
		$uploads = $this->model->{$this->attribute}->getUploads();
		return $this->renderFile( __DIR__ . '/views/file_collection_widget.php', ['uploads' => $uploads]);
    }
	
}
