function Finances_ManageNav() {
  Finances_CloseAll("");

  if (jQuery(".finances-menu-nav").is(":visible")) {
    jQuery(".finances-menu-nav").hide();
    jQuery(".finances-menu-bar").show();
  } else {
    jQuery(".finances-menu-nav").show();
    jQuery(".finances-menu-bar").hide();
  }
}

function UpdateDisplayType() {
  alert("Gas");
}

function LoadBudget() {
  $iSessionID = document.getElementById("SessionID").value;
  $strTransTableToUse = GetCurrentTransTable();

  $strMonth = document.getElementById("Month").value;
  $strURL = document.getElementById("HIDDEN_URL").value;
  $strDisplayType = "";
  if ($(".DisplayType").val())
    $strDisplayType = document.getElementById("DisplayType").value;
  else $strDisplayType = "";

  UserWait(true);
  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "LoadBudget",
      Month: $strMonth,
      URL: $strURL,
      SessionID: $iSessionID,
      DisplayType: $strDisplayType,
    },
    function ($strData) {
      if ($strData != "") {
        $aMainParts = $strData.split("YYYYY");
        $strScreen = $aMainParts[0];
        $strArgs = $aMainParts[1];

        document.getElementById("DIV_Budget").innerHTML = $strScreen;

        $aParts = $strArgs.split("XXXXX");
        for ($I = 0; $I < $aParts.length - 1; $I++) {
          $aMoreParts = $aParts[$I].split("-----");
          $iPercentage = ($aMoreParts[1] / $aMoreParts[2]) * 100;
          if ($iPercentage == "" || $iPercentage == 0) $iPercentage = 1;

          $(function () {
            $("#ProgressBar_" + $aMoreParts[0]).progressbar({
              value: $iPercentage,
            });
          });

          if ($aMoreParts[3] == "Exp") {
            $("#ProgressBar_" + $aMoreParts[0]).css({
              background: "LightYellow",
            });
            if ($iPercentage > 100)
              $("#ProgressBar_" + $aMoreParts[0] + " > div").css({
                background: "Red",
              });
            else
              $("#ProgressBar_" + $aMoreParts[0] + " > div").css({
                background: "Green",
              });
          } else {
            $("#ProgressBar_" + $aMoreParts[0]).css({ background: "Red" });
            $("#ProgressBar_" + $aMoreParts[0] + " > div").css({
              background: "Green",
            });
          }
        }
      }

      UserWait(false);
    }
  );
}

function ShowGroups($strType) {
  $strTransTableToUse = GetCurrentTransTable();

  Finances_CloseAll("DIV_Groups");

  if (!jQuery("#DIV_Groups").is(":visible")) {
    jQuery("#DIV_Groups").show();
    GroupActions($strType, "");
  } else jQuery("#DIV_Groups").hide();
}

function GroupActions($strType, $iID) {
  $strTransTableToUse = GetCurrentTransTable();

  if ($strType == "Cancel") {
    document.getElementById("NewGroup").value = "";
    $strType = "ListGroup";
  }

  if ($strType == "AddGroup")
    $strValue = document.getElementById("NewGroup").value;
  else $strValue = "";

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: $strType,
      NewGroup: $strValue,
      ID: $iID,
    },
    function ($strData) {
      document.getElementById("DIV_Groups").innerHTML = $strData;
      if ($strType == "AddGroup")
        document.getElementById("NewGroup").value = "";
      LoadBudget();
    }
  );
}

function EditEnv($iID) {
  $strTransTableToUse = GetCurrentTransTable();

  if (!jQuery("#DIV_ENV_EDIT_" + $iID).is(":visible")) {
    $.post(
      "/cgi-bin/FinancesFunctions.php",
      { action: "EditEnv", ID: $iID },
      function ($strData) {
        jQuery("#DIV_ENV_EDIT_" + $iID).show();
        document.getElementById("DIV_ENV_EDIT_" + $iID).innerHTML = $strData;
      }
    );
  } else CloseEnvEdit($iID);
}

function ShowEnvelopes() {
  $strTransTableToUse = GetCurrentTransTable();

  Finances_CloseAll("DIV_Envelopes");

  if (!jQuery("#DIV_Envelopes").is(":visible")) {
    $.post(
      "/cgi-bin/FinancesFunctions.php",
      { action: "AddEnvDisplay" },
      function ($strData) {
        jQuery("#DIV_Envelopes").show();
        document.getElementById("DIV_Envelopes").innerHTML = $strData;
      }
    );
  } else jQuery("#DIV_Envelopes").hide();
}

function CloseEnvEdit($iID) {
  jQuery("#DIV_ENV_EDIT_" + $iID).hide();
}

function ListTransactions($strType, $iEnvID) {
  UserWait(true);
  $iSessionID = document.getElementById("SessionID").value;
  $strTransTableToUse = GetCurrentTransTable();

  if ($strType == "Uncat") {
    $strMonth = "";
    $strKeyword = "";
    $iEnvID = "";
  } else {
    $strMonth = document.getElementById("Month").value;
    $strKeyword = document.getElementById("Keyword").value;
    if ($iEnvID == "") $iEnvID = jQuery("#Envelope option:selected").val();
  }

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "ListTransactions",
      Type: $strType,
      Month: $strMonth,
      Envelope: $iEnvID,
      Keyword: $strKeyword,
      SessionID: $iSessionID,
    },
    function ($strData) {
      document.getElementById("DIV_Transactions").innerHTML = $strData;
      UserWait(false);
    }
  );
}

function TransGroups($strURL) {
  window.location = $strURL;
}

function WipeTransTable($strTable) {
  $.post(
    "/cgi-bin/FinancesFunctions.php",
    { action: "WipeTransTable", Table: $strTable },
    function ($strData) {
      $aURL = window.location.href.split("&outcome");
      window.location.href = $aURL[0] + "&outcome=" + $strTable;
    }
  );
}

function LoadCompare() {
  UserWait(true);

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    { action: "LoadCompare" },
    function ($strData) {
      document.getElementById("DIV_Compare").innerHTML = $strData;
      UserWait(false);
    }
  );
}

function HandleAction(e) {
  e.stopPropagation();
  e.preventDefault();
}

function UserWait($bWait) {
  if ($bWait) {
    $("a").css("pointer-events", "none");

    if ($(".container").length) $(".container").css("opacity", "0.5");

    $("body").css("cursor", "wait");
    document.addEventListener("click", HandleAction, true);
  } else {
    $("a").css("pointer-events", "auto");

    if ($(".container").length) $(".container").css("opacity", "1.0");

    $("body").css("cursor", "auto");
    document.removeEventListener("click", HandleAction, true);
  }
}

function CloseTransNotes($iID) {
  if (jQuery("#TRANS_EDIT_STATUS_" + $iID).length) {
    document.getElementById("TRANS_EDIT_STATUS_" + $iID).value = "Closed";
    jQuery("#TR_" + $iID).hide();
    document.getElementById("TD_" + $iID).innerHTML = "";

    jQuery("#TR_" + $iID + "_Transfer").hide();
  }
}

function ClearSearch() {
  document.getElementById("Keyword").value = "";
  jQuery("#Envelope").val("");
  ListTransactions("ByKeyword", "");
  jQuery("#DIV_Filters").hide();
}

function FilterOpen() {
  if (jQuery("#DIV_Filters").is(":visible")) jQuery("#DIV_Filters").hide();
  else {
    CloseTransfer();
    jQuery("#DIV_Filters").show();
  }
}

function CloseTransfer() {
  jQuery("#DIV_Transfer").hide();
}

function OpenTransfer($iID) {
  $strTransTableToUse = GetCurrentTransTable();

  Finances_CloseAll("DIV_Transfer");

  if (jQuery("#DIV_Transfer").is(":visible")) CloseTransfer();
  else {
    document.getElementById("TransDate").value = "";
    document.getElementById("Amount").value = "";
    document.getElementById("Description").value = "";
    document.getElementById("ToEnvelopeID").selectedIndex = 0;
    document.getElementById("FromEnvelopeID").selectedIndex = 0;

    if ($iID == "") {
      jQuery("#DIV_TransferButtonSave").show();
      jQuery("#DIV_TransferButtonEdit").hide();
    } else {
      jQuery("#DIV_TransferButtonSave").hide();
      jQuery("#DIV_TransferButtonEdit").show();
    }

    jQuery("#DIV_Transfer").show();
  }
}

function ListUncat() {
  $strTransTableToUse = GetCurrentTransTable();

  document.getElementById("Month").selectedIndex = 0;
  document.getElementById("Keyword").value = "";
  document.getElementById("Envelope").selectedIndex = 0;
  jQuery("#DIV_Filters").hide();
  ListTransactions("Uncat", "");
}

function ListTransactionDups() {
  UserWait(true);
  $strTransTableToUse = GetCurrentTransTable();

  document.getElementById("DIV_Transactions").innerHTML =
    "Search for duplicates...";
  $.post(
    "/cgi-bin/FinancesFunctions.php",
    { TransTableToUse: $strTransTableToUse, action: "ListTransactionDups" },
    function ($strData) {
      document.getElementById("DIV_Transactions").innerHTML = $strData;
      UserWait(false);
    }
  );
}

function CheckboxEnableButton($strFieldName, $strCheckboxName) {
  if (document.myForm.elements[$strCheckboxName].checked == true)
    document.myForm.elements[$strFieldName].disabled = false;
  else document.myForm.elements[$strFieldName].disabled = true;
}

function DisableField($strFieldname) {
  document.myForm.elements[$strFieldname].disabled = true;
}

function DropDownFormSubmit($strFormName, $strFieldName) {
  if (document.forms[$strFormName].elements[$strFieldName].value != "")
    document.forms[$strFormName].submit();
}

function SetAllChecks($strFormName, $strNamePiece, $bValue) {
  for ($I = 0; $I < document.forms[$strFormName].elements.length; $I++) {
    if (
      document.forms[$strFormName].elements[$I].name.indexOf($strNamePiece) > -1
    ) {
      if (
        $bValue == "check" &&
        document.forms[$strFormName].elements[$I].disabled == false
      )
        document.forms[$strFormName].elements[$I].checked = true;
      else document.forms[$strFormName].elements[$I].checked = false;
    }
  }
}

function RoundNumber($iNbr, $iDecs) {
  var $strResult =
    Math.round($iNbr * Math.pow(10, $iDecs)) / Math.pow(10, $iDecs);

  return $strResult;
}

function CommonFormSubmit($strFormName) {
  if ($strFormName == "") document.myForm.submit();
  else document.forms[$strFormName].submit();
}

function CommonGOTO($strURL) {
  window.location = $strURL;
}

function SelectEnableFields(
  $strFormName,
  $strSelectName,
  $strFieldNames,
  $strValue,
  $bInverse
) {
  $aParts = $strFieldNames.split(";");

  $box = document.forms[$strFormName].elements[$strSelectName];
  $strSelectedValue = $box[$box.selectedIndex].value;

  if (
    ($strSelectedValue == $strValue && !$bInverse) ||
    ($strSelectedValue != $strValue && $bInverse)
  ) {
    for ($I = 0; $I < $aParts.length; $I++) {
      if ($aParts[$I] != "") {
        document.forms[$strFormName].elements[$aParts[$I]].disabled = false;
      }
    }
  } else {
    for ($I = 0; $I < $aParts.length; $I++) {
      if ($aParts[$I] != "") {
        document.forms[$strFormName].elements[$aParts[$I]].disabled = true;

        if (document.forms[$strFormName].elements[$aParts[$I]].type == "text")
          document.forms[$strFormName].elements[$aParts[$I]].value = "";
        else if (
          document.forms[$strFormName].elements[$aParts[$I]].type == "checkbox"
        ) {
          document.forms[$strFormName].elements[$aParts[$I]].checked = false;
        } else if (
          document.forms[$strFormName].elements[$aParts[$I]].type ==
          "select-one"
        ) {
          document.forms[$strFormName].elements[$aParts[$I]].selectedIndex = 0;
          if (document.getElementById($aParts[$I]) != null)
            document.getElementById($aParts[$I]).onchange();
        }
      }
    }
  }
}

