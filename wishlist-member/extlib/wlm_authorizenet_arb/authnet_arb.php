<?php

class AuthnetARBException extends Exception {}

class AuthnetARB {
  //const   LOGIN    = "";
  //const   TRANSKEY = "";

  private $login = "";
  private $trans_key = "";
  private $test    = true;

  private $params  = array();
  private $sucess  = false;
  private $error   = true;

  private $xml;
  private $response;
  private $resultCode;
  private $code;
  private $text;
  private $subscrId;
  private $refId;

  public function __construct($test = false, $login, $key) {
    //if (!trim(self::LOGIN) || !trim(self::TRANSKEY))
    //{
      //throw new AuthnetARBException("You have not configured your Authnet login credentials.");
    //}

    $this->login = $login;
    $this->trans_key = $key;
    if (!trim($this->login) || !trim($this->trans_key)) {
      throw new AuthnetARBException("You have not configured your Authnet login credentials.");
    }

    $this->test = $test;
    $subdomain = ($this->test) ? 'apitest' : 'api';
    $this->url = "https://" . $subdomain . ".authorize.net/xml/v1/request.api";

    $this->params['interval_length']  = 1;
    $this->params['interval_unit']    = 'months';
    $this->params['startDate']        = date("Y-m-d", strtotime("+ 1 month"));
    $this->params['totalOccurrences'] = 9999;
    $this->params['trialOccurrences'] = 0;
    $this->params['trialAmount']      = 0.00;
  }

  public function __toString() {
    if (!$this->params)
    {
      return (string) $this;
    }

    $output  = "";
    $output .= '<table summary="Authnet Results" id="authnet">' . "\n";
    $output .= '<tr>' . "\n\t\t" . '<th colspan="2"><b>Outgoing Parameters</b></th>' . "\n" . '</tr>' . "\n";

    foreach ($this->params as $key => $value)
    {
      $output .= "\t" . '<tr>' . "\n\t\t" . '<td><b>' . $key . '</b></td>';
      $output .= '<td>' . $value . '</td>' . "\n" . '</tr>' . "\n";
    }

    $output .= '</table>' . "\n";
    return $output;
  }

