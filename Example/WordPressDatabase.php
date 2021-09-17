<?php
require_once "debug.php";
require_once "vendor/digitalzenworks/consumer-provider/SourceCode/ConsumerInterface.php";

define('WP_USE_THEMES', false);

class WordPressDatabase implements digitalzenworks\ConsumerProvider\ConsumerInterface
{
	private $debug = null;
	private $multiLingualSupport = false;
	private $newDatabase = null;
	private $production = false;
	private $guidTemplate;
	private $wpRoot;
	private $wpWebRoot;

	public function __construct()
	{
		$logFile = __DIR__ . '/Import.log';
		$this->debug = new Debug(Debug::DEBUG, $logFile);

		$this->guidTemplate = "http://wp.localhost/";
		$this->wpRoot = '/xampp/htdocs/';
		$this->wpWebRoot = "/";

		$this->newDatabase = new DatabaseLibrary(
			'localhost', 'example_wp_database', 'example_user',
			'example_password', false, $this->debug);
	}

	public function Process($articles)
	{
		$this->debug->Show(Debug::DEBUG, "Consumer Processing");

		$item = null;

		if (!empty($articles))
		{
			$languages = $this->GetLanguages();

			require $this->wpRoot . 'wp-load.php';

			$language = $languages[0];
			$this->debug->Show(
				Debug::DEBUG, "processing language: $language");
			$item = $this->ProcessArticles($language, $articles);
		}

		return $item;
	}

	private function AddImage($imageFile, $id, $time, $gmtTime, $title,
		$imageNumber, $exists = false)
	{
		$filePath = $this->CopyMediaFile($imageFile);
		$this->debug->Show(Debug::DEBUG, "path: $filePath");

		$fileName = basename($imageFile);
		$name = basename($imageFile, '.jpg');
		$this->debug->Show(Debug::DEBUG, "filename: $fileName");

		$year = date('Y');
		$month = date('m');

		$guid = $this->GetImageDestinationPath($this->guidTemplate, $fileName);
		$this->debug->Show(Debug::DEBUG, "guid: $guid");

		$insertAttachment = "INSERT INTO wp_posts (post_author, ".
			"post_date, post_date_gmt, post_title, post_status, ".
			"comment_status, ping_status, post_name, post_modified, ".
			"post_modified_gmt, post_parent, guid, post_type, ".
			"post_mime_type) VALUES (1, '$time', '$gmtTime', '$title', ".
			"'inherit', 'open', 'closed', '$name', '$time', '$gmtTime', ".
			"$id, '$guid', 'attachment', 'image/jpeg')";
		$attachment_id = $this->newDatabase->Insert($insertAttachment);

		$value = "$year/$month/$fileName";

		if ($exists == false)
		{
			$this->InsertPostMeta($id, '_wp_attached_file', "'$value'");
		}
		else
		{
			$checkQuery = "SELECT * FROM wp_postmeta WHERE post_id = $id AND meta_key like '%image%'";
			$count = $this->newDatabase->GetAll($checkQuery, $rows);

			if ($count > 0)
			{
				$this->debug->Show(Debug::DEBUG, "PostMeta already exists");
				$updateMeta = "UPDATE wp_postmeta SET ".
					"post_id = $attachment_id, ".
					"meta_key = '_wp_attached_file', ".
					"meta_value = '$value' WHERE post_id = $id AND meta_key like '%image%'";
				$this->newDatabase->Update($updateMeta);
			}
			else
			{
				$this->InsertPostMeta($id, '_wp_attached_file', "'$value'");
			}
		}
	}

	private function AddImages($article, $id, $time, $gmtTime, $exists = false)
	{
		if (!empty($article['img1']))
		{
			$this->AddImage($article['img1'], $id, $time, $gmtTime,
				$article['photo1str'], 1, $exists);

			if (!empty($article['img2']))
			{
				$this->AddImage($article['img2'], $id, $time, $gmtTime,
					$article['photo2str'], 2, $exists);

				if (!empty($article['img3']))
				{
					$this->AddImage($article['img3'], $id, $time, $gmtTime,
						$article['photo3str'], 3, $exists);

					if (!empty($article['img4']))
					{
						$this->AddImage($article['img4'], $id, $time, $gmtTime,
							$article['photo4str'], 4, $exists);

						if (!empty($article['img5']))
						{
							$this->AddImage($article['img5'], $id, $time,
								$gmtTime, $article['photo5str'], 5, $exists);
						}
					}
				}
			}
		}
		else
		{
			$this->debug->Show(Debug::DEBUG, "Warning: article::img1 empty");
		}
	}

	private function AddImageToContent($article, $content)
	{
		if ($this->multiLingualSupport == true)
		{
			// TODO
		}
		else
		{
			if (!empty($article['img1']))
			{
				$source = $this->GetImageDestinationPath(
					$this->wpWebRoot, $article['img1']);

				$top =
					'    <div style="clear: both; padding: 20px;">' . PHP_EOL .
					'      <p><img src="' . $source . '"></p>' . PHP_EOL .
					'    </div>' . PHP_EOL;

				$content = $top . $content;
			}
		}

		return $content;
	}