function RadioEnableFields(
  $strFormName,
  $strRadioName,
  $strFieldNames,
  $strEnableValue
) {
  if ($strFieldNames != "") {
    $iSelected = "";
    for (
      $I = 0;
      $I < document.forms[$strFormName].elements[$strRadioName].length;
      $I++
    ) {
      if (document.forms[$strFormName].elements[$strRadioName][$I].checked) {
        $iSelected = $I;
      }
    }

    $strLowerFieldName = $strFieldNames.toLowerCase();
    $bIsPhone = $strLowerFieldName.indexOf("phone");

    if (
      document.forms[$strFormName].elements[$strRadioName][$iSelected].value ==
      $strEnableValue
    ) {
      $aParts = $strFieldNames.split(";");
      for ($I = 0; $I < $aParts.length; $I++) {
        if ($aParts[$I] != "") {
          if ($bIsPhone >= 0) {
            document.forms[$strFormName].elements[
              $aParts[$I] + "1"
            ].disabled = false;
            document.forms[$strFormName].elements[
              $aParts[$I] + "2"
            ].disabled = false;
            document.forms[$strFormName].elements[
              $aParts[$I] + "3"
            ].disabled = false;
          } else {
            if (
              document.forms[$strFormName].elements[$aParts[$I]].type ==
              undefined
            ) {
              if (
                document.forms[$strFormName].elements[$aParts[$I]][0].type ==
                "radio"
              )
                for (
                  $X = 0;
                  $X <
                  document.forms[$strFormName].elements[$aParts[$I]].length;
                  $X++
                )
                  document.forms[$strFormName].elements[$aParts[$I]][
                    $X
                  ].disabled = false;
            } else {
              document.forms[$strFormName].elements[
                $aParts[$I]
              ].disabled = false;
            }
          }
        }
      }
    } else {
      $aParts = $strFieldNames.split(";");
      for ($I = 0; $I < $aParts.length; $I++) {
        if ($aParts[$I] != "") {
          if ($bIsPhone >= 0) {
            document.forms[$strFormName].elements[
              $aParts[$I] + "1"
            ].disabled = true;
            document.forms[$strFormName].elements[$aParts[$I] + "1"].value = "";
            document.forms[$strFormName].elements[
              $aParts[$I] + "2"
            ].disabled = true;
            document.forms[$strFormName].elements[$aParts[$I] + "2"].value = "";
            document.forms[$strFormName].elements[
              $aParts[$I] + "3"
            ].disabled = true;
            document.forms[$strFormName].elements[$aParts[$I] + "3"].value = "";
          } else {
            document.forms[$strFormName].elements[$aParts[$I]].disabled = true;

            if (
              document.forms[$strFormName].elements[$aParts[$I]].type == "text"
            )
              document.forms[$strFormName].elements[$aParts[$I]].value = "";
            else if (
              document.forms[$strFormName].elements[$aParts[$I]].type ==
              "checkbox"
            )
              document.forms[$strFormName].elements[
                $aParts[$I]
              ].checked = false;
            else if (
              document.forms[$strFormName].elements[$aParts[$I]].type ==
                "select-one" ||
              document.forms[$strFormName].elements[$aParts[$I]].type ==
                "select-multiple"
            )
              document.forms[$strFormName].elements[
                $aParts[$I]
              ].selectedIndex = 0;
            if (
              document.forms[$strFormName].elements[$aParts[$I]].type ==
              undefined
            ) {
              if (
                document.forms[$strFormName].elements[$aParts[$I]][0].type ==
                "radio"
              ) {
                for (
                  $X = 0;
                  $X <
                  document.forms[$strFormName].elements[$aParts[$I]].length;
                  $X++
                ) {
                  document.forms[$strFormName].elements[$aParts[$I]][
                    $X
                  ].disabled = true;
                  document.forms[$strFormName].elements[$aParts[$I]][
                    $X
                  ].checked = false;
                }
              }
            }

            if (document.getElementById($aParts[$I]) != null)
              document.getElementById($aParts[$I]).onchange();
          }
        }
      }
    }
  }
}

function FormatCurrencyNumber($iNumber, $iDecimalPlacesAllowed) {
  $strTransTableToUse = GetCurrentTransTable();

  $iNumber = RoundNumber($iNumber, $iDecimalPlacesAllowed);

  $strOutput = String($iNumber);
  while ($strOutput.indexOf(",") > -1) $strOutput = $strOutput.replace(",", "");

  while ($strOutput.indexOf("$") > -1) $strOutput = $strOutput.replace("$", "");

  $strTemp = "";
  for ($I = 0; $I < $strOutput.length; $I++)
    if (!isNaN($strOutput[$I]) || $strOutput[$I] == ".")
      $strTemp += $strOutput[$I];
  $strOutput = $strTemp;

  $strTemp = "";
  $iSpot = 1;
  $aParts = $strOutput.split(".");
  for ($I = $aParts[0].length - 1; $I >= 0; $I--) {
    if ($iSpot == 4) {
      $strTemp += ",";
      $iSpot = 1;
    }
    $strTemp += $aParts[0][$I];

    $iSpot++;
  }
  $strTemp = $strTemp.split("").reverse().join("");

  $strTemp = "$" + $strTemp;

  if ($aParts.length == 2) {
    if ($aParts[1].length == 1) $strOutput = $strTemp + "." + $aParts[1] + "0";
    else $strOutput = $strTemp + "." + $aParts[1];
  } else $strOutput = $strTemp;

  return $strOutput;
}

function SaveTransNotes($iID, $strView) {
  $strTransTableToUse = GetCurrentTransTable();

  $iSplitNumber = document.getElementById("SPLIT_HIDDEN_" + $iID).value;
  $strName1 = "";
  $strEnv1 = "";
  $strAmount1 = "";

  $strName2 = "";
  $strEnv2 = "";
  $strAmount2 = "";

  $strName3 = "";
  $strEnv3 = "";
  $strAmount3 = "";

  $strName4 = "";
  $strEnv4 = "";
  $strAmount4 = "";

  if ($iSplitNumber > "0") {
    $iTotal = 0;
    for (
      $I = 1;
      $I <= document.getElementById("SPLIT_HIDDEN_" + $iID).value;
      $I++
    ) {
      $iTotal += Number(
        document
          .getElementById("SPLIT_AMOUNT_" + $iID + "_" + $I)
          .value.replace("$", "")
          .replace(",", "")
      );

      if (
        document.getElementById("SPLIT_NAME_" + $iID + "_" + $I).value == ""
      ) {
        alert("Name #" + $I + " is required");
        return;
      }
      if (
        document.getElementById("SPLIT_AMOUNT_" + $iID + "_" + $I).value == ""
      ) {
        alert("Dollar Amount #" + $I + " is required");
        return;
      }

      $selectBox = document.getElementById("SPLIT_ENV_" + $iID + "_" + $I);
      if ($selectBox[$selectBox.selectedIndex].value == "") {
        alert("Envelope #" + $I + " is required");
        return;
      }
    }
    $iTotal = RoundNumber($iTotal, 2);

    if (
      $iTotal - Number(document.getElementById("ORIG_AMOUNT_" + $iID).value) !=
      0
    ) {
      alert("Remaining amount must be zero.");
      return;
    }

    if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 1) {
      $strName1 = document.getElementById("SPLIT_NAME_" + $iID + "_1").value;
      $strEnv1 = document.getElementById("SPLIT_ENV_" + $iID + "_1").value;
      $strAmount1 = document.getElementById(
        "SPLIT_AMOUNT_" + $iID + "_1"
      ).value;
    }
    if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 2) {
      $strName2 = document.getElementById("SPLIT_NAME_" + $iID + "_2").value;
      $strEnv2 = document.getElementById("SPLIT_ENV_" + $iID + "_2").value;
      $strAmount2 = document.getElementById(
        "SPLIT_AMOUNT_" + $iID + "_2"
      ).value;
    }
    if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 3) {
      $strName3 = document.getElementById("SPLIT_NAME_" + $iID + "_3").value;
      $strEnv3 = document.getElementById("SPLIT_ENV_" + $iID + "_3").value;
      $strAmount3 = document.getElementById(
        "SPLIT_AMOUNT_" + $iID + "_3"
      ).value;
    }
    if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 4) {
      $strName4 = document.getElementById("SPLIT_NAME_" + $iID + "_4").value;
      $strEnv4 = document.getElementById("SPLIT_ENV_" + $iID + "_4").value;
      $strAmount4 = document.getElementById(
        "SPLIT_AMOUNT_" + $iID + "_4"
      ).value;
    }
  }

  $strNotes = document.getElementById("TextArea_NOTES_" + $iID).value;

  document.getElementById("HIDDEN_OPEN_ID").value = "";

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "SaveTransNotes",
      ID: $iID,
      Notes: $strNotes,
      SplitNumber: $iSplitNumber,
      Name1: $strName1,
      Env1: $strEnv1,
      Amount1: $strAmount1,
      Name2: $strName2,
      Env2: $strEnv2,
      Amount2: $strAmount2,
      Name3: $strName3,
      Env3: $strEnv3,
      Amount3: $strAmount3,
      Name4: $strName4,
      Env4: $strEnv4,
      Amount4: $strAmount4,
    },
    function ($strData) {
      if ($strView == "ShowNormal") ListTransactions("", "");
      else CloseTransNotes($iID);
    }
  );
}

function SaveTransCat($iID, $strOverideTable, $strCaller) {
  if ($strOverideTable != "") $strTransTableToUse = $strOverideTable;
  else $strTransTableToUse = GetCurrentTransTable();

  $selectBox = document.getElementById("Envelope_" + $iID);
  $iCat = $selectBox[$selectBox.selectedIndex].value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "SaveTransCat",
      ID: $iID,
      Cat: $iCat,
      Caller: $strCaller,
    },
    function ($strData) {
      if ($strCaller == "Compare") {
        document.getElementById("DIV_" + $iID).innerHTML =
          '<span class="finances-success">Saved</span>';
        setTimeout(function () {
          document.getElementById("DIV_" + $iID).innerHTML = "";
        }, 3000);
      }
    }
  );
}

function SetTransCat($iTransID, $iEnvID) {
  $.post(
    "/cgi-bin/FinancesFunctions.php",
    { action: "SetTransCat", TransID: $iTransID, EnvID: $iEnvID },
    function ($strData) {
      document.getElementById("DIV_" + $iTransID).innerHTML =
        '<span class="finances-success">Saved</span>';
      setTimeout(function () {
        document.getElementById("DIV_" + $iTransID).innerHTML = "";
        document.getElementById("DIV_" + $iTransID).hide();
      }, 3000);
    }
  );
}

function SaveAllTransCat() {
  $strTransTableToUse = GetCurrentTransTable();

  $("select").each(function () {
    if (this.id.indexOf("Envelope_") >= 0) {
      if ($(this).css("border-top-color") == "rgb(0, 128, 0)") {
        $iID = this.id.replace("Envelope_", "");
        SaveTransCat($iID, "", "");

        $(this).removeClass("finance-previous-choice");
      }
    }
  });
}

function ShowTransDetails($iID, $bIsTransfer, $strView) {
  $strTransTableToUse = GetCurrentTransTable();

  if (!jQuery("#TR_" + $iID).is(":visible")) {
    if ($bIsTransfer == 0) {
      $.post(
        "/cgi-bin/FinancesFunctions.php",
        {
          TransTableToUse: $strTransTableToUse,
          action: "ShowTransDetails",
          ID: $iID,
          View: $strView,
        },
        function ($strData) {
          jQuery("#TR_" + $iID).show();
          document.getElementById("TR_" + $iID).innerHTML = $strData;
        }
      );
    } else {
      jQuery("#TR_" + $iID).show();
      alert("not working yet");
      OpenTransfer($iID);
    }
  } else jQuery("#TR_" + $iID).hide();
}

function SaveEditEnv($iID) {
  $strTransTableToUse = GetCurrentTransTable();

  if ($iID != "") $strExt = "_" + $iID;
  else $strExt = "";

  Name = document.getElementById("EnvName" + $strExt).value;
  $iAmount = document.getElementById("EnvAmount" + $strExt).value;

  $selectBox = document.getElementById("GroupName" + $strExt);
  $strGroupName = $selectBox[$selectBox.selectedIndex].value;

  $strMonth = document.getElementById("Month").value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "SaveEditEnv",
      EnvName: Name,
      EnvAmount: $iAmount,
      GroupName: $strGroupName,
      ID: $iID,
      Month: $strMonth,
    },
    function ($strData) {
      if ($iID == "") {
        if (
          $strData.indexOf("ERROR") == 0 ||
          $strData.indexOf("Please fix the following") > -1
        )
          document.getElementById("DIV_Envelopes").innerHTML = $strData;
        else {
          ShowEnvelopes();
          LoadBudget();
        }
      } else LoadBudget();
    }
  );
}

function CommaSeparateNumber($iValue) {
  $iValue = $iValue.toString();
  var $strPattern = /(-?\d+)(\d{3})/;
  while ($strPattern.test($iValue))
    $iValue = $iValue.replace($strPattern, "$1,$2");
  return $iValue;
}

function RoundToNearest($iNumber, $iNearestNumber) {
  $iNumber = RoundNumber($iNumber, 0);
  $iResult = Math.ceil($iNumber / $iNearestNumber) * $iNearestNumber;

  return $iResult;
}

function SaveTransferFromBudget() {
  $strTransTableToUse = GetCurrentTransTable();

  $strTransDate = document.getElementById("TransDate").value;
  $iAmount = document.getElementById("Amount").value;
  $strDescription = document.getElementById("Description").value;

  $selectBox = document.getElementById("FromEnvelopeID");
  $strFromEnvelopeID = $selectBox[$selectBox.selectedIndex].value;

  $selectBox = document.getElementById("ToEnvelopeID");
  $strToEnvelopeID = $selectBox[$selectBox.selectedIndex].value;
  $iID = "";

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "SaveTransfer",
      ID: $iID,
      TransDate: $strTransDate,
      Amount: $iAmount,
      Description: $strDescription,
      FromEnvelopeID: $strFromEnvelopeID,
      ToEnvelopeID: $strToEnvelopeID,
    },
    function ($strData) {
      if ($strData == "") {
        CloseTransfer();
        LoadBudget();
      } else
        document.getElementById("DIV_TransferMessage").innerHTML = $strData;
    }
  );
}

