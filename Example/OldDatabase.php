<?php
require_once "vendor/digitalzenworks/consumer-provider/SourceCode/ProviderInterface.php";

class OldDatabase implements digitalzenworks\ConsumerProvider\ProviderInterface
{
	private $oldDatabase = null;

	public function __construct()
	{
	}

	public function Process(): ?array
	{
		$data =
		[
			[1, 'First Post', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit', '2020-01-01 12:01:01'],
			[2, 'Another Post', 'Quisque eleifend aliquam ex quis pellentesque', '2020-01-01 17:01:01'],
			[3, 'A New Post', 'Nam laoreet quam sed auctor hendrerit', '2020-07-01 15:01:01'],
			[8, 'Some Other Post', 'Interdum et malesuada fames ac ante ipsum primis in faucibus', '2020-09-01 19:01:01']
		];

		return $data;
	}
}
