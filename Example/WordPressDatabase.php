<?php
require_once "vendor/digitalzenworks/consumer-provider/SourceCode/ConsumerInterface.php";

class WordPressDatabase implements digitalzenworks\ConsumerProvider\ConsumerInterface
{
	private $newDatabase = null;

	public function __construct()
	{
		$this->newDatabase = new DatabaseLibrary(
			'localhost', 'example_wp_database', 'example_user',
			'example_password');
	}

	public function Process($articles)
	{
		$item = null;

		if (!empty($articles))
		{
			foreach($articles as $article)
			{
				// Insert into new database
			}
			}

		return $item;
	}
}