function SaveTransfer() {
  $strTransTableToUse = GetCurrentTransTable();

  $iID = document.getElementById("HIDDEN_ID").value;

  $strTransDate = document.getElementById("TransDate").value;
  $iAmount = document.getElementById("Amount").value;
  $strDescription = document.getElementById("Description").value;

  $selectBox = document.getElementById("FromEnvelopeID");
  $strFromEnvelopeID = $selectBox[$selectBox.selectedIndex].value;

  $selectBox = document.getElementById("ToEnvelopeID");
  $strToEnvelopeID = $selectBox[$selectBox.selectedIndex].value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "SaveTransfer",
      ID: $iID,
      TransDate: $strTransDate,
      Amount: $iAmount,
      Description: $strDescription,
      FromEnvelopeID: $strFromEnvelopeID,
      ToEnvelopeID: $strToEnvelopeID,
    },
    function ($strData) {
      if ($strData == "") {
        CloseTransfer();

        ListTransactions("", "");
      } else
        document.getElementById("DIV_TransferMessage").innerHTML = $strData;
    }
  );
}

function DeleteTransfer() {
  $strTransTableToUse = GetCurrentTransTable();

  $iID = document.getElementById("HIDDEN_ID").value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "DeleteTransfer",
      ID: $iID,
    },
    function ($strData) {
      CloseTransfer();
      ListTransactions("", "");
    }
  );
}

function FormatCurrency($strID, $iDecimalPlacesAllowed) {
  var $myTextField = document.getElementById($strID);
  var $strOutput = $myTextField.value;

  $iDecimalPlace = $strOutput.indexOf(".");
  if ($iDecimalPlace > -1 && $iDecimalPlacesAllowed == 0) {
    $strOutput = $strOutput.replace(".", "");
    alert("Cents not allowed");
  } else {
    if ($iDecimalPlace > -1 && $iDecimalPlacesAllowed == 2) {
      $aParts = $strOutput.split(".");
      if ($aParts[1].length > 2) {
        $strPartOne = $strOutput.substr(0, $strOutput.indexOf("."));
        $strPartTwo = $strOutput.substr($strOutput.indexOf(".") + 1, 2);
        $strFinal = $strPartOne + "." + $strPartTwo;

        $strOutput = $strFinal;
      } else if ($aParts.length > 2) {
        $strPartOne = $strOutput.substr(0, $strOutput.indexOf("."));
        $strOutput = $strPartOne + ".";
      }
    }
  }

  while ($strOutput.indexOf(",") > -1) $strOutput = $strOutput.replace(",", "");

  while ($strOutput.indexOf("$") > -1) $strOutput = $strOutput.replace("$", "");

  $strTemp = "";
  for ($I = 0; $I < $strOutput.length; $I++)
    if (!isNaN($strOutput[$I]) || $strOutput[$I] == ".")
      $strTemp += $strOutput[$I];
  $strOutput = $strTemp;

  $strTemp = "";
  $iSpot = 1;
  $aParts = $strOutput.split(".");
  for ($I = $aParts[0].length - 1; $I >= 0; $I--) {
    if ($iSpot == 4) {
      $strTemp += ",";
      $iSpot = 1;
    }
    $strTemp += $aParts[0][$I];

    $iSpot++;
  }
  $strTemp = $strTemp.split("").reverse().join("");
  $strTemp = "$" + $strTemp;

  if ($aParts.length == 2) $strOutput = $strTemp + "." + $aParts[1];
  else $strOutput = $strTemp;

  if ($myTextField.value != $strOutput) $myTextField.value = $strOutput;
}

function Deactivate($iID) {
  $strTransTableToUse = GetCurrentTransTable();

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    { TransTableToUse: $strTransTableToUse, action: "Deactivate", ID: $iID },
    function ($strData) {
      LoadBudget();
    }
  );
}

function HideDiv($strForm, $strElementName, $strDivID, $strValue, $bInverse) {
  if (document.forms[$strForm].elements[$strElementName].type == undefined) {
    if (document.forms[$strForm].elements[$strElementName][0].type == "radio") {
      $iSelected = "";
      for (
        $I = 0;
        $I < document.forms[$strForm].elements[$strElementName].length;
        $I++
      )
        if (document.forms[$strForm].elements[$strElementName][$I].checked)
          $iSelected = $I;

      if (
        document.forms[$strForm].elements[$strElementName][$iSelected].value ==
        $strValue
      )
        document.getElementById($strDivID).style.display = SetDiv(
          true,
          $bInverse
        );
      else
        document.getElementById($strDivID).style.display = SetDiv(
          false,
          $bInverse
        );
    }
  } else if (
    document.forms[$strForm].elements[$strElementName].type == "select-one"
  ) {
    $iSelectedIndex =
      document.forms[$strForm].elements[$strElementName].selectedIndex;
    if (
      document.forms[$strForm].elements[$strElementName][$iSelectedIndex]
        .value == $strValue
    )
      document.getElementById($strDivID).style.display = SetDiv(
        true,
        $bInverse
      );
    else
      document.getElementById($strDivID).style.display = SetDiv(
        false,
        $bInverse
      );
  } else if (
    document.forms[$strForm].elements[$strElementName].type == "checkbox"
  ) {
    if (document.forms[$strForm].elements[$strElementName].checked == true)
      document.getElementById($strDivID).style.display = SetDiv(
        true,
        $bInverse
      );
    else
      document.getElementById($strDivID).style.display = SetDiv(
        false,
        $bInverse
      );
  }
}

function SetDiv($bShow, $bInverse) {
  if ($bShow && !$bInverse) return "";
  if ($bShow && $bInverse) return "none";
  if (!$bShow && !$bInverse) return "none";
  if (!$bShow && $bInverse) return "";
}

function CheckEnableFields($strForm, $strCheckboxName, $strChildren) {
  if ($strChildren != "") {
    $aParts = $strChildren.split(";");
    if (document.forms[$strForm].elements[$strCheckboxName].checked == true) {
      for ($I = 0; $I < $aParts.length; $I++)
        if ($aParts[$I] != "")
          document.forms[$strForm].elements[$aParts[$I]].disabled = false;
    } else {
      for ($I = 0; $I < $aParts.length; $I++) {
        if ($aParts[$I] != "") {
          document.forms[$strForm].elements[$aParts[$I]].disabled = true;

          if (document.forms[$strForm].elements[$aParts[$I]].type == "text")
            document.forms[$strForm].elements[$aParts[$I]].value = "";

          if (document.forms[$strForm].elements[$aParts[$I]].type == "checkbox")
            document.forms[$strForm].elements[$aParts[$I]].checked = false;

          if (
            document.forms[$strForm].elements[$aParts[$I]].type ==
              "select-one" ||
            document.forms[$strForm].elements[$aParts[$I]].type ==
              "select-multiple"
          )
            document.forms[$strForm].elements[$aParts[$I]].selectedIndex = 0;

          if (
            document.getElementById($aParts[$I]) != null &&
            document.forms[$strForm].elements[$aParts[$I]].type != "checkbox"
          )
            document.getElementById($aParts[$I]).onchange();
        }
      }
    }
  }
}

function MarkDup($iID, $strType) {
  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "MarkDup",
      Type: $strType,
      ID: $iID,
    },
    function ($strData) {
      ListTransactionDups();
    }
  );
}

function RemoveSplit($iID, $iNbr) {
  alert("RemoveSplit");
  $strTransTableToUse = GetCurrentTransTable();

  $iSpot = 1;
  for (
    $I = 1;
    $I <= Number(document.getElementById("SPLIT_HIDDEN_" + $iID).value);
    $I++
  ) {
    if ($I != $iNbr) {
      document.getElementById("SPLIT_NAME_" + $iID + "_" + $iSpot).value =
        document.getElementById("SPLIT_NAME_" + $iID + "_" + $I).value;
      document.getElementById("SPLIT_AMOUNT_" + $iID + "_" + $iSpot).value =
        document.getElementById("SPLIT_AMOUNT_" + $iID + "_" + $I).value;

      document.getElementById(
        "SPLIT_ENV_" + $iID + "_" + $iSpot
      ).selectedIndex = document.getElementById(
        "SPLIT_ENV_" + $iID + "_" + $I
      ).selectedIndex;
      $iSpot++;
    }
  }

  $eleWrapper = document.getElementById(
    "TRANS_DIV_WRAPPER_" +
      $iID +
      "_" +
      document.getElementById("SPLIT_HIDDEN_" + $iID).value
  );
  $eleWrapper.parentNode.removeChild($eleWrapper);

  document.getElementById("SPLIT_HIDDEN_" + $iID).value =
    Number(document.getElementById("SPLIT_HIDDEN_" + $iID).value) - 1;

  UpdateRemainingSplit($iID);

  $iNbr = Number(document.getElementById("SPLIT_HIDDEN_" + $iID).value);

  if ($iNbr == 4)
    document.getElementById("TRANS_DIV_NAME_" + $iID + "2").style.visibility =
      "hidden";
  else
    document.getElementById("TRANS_DIV_NAME_" + $iID + "2").style.visibility =
      "visible";
}

function UpdateRemainingSplit($iID) {
  alert("UpdateRemainingSplit");
  $strTransTableToUse = GetCurrentTransTable();

  $iTotal = 0;
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 1)
    $iTotal += Number(
      document
        .getElementById("SPLIT_AMOUNT_" + $iID + "_1")
        .value.replace("$", "")
        .replace(",", "")
    );
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 2)
    $iTotal += Number(
      document
        .getElementById("SPLIT_AMOUNT_" + $iID + "_2")
        .value.replace("$", "")
        .replace(",", "")
    );
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 3)
    $iTotal += Number(
      document
        .getElementById("SPLIT_AMOUNT_" + $iID + "_3")
        .value.replace("$", "")
        .replace(",", "")
    );
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 4)
    $iTotal += Number(
      document
        .getElementById("SPLIT_AMOUNT_" + $iID + "_4")
        .value.replace("$", "")
        .replace(",", "")
    );
  $iTotal = RoundNumber($iTotal, 2);

  $iRemaning =
    Number(document.getElementById("ORIG_AMOUNT_" + $iID).value) -
    Number($iTotal);
  $iRemaning = FormatCurrencyNumber($iRemaning, 2);

  if (
    Number($iTotal) >
    Number(document.getElementById("ORIG_AMOUNT_" + $iID).value)
  )
    $iRemaning = "-" + $iRemaning;
  document.getElementById("REMAINING_" + $iID).innerHTML = $iRemaning;
}

function SplitTrans($iID) {
  alert("SplitTrans");
  $strTransTableToUse = GetCurrentTransTable();

  jQuery("#TR_SPLIT_" + $iID).hide();
  jQuery("#TR_SPLIT_" + $iID + "2").hide();

  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 4) {
    alert("Transactions can only be broken into 4 parts.");
    return;
  }

  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 1) {
    $strName1 = document.getElementById("SPLIT_NAME_" + $iID + "_1").value;
    $strEnv1 = document.getElementById("SPLIT_ENV_" + $iID + "_1").value;
    $strAmount1 = document.getElementById("SPLIT_AMOUNT_" + $iID + "_1").value;
  }
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 2) {
    $strName2 = document.getElementById("SPLIT_NAME_" + $iID + "_2").value;
    $strEnv2 = document.getElementById("SPLIT_ENV_" + $iID + "_2").value;
    $strAmount2 = document.getElementById("SPLIT_AMOUNT_" + $iID + "_2").value;
  }
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 3) {
    $strName3 = document.getElementById("SPLIT_NAME_" + $iID + "_3").value;
    $strEnv3 = document.getElementById("SPLIT_ENV_" + $iID + "_3").value;
    $strAmount3 = document.getElementById("SPLIT_AMOUNT_" + $iID + "_3").value;
  }
  if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 4) {
    $strName4 = document.getElementById("SPLIT_NAME_" + $iID + "_4").value;
    $strEnv4 = document.getElementById("SPLIT_ENV_" + $iID + "_4").value;
    $strAmount4 = document.getElementById("SPLIT_AMOUNT_" + $iID + "_4").value;
  }
  document.getElementById("SPLIT_HIDDEN_" + $iID).value =
    Number(document.getElementById("SPLIT_HIDDEN_" + $iID).value) + 1;
  $strMonth = document.getElementById("Month").value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "SplitTrans",
      ID: $iID,
      NUMBER: document.getElementById("SPLIT_HIDDEN_" + $iID).value,
      Month: $strMonth,
    },
    function ($strData) {
      document.getElementById("TD_SPLIT_" + $iID).innerHTML =
        document.getElementById("TD_SPLIT_" + $iID).innerHTML + $strData;
      if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 2) {
        document.getElementById("SPLIT_NAME_" + $iID + "_1").value = $strName1;
        document.getElementById("SPLIT_ENV_" + $iID + "_1").value = $strEnv1;
        document.getElementById("SPLIT_AMOUNT_" + $iID + "_1").value =
          $strAmount1;
      }
      if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 3) {
        document.getElementById("SPLIT_NAME_" + $iID + "_2").value = $strName2;
        document.getElementById("SPLIT_ENV_" + $iID + "_2").value = $strEnv2;
        document.getElementById("SPLIT_AMOUNT_" + $iID + "_2").value =
          $strAmount2;
      }
      if (document.getElementById("SPLIT_HIDDEN_" + $iID).value >= 4) {
        document.getElementById("SPLIT_NAME_" + $iID + "_3").value = $strName3;
        document.getElementById("SPLIT_ENV_" + $iID + "_3").value = $strEnv3;
        document.getElementById("SPLIT_AMOUNT_" + $iID + "_3").value =
          $strAmount3;
      }

      $iNbr = Number(document.getElementById("SPLIT_HIDDEN_" + $iID).value);
      if ($iNbr == 1) {
        $iDIVNameWidth = document.getElementById(
          "TRANS_DIV_NAME_" + $iID + "_" + $iNbr
        ).offsetWidth;
        $iDIVEnvWidth = document.getElementById(
          "TRANS_DIV_ENV_" + $iID + "_" + $iNbr
        ).offsetWidth;
        $iDIVAmountWidth = document.getElementById(
          "TRANS_DIV_AMOUNT_" + $iID + "_" + $iNbr
        ).offsetWidth;

        document.getElementById("TRANS_DIV_NAME_" + $iID).style.width =
          Number($iDIVNameWidth) - 20 + "px";
        document.getElementById("TRANS_DIV_ENV_" + $iID).style.width =
          Number($iDIVEnvWidth) - 20 + "px";
        document.getElementById("TRANS_DIV_AMOUNT_" + $iID).style.width =
          Number($iDIVAmountWidth) - 20 + "px";

        document.getElementById("TRANS_DIV_NAME_" + $iID + "2").style.width =
          Number($iDIVNameWidth) - 20 + "px";
        document.getElementById("TRANS_DIV_ENV_" + $iID + "2").style.width =
          Number($iDIVEnvWidth) - 20 + "px";
        document.getElementById("TRANS_DIV_AMOUNT_" + $iID + "2").style.width =
          Number($iDIVAmountWidth) - 20 + "px";

        UpdateRemainingSplit($iID);
      }
      if ($iNbr == 4) {
        document.getElementById(
          "TRANS_DIV_NAME_" + $iID + "2"
        ).style.visibility = "hidden";
      }
    }
  );
}