	private function CopyMediaFile($sourceFile)
	{
		//$this->debug->Show(Debug::DEBUG, "sourceFile: $sourceFile");

		$sourcePath = "https://www.panorientnews.com/upimg/news/$sourceFile";

		$destination =
			$this->GetImageDestinationPath($this->wpRoot, $sourceFile);
		$dirname = dirname($destination);
		//$this->debug->Show(Debug::DEBUG, "sourcePath: $sourcePath");
		$this->debug->Show(Debug::DEBUG, "destination: $destination");
		$this->debug->Show(Debug::DEBUG, "dirname: $dirname");

		if (!is_dir($dirname))
		{
			//echo "sub_directory: $sub_directory<br />\r\n";
			//echo "image_path: $image_path<br />\r\n";
			//echo "new_image_path: $new_image_path<br />\r\n";
			if ($this->IsWindows())
			{
				$dirname = str_replace('/', DIRECTORY_SEPARATOR, $dirname);
			}

			mkdir($dirname);
		}

		if ($this->UrlExists($sourcePath))
		{
			$size =
				file_put_contents($destination, file_get_contents($sourcePath));

			if (false !== $size)
			{
				//$this->debug->Show(Debug::DEBUG, "size: $size");
				//$this->debug->Show(Debug::DEBUG, "CopyMediaFile: success");
				$file_exists = true;
			}
		}
		else
		{
			$this->debug->Show(Debug::DEBUG, 
				"GetImageDestinationPath: NOT EXISTS: $sourcePath");
		}

		return $destination;
	}

	private function GetContent($article, $language)
	{
		if ($this->multiLingualSupport == true)
		{
			if (!empty($article['dougastr']))
			{
				$content = "[:$language]$article[dougastr][:]";
			}
			else
			{
				$content = "[:$language]$article[kiji][:]";
			}
		}
		else
		{
			if (!empty($article['dougastr']))
			{
				$content = "$article[dougastr]";
			}
			else if (!empty($article['kiji']))
			{
				$content = "$article[kiji]";
			}
			else
			{
				$content = "$article[photo1str]";
			}
		}

		$content = $this->AddImageToContent($article, $content);

		return $content;
	}

	private function GetCategory($article)
	{
		/*
		if ($article['kijitype'] == "3")
		{
			$category = 4;
		}
		else if ($article['kijitype'] == "2")
		{
			$category = 5;
		}
		*/

		switch($article['cid'])
		{
			case '35':
			{
				$category = 1;
				break;
			}
			case '1':
			case '22':
			case '2':
			case '21':
			case '42':
			case '3':
			case '23':
			case '44':
			{
				$category = 236;
				break;
			}
			case '12':
			case '33':
			case '43':
			case '5':
			case '25':
			{
				$category = 237;
				break;
			}
			case '7':
			case '27':
			case '47':
			case '4':
			case '24':
			case '45':
			{
				$category = 238;
				break;
			}
			case '9':
			case '34':
			case '53':
			case '6':
			case '26':
			case '46':
			case '8':
			case '32':
			case '52':
			{
				$category = 239;
				break;
			}
			case '10':
			case '29':
			case '41':
			{
				$category = 240;
				break;
			}
			case '11':
			case '30':
			case '13':
			case '31':
			case '50':
			{
				$category = 241;
				break;
			}
			case '14':
			case '28':
			case '49':
			{
				$category = 1;
				break;
			}
			case '15':
			case '54':
			default:
			{
				$category = 1;
				break;
			}
		}

		return $category;
	}

	private function GetGuid($id)
	{
		$guid = $this->guidTemplate . '?p=' . $id;

		$this->debug->Show(Debug::DEBUG, "guid: $guid");

		return $guid;
	}

	private function GetImageDestinationPath($baseDirectory, $sourceFile)
	{
		$destination = null;

		$year = date('Y');
		$month = date('m');

		$destination =
			$baseDirectory . "wp-content/uploads/$year/$month/$sourceFile";
		$this->debug->Show(Debug::DEBUG, "destination: $destination");

		return $destination;
	}

	private function GetLanguages()
	{
		$languages = array();

		if ((!empty($_GET)) && (!empty($_GET['language'])))
		{
			$language = $_GET['language'];
			$languages[] = $language;
		}
		else
		{
			$languages[] = 'ab';
			$languages[] = 'en';
			$languages[] = 'jp';
		}

		return $languages;
	}

    private function GetPostName($id, $originalName, $language, $status, $time)
	{
		$this->debug->Show(Debug::DEBUG, "original name: $originalName");

		if ($language == 'en')
		{
			$sanitizedName = sanitize_title($originalName);
			$this->debug->Show(Debug::DEBUG, "sanitize name: $sanitizedName");

			$name = wp_unique_post_slug($sanitizedName, $id, $status, 'post', 0);
			$this->debug->Show(Debug::DEBUG, "final name: $name");
		}
		else
		{
			// if not english, the other functions make some pretty ugly names
			// so make something really basic
			$name = "$id-$time";
			$name = str_replace(":", "", $name);
			$name = str_replace(" ", "", $name);
			$name = wp_unique_post_slug($name, $id, $status, 'post', 0);
		}

		$this->debug->Show(Debug::DEBUG, "final name: $name");

		return $name;
	}

