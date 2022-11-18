<?php

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	// tell people trying to access this file directly goodbye...
	exit('This file can not be accessed directly...');
}


class FileHandler // Uploads files to a common area
{
	private $strFilename;
	private $strLocalPath; // adds to BasePath
	private $strBasePath = "/htdocs/Webb/DynamicallyCreatedFiles/";
	private $strFullPathFilename;
	private $fhHandle;

	function __construct($strThePath, $strTheFilename, $strIntendedAction)
	{
		try
		{
			if ($strThePath[0] != "/")
				$strThePath = '/'.$strThePath;
			if ($strThePath[strlen($strThePath) - 1] != "/")
				$strThePath = $strThePath.'/';

			if (!file_exists($this->strBasePath.$strThePath))
				throw new Exception("Directory does not exist. ");

			$this->strLocalPath = $strThePath;
			$this->strFilename = strtolower($strTheFilename);
			$this->strFullPathFilename = $this->strBasePath.$this->strLocalPath.$this->strFilename;

			if (!$this->fhHandle = fopen($this->strFullPathFilename, $strIntendedAction))
				throw new Exception("Unable to create file: ".$this->strFullPathFilename);


			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function AddLine($strTheRow)
	{
		try
		{
			$strTheRow .= " \n";
			if (fwrite($this->fhHandle, $strTheRow) === FALSE)
				throw new Exception("Error writing line to file: ".$this->strFullPathFilename."<br/><br/>Line was: ".$strTheRow);

			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function CloseFile()
	{
		try
		{
			fclose($this->fhHandle);
			return Const_Success;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function GetDownloadPath()
	{
		try
		{
			$strPath = "http://".$_SERVER['HTTP_HOST']."/Webb/DynamicallyCreatedFiles/".$this->strLocalPath.$this->strFilename;
			return $strPath;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}

	function GetFullPath()
	{
		try
		{
			return $this->strFullPathFilename;
		}
		catch (Exception $EX)
		{
			ErrorHandler::ErrorHandler_CatchError($EX);
		}
	}
}