function PrepBarRange($iMax) {
  alert("PrepBarRange");
  $iMax = RoundNumber($iMax * 1.15, 0);

  $iToNearest = 1;
  for ($I = 1; $I < String().length; $I++) $iToNearest *= 10;

  $iBarRange = RoundToNearest($iMax, $iToNearest);

  return $iBarRange;
}

function SyncAmyDrew() {
  alert("SyncAmyDrew");
  $.post(
    "/cgi-bin/FinancesFunctions.php",
    { action: "SyncAmyDrew" },
    function ($strData) {
      location.reload();
    }
  );
}

function Reports() {
  alert("Reports");
  $strTransTableToUse = GetCurrentTransTable();

  $iStart = document.getElementById("DateStart").value;
  $iEnd = document.getElementById("DateEnd").value;
  $iEnvID = document.getElementById("EnvID").value;
  $strOrderBy = document.getElementById("OrderBy").value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "Reports",
      Start: $iStart,
      End: $iEnd,
      EnvID: $iEnvID,
      OrderBy: $strOrderBy,
    },
    function ($strData) {
      document.getElementById("DIV_Chart1").innerHTML = $strData;
    }
  );
}

function Reports_old() {
  alert("Reports_old");
  $strTransTableToUse = GetCurrentTransTable();

  $iStart = document.getElementById("DateStart").value;
  $iEnd = document.getElementById("DateEnd").value;
  $iEnvID = document.getElementById("EnvID").value;
  $strOrderBy = document.getElementById("OrderBy").value;

  $.post(
    "/cgi-bin/FinancesFunctions.php",
    {
      TransTableToUse: $strTransTableToUse,
      action: "Reports",
      Start: $iStart,
      End: $iEnd,
      EnvID: $iEnvID,
      OrderBy: $strOrderBy,
    },
    function ($strData) {
      /*
			document.getElementById("DIV_Chart1").innerHTML = '';
			document.getElementById("DIV_Chart2").innerHTML = '';
			document.getElementById("DIV_Chart3").innerHTML = '';

			document.getElementById("DIV_ChartAdditional1").innerHTML = '';
			document.getElementById("DIV_ChartAdditional2").innerHTML = '';
			document.getElementById("DIV_ChartAdditional3").innerHTML = '';

			document.getElementById("DIV_Chart1").style.display = "none";
			document.getElementById("DIV_Chart2").style.display = "none";
			document.getElementById("DIV_Chart3").style.display = "none";
			document.getElementById("DIV_ChartAdditional1").style.display = "none";
			document.getElementById("DIV_ChartAdditional2").style.display = "none";
			document.getElementById("DIV_ChartAdditional3").style.display = "none";
			document.getElementById("DIV_ChartLegend1").style.display = "none";
			document.getElementById("DIV_ChartLegend2").style.display = "none";
			document.getElementById("DIV_ChartLegend3").style.display = "none";

			$aReportParts = $strData.split("RRRRR");

			/******** Actual Expenditures vs. Budgeted Expenditures ********/
      document.getElementById("DIV_Chart1").style.display = "";
      document.getElementById("DIV_ChartAdditional1").style.display = "";
      document.getElementById("DIV_ChartLegend1").style.display = "";

      $aParts = $aReportParts[0].split("YYYYY");
      $aDataParts = $aParts[0].split("XXXXX");

      $aTicks = new Array($aDataParts.length);
      $aExpend = new Array($aDataParts.length);
      $aIncomeBgt = new Array($aDataParts.length);
      $iMax = 0;

      for ($I = 0; $I < $aDataParts.length; $I++) {
        $aFinalParts = $aDataParts[$I].split("-----");
        $aTicks[$I] = $aFinalParts[0];
        $aExpend[$I] = Number($aFinalParts[1]);
        $aIncomeBgt[$I] = Number($aFinalParts[2]);

        if (Number($aFinalParts[1]) > $iMax) $iMax = Number($aFinalParts[1]);

        if (Number($aFinalParts[2]) > $iMax) $iMax = Number($aFinalParts[2]);
      }

      DisplayChart(
        "Actual Expenditures vs. Budgeted Expenditures",
        "DIV_Chart1",
        $aTicks,
        $aExpend,
        $aIncomeBgt,
        PrepBarRange($iMax)
      );

      $strText =
        '<table><tr><td>Total Expenditures: </td><td style="padding-left:5px">$' +
        CommaSeparateNumber(RoundNumber($aParts[1], 2)) +
        '</td><td style="padding-left:10px;">Budgeted Income: </td><td style="padding-left:5px">$' +
        CommaSeparateNumber(RoundNumber($aParts[2], 2)) +
        '</td><td style="padding-left:15px;">Balance: </td><td style="padding-left:5px">$' +
        CommaSeparateNumber(
          RoundNumber(Number($aParts[2]) - Number($aParts[1]), 2)
        ) +
        "</td></td></tr></table>";
      document.getElementById("DIV_ChartAdditional1").innerHTML = $strText;

      /******** Actual Expenditures vs. Actual Income ********/
      if ($aReportParts.length > 1) {
        document.getElementById("DIV_Chart2").style.display = "";
        document.getElementById("DIV_ChartAdditional2").style.display = "";
        document.getElementById("DIV_ChartLegend2").style.display = "";

        $aParts = $aReportParts[1].split("YYYYY");
        $aDataParts = $aParts[0].split("XXXXX");

        $aTicks = new Array($aDataParts.length);
        $aExpend = new Array($aDataParts.length);
        $aIncomeAct = new Array($aDataParts.length);
        $iMax = 0;

        for ($I = 0; $I < $aDataParts.length; $I++) {
          $aFinalParts = $aDataParts[$I].split("-----");
          $aTicks[$I] = $aFinalParts[0];
          $aExpend[$I] = Number($aFinalParts[1]);
          $aIncomeAct[$I] = Number($aFinalParts[2]);

          if (Number($aFinalParts[1]) > $iMax) $iMax = Number($aFinalParts[1]);

          if (Number($aFinalParts[2]) > $iMax) $iMax = Number($aFinalParts[2]);
        }

        DisplayChart(
          "Actual Expenditures vs. Actual Income",
          "DIV_Chart2",
          $aTicks,
          $aExpend,
          $aIncomeAct,
          PrepBarRange($iMax)
        );

        $strText =
          '<table><tr><td>Total Expenditures: </td><td style="padding-left:5px">$' +
          CommaSeparateNumber(RoundNumber($aParts[1], 2)) +
          '</td><td style="padding-left:10px;">Total Income: </td><td style="padding-left:5px">$' +
          CommaSeparateNumber(RoundNumber($aParts[2], 2)) +
          '</td><td style="padding-left:10px;">Balance: </td><td style="padding-left:5px">$' +
          CommaSeparateNumber(
            RoundNumber(Number($aParts[2]) - Number($aParts[1]), 2)
          ) +
          "</td></td></tr></table>";
        document.getElementById("DIV_ChartAdditional2").innerHTML = $strText;
      }

      /******** Budgeted Out vs. Budgeted In ********/
      if ($aReportParts.length > 2) {
        document.getElementById("DIV_Chart3").style.display = "";
        document.getElementById("DIV_ChartAdditional3").style.display = "";
        document.getElementById("DIV_ChartLegend3").style.display = "";

        $aParts = $aReportParts[2].split("YYYYY");
        $aDataParts = $aParts[0].split("XXXXX");

        $aTicks = new Array($aDataParts.length);
        $aExpendBgt = new Array($aDataParts.length);
        $aIncomeBgt = new Array($aDataParts.length);
        $iMax = 0;

        for ($I = 0; $I < $aDataParts.length; $I++) {
          $aFinalParts = $aDataParts[$I].split("-----");
          $aTicks[$I] = $aFinalParts[0];
          $aExpendBgt[$I] = Number($aFinalParts[1]);
          $aIncomeBgt[$I] = Number($aFinalParts[2]);

          if (Number($aFinalParts[1]) > $iMax) $iMax = Number($aFinalParts[1]);

          if (Number($aFinalParts[2]) > $iMax) $iMax = Number($aFinalParts[2]);
        }

        DisplayChart(
          "Budgeted Out vs. Budgeted In",
          "DIV_Chart3",
          $aTicks,
          $aExpendBgt,
          $aIncomeBgt,
          PrepBarRange($iMax)
        );

        $strText =
          '<table><tr><td>Budgeted Expenditures: </td><td style="padding-left:5px">$' +
          CommaSeparateNumber(RoundNumber($aParts[1], 2)) +
          '</td><td style="padding-left:10px;">Total Income: </td><td style="padding-left:5px">$' +
          CommaSeparateNumber(RoundNumber($aParts[2], 2)) +
          '</td><td style="padding-left:10px;">Balance: </td><td style="padding-left:5px">$' +
          CommaSeparateNumber(
            RoundNumber(Number($aParts[2]) - Number($aParts[1]), 2)
          ) +
          "</td></td></tr></table>";
        document.getElementById("DIV_ChartAdditional3").innerHTML = $strText;
      }
    }
  );
}

///////////////////////////////////////////////
// 			SSOLogin Class Javascript
///////////////////////////////////////////////

var $hLoginWindow = {};

function SSOLogin_OpenLogin(
  $strApplication,
  $strUser,
  $strFunctionCallBack,
  $strSessionID
) {
  $iX = screen.width / 2 - 450;
  if ($iX < 0) $iX = 0;
  $iY = 180;

  if (screen.width >= 900) $iWidth = 900;
  else $iWidth = screen.width;

  if (screen.height >= 500) $iHeight = 500;
  else $iHeight = screen.height;

  $strURL =
    "https://myuser.nmu.edu/NMUsso?sys=" +
    $strUser +
    "&SessionMgmt_SessionID=" +
    $strSessionID;
  $hLoginWindow = window.open(
    $strURL,
    "_blank",
    (config =
      "height=" +
      $iHeight +
      ", width=" +
      $iWidth +
      ", toolbar=no, menubar=no, scrollbars=no, resizable=no, location=yes, directories=no, status=no, left=" +
      $iX +
      ", top=" +
      $iY)
  );

  setTimeout(function () {
    SSOLogin_WaitingOnLogin($strApplication, $strFunctionCallBack);
  }, 500);
}

function SSOLogin_WaitingOnLogin($strApplication, $strFunctionCallBack) {
  if (!$hLoginWindow.closed) {
    setTimeout(function () {
      SSOLogin_WaitingOnLogin($strApplication, $strFunctionCallBack);
    }, 500);
  } else {
    jQuery("html,body").css("cursor", "wait");
    jQuery.post(
      "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php?",
      {
        action: "ValidateLogin",
        ApplicationID: $strApplication,
      },
      function ($strData) {
        //console.log($strData);
        if ($strData == "success") eval($strFunctionCallBack);
        jQuery("html,body").css("cursor", "");
      }
    );
  }
}

function SSOLogin_StdRefresh() {
  $strURL = window.location.href;
  $strURL = $strURL.replace("Logout=yes", "");
  window.location.assign($strURL);
}