	private function GetTitle($article, $language)
	{
		if ($this->multiLingualSupport == true)
		{
			$title = "[:$language]$article[title][:]";
		}
		else
		{
			$title = "$article[title]";
		}

		return $title;
	}

	private function InsertPostMeta($post_id, $meta_key, $meta_value)
	{
		$statement = "INSERT INTO wp_postmeta (post_id, meta_key, ".
			"meta_value) VALUES ($post_id, '$meta_key', $meta_value)";
		$this->newDatabase->Insert($statement);
	}

	private function IsWindows()
	{
		$is_windows = FALSE;

		if (strncasecmp(PHP_OS, 'WIN', 3) == 0)
		{
			$is_windows = TRUE;
		}

		return $is_windows;
	}

	public function ProcessArticles($language, $articles)
	{
		$item = null;

		$index = 0;
		foreach($articles as $article)
		{
			$flag = $article['delflg'];
			$lang = $article['lang'];

			if ($flag !== '1' && ($lang == $language || $language == 'all'))
			{
				$exists = false;

				$time = $article['dispdate'].' '.$article['disptime'].':00';
				$gmtTime = date('Y-m-d H:i:s', strtotime($time) - 60 * 60 * 9);

				$content = $this->GetContent($article, $language);
				$title = $this->GetTitle($article, $language);
				$this->debug->Show(Debug::DEBUG, "title: $title");

				$checkQuery = "SELECT * FROM wp_postmeta WHERE " .
					"meta_key = 'old_id' AND meta_value = '$article[auto_id]'";
				$count = $this->newDatabase->GetAll($checkQuery, $rows);

				if ($count > 0)
				{
					$this->debug->Show(Debug::DEBUG, "Article already exists");
					$record = $rows[0];
					$id = $record['post_id'];
					$exists = true;
				}
				else
				{
					$id = null;

					// release
					if ($article['flg_status'] == "1")
					{
						$status = "publish";
					}
					// members only
					else if ($article['flg_status'] == "2")
					{
						$status = "private";
					}
					else if ($article['flg_status'] == "3")
					{
						$status = "draft";
					}

					$processLanguage = $language;
					if ($article['lang'] == 'ab')
					{
						$processLanguage = 'ar';
					}
					else if ($article['lang'] == 'jp')
					{
						$processLanguage = 'ja';
					}

					$category = $this->GetCategory($article);

					//	kwd1	search keywords	not using
					$this->debug->Show(Debug::DEBUG, "category: $category");

					$id = $this->SavePost($exists, $id, $time, $gmtTime,
						$content, $title, $status, $category,
						$article['dougaurl'], $article['auto_id']);
				}

				// deal with images
				$this->AddImages($article, $id, $time, $gmtTime, $exists);

				$index++;
			}
		}

		$this->debug->Show(Debug::DEBUG, "articles processed: $index");

		return $item;
	}

	private function SavePost($exists, $id, $time, $gmtTime, $content, $title,
		$status, $category, $videoUrl, $oldId)
	{
		$id = null;

		if ($exists == true)
		{
			$query = "UPDATE wp_posts SET post_content = '$content'" .
				"WHERE ID = $id";
			$this->newDatabase->Update($query);
		}
		else
		{
			$insertQuery = "INSERT INTO wp_posts (post_author, " .
				"post_date, post_date_gmt, post_content, post_title, " .
				"post_excerpt, post_status, post_modified, ".
				"post_modified_gmt) VALUES (1,'$time','$gmtTime',".
				"'$content','$title','','$status','$time','$gmtTime')";
			$id = $this->newDatabase->Insert($insertQuery);

			$insertCategory = "INSERT INTO wp_term_relationships ".
				"VALUES ($id, '$category', 0)";
			$this->newDatabase->Insert($insertCategory);

			if (!empty($videoUrl))
			{
				$this->InsertPostMeta($id, 'video_link', $videoUrl);
			}

			// add old id, in case we need to re-import or modify
			$this->InsertPostMeta($id, 'old_id', $oldId);

			if ($status == "private")
			{
				// allow members to see private posts
				$this->InsertPostMeta($id, 'wp_jv_post_rg', "'a:1:{i:0;i:0;}'");
			}
		}

		$this->debug->Show(Debug::DEBUG, "id: $id");
		$name = $this->GetPostName(
			$id, $title, $language, $status, $time);

		$guid = $this->GetGuid($id);

		$updateQuery = "UPDATE wp_posts SET post_name = '$name', ".
			"guid = '$guid' WHERE ID = $id";
		$this->newDatabase->Update($updateQuery);

		return $id;
	}

	private function UrlExists($url)
	{
		$exists = false;

		$file_headers = @get_headers($url);

		//log_message('debug', "url_exists : $file_headers[0]");

		if ($file_headers[0] == 'HTTP/1.1 200 OK')
		{
			$exists = true;
		}

		return $exists;
	}
}
