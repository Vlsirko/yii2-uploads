<?php

namespace Uploads\widgets;
use iutbay\yii2kcfinder\KCFinderInputWidget;
/**
 * Description of Kcfinder
 *
 * @author vlad
 */
class Kcfinder extends KCFinderInputWidget{
	
	public $kcfDefaultOptions = [
        'disabled'=>false,
        'denyZipDownload' => true,
        'denyUpdateCheck' => true,
        'denyExtensionRename' => true,
        'theme' => 'default',
        'access' =>[    // @link http://kcfinder.sunhater.com/install#_access
            'files' =>[
                'upload' => true,
                'delete' => true,
                'copy' => true,
                'move' => true,
                'rename' => true,
            ],
            'dirs' =>[
                'create' => true,
                'delete' => true,
                'rename' => true,
            ],
        ],
        'types'=>[  // @link http://kcfinder.sunhater.com/install#_types
            'files' => [
                'type' => '',
            ],
            'images' => [
                'type' => '*img',
            ],
        ],
        'thumbsDir' => '.thumbs',
        'thumbWidth' => 100,
        'thumbHeight' => 100,
    ];
	
}