function SSOLogin_LoginBoxStyle($strApplication) {
  $objParent = jQuery(
    "#SSOLogin_LoginDivID_" + $strApplication + "_Wrapper"
  ).parent();
  $iWidth = $objParent.width();
  if ($iWidth > 350) {
    document.getElementById(
      "SSOLogin_LoginDivID_" + $strApplication + "_Wrapper"
    ).innerHTML =
      '<div class="SSOLogin_LoginButton_LargeScreenStyle_Head"><h3>Application Login</h3></div><div class="SSOLogin_LoginButton_LargeScreenStyle_Body">' +
      document.getElementById(
        "SSOLogin_LoginDivID_" + $strApplication + "_Wrapper"
      ).innerHTML +
      "</div>";
    jQuery("#SSOLogin_LoginDivID_" + $strApplication + "_Wrapper").addClass(
      "SSOLogin_LoginButton_LargeScreenStyle"
    );
  } else {
    document.getElementById(
      "SSOLogin_LoginDivID_" + $strApplication + "_Wrapper"
    ).innerHTML =
      '<div class="SSOLogin_LoginButton_SmallScreenStyle_Head"><h3>Application Login</h3></div><div class="SSOLogin_LoginButton_SmallScreenStyle_Body">' +
      document.getElementById(
        "SSOLogin_LoginDivID_" + $strApplication + "_Wrapper"
      ).innerHTML +
      "</div>";
    jQuery("#SSOLogin_LoginDivID_" + $strApplication + "_Wrapper").addClass(
      "SSOLogin_LoginButton_SmallScreenStyle"
    );
  }
}

///////////////////////////////////////////////
//      ProcessJavascript Class Javascript
///////////////////////////////////////////////

$ProcessJavascript_DividerStart = "##########-AsyncFunctions_Start-##########";
$ProcessJavascript_DividerStop = "##########-AsyncFunctions_End-##########";

function ProcessJavascript_ProcessResults($strData, $strDivID) {
  $aDataParts = ProcessJavascript_ProcessResultsGrabSubstr($strData);
  document.getElementById($strDivID).innerHTML = $aDataParts[0];

  if ($aDataParts[1].length > 0) {
    $aDataParts[1].forEach(function ($strScriptSegment) {
      Script = document.createElement("script");
      Script.innerHTML = $strScriptSegment;
      document.getElementById($strDivID).appendChild(Script);
    });
  }
}

function ProcessJavascript_ProcessResultsGrabSubstr($strData) {
  $aTempArray = new Array();

  $iLimiter = 0;
  while (
    $strData.indexOf($ProcessJavascript_DividerStart) > -1 &&
    $iLimiter < 250
  ) {
    $iStart =
      $strData.indexOf($ProcessJavascript_DividerStart) +
      $ProcessJavascript_DividerStart.length;
    $iEnd = $strData.indexOf($ProcessJavascript_DividerStop);
    $strTarget = $strData.substring($iStart, $iEnd);

    $aTempArray.push($strTarget);

    $strData = $strData.replace(
      $ProcessJavascript_DividerStart +
        $strTarget +
        $ProcessJavascript_DividerStop,
      ""
    );
    $iLimiter = $iLimiter + 1;
  }

  $aResults = new Array();
  $aResults.push($strData);
  $aResults.push($aTempArray);

  return $aResults;
}

///////////////////////////////////////////////
//      MiscJavascript Class Javascript
///////////////////////////////////////////////

function MiscJavascript_ScrollTo(
  $strDivID,
  strFunctionName,
  $iSpeed,
  $iAdditionalOffset
) {
  jQuery("html body").animate(
    {
      scrollTop: jQuery("#" + $strDivID).offset().top - 70 + $iAdditionalOffset,
    },
    $iSpeed,
    function () {
      if (strFunctionName != "") window[strFunctionName]();
    }
  );
}

function MiscJavascript_Sleep($iMilliseconds) {
  var $iStart = new Date().getTime();
  for (var $I = 0; $I < 1e7; $I++)
    if (new Date().getTime() - $iStart > $iMilliseconds) break;
}

function MiscJavascript_OutcomeMgmt(
  $strData,
  $strDivID,
  $strMsg,
  $strScrollToDivID
) {
  $bOK = true;
  $bPostingMsg = false;
  if ($strData.indexOf("An error has occured:") > -1) {
    document.getElementById($strDivID).innerHTML = $strData.replace(
      "An error has occured:",
      $strMsg
    );

    $bOK = false;
    $bPostingMsg = true;
  } else if ($strData.indexOf("A warning has occured:") > -1) {
    document.getElementById($strDivID).innerHTML =
      '<span class="JavaHelper_OutcomeMgmt_Warning">' +
      $strData.replace("A warning has occured:", $strMsg) +
      "</span>";
    $bPostingMsg = true;
  } else if ($strData.indexOf("A positive outcome has occurred:") > -1) {
    document.getElementById($strDivID).innerHTML =
      '<span class="JavaHelper_OutcomeMgmt_Positive">' +
      $strData.replace("A positive outcome has occurred:", $strMsg) +
      "</span>";
    $bPostingMsg = true;
  }

  if ($bPostingMsg) {
    if ($strScrollToDivID != "")
      MiscJavascript_ScrollTo($strScrollToDivID, "", 350, 0);
    jQuery("#" + $strDivID).slideDown("medium");
  } else jQuery("#" + $strDivID).slideUp("medium");

  return $bOK;
}

///////////////////////////////////////////////
//        FileUploadJQ Class Javascript
///////////////////////////////////////////////

function FileUploadJQ_PresetEmptyFieldsAll(
  $aAllUploaders,
  $aAllNumberAllowed,
  $strObject,
  $strShortDir
) {
  for ($I = 0; $I < $aAllUploaders; $I++)
    FileUploadJQ_PresetEmptyFields(
      $strObject,
      $aAllUploaders[$I],
      $aAllNumberAllowed[$I],
      $strShortDir
    );
}

function FileUploadJQ_PresetEmptyFields(
  $strObjName,
  $strPrefix,
  $strNumberAllowed,
  $strShortDir
) {
  for ($I = 1; $I <= $strNumberAllowed; $I)
    FileUploadJQ_FileUploadRemove($strPrefix + $I, $strObjName, $strShortDir);
}

function FileUploadJQ_ProcessjQueryUploadResponse(
  $strFieldName,
  data,
  $objObjName,
  $strShortDir,
  $strDirHTTP
) {
  jQuery("body").css("cursor", "default");

  if (data.originalFiles[0].name != "" && data.result.files[0].name != "") {
    jQuery.post(
      "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php",
      {
        action: "FileUploadUpload",
        ObjName: $objObjName,
        Dir: $strShortDir,
        FieldName: $strFieldName,
        OldFileName: data.originalFiles[0].name,
        AdjustedFileName: data.result.files[0].name,
      },
      function ($strData) {
        FileUploadJQ_ProcessResponse(
          $strFieldName,
          $strData,
          $strDirHTTP,
          data
        );
      }
    );
  } else FileUploadJQ_HandleError("");
}

function FileUploadJQ_UploadStart($strFieldName) {
  document.getElementById($strFieldName + "UploadParentDiv").style.display =
    "none";
  document.getElementById($strFieldName + "Progress").style.display =
    "inline-block";
  document.getElementById($strFieldName + "ProgressLine").style.display =
    "inline-block";
  document.getElementById($strFieldName + "ProgressLine").innerHTML = "0%";
}

function FileUploadJQ_ProgressAll($strFieldName, data) {
  $iProgress = parseInt((data.loaded / data.total) * 100, 10);
  if ($iProgress > 0 && $iProgress < 100)
    document.getElementById($strFieldName + "ProgressLine").innerHTML =
      $iProgress + "%";
  else if ($iProgress == 100)
    document.getElementById($strFieldName + "ProgressLine").innerHTML =
      $iProgress + "% - processing...";

  jQuery("#" + $strFieldName + "Progress .UploadLine").css(
    "width",
    $iProgress + "%"
  );
}

function FileUploadJQ_ProcessResponse(
  $strFieldName,
  $strData,
  $strDirHTTP,
  data
) {
  if ($strData.indexOf("Error:") > -1) {
    FileUploadJQ_HandleError($strFieldName + "", $strData);
  } else {
    if ($strData == "IsImage") {
      document.getElementById($strFieldName + "UploadParentDiv").style.display =
        "none";
      document.getElementById($strFieldName + "Progress").style.display =
        "none";
      document.getElementById($strFieldName + "ProgressLine").style.display =
        "none";
      document.getElementById(
        $strFieldName + "UploadResultWrapper"
      ).style.display = "none";

      document.getElementById($strFieldName + "UploadImage").innerHTML = "";
      document.getElementById($strFieldName + "UploadImage").style.display =
        "inline-block";
      document.getElementById($strFieldName + "UploadResultCaption").innerHTML =
        "";
      document.getElementById($strFieldName + "Result").style.display = "none";
      document.getElementById($strFieldName + "Result").innerHTML = "";

      document.getElementById(
        $strFieldName + "UploadCaptionWrapper"
      ).style.display = "inline-block";
      document.getElementById($strFieldName + "UploadCaptionField").value = "";
      document.getElementById($strFieldName + "UploadCaptionButton").disabled =
        "true";
    } else {
      $strCaption = "";
      $strFileName = FileUploadJQ_AlterName(data.result.files[0].name);

      document.getElementById($strFieldName + "UploadParentDiv").style.display =
        "none";
      document.getElementById($strFieldName + "Progress").style.display =
        "none";
      document.getElementById($strFieldName + "ProgressLine").style.display =
        "none";

      document.getElementById(
        $strFieldName + "UploadResultWrapper"
      ).style.display = "inline-block";
      document.getElementById($strFieldName + "UploadImage").innerHTML = "";
      document.getElementById($strFieldName + "UploadImage").style.display =
        "none";
      document.getElementById(
        $strFieldName + "UploadResultCaption"
      ).style.display = "none";
      document.getElementById($strFieldName + "UploadResultCaption").innerHTML =
        "";

      document.getElementById($strFieldName + "Result").style.display =
        "inline-block";
      document.getElementById($strFieldName + "Result").style.color = "inherit";
      document.getElementById($strFieldName + "Result").innerHTML =
        '<a href="' +
        $strDirHTTP +
        data.result.files[0].name +
        '" target="_blank">' +
        $strFileName +
        "</a>";
    }
  }
}

function FileUploadJQ_PresetEmptyField($strFieldName) {
  document.getElementById($strFieldName + "UploadParentDiv").style.display =
    "inline-block";
  document.getElementById($strFieldName + "Progress").style.display = "none";
  document.getElementById($strFieldName + "ProgressLine").style.display =
    "none";

  document.getElementById($strFieldName + "UploadResultWrapper").style.display =
    "none";
  document.getElementById($strFieldName + "UploadImage").innerHTML = "";
  document.getElementById($strFieldName + "UploadImage").style.display = "none";
  document.getElementById($strFieldName + "UploadResultCaption").style.display =
    "none";
  document.getElementById($strFieldName + "UploadResultCaption").innerHTML = "";

  document.getElementById($strFieldName + "Result").style.display = "none";
  document.getElementById($strFieldName + "Result").style.color = "inherit";
  document.getElementById($strFieldName + "Result").innerHTML = "";
}

function FileUploadJQ_ConfigureInitial(
  $strFieldName,
  $strFileName,
  $strCaption,
  $strActualType,
  $strDisplayImage,
  $strDirHTTP
) {
  if ($strFileName != "") {
    if ($strFileName.indexOf("/") > -1) {
      $aParts = $strFileName.split("/");
      $strFileName = $aParts[1];
    }

    $strAltFileName = FileUploadJQ_AlterName($strFileName);

    document.getElementById($strFieldName + "Progress").style.display = "none";
    document.getElementById($strFieldName + "ProgressLine").style.display =
      "none";
    document.getElementById($strFieldName + "UploadParentDiv").style.display =
      "none";

    if ($strActualType == "image" && $strDisplayImage) {
      $strImageField =
        '<img src="' +
        $strDirHTTP +
        $strFileName +
        '" alt="' +
        $strCaption +
        '" title="' +
        $strCaption +
        '">';

      document.getElementById(
        $strFieldName + "UploadResultWrapper"
      ).style.display = "inline-block";
      document.getElementById($strFieldName + "UploadImage").innerHTML =
        '<a href="' +
        $strDirHTTP +
        $strFileName +
        '" target="_blank">' +
        $strImageField +
        "</a>";
      document.getElementById($strFieldName + "UploadImage").style.display =
        "inline-block";
      document.getElementById(
        $strFieldName + "UploadResultCaption"
      ).style.display = "";
      document.getElementById($strFieldName + "UploadResultCaption").innerHTML =
        '<span style="font-size:smaller">' + $strCaption + "</span>";

      document.getElementById($strFieldName + "Result").style.display =
        "inline-block";
      document.getElementById($strFieldName + "Result").style.color = "inherit";
      document.getElementById($strFieldName + "Result").innerHTML =
        '<a href="' +
        $strDirHTTP +
        $strFileName +
        '" target="_blank">' +
        $strAltFileName +
        "</a>";
    } else {
      document.getElementById(
        $strFieldName + "UploadResultWrapper"
      ).style.display = "inline-block";
      document.getElementById($strFieldName + "UploadImage").innerHTML = "";
      document.getElementById($strFieldName + "UploadImage").style.display =
        "none";
      document.getElementById(
        $strFieldName + "UploadResultCaption"
      ).style.display = "none";
      document.getElementById($strFieldName + "UploadResultCaption").innerHTML =
        "";

      document.getElementById($strFieldName + "Result").style.display =
        "inline-block";
      document.getElementById($strFieldName + "Result").style.color = "inherit";
      document.getElementById($strFieldName + "Result").innerHTML =
        '<a href="' +
        $strDirHTTP +
        $strFileName +
        '" target="_blank">' +
        $strAltFileName +
        "</a>";
    }
  } else FileUploadJQ_PresetEmptyField($strFieldName);
}

