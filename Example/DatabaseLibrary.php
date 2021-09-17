<?php
/////////////////////////////////////////////////////////////////////////////
// DatabaseLib - Generic Database Class
//
// NOTES:
// Compatible with PEAR::DB.php
//
// TODO: replication support, master, slave servers conn pool
// $db->getOne();
// $db->nextId();
// $db->query();
// $db->getRow();
// $db->getAll();
// $db->autocommit();
// $db->commit();
/////////////////////////////////////////////////////////////////////////////
class DatabaseLibrary
{
	private $database;
	private $mailer;
	private $sendMail;
	private $errorPage;

	public $debug;

	/////////////////////////////////////////////////////////////////////////
	// constructor
	/////////////////////////////////////////////////////////////////////////
	public function __construct($hostName, $databaseName, $username,
		$password, $sendMail = false, $debug = null, $errorPage = null)
	{
		if (null == $debug)
		{
			$debug = new Debug(Debug::WARNING);
		}

		$this->debug = $debug;

		if (!empty($errorPage))
		{
			$this->errorPage = $errorPage;
		}

		$debug->Show(Debug::DEBUG, "HostName: ".$hostName);
		$this->database	=
			new mysqli($hostName, $username, $password, $databaseName);

		$error = mysqli_connect_error();
		if (!empty($error))
		{
			$debug->Show(Debug::DEBUG, "DatabaseLib::DatabaseLib Error: $error");

			if (true == $sendMail)
			{
				$this->mailer->ErrorReport($error);
			}

			$debug->Exit();

			if (!empty($errorPage))
			{
				header("Location: $errorPage");
			}
		}
		else
		{
			$debug->Show(Debug::DEBUG, "DatabaseLib::DatabaseLib connection ok");

			$this->database->query("SET NAMES utf8;");
			
			$this->database->set_charset("utf8");
		}
	}

	/////////////////////////////////////////////////////////////////////////
	// Delete
	/////////////////////////////////////////////////////////////////////////
	function Delete($sqlQuery)
	{
		$result = $this->BaseQuery($sqlQuery);

		return $result;
	}

