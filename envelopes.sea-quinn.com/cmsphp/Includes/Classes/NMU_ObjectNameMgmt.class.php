<?php

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
	// tell people trying to access this file directly goodbye...
	exit('This file can not be accessed directly...');
}


class ObjectNameMgmt
{
	private $strStorageName = "2015_DeclaredObjects";

	function ObjectNameMgmt()
	{
		try
		{
			if(!isset($GLOBALS[$this->strStorageName]))
				$GLOBALS[$this->strStorageName] = array();
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	function ObjectNameMgmt_VarifyNameIsUnique($strObjName)
	{
		try
		{
			if($this->ObjectNameMgmt_CheckIfUsed($strObjName))
				throw new Exception('The object name "'.$strObjName.'" has already been used. It must be unique. (failed in ObjectNameMgmt_)');
			else
				$this->ObjectNameMgmt_StoreName($strObjName);
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	function ObjectNameMgmt_GetUniqueName($strPrefix)
	{
		try
		{
			$strUnique = str_replace(".", "", uniqid($strPrefix, true));
			while($this->ObjectNameMgmt_CheckIfUsed($strUnique))
				$strUnique = str_replace(".", "", uniqid($strPrefix, true));

			$this->ObjectNameMgmt_StoreName($strUnique);

			return $strUnique;
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	function ObjectNameMgmt_SelfDump()
	{
		try
		{
			$classDump = new Dump();
			$classDump->Display($this);
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}


	private function ObjectNameMgmt_StoreName($strObjName)
	{
		try
		{
			$GLOBALS[$this->strStorageName][] = $strObjName;
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	private function ObjectNameMgmt_CheckIfUsed($strObjName)
	{
		try
		{
			if(in_array($strObjName, $GLOBALS[$this->strStorageName]))
				return true;
			else
				return false;
		}
		catch (Exception $EX)
		{
			$this->ObjectNameMgmt_ErrorHandler($ex);
		}
	}

	private function ObjectNameMgmt_ErrorHandler($ex)
	{
		$classError = new ErrorHandler();
		$classError->ErrorHandler_CatchError($ex);
	}
}