function FileUploadJQ_FileResetUploader($strObjName) {
  jQuery.post(
    "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php",
    {
      action: "FileUploadResetFieldAll",
      ObjName: $strObjName,
    },
    function ($strData) {
      $aParts = $strData.split("XXXXOOOOXXXX");
      $aParts.forEach(function ($strFieldName) {
        if ($strFieldName != "") {
          FileUploadJQ_ResetInput($strFieldName, $strObjName);
          document.getElementById(
            $strFieldName + "UploadWrapper"
          ).style.display = "none";
          $strFinalFieldName = $strFieldName;
        }
      });

      FileUploadJQ_ExposeNext($strFinalFieldName);
    }
  );
}

function FileUploadJQ_AlterName($strName) {
  if ($strName.length > 20) {
    $strNew = "";

    $aParts = $strName.split(".");
    for ($i = 0; $i < $aParts.length - 1; $i++) $strNew += "." + $aParts[$i];
    $strNew = $strNew.substr(1, 15);
    $strNew += "..." + $aParts[$aParts.length - 1];
    return $strNew;
  } else return $strName;
}

function FileUploadJQ_UploadFlipColor($strFieldName, $strColor, $strID) {
  document.getElementById($strFieldName + $strID).style.color = $strColor;
}

function FileUploadJQ_FileUploadRemove(
  $strFieldName,
  $strObjName,
  $strShortDir
) {
  jQuery.post(
    "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php",
    {
      action: "FileUploadRemove",
      ObjName: $strObjName,
      Dir: $strShortDir,
      FieldName: $strFieldName,
    },
    function ($strData) {
      if ($strData.indexOf("Error:") > -1)
        FileUploadJQ_HandleError($strFieldName, $strData);
      else {
        var testElement = document.getElementById(
          $strFieldName + "UploadWrapper"
        );
        if (testElement != null) {
          document.getElementById(
            $strFieldName + "UploadWrapper"
          ).style.display = "none";
          document.getElementById(
            $strFieldName + "UploadParentDiv"
          ).style.display = "inline-block";

          document.getElementById($strFieldName + "Progress").style.display =
            "none";
          document.getElementById(
            $strFieldName + "ProgressLine"
          ).style.display = "none";

          document.getElementById(
            $strFieldName + "UploadResultWrapper"
          ).style.display = "none";
          document.getElementById($strFieldName + "UploadImage").innerHTML = "";
          document.getElementById($strFieldName + "UploadImage").style.display =
            "none";
          document.getElementById($strFieldName + "Result").style.display =
            "none";
          document.getElementById($strFieldName + "Result").style.color =
            "inherit";
          document.getElementById($strFieldName + "Result").innerHTML = "";

          FileUploadJQ_ResetVisibility($strFieldName);
        }
      }
    }
  );
}

function FileUploadJQ_HandleError($strFieldName, $strError) {
  if ($strError == "")
    $strError =
      "An error has occured. Please try again or contant NMU's web team at 906-227-2720.";

  document.getElementById($strFieldName + "Progress").style.display = "none";
  document.getElementById($strFieldName + "ProgressLine").style.display =
    "none";
  document.getElementById($strFieldName + "UploadParentDiv").style.display =
    "none";
  document.getElementById($strFieldName + "UploadImage").style.display = "none";

  document.getElementById($strFieldName + "UploadResultWrapper").style.display =
    "inline-block";
  document.getElementById($strFieldName + "Result").style.color = "red";
  document.getElementById($strFieldName + "Result").style.display =
    "inline-block";
  document.getElementById($strFieldName + "Result").style.whiteSpace = "normal";
  document.getElementById($strFieldName + "Result").innerHTML = $strError;
}

function FileUploadJQ_CaptionDisability($strFieldName) {
  if (document.getElementById($strFieldName + "UploadCaptionField").value == "")
    document.getElementById($strFieldName + "UploadCaptionButton").disabled =
      "true";
  else
    document.getElementById($strFieldName + "UploadCaptionButton").disabled =
      "";
}

function FileUploadJQ_ResetInput($strFieldName, $strObjName) {
  document.getElementById($strFieldName + "UploadParentDiv").style.display =
    "inline-block";
  document.getElementById($strFieldName + "Progress").style.display = "none";
  document.getElementById($strFieldName + "ProgressLine").style.display =
    "none";

  document.getElementById($strFieldName + "UploadResultWrapper").style.display =
    "none";
  document.getElementById($strFieldName + "UploadImage").innerHTML = "";
  document.getElementById($strFieldName + "UploadImage").style.display = "none";
  document.getElementById(
    $strFieldName + "UploadCaptionWrapper"
  ).style.display = "none";
  document.getElementById($strFieldName + "UploadResultCaption").style.display =
    "none";
  document.getElementById($strFieldName + "UploadResultCaption").innerHTML = "";

  document.getElementById($strFieldName + "Result").style.display = "none";
  document.getElementById($strFieldName + "Result").style.color = "inherit";
  document.getElementById($strFieldName + "Result").innerHTML = "";

  jQuery.post(
    "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php",
    {
      action: "FileUploadResetField",
      ObjName: $strObjName,
      FieldName: $strFieldName,
    },
    function ($strData) {}
  );
}

function FileUploadJQ_SaveCaption(
  $strFieldName,
  $strDisplayImage,
  $iID,
  $strObjName,
  $strShortDir,
  $bSaveOnFinish,
  $strDirHTTP,
  $strThumbDir
) {
  $strCaption = document.getElementById(
    $strFieldName + "UploadCaptionField"
  ).value;

  jQuery.post(
    "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php",
    {
      action: "SaveCaption",
      ObjName: $strObjName,
      Dir: $strShortDir,
      FieldName: $strFieldName,
      Caption: $strCaption,
    },
    function ($strData) {
      $aParts = $strData.split("XXXXOOOOXXXX");
      $strData = $aParts[1];
      if ($aParts[0] == "") $strDisplayImage = false;

      if ($strData.indexOf("/") > -1) {
        $aParts = $strData.split("/");
        $strNameOnly = $aParts[1];
      } else $strNameOnly = $strData;
      $strNameOnly = FileUploadJQ_AlterName($strNameOnly);

      if ($bSaveOnFinish == true) {
        $strCaption = "";

        document.getElementById(
          $strFieldName + "UploadCaptionWrapper"
        ).style.display = "none";
        document.getElementById($strFieldName + "UploadCaptionField").value =
          "";
        document.getElementById(
          $strFieldName + "UploadCaptionButton"
        ).disabled = "true";

        document.getElementById(
          $strFieldName + "UploadParentDiv"
        ).style.display = "none";
        document.getElementById($strFieldName + "Progress").style.display =
          "none";
        document.getElementById($strFieldName + "ProgressLine").style.display =
          "none";

        document.getElementById(
          $strFieldName + "UploadResultWrapper"
        ).style.display = "inline-block";
        document.getElementById($strFieldName + "UploadImage").innerHTML = "";
        document.getElementById($strFieldName + "UploadImage").style.display =
          "none";
        document.getElementById(
          $strFieldName + "UploadResultCaption"
        ).style.display = "none";
        document.getElementById(
          $strFieldName + "UploadResultCaption"
        ).innerHTML = "";

        document.getElementById($strFieldName + "Result").style.display =
          "inline-block";
        document.getElementById($strFieldName + "Result").style.color =
          "inherit";
        document.getElementById($strFieldName + "Result").innerHTML =
          '<span style="color:green">File uploaded sucessfully</span>';
        document.getElementById($strFieldName + "UploadDelete").style.display =
          "none";

        setTimeout(function () {
          FileUploadJQ_ResetInput($strFieldName);
        }, 1500);

        jQuery.post(
          "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php",
          {
            action: "StoreToDBOnCompletion",
            ObjName: $strObjName,
            FieldName: $strFieldName,
            Caption: $strCaption,
            ID: $iID,
          },
          function ($strData) {
            if (
              typeof UploadCallback == "function" &&
              jQuery.isFunction(UploadCallback)
            )
              UploadCallback($strFieldName, $strCaption, $iID);
          }
        );
      } else {
        if ($strDisplayImage == true) {
          document.getElementById(
            $strFieldName + "UploadCaptionWrapper"
          ).style.display = "none";
          document.getElementById($strFieldName + "UploadCaptionField").value =
            "";
          document.getElementById(
            $strFieldName + "UploadCaptionButton"
          ).disabled = "true";

          document.getElementById(
            $strFieldName + "UploadParentDiv"
          ).style.display = "none";
          document.getElementById($strFieldName + "Progress").style.display =
            "none";
          document.getElementById(
            $strFieldName + "ProgressLine"
          ).style.display = "none";

          document.getElementById(
            $strFieldName + "UploadResultWrapper"
          ).style.display = "inline-block";
          document.getElementById($strFieldName + "UploadImage").innerHTML =
            '<a href="' +
            $strDirHTTP +
            $strData +
            '" target="_blank"><img src="' +
            $strDirHTTP +
            $strThumbDir +
            $strData +
            '" alt="' +
            $strCaption +
            '" title="' +
            $strCaption +
            '"></a>';
          document.getElementById($strFieldName + "UploadImage").style.display =
            "inline-block";
          document.getElementById(
            $strFieldName + "UploadResultCaption"
          ).style.display = "";
          document.getElementById(
            $strFieldName + "UploadResultCaption"
          ).innerHTML =
            '<span style="font-size:smaller">' + $strCaption + "</span>";

          document.getElementById($strFieldName + "Result").style.display =
            "inline-block";
          document.getElementById($strFieldName + "Result").style.color =
            "inherit";
          document.getElementById($strFieldName + "Result").innerHTML =
            '<a href="' +
            $strDirHTTP +
            $strData +
            '" target="_blank">' +
            $strNameOnly +
            "</a>";
        } else if ($strDisplayImage == false) {
          $strCaption = "";

          document.getElementById(
            $strFieldName + "UploadCaptionWrapper"
          ).style.display = "none";
          document.getElementById($strFieldName + "UploadCaptionField").value =
            "";
          document.getElementById(
            $strFieldName + "UploadCaptionButton"
          ).disabled = "true";

          document.getElementById(
            $strFieldName + "UploadParentDiv"
          ).style.display = "none";
          document.getElementById($strFieldName + "Progress").style.display =
            "none";
          document.getElementById(
            $strFieldName + "ProgressLine"
          ).style.display = "none";

          document.getElementById(
            $strFieldName + "UploadResultWrapper"
          ).style.display = "inline-block";
          document.getElementById($strFieldName + "UploadImage").innerHTML = "";
          document.getElementById($strFieldName + "UploadImage").style.display =
            "none";
          document.getElementById(
            $strFieldName + "UploadResultCaption"
          ).style.display = "none";
          document.getElementById(
            $strFieldName + "UploadResultCaption"
          ).innerHTML = "";

          document.getElementById($strFieldName + "Result").style.display =
            "inline-block";
          document.getElementById($strFieldName + "Result").style.color =
            "inherit";
          document.getElementById($strFieldName + "Result").innerHTML =
            '<a href="' +
            $strDirHTTP +
            $strData +
            '" target="_blank">' +
            $strNameOnly +
            "</a>";
        }

        if (
          typeof UploadCallback == "function" &&
          jQuery.isFunction(UploadCallback)
        )
          UploadCallback($strFieldName, $strCaption, $iID);
      }

      FileUploadJQ_ExposeNext($strFieldName);
    }
  );
}

function FileUploadJQ_ExposeNext($strFieldName) {
  $strPrefix = FileUploadJQ_GetPrefix($strFieldName);

  $iCurrent = FileUploadJQ_IncrementFieldName($strFieldName, 0);
  while (
    jQuery("#" + $strPrefix + $iCurrent + "UploadWrapper").length > 0 &&
    jQuery("#" + $strPrefix + $iCurrent + "UploadWrapper").is(":visible")
  )
    $iCurrent = FileUploadJQ_IncrementFieldName($strFieldName, $iCurrent);

  if (jQuery("#" + $strPrefix + $iCurrent + "UploadWrapper").length > 0)
    document.getElementById(
      $strPrefix + $iCurrent + "UploadWrapper"
    ).style.display = "inline-block";
}

function FileUploadJQ_ResetVisibility($strFieldName) {
  $strPrefix = FileUploadJQ_GetPrefix($strFieldName);

  $strCurrent = FileUploadJQ_IncrementFieldName($strFieldName, 0);
  while (jQuery("#" + $strPrefix + $strCurrent + "UploadWrapper").length > 0) {
    if (
      document.getElementById($strPrefix + $strCurrent + "UploadImage")
        .innerHTML != ""
    )
      document.getElementById(
        $strPrefix + $strCurrent + "UploadWrapper"
      ).style.display = "inline-block";
    else
      document.getElementById(
        $strPrefix + $strCurrent + "UploadWrapper"
      ).style.display = "none";
    $strCurrent = FileUploadJQ_IncrementFieldName($strFieldName, $strCurrent);
  }

  FileUploadJQ_ExposeNext($strFieldName);
}