  private function process($retries = 3) {
    $count = 0;
    while ($count < $retries)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $this->response = curl_exec($ch);
      $this->parseResults();
      if ($this->resultCode === "Ok")
      {
        $this->success = true;
        $this->error   = false;
        break;
      }
      else
      {
        $this->success = false;
        $this->error   = true;
        break;
      }
      $count++;
    }
    curl_close($ch);
  }

  public function createAccount() {
    $this->xml = "<?xml version='1.0' encoding='utf-8'?>
      <ARBCreateSubscriptionRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
      <merchantAuthentication>
      <name>" . $this->login . "</name>
      <transactionKey>" . $this->trans_key. "</transactionKey>
      </merchantAuthentication>
      <refId>" . $this->params['refID'] ."</refId>
      <subscription>
      <name>". $this->params['subscrName'] ."</name>
      <paymentSchedule>
      <interval>
      <length>". $this->params['interval_length'] ."</length>
      <unit>". $this->params['interval_unit'] ."</unit>
      </interval>
      <startDate>" . $this->params['startDate'] . "</startDate>
      <totalOccurrences>". $this->params['totalOccurrences'] . "</totalOccurrences>
      <trialOccurrences>". $this->params['trialOccurrences'] . "</trialOccurrences>
      </paymentSchedule>
      <amount>". $this->params['amount'] ."</amount>
      <trialAmount>" . $this->params['trialAmount'] . "</trialAmount>
      <payment>
      <creditCard>
      <cardNumber>" . $this->params['cardNumber'] . "</cardNumber>
      <expirationDate>" . $this->params['expirationDate'] . "</expirationDate>
      </creditCard>
      </payment>
      <order>
      <invoiceNumber>" . $this->params['orderInvoiceNumber'] . "</invoiceNumber>
      <description>" . $this->params['orderDescription'] . "</description>
      </order>
      <customer>
      <id>" . $this->params['customerId'] . "</id>
      <email>" . $this->params['customerEmail'] . "</email>
      <phoneNumber>" . $this->params['customerPhoneNumber'] . "</phoneNumber>
      <faxNumber>" . $this->params['customerFaxNumber'] . "</faxNumber>
      </customer>
      <billTo>
      <firstName>". $this->params['firstName'] . "</firstName>
      <lastName>" . $this->params['lastName'] . "</lastName>
      <company>" . $this->params['company'] . "</company>
      <address>" . $this->params['address'] . "</address>
      <city>" . $this->params['city'] . "</city>
      <state>" . $this->params['state'] . "</state>
      <zip>" . $this->params['zip'] . "</zip>
      </billTo>
      <shipTo>
      <firstName>". $this->params['shipFirstName'] . "</firstName>
      <lastName>" . $this->params['shipLastName'] . "</lastName>
      <company>" . $this->params['shipCompany'] . "</company>
      <address>" . $this->params['shipAddress'] . "</address>
      <city>" . $this->params['shipCity'] . "</city>
      <state>" . $this->params['shipState'] . "</state>
      <zip>" . $this->params['shipZip'] . "</zip>
      </shipTo>
      </subscription>
      </ARBCreateSubscriptionRequest>";
    $this->process();
  }

  public function updateAccount() {
    $this->xml = "<?xml version='1.0' encoding='utf-8'?>
      <ARBUpdateSubscriptionRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
      <merchantAuthentication>
      <name>" . $this->login. "</name>
      <transactionKey>" . $this->trans_key. "</transactionKey>
      </merchantAuthentication>
      <refId>" . $this->params['refID'] ."</refId>
      <subscriptionId>" . $this->params['subscrId'] . "</subscriptionId>
      <subscription>
      <name>". $this->params['subscrName'] ."</name>
      <amount>". $this->params['amount'] ."</amount>
      <trialAmount>" . $this->params['trialAmount'] . "</trialAmount>
      <payment>
      <creditCard>
      <cardNumber>" . $this->params['cardNumber'] . "</cardNumber>
      <expirationDate>" . $this->params['expirationDate'] . "</expirationDate>
      </creditCard>
      </payment>
      <billTo>
      <firstName>". $this->params['firstName'] . "</firstName>
      <lastName>" . $this->params['lastName'] . "</lastName>
      <address>" . $this->params['address'] . "</address>
      <city>" . $this->params['city'] . "</city>
      <state>" . $this->params['state'] . "</state>
      <zip>" . $this->params['zip'] . "</zip>
      </billTo>
      </subscription>
      </ARBUpdateSubscriptionRequest>";
    $this->process();
  }

  public function deleteAccount() {
    $this->xml = "<?xml version='1.0' encoding='utf-8'?>
      <ARBCancelSubscriptionRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
      <merchantAuthentication>
      <name>" . $this->login . "</name>
      <transactionKey>" . $this->trans_key . "</transactionKey>
      </merchantAuthentication>
      <refId>" . $this->params['refID'] ."</refId>
      <subscriptionId>" . $this->params['subscrId'] . "</subscriptionId>
      </ARBCancelSubscriptionRequest>";
    $this->process();
  }

  //private function parseResults() {
    //$this->resultCode = $this->parseXML('<resultCode>', '</resultCode>');
    //$this->code       = $this->parseXML('<code>', '</code>');
    //$this->text       = $this->parseXML('<text>', '</text>');
    //$this->subscrId   = $this->parseXML('<subscriptionId>', '</subscriptionId>');
  //}

  //function to parse Authorize.net response
  private function parseResults(){
    $this->refId = $this->substring_between($this->response,'<refId>','</refId>');
    $this->resultCode = $this->substring_between($this->response,'<resultCode>','</resultCode>');
    $this->code = $this->substring_between($this->response,'<code>','</code>');
    $this->text = $this->substring_between($this->response,'<text>','</text>');
    $this->subscrId = $this->substring_between($this->response,'<subscriptionId>','</subscriptionId>');
  }

  //helper function for parsing response
  private function substring_between($haystack,$start,$end){
    if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) {
      return false;
    } else {
      $start_position = strpos($haystack,$start)+strlen($start);
      $end_position = strpos($haystack,$end);
      return substr($haystack,$start_position,$end_position-$start_position);
    }
  }

  //private function parseXML($start, $end) {
    //return preg_replace('|^.*?'.$start.'(.*?)'.$end.'.*?$|i', '$1', substr($this->response, 335));
  //}

  public function setParameter($field = "", $value = null) {
    $field = (is_string($field)) ? trim($field) : $field;
    $value = (is_string($value)) ? trim($value) : $value;
    if (!is_string($field))
    {
      throw new AuthnetARBException("setParameter() arg 1 must be a string or integer: " . gettype($field) . " given.");
    }
    if (!is_string($value) && !is_numeric($value) && !is_bool($value))
    {
      throw new AuthnetARBException("setParameter() arg 2 must be a string, integer, or boolean value: " . gettype($value) . " given.");
    }
    if (empty($field))
    {
      throw new AuthnetARBException("setParameter() requires a parameter field to be named.");
    }
    if ($value === "")
    {
      throw new AuthnetARBException("setParameter() requires a parameter value to be assigned: $field");
    }
    $this->params[$field] = $value;
  }

  public function isSuccessful() {
    return $this->success;
  }

  public function isError() {
    return $this->error;
  }

  public function getResponse() {
    return strip_tags($this->text);
  }

  public function getResponseCode() {
    return $this->code;
  }

  public function getSubscriberID() {
    return $this->subscrId;
  }
}
