<?php

interface SqlDataQueriesInterface
{
	function __construct();
	function __destruct();

	function GetConnectInfo();
	function SpecifyDB($strHost, $strDB, $strUser, $strPassword);
	function Transaction_Start();
	function Transaction_Commit();
	function Transaction_Rollback();
	function MySQL_Queries($strQuery);
	function Fetch_Fields($strTable);
}


if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	// tell people trying to access this file directly goodbye...
	exit('This file can not be accessed directly...');
}

class SqlDataQueries implements SqlDataQueriesInterface
{
	private $host = Const_connHost;
	private $dbname = Const_connDB;
	private $user = Const_connUser;
	private $password = Const_connPSW;
	private $dbConnection = false;
	private $dbTransEnabled = false;

	function __construct()
	{
        try
        {
    		return Const_Success;
        }
        catch (Exception $EX)
        {
			ErrorHandler::ErrorHandler_CatchError($EX);
        }
	}

	function __destruct()
	{
		try
		{
			#$this->Disconnect();
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function GetConnectInfo()
	{
		try
		{
			$aInfo[] = $this->host;
			$aInfo[] = $this->dbname;
			$aInfo[] = $this->user;
			$aInfo[] = $this->password;
			$aInfo[] = $this->dbConnection;
			$aInfo[] = $this->dbTransEnabled;
			return $aInfo;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function SpecifyDB($strHost, $strDB, $strUser, $strPassword)
	{
		try
		{
			if ($this->dbConnection !== false)
				$this->Disconnect();

			if ($strHost != "")
				$this->host = $strHost;
			if ($strDB != "")
				$this->dbname = $strDB;
			if ($strUser != "")
				$this->user = $strUser;
			if ($strPassword != "")
				$this->password = $strPassword;

			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function Transaction_Start()
	{
		try
		{
			$this->Connect();
			$this->dbConnection->autocommit(FALSE);
			$this->dbTransEnabled = true;
			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function Transaction_Commit()
	{
		try
		{
			if($this->dbTransEnabled == true)
			{
				$this->dbTransEnabled = false;
				$this->dbConnection->commit();
			}

			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function Transaction_Rollback()
	{
		try
		{
			if ($this->dbTransEnabled == true)
			{
				$this->dbTransEnabled = false;
				$this->dbConnection->rollback();
			}
			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function MySQL_Queries($strQuery)
	{
		try
		{
			if (isset($_SESSION['Counter']))
				$_SESSION['Counter'] += 1;
			else
				$_SESSION['Counter'] = 1;

			$aResults = [];

			$this->Connect();
			$objResult = $this->dbConnection->query($strQuery);
			if ($objResult === false)
			{
				$strErrMessage = $this->dbConnection->error;

				if ($this->dbTransEnabled)
					$this->Transaction_Rollback();
				ErrorHandler::ErrorHandler_CatchError("MySQL Error: ".$strErrMessage, [$strQuery]);
			}

			if (strpos(' SELECT ', $this->FirstWord($strQuery)))
			{
				if (get_class($objResult) == "mysqli_result" && $objResult->num_rows > 0)
				{
					$indx = 0;
					while ($aResults[$indx] = $objResult->fetch_assoc())
						$indx++;

					unset($aResults[$indx]);

					$objResult->close();
				}
			}

			if (strpos(' INSERT UPDATE DELETE', $this->FirstWord($strQuery)))
			{
				$aResults['rows'] = $this->dbConnection->affected_rows;
				if ($this->FirstWord($strQuery) == 'INSERT')
				{
					$aResults['insertid'] = $this->dbConnection->insert_id;
					$aResults['ID'] = $this->dbConnection->insert_id;
				}
			}

			#if($this->host != Const_connHost)
			if(!$this->dbTransEnabled)
				$this->Disconnect();

			return $aResults;
		}
		catch (Exception $EX)
		{
			if ($this->dbTransEnabled)
				$this->Transaction_Rollback();

			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function Fetch_Fields($strTable)
	{
		try
		{
			$arrFields = [];

			$this->Connect();
			if (!$objResult = $this->dbConnection->query("SELECT * FROM `".$strTable."` LIMIT 1")) {
				if ($this->dbTransEnabled)
					$this->Transaction_Rollback();
				ErrorHandler::ErrorHandler_CatchError("MySQL Error: ".$this->dbConnection->error);
			}
			$objFields = $objResult->fetch_fields();

			foreach ($objFields as $field)
				$arrFields[$field->name] = $field->name;
			return $arrFields;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	private function Connect()
	{
		try
		{
			if ($this->dbConnection === false)
			{
				$this->dbConnection = new mysqli($this->host, $this->user, $this->password, $this->dbname);
				if ($this->dbConnection->connect_errno) {
					if ($this->dbTransEnabled)
						$this->Transaction_Rollback();
					ErrorHandler::ErrorHandler_CatchError("Unable to connect to DB: ".$this->dbConnection->connect_error);
				}

				if (!$objResult = $this->dbConnection->query("SET NAMES utf8")) 
				{
					if ($this->dbTransEnabled)
						$this->Transaction_Rollback();
					ErrorHandler::ErrorHandler_CatchError("MySQL Error: ".$this->dbConnection->error);
				}
			}
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	private function Disconnect()
	{
		try
		{
			if(get_class($this->dbConnection) == "mysqli")
				$this->dbConnection->close();
			$this->dbConnection = false;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	private function FirstWord($strString)
	{
		try
		{
			$words = preg_split("/[\s,]+/", strtoupper($strString));
			if (count($words) == 0) {
					if ($this->dbTransEnabled)
						$this->Transaction_Rollback();
					ErrorHandler::ErrorHandler_CatchError("Query string appears corrupt or empty. ");
			}

			return $words[0];
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}
}