function FileUploadJQ_IncrementFieldName($strFieldName, $iCurrent) {
  $strPrefix = FileUploadJQ_GetPrefix($strFieldName);
  $iNumberPart = $strFieldName.replace($strPrefix, "");

  $iCurrent++;
  $strCurrent = String($iCurrent);
  while ($strCurrent.length < $iNumberPart.length)
    $strCurrent = "0" + $strCurrent;

  return $strCurrent;
}

function FileUploadJQ_DecrimentFieldName($strFieldName, $iCurrent) {
  $strPrefix = FileUploadJQ_GetPrefix($strFieldName);
  $iNumberPart = $strFieldName.replace($strPrefix, "");

  $iCurrent--;
  $strCurrent = String($iCurrent);
  while ($strCurrent.length < $iNumberPart.length)
    $strCurrent = "0" + $strCurrent;

  return $strCurrent;
}

function FileUploadJQ_GetPrefix($strFieldName) {
  $I = $strFieldName.length - 1;
  while (!isNaN($strFieldName.substring($I, $I + 1))) $I--;
  $strPrefix = $strFieldName.substring(0, $I + 1);

  return $strPrefix;
}

///////////////////////////////////////////////
//           Modal Class Javascript
///////////////////////////////////////////////

/*
function Modal_AddHTML_SetTheBlowupSize($objImgID)
{
	$iMaxWidth = jQuery( window ).width()*.85;
	$iMaxHeight = jQuery( window ).height()-230;

	$iRatio = 0;
	$iWidth = document.getElementById($objImgID).width;
	$iHeight = document.getElementById($objImgID).height;

	if($iWidth > $iMaxWidth)
	{
		$iRatio = $iMaxWidth/$iWidth;
		document.getElementById($objImgID).width = ($iMaxWidth);
		document.getElementById($objImgID).height = ($iHeight*$iRatio);

		$iWidth = $iMaxWidth;
		$iHeight = $iHeight*$iRatio;
	}

	if($iHeight > $iMaxHeight)
	{
		$iRatio = $iMaxHeight/$iHeight;
		document.getElementById($objImgID).height = ($iMaxHeight);
		document.getElementById($objImgID).width = ($iWidth*$iRatio);

		$iHeight = $iMaxHeight;
		$iWidth = $iWidth*$iRatio;
	}
}
*/

function Modal_SetValues($strContent, $iSlidNumber, $strName) {
  document.getElementById("DIV_" + $strName + "Content").innerHTML =
    $strContent;
  Modal_AddHTML_SetTheBlowupSize("Modal_AddCarouselImage" + $iSlidNumber);
}

function Modal_AddCarousel_HighlightArrows($strName) {
  jQuery("#Modal_AddCarousel_" + $strName + "_Left").addClass(
    "glyphicon-triangle-highlight"
  );
  jQuery("#Modal_AddCarousel_" + $strName + "_Right").addClass(
    "glyphicon-triangle-highlight"
  );
}

function Modal_AddCarousel_NormalArrows($strName) {
  jQuery("#Modal_AddCarousel_" + $strName + "_Left").removeClass(
    "glyphicon-triangle-highlight"
  );
  jQuery("#Modal_AddCarousel_" + $strName + "_Right").removeClass(
    "glyphicon-triangle-highlight"
  );
}

///////////////////////////////////////////////
//   ScreenElements WYSIWYG Class Javascript
///////////////////////////////////////////////

function ScreenElements_WYSIWYG_StopInput(event, $strFieldName, $iMaxChars) {
  $strBody = ScreenElements_WYSIWYG_BodyStripper($strFieldName);

  if ($strBody.length >= $iMaxChars && event.data.getKeystroke() != 8)
    event.data.$.preventDefault();
}

function ScreenElements_WYSIWYG_CheckLength(event, $strFieldName, $iMaxChars) {
  $strBody = ScreenElements_WYSIWYG_BodyStripper($strFieldName);
  if ($strBody.length < $iMaxChars)
    $strStatus =
      '<span style="color:black">' + ($iMaxChars - $strBody.length) + "</span>";
  else
    $strStatus =
      '<span style="color:red">' + ($iMaxChars - $strBody.length) + "</span>";

  document.getElementById(
    "JS_AddEditor_" + $strFieldName + "_Counter"
  ).innerHTML = "Characters remaining: " + $strStatus;
}

function ScreenElements_WYSIWYG_BodyStripper($strFieldName) {
  $strBody = CKEDITOR.instances[$strFieldName].getData();
  $strRegex = /(<([^>]+)>)/gi;
  $strBody = $strBody.replace($strRegex, "");
  $strBody = $strBody.replace("&nbsp;", "_");
  $strBody = $strBody.replace(" _", " ");
  $strBody = $strBody.replace("_ ", " ");
  $strBody = $strBody.replace("_", " ");

  return $strBody;
}

function ScreenElements_WYSIWYG_CheckCharCount(
  $strFieldName,
  $iMaxChars,
  $iAdditionalOffset
) {
  $strBody = ScreenElements_WYSIWYG_BodyStripper($strFieldName);

  if ($strBody.length >= $iMaxChars) {
    document.getElementById(
      "JS_AddEditor_" + $strFieldName + "_Counter"
    ).innerHTML =
      '<span style="color:red">Maximum text length is ' +
      $iMaxChars +
      ". Current remaining " +
      ($iMaxChars - $strBody.length) +
      "</span>";
    MiscJavascript_ScrollTo(
      "JS_AddEditor_" + $strFieldName + "_Counter",
      "",
      500,
      0 - $iAdditionalOffset
    );

    return false;
  } else return true;
}

function ScreenElements_RoundNumber($strID, $iDecimalPlacesAllowed) {
  $iValue = Number(jQuery("#" + $strID).val());
  $iValue = $iValue.toFixed($iDecimalPlacesAllowed);
  jQuery("#" + $strID).val($iValue);
}

function ScreenElements_SetAllChecks($strFieldName, $bCheck, $strFormName) {
  if ($strFormName == "") $strFormName = "myForm";

  for ($I = 0; $I < document.forms[$strFormName].elements.length; $I++)
    if (
      document.forms[$strFormName].elements[$I].name.indexOf($strFieldName) > -1
    ) {
      if (document.forms[$strFormName].elements[$I].disabled == false)
        document.forms[$strFormName].elements[$I].checked = $bCheck;
      else document.forms[$strFormName].elements[$I].checked = $bCheck;
    }
}

function ScreenElements_CompletePartialPhone($strFieldName) {
  $strValue = jQuery("#" + $strFieldName).val();

  $regPattern = new RegExp("[^1-9][0-9]");
  $strResult = $regPattern.test($strValue);

  if (!$strResult && $strValue > 0 && $strValue.length == 10)
    jQuery("#" + $strFieldName).val(
      $strValue.substr(0, 3) +
        "-" +
        $strValue.substr(3, 3) +
        "-" +
        $strValue.substr(6, 4)
    );
  else if (
    !$strResult &&
    $strValue > 0 &&
    $strValue.length == 11 &&
    $strValue.substr(0, 1) == "1"
  )
    jQuery("#" + $strFieldName).val(
      $strValue.substr(1, 3) +
        "-" +
        $strValue.substr(4, 3) +
        "-" +
        $strValue.substr(7, 4)
    );

  Private_ScreenElements_ValidateField($strFieldName);
}

function ScreenElements_CompletePartialZip($strFieldName) {
  $strValue = jQuery("#" + $strFieldName).val();

  $strValue.replace("-", "");
  if (jQuery.isNumeric($strValue) && $strValue.length == 5)
    jQuery("#" + $strFieldName).val($strValue);
  else if (jQuery.isNumeric($strValue) && $strValue.length == 9)
    jQuery("#" + $strFieldName).val(
      $strValue.substr(0, 5) + "-" + $strValue.substr(5, 4)
    );
  else ScreenElements_ValidateField($strFieldName);
}

function ScreenElements_ValidateForm($strFormID, $strAction, $strURL) {
  localStorage.setItem("HighestErrorFieldID", "");

  $bSuccess = true;
  jQuery("#" + $strFormID + " input").each(function () {
    $bResult = Private_ScreenElements_ValidateField(this.id);
    if (!$bResult) $bSuccess = false;
  });

  jQuery("#" + $strFormID + " select").each(function () {
    $bResult = Private_ScreenElements_ValidateField(this.id);
    if (!$bResult) $bSuccess = false;
  });

  jQuery("#" + $strFormID + " textarea").each(function () {
    $strFieldID = this.id;
    $nmuType = jQuery("#" + $strFieldID).attr("data-nmu-special-type");
    if ($nmuType == "wysiwyg") {
      $bReq = jQuery("#" + $strFieldID).attr("data-nmu-req");

      if ($bReq && CKEDITOR.instances[$strFieldID].getData() == "") {
        ScreenElements_MarkCKEditorValidOrNot($strFieldID, false);
        Private_ScreenElements_LogHeight($strFieldID, true);
        $bSuccess = false;
      } else ScreenElements_MarkCKEditorValidOrNot($strFieldID, true);
    } else {
      $bResult = Private_ScreenElements_ValidateField(this.id);
      if (!$bResult) $bSuccess = false;
    }
  });

  if ($bSuccess) {
    if ($strAction != "") eval($strAction);
    else if ($strURL != "") window.location = $strURL;
    else document.getElementById($strFormID).submit();
  } else
    Private_DetermineNeedForScroll(
      jQuery("#" + localStorage.getItem("HighestErrorFieldID")).offset().top -
        72
    );
}

function Private_DetermineNeedForScroll($iLocation) {
  $iSpot = jQuery("#" + localStorage.getItem("HighestErrorFieldID")).offset()
    .top;
  $iOffTheTop = jQuery(window).scrollTop() + 60;
  $iOffTheBottom = jQuery(window).scrollTop() + jQuery(window).height();

  if ($iSpot < $iOffTheTop || $iSpot > $iOffTheBottom)
    jQuery("html, body").animate(
      {
        scrollTop:
          jQuery("#" + localStorage.getItem("HighestErrorFieldID")).offset()
            .top - 72,
      },
      1000
    );
}

function ScreenElements_ValidateField(event) {
  $strFieldID = event.target.id;
  Private_ScreenElements_ValidateField($strFieldID);
}

function Private_ScreenElements_ValidateField($strFieldID) {
  $strType = jQuery("#" + $strFieldID).prop("type");
  $strValue = jQuery("#" + $strFieldID).prop("value");
  $bReq = jQuery("#" + $strFieldID).attr("data-nmu-req");

  $strDivID = $strFieldID + "-form-group";
  $strHasError = "has-error";
  $strInvalidAttr = "aria-invalid";

  if ($strType != "checkbox" && $strType != "radio" && $strType != "file") {
    if ($bReq == true && $strValue == "")
      jQuery("#" + $strDivID)
        .addClass($strHasError)
        .attr($strInvalidAttr, "true");
    else if ($strValue != "") {
      $strPattern = jQuery("#" + $strFieldID).prop("pattern");
      if ($strPattern != "") {
        $regPattern = new RegExp($strPattern);
        $strResult = $regPattern.test($strValue);
        if ($strResult == false)
          jQuery("#" + $strDivID)
            .addClass($strHasError)
            .attr($strInvalidAttr, "true");
        else
          jQuery("#" + $strDivID)
            .removeClass($strHasError)
            .removeAttr($strInvalidAttr);
      } else
        jQuery("#" + $strDivID)
          .removeClass($strHasError)
          .removeAttr($strInvalidAttr);
    } else
      jQuery("#" + $strDivID)
        .removeClass($strHasError)
        .removeAttr($strInvalidAttr);

    if (jQuery("#" + $strDivID).hasClass($strHasError)) {
      Private_ScreenElements_LogHeight($strDivID, false);
      return false;
    } else return true;
  } else if ($strType == "file") {
    $strCaptionFieldID = $strFieldID + "UploadCaptionField";
    $strCaptionDivID = $strFieldID + "UploadCaptionWrapper";

    if (jQuery("#" + $strCaptionDivID).is(":visible"))
      jQuery("#" + $strCaptionDivID)
        .addClass($strHasError)
        .attr($strInvalidAttr, "true");
    else
      jQuery("#" + $strCaptionDivID)
        .removeClass($strHasError)
        .removeAttr($strInvalidAttr);

    if (jQuery("#" + $strCaptionDivID).hasClass($strHasError)) {
      Private_ScreenElements_LogHeight($strCaptionDivID, false);
      return false;
    } else return true;
  } else return true;
}

function Private_ScreenElements_LogHeight($strDivID, $bIsWYSIWYG) {
  if ($bIsWYSIWYG) $strDivID = $strDivID + "-validation-div";

  $strHighestID = localStorage.getItem("HighestErrorFieldID");
  if (
    !$strHighestID ||
    ($strHighestID != "" &&
      jQuery("#" + $strDivID).offset().top <
        jQuery("#" + $strHighestID).offset().top)
  )
    localStorage.setItem("HighestErrorFieldID", $strDivID);
}

function ScreenElements_MarkCKEditorValidOrNot($strFieldID, $bPassed) {
  if ($bPassed)
    jQuery("#" + $strFieldID + "-validation-div").removeClass(
      "screen-elements-required"
    );
  else
    jQuery("#" + $strFieldID + "-validation-div").addClass(
      "screen-elements-required"
    );
}

function ScreenElements_CheckUncheck($strClassCheckGroup, $bChecked) {
  jQuery("." + $strClassCheckGroup).prop("checked", $bChecked);
}