	/////////////////////////////////////////////////////////////////////////
	// DoesDataExist
	/////////////////////////////////////////////////////////////////////////
	function DoesDataExist($result)
	{
		$returnValue	= false;

		if (null != $result)
		{
			//if (($result != false) && (mysqli_numrows($result) > 0))
			if ($result != false)
			{
				return true;
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////
	// Drop
	//
	// DANGER! Use with caution
	/////////////////////////////////////////////////////////////////////////
	function Drop($databaseName)
	{
		$sqlQuery = "DROP DATABASE $databaseName";
		$result = $this->BaseQuery($sqlQuery);

		return $result;
	}

	/////////////////////////////////////////////////////////////////////////
	// GetCount
	//
	// Assumes query in the form of 'SELECT COUNT(*) FROM...'
	/////////////////////////////////////////////////////////////////////////
	function GetCount($Query)
	{
		$Count	= 0;

		$Result	= $this->BaseQuery($Query);

		if ($Result != NULL)
		{
			$ResultSet = mysql_fetch_array($Result);
			DebugPrint($ResultSet);
			$Count = $ResultSet['COUNT(*)'];
			$this->debug->Show(Debug::DEBUG, "Count: ".$Count);
		}

		return $Count;
	}

	/////////////////////////////////////////////////////////////////////////
	// GetAll
	/////////////////////////////////////////////////////////////////////////
	public function GetAll($sqlQuery, &$rows)
	{
		//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetAll: Begin" );
		$rowCount	= -1;
		$rows		= NULL;
		$result = $this->BaseQuery($sqlQuery);

		if (false != $result)
		{
			//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetAll: Checking row count" );
			$rowCount = $result->num_rows;
			//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetAll: RowCount: $rowCount");

			if (0 < $rowCount)
			{
				$rows = array();
				for ($i=0; $i<$rowCount; $i++)
				{
					$rows[] = $result->fetch_assoc();
				}
			}
		}

		//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetAll: End" );
		return $rowCount;
	}

	/////////////////////////////////////////////////////////////////////////
	// GetFirstRowFirstColum
	/////////////////////////////////////////////////////////////////////////
	function GetFirstRowFirstColum($SqlQuery)
	{
		$Result = $this->BaseQuery($SqlQuery);
		$ResultSet = mysql_fetch_array($Result);

		return $ResultSet[0];
	}

	/////////////////////////////////////////////////////////////////////////
	// GetRow
	/////////////////////////////////////////////////////////////////////////
	function GetRow($SqlQuery)
	{
		//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetRow: Begin" );
		$Row			= null;
		$RowCount		= -1;
		$Rows			= NULL;
		$Result			= $this->BaseQuery($SqlQuery);

		if (NULL != $Result)
		{
			//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetRow: Checking row count" );
			$RowCount = mysql_num_rows($Result);
			$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetRow: RowCount: ".$RowCount );

			if (0 < $RowCount)
			{
				$Rows = array();
				for ($i=0;$i<$RowCount;$i++)
				{
					$Rows[] = mysql_fetch_assoc($Result);
				}

				$Row	= $Rows[0];
			}
			//DebugPrint($Row);
			//$this->debug->Show(Debug::DEBUG, "Result: ".$Result);
			//$this->debug->Show(Debug::DEBUG, "Row: ".$Row);
		}
		else
		{
			//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetRow: Result == NULL" );
		}

		//$this->debug->Show(Debug::DEBUG,  "DatabaseLib::GetRow: End" );
		return $Row;
	}

	/////////////////////////////////////////////////////////////////////////
	// Insert
	//
	//	Returns the Id of the successfully inserted record, 
	// otherwise false or zero
	/////////////////////////////////////////////////////////////////////////
	function Insert($sqlQuery)
	{
		$result = 0;

		$resource = $this->BaseQuery($sqlQuery);

		//$this->debug->Show(Debug::DEBUG, "resource: ".$resource);
		if (false != $resource)
		{
			$result = $this->database->insert_id;
		}

		return $result;
	}

	/////////////////////////////////////////////////////////////////////////
	// NextId
	/////////////////////////////////////////////////////////////////////////
	function NextId($sequence)
	{
		return $this->GetFirstRowFirstColum("select nextval('".$sequence."_seq')");
	}

	/////////////////////////////////////////////////////////////////////////
	// Update
	/////////////////////////////////////////////////////////////////////////
	function Update($sqlQuery)
	{
		$result = $this->BaseQuery($sqlQuery);

		return $result;
	}

	/////////////////////////////////////////////////////////////////////////
	// BaseQuery
	//
	// Returns a database resource for future use or FALSE if invalid query
	/////////////////////////////////////////////////////////////////////////
	private function BaseQuery($sqlQuery)
	{
		$returnValue = $this->database->query($sqlQuery);

		if (false == $returnValue)
		{
			$error = mysqli_connect_error();
			$this->debug->Show(Debug::DEBUG, "<span style='color: red'>WARNING: query failed: $sqlQuery</span>");
			$this->debug->Show(Debug::DEBUG, $error);
		}

		return $returnValue;
	}

	/////////////////////////////////////////////////////////////////////////
	// Initialize
	//
	// alternative constructor
	/////////////////////////////////////////////////////////////////////////
	private function Initialize($hostName, $username, $password)
	{
		$this->debug->Show(Debug::DEBUG, "DatabaseLib::Initialize");
		$this->mailer = new Mailer;

		$this->connection = mysqli_connect($hostName, $username, $password);

		if (false == $this->connection)
		{
			$this->debug->Show(Debug::DEBUG, "DatabaseLib::Initialize Error: ");
			$this->debug->Show(Debug::DEBUG, mysqli_error());
			$this->mailer->ErrorReport(mysqli_error());
			header("Location: ./Html/Maintainence.html");
		}

		$this->debug->Show(Debug::DEBUG, "DatabaseLib::Initialize End");
	}
}
?>
