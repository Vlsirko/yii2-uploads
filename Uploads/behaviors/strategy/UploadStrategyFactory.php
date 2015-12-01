<?php

namespace Uploads\behaviors\strategy;

/**
 * Description of UploadStrategyFactory
 *
 * @author vlad
 */
class UploadStrategyFactory {

	const UPLOAD_MULTIPLE = 'multiple';
	const UPLOAD_SINGLE = 'single';

	public static function get($mark)
	{
		switch ($mark) {
			case self::UPLOAD_MULTIPLE:
				return new MultipleUploadStrategy();
			case self::UPLOAD_SINGLE:
				return new SingleUploadStrategy();
		}
		throw new \Exception("Undefined mark in UploadStrategyFactory");
	}

}
