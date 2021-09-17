<?php
require_once "debug.php";
require_once "vendor/digitalzenworks/consumer-provider/SourceCode/ProviderInterface.php";
require_once "DatabaseLibrary.php";

class OldDatabase implements digitalzenworks\ConsumerProvider\ProviderInterface
{
	private $consumer = null;
	private $debug = null;
	private $inException = false;
	private $list = array();
	private $oldDatabase = null;

	public function __construct()
	{
		$this->name = 'Old Database';

		$logFile = __DIR__ . '/Import.log';
		$this->debug = new Debug(Debug::DEBUG, $logFile);

		$this->oldDatabase = new DatabaseLibrary(
			'localhost', 'example_old_database', 'example_user',
			'example_password', false, $this->debug);
	}

	public function Process($consumer)
	{
		$this->debug->Show(Debug::DEBUG, "Provider Processing...");

		$this->consumer = $consumer;

		$query = 'select * from tbl_news';

		$count = $this->oldDatabase->GetAll($query, $articles);

		$this->list = $consumer->Process($articles);

		return $this->list;
	}
}
