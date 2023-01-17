<?php
require_once "vendor/digitalzenworks/consumer-provider/SourceCode/ConsumerInterface.php";

class WordPressDatabase implements digitalzenworks\ConsumerProvider\ConsumerInterface
{
	public function __construct()
	{
	}

	public function Process(?array $articles) : void
	{
		$item = null;

		if (!empty($articles))
		{
			foreach($articles as $article)
			{
				// Insert into new database
			}
		}
	}
}
