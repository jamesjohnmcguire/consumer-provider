<?php
require_once "vendor/digitalzenworks/consumer-provider/SourceCode/ProviderInterface.php";
require_once "DatabaseLibrary.php";

class OldDatabase implements digitalzenworks\ConsumerProvider\ProviderInterface
{
	private $oldDatabase = null;

	public function __construct()
	{
		$this->oldDatabase = new DatabaseLibrary(
			'localhost', 'example_old_database', 'example_user',
			'example_password');
	}

	public function Process()
	{
		$query = 'select * from News';

		$count = $this->oldDatabase->GetAll($query, $articles);

		return $articles;
	}
}