function ScreenElements_SwapClass(
  $strClassParentGroup,
  $strElementType,
  $strToRemove,
  $strToAdd
) {
  if ($strToRemove != "")
    jQuery("#" + $strClassParentGroup + " " + $strElementType).removeClass(
      $strToRemove
    );

  if ($strToAdd != "")
    jQuery("#" + $strClassParentGroup + " " + $strElementType).addClass(
      $strToAdd
    );
}

///// TimeSelect
function ScreenElements_TimeSelectInit($strFieldName) {
  if (!jQuery("#ptTimeSelectCntr").length)
    jQuery.post(
      "/cgi-bin/Includes/2015_FunctionsCommonFunctions.php?",
      { action: "TimePopUp" },
      function ($strData) {
        jQuery("#" + $strFieldName).bind("click", function () {
          ScreenElements_TimeSelectOpen($strFieldName);
        });
        jQuery("body").append($strData);
        localStorage["TimeSelect"] = jQuery("#ptTimeSelectCntr").width();
      }
    );
}

function ScreenElements_TimeSelectOpen($strFieldname) {
  ScreenElements_PositionObject("ptTimeSelectCntr", $strFieldname, "bottom");

  if (jQuery("#ptTimeSelectCntr").width() < localStorage["TimeSelect"]) {
    jQuery(".ptTimeSelectHeadRight").css("margin-top", "6px");
    jQuery("#ptTimeSelectLeftBlock")
      .addClass("ptTimeSelectLeftBlockTall")
      .removeClass("ptTimeSelectLeftBlock");
  } else {
    jQuery(".ptTimeSelectHeadRight").css("margin-top", "");
    jQuery("#ptTimeSelectLeftBlock")
      .addClass("ptTimeSelectLeftBlock")
      .removeClass("ptTimeSelectLeftBlockTall");
  }

  if (jQuery("#" + $strFieldname).val() != "") {
    $strRegEx = /([0-9]{1,2}).*:.*([0-9]{2}).*(PM|AM)/i;
    $aMatch = $strRegEx.exec(jQuery("#" + $strFieldname).val());

    if ($aMatch) {
      $iHr = $aMatch[1] || 1;
      $iMin = $aMatch[2] || "00";
      $strAM = $aMatch[3] || "AM";

      jQuery(".ptTimeSelectHrDiv a")
        .removeClass("ptTimeSelectSelectedButton")
        .addClass("ptTimeSelectUnselectedButton");
      jQuery(".ptTimeSelectMinDiv a")
        .removeClass("ptTimeSelectSelectedButton")
        .addClass("ptTimeSelectUnselectedButton");

      jQuery("#Hour_" + $iHr)
        .removeClass("ptTimeSelectUnselectedButton")
        .addClass("ptTimeSelectSelectedButton");
      jQuery("#Minute_" + $iMin)
        .removeClass("ptTimeSelectUnselectedButton")
        .addClass("ptTimeSelectSelectedButton");
      jQuery("#AmPm_" + $strAM)
        .removeClass("ptTimeSelectUnselectedButton")
        .addClass("ptTimeSelectSelectedButton");
    }
  }

  jQuery(".ptTimeSelectMin").bind("click", function () {
    ScreenElements_TimeSelectMin($strFieldname, event);
  });
  jQuery(".ptTimeSelectHr").bind("click", function () {
    ScreenElements_TimeSelectHour($strFieldname, event);
  });
  jQuery("#TimeSelectSet").bind("click", function () {
    ScreenElements_TimeSelectClose($strFieldname);
  });
  jQuery("#TimeSelectClear").bind("click", function () {
    ScreenElements_TimeSelectClear($strFieldname);
  });
  jQuery(document).bind("mouseup", function () {
    ScreenElements_TimeSelectCheckClick($strFieldname, event);
  });

  jQuery("#ptTimeSelectCntr").slideDown("fast");
}

function ScreenElements_TimeSelectClear($strFieldname) {
  jQuery("#" + $strFieldname).val("");
  ScreenElements_TimeSelectClose($strFieldname);
}

function ScreenElements_TimeSelectClose($strFieldname) {
  jQuery("#ptTimeSelectCntr").css("display", "none");

  jQuery(".ptTimeSelectMin").unbind("click");
  jQuery(".ptTimeSelectHr").unbind("click");
  jQuery("#TimeSelectSet").unbind("click");
  jQuery("#TimeSelectClear").unbind("click");
  jQuery(document).unbind("mouseup");

  Private_ScreenElements_ValidateField($strFieldname);
}

function ScreenElements_TimeSelectHour($strFieldname, $Event) {
  if ($Event.target.id == "AmPm_AM" || $Event.target.id == "AmPm_PM")
    jQuery(".ptTimeSelectAmPmDiv a")
      .addClass("ptTimeSelectUnselectedButton")
      .removeClass("ptTimeSelectSelectedButton");
  else
    jQuery(".ptTimeSelectHrDiv a")
      .addClass("ptTimeSelectUnselectedButton")
      .removeClass("ptTimeSelectSelectedButton");
  jQuery("#" + $Event.target.id)
    .addClass("ptTimeSelectSelectedButton")
    .removeClass("ptTimeSelectUnselectedButton");

  ScreenElements_TimeSelectSetTime($strFieldname, $Event);
}

function ScreenElements_TimeSelectMin($strFieldname, $Event) {
  jQuery(".ptTimeSelectMinDiv a")
    .addClass("ptTimeSelectUnselectedButton")
    .removeClass("ptTimeSelectSelectedButton");
  jQuery("#" + $Event.target.id)
    .addClass("ptTimeSelectSelectedButton")
    .removeClass("ptTimeSelectUnselectedButton");

  ScreenElements_TimeSelectSetTime($strFieldname, $Event);
}

function ScreenElements_TimeSelectSetTime($strFieldname, $Event) {
  $strTime = jQuery("#" + $strFieldname).val();
  $strRegEx = /([0-9]{1,2}).*:.*([0-9]{2}).*(PM|AM)/i;
  $aMatch = $strRegEx.exec($strTime);

  if ($aMatch) {
    $iHr = $aMatch[1] || 1;
    $iMin = $aMatch[2] || "00";
    $strAM = $aMatch[3] || "AM";
  } else {
    $iHr = 1;
    $iMin = "00";
    $strAM = "AM";
  }

  $aParts = event.target.id.split("_");

  if (event.target.id.indexOf("Hour_") > -1) $iHr = $aParts[1];
  else if (event.target.id.indexOf("Minute_") > -1) $iMin = $aParts[1];
  else if (event.target.id.indexOf("AmPm_") > -1) $strAM = $aParts[1];

  $strTime = $iHr + ":" + $iMin + " " + $strAM;
  jQuery("#" + $strFieldname).val($strTime);
}

function ScreenElements_TimeSelectCheckClick($strFieldname, $Event) {
  $bPassed = ScreenElements_CloseObjectIfClickElseWhere(
    "ptTimeSelectCntr",
    $Event,
    ""
  );
  if (!$bPassed) ScreenElements_TimeSelectClose($strFieldname);
}

function ScreenElements_CloseObjectIfClickElseWhere(
  $strObjectID,
  $Event,
  $strCloseFunction
) {
  if (!jQuery("#" + $strObjectID + ":visible").length) return;

  $objDiv = jQuery("#" + $strObjectID);
  if (!$objDiv.is($Event.target) && $objDiv.has($Event.target).length === 0) {
    if ($strCloseFunction != "")
      window[$strCloseFunction]($strObjectID, $Event);
    else jQuery("#" + $strObjectID).hide();

    return false;
  } else return true;
}

function ScreenElements_ContainWithin($strObjID) {
  /*
    if (jQuery("#"+$strObjID).length)
    {
        $objObjOffSet = jQuery("#"+$strObjID).offset()
        $objContainerOffSet = jQuery("#"+$strConatinerID).offset()

        $iObjRight = $objObjOffSet.left + jQuery("#"+$strObjID).outerWidth()
        $iContainerRight = $objContainerOffSet.left + jQuery("#"+$strConatinerID).outerWidth()

        if($iObjRight > $iContainerRight)
        {
            $iOverage = $iObjRight - $iContainerRight;
            $iHopeforPos = $objObjOffSet.left - $iOverage
            if($iHopeforPos > $objContainerOffSet.left)
            {
console.log("inside1: "+$objObjOffSet.left)
console.log("inside2: "+$iHopeforPos)
                jQuery("#"+$strObjID).css({ left: $iHopeforPos+"px" });
            }
            else
            {
console.log("outside")
                jQuery("#"+$strObjID).css({ left: $objContainerOffSet.left+"px" });
                jQuery("#"+$strObjID).width(jQuery( "#"+$strConatinerID ).width())
            }
        }
    }
    */
}

function ScreenElements_PositionObject(
  $strObjectID,
  $strAdjacentObj,
  $strPosition
) {
  $iOffSet = jQuery("#" + $strAdjacentObj).offset();
  if ($strPosition == "bottom") {
    $iOffSetTop = $iOffSet.top + jQuery("#" + $strAdjacentObj).outerHeight();
    $iOffsetLeft = $iOffSet.left;
  } else if ($strPosition == "right") {
    $iOffSetTop = $iOffSet.top;
    $iOffsetLeft = $iOffSet.left + jQuery("#" + $strAdjacentObj).outerWidth();
  } else if ($strPosition == "top") {
    $iOffSetTop = $iOffSet.top - jQuery("#" + $strObjectID).outerHeight();
    $iOffsetLeft = $iOffSet.left;
  } else if ($strPosition == "left") {
    $iOffSetTop = $iOffSet.top;
    $iOffsetLeft = $iOffSet.left - jQuery("#" + $strObjectID).outerWidth();
  }

  jQuery("#" + $strObjectID).css({ top: $iOffSetTop, left: $iOffsetLeft });
  jQuery("#" + $strObjectID).zIndex(10);
}

///////////////////////////////////////////////
//   Generic Functions
///////////////////////////////////////////////

function FlipDiv($iDivID) {
  $iDivID = "#" + $iDivID;
  if (jQuery($iDivID).is(":visible")) jQuery($iDivID).hide(600);
  else jQuery($iDivID).show(600);
}

///////////////////////////////////////////////
//   FileUploader Class Javascript
///////////////////////////////////////////////

function FileUploader_UploadAdd(data) {
  data.context = jQuery("<div/>").addClass("file-wrapper").appendTo("#files"); //create new DIV with "file-wrapper" class

  jQuery.each(data.files, function (index, file) {
    //loop though each file
    var node = jQuery("<div/>").addClass("file-row"); //create a new node with \"file-row\" class
    var removeBtn = jQuery("<button/>")
      .addClass("button btn-red remove")
      .text("Remove"); //create new remove button

    var progressBar = jQuery("<div/>")
      .addClass("progress")
      .append(jQuery("<div/>").addClass("progress-bar")); //create progress bar
    var uploadButton = jQuery("<button/>")
      .addClass("button btn-nmu upload")
      .text("Upload"); //create upload button

    uploadButton.on("click", function () {
      //button click function
      var $this = jQuery(this),
        data = $this.data();
      data.submit().always(function () {
        //upload the file
        $this.remove(); //remove this button
      });
    });

    removeBtn.on("click", function (e, data) {
      //remove button function
      jQuery(this).parent().parent().remove(); //remove file's wrapper to remove queued file
    });

    //create file info text, name and file size
    var file_txt = jQuery("<div/>")
      .addClass("file-row-text")
      .append(
        "<span>" + file.name + " (" + file.size / 1000 + " KB)" + "</span>"
      );

    file_txt.append(removeBtn); //add remove button inside info text element
    file_txt.prependTo(node).append(uploadButton.clone(true).data(data)); //add to node element
    progressBar.clone().appendTo(file_txt); //add progress bar

    node.appendTo(data.context); //attach node to data context
  });
}

function FileUploader_Progress(data) {
  var progress = parseInt((data.loaded / data.total) * 100, 10);
  if (data.context) {
    data.context.each(function () {
      jQuery(this)
        .find(".progress")
        .attr("aria-valuenow", progress)
        .children()
        .first()
        .css("width", progress + "%")
        .text(progress + "%");
    });
  }
}

function FileUploader_Done(data) {
  jQuery.each(data.result.files, function (index, file) {
    //loop though each file
    if (file.url) {
      //successful upload returns a file url
      var link = jQuery("<a>").attr("target", "_blank").prop("href", file.url);
      jQuery(data.context.children()[index]).addClass("file-uploaded");
      jQuery(data.context.children()[index]).find("canvas").wrap(link); //create a link to uploaded file url
      jQuery(data.context.children()[index]).find(".file-remove").hide(); //hide remove button
      var done = jQuery('<span class="text-success"/>').text("Uploaded!"); //show success message
      jQuery(data.context.children()[index]).append(done); //add everything to data context
    } else if (file.error) {
      var error = jQuery('<span class="text-danger"/>').text(file.error); //error text
      jQuery(data.context.children()[index]).append(error); //add to data context
    }
  });
}

function FileUploader_ProcessAlways(data) {
  var index = data.index,
    file = data.files[index],
    node = jQuery(data.context.children()[index]);
  if (file.preview) {
    node.prepend(file.preview);
  }
  if (file.error) {
    node.append(jQuery('<span> class="text-danger"/>').text(file.error));
  }
}
