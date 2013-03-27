<?php

class EmailLib {

  public $arrTo = array();
  public $arrCC = array();
  public $arrBCC = array();
  public $strContent = "";
  public $strSubject = "";
  public $arrHeader = array();
  public $arrAttachments = array();
  public $numPriority = 3;
  public $strCharSet = "iso-8859-1";
  public $strContentType = "text/plain";
  public $strEncoding = "8bit";

  function newEmail($addressUser, $addressForm, $title, $content) {
    $this->addUser($addressUser);
    $this->setFrom($addressForm);
    $this->setSubject($title);
    $this->setHTML();
    $this->setContent($content);
  }

  function addUser($strAddress) {
    if ($this->emailCheck($strAddress)) {
      $this->arrTo[] = $strAddress;
      return 1;
    } else {
      return 0;
    }
  }

  function removeUser($strAddress) {
//create array or arrays to check
    $arrVars = array('arrTo', 'arrCC', 'arrBCC');
    foreach ($arrVars as $resVars) {
//sees if the item exists in the array
      $numKey = array_search($strAddress, $this->{$resVars});
      if (isset($numKey)) {
//remove the email address
        $this->{$resVars}[$numKey] = "";
      }
      unset($numKey);
    }
  }

  function addCC($strAddress) {
    if ($this->emailCheck($strAddress)) {
      $this->arrCC[] = $strAddress;
      return 1;
    } else {
      return 0;
    }
  }

  function addBCC($strAddress) {
    if ($this->emailCheck($strAddress)) {
      $this->arrBCC[] = $strAddress;
      return 1;
    } else {
      return 0;
    }
  }

  function addReplyTo($strEmail) {
    if ($this->emailCheck($strEmail)) {
      $this->addHeader("Reply-to: $strEmail");
    }
  }

  function setContent($strText) {
    $this->strContent = $strText;
  }

  function setSubject($strText) {
    $this->strSubject = $strText;
  }

  function setFrom($strText) {
    $this->addHeader("From: $strText");
  }

  function setHTML() {
    $this->strContentType = "text/html";
  }

  function setPlain() {
    $this->strContentType = "text/plain";
  }

  function setPriority($numPriority) {
    $this->numPriority = $numPriority;
  }

  function addReadConfirmationEmail($strEmail) {
    if ($this->emailCheck($strEmail)) {
      $this->addHeader("Disposition-Notification-To: <$strEmail>");
    }
  }

  function addHeader($strText) {
    $this->arrHeader[] = $strText;
  }

  function addAttachment($strFile, $strName, $strType) {
//check to make sure the file exists
    if (file_exists($strFile)) {
      $this->arrAttachments[] = array('path' => $strFile, 'name' => $strName, 'type' => $strType);
    }
  }

  function send() {
    //get all the emails in a string to use
    $strTo = implode(", ", $this->arrTo);

    //add any CC addresses if needed
    if ($this->arrCC) {
      $this->addHeader("Cc: " . implode(", ", $this->arrCC));
    }

    if ($this->arrBCC) {
      $this->addHeader("Bcc: " . implode(", ", $this->arrBCC));
    }

    //append any attachments to the end of the content
    if (count($this->arrAttachments)) {
      $this->appendAttachments();
    }

    //add the additional headers
    $this->addAdditionalHeaders();
    $strHeader = implode("\r\n", $this->arrHeader);

    mail($strTo, $this->strSubject, $this->strContent, $strHeader);
  }

  function appendAttachments() {
    $strBoundary = "H2O-" . time();
    $this->addHeader("Content-Type: multipart/alternitive; boundary=$strBoundary");
    if ($this->strContent) {
      $strContent = "–$strBoundary\r\n";
      $strContent .= "Content-Transfer-Encoding: {$this->strEncoding}\r\n";
      $strContent .= "Content-type: {$this->strContentType}; charset={$this->strCharSet}\r\n\r\n";

      $this->strContent = $strContent . $this->strContent . "\r\n\r\n\r\n";
    }

    //loop through all the attachments
    foreach ($this->arrAttachments as $arrFile) {
      //read the content of the file into a string and base64 encode and split into the correct chunk size
      $strData = chunk_split(base64_encode(implode("", file($arrFile['path']))));

      //add the attachments
      $strAttachment = "–$strBoundary\r\n";
      $strAttachment .= "Content-Transfer-Encoding: base64\r\n";
      $strAttachment .= "Content-Type: {$arrFile['type']}; name={$arrFile['name']}\r\n";
      $strAttachment .= "Content-Disposition: attachment; filename={$arrFile['name']}\r\n\r\n";
      $strAttachment .= "$strData\r\n";

      //add the attachment to the email content
      $this->strContent .= $strAttachment;

      unset($strAttachment);
    }
    $this->strContent .= "–$strBoundary–";
  }

  function addAdditionalHeaders() {
    $this->addHeader("MIME-Version: 1.0");

    //add the content type and charset if there are no attachments
    if (!count($this->arrAttachments)) {
      $this->addHeader("Content-type: {$this->strContentType}; charset={$this->strCharSet}");
    }

    $this->addHeader("X-Priority: $this->numPriority");
  }

  function emailCheck($strAddress) {
    if (ereg("^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$", $strAddress)) {
      return 1;
    } else {
      return 0;
    }
  }

}
?>

