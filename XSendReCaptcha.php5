//<?php

require $SOSE->TranslateURI('/templates/scripts/ReCaptcha/ReCaptcha.php5');

// your secret key
$secret = $SOSE->GetVar("GooglePrivateKey");

// empty response
$response = null;

// check secret key
$reCaptcha = new ReCaptcha($secret);

// if submitted check response
if ($SOSE->GetVar("g-recaptcha-response")!=null) {
    $response = $reCaptcha->verifyResponse(
        $SOSE->ReadConfig("Domain", "", "GENERELT"),
        $SOSE->GetVar("g-recaptcha-response")
    );
}

if($response != null && $response->success){
  $ch = curl_init();

  $xsendMailData = array(
    'body' => $SOSE->GetVar("body"),
    'fileUpload' => $SOSE->GetVar("fileupload"),
    'from' => $SOSE->GetVar("from"),
    'fromName' => $SOSE->GetVar("fromname"),
    'hideRecipient' => $SOSE->GetVar("hiderecipient"),
    'lang' => $SOSE->GetVar("lang"),
    'name' => $SOSE->GetVar("username"),
    'noHtml' => $SOSE->GetVar("nohtml"),
    'noReceipt' => $SOSE->GetVar("noreceipt"),
    'objectClass' => $SOSE->GetVar("objectclass"),
    'replyTo' => $SOSE->GetVar("reply-to"),
    'returnLink' => $SOSE->GetVar("return_link"),
    'returnEmail' => $SOSE->GetVar("returnemail"),
    'redirectUrl' => $SOSE->GetVar("redirect_url"),
    'subject' => $SOSE->GetVar("subject"),
    'title' => $SOSE->GetVar("title"),
    'to' => $SOSE->GetVar("to"),
    'validatekey' => $SOSE->GetVar("validatekey"),
  );

  $curlConfig = array(
    CURLOPT_URL => $SOSE->ReadConfig("Domain", "", "GENERELT")."/apps/XSendMail.dll",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $xsendMailData
  );

  curl_setopt_array($ch, $curlConfig);
  $content = curl_exec($ch);

  if(!$content){
    $SOSE->Echo("An error has occurred. Please contact the site administrator directly.");
  }

  curl_close($ch);
  $SOSE->Redirect($SOSE->GetVar("redirect_url"));
} else {
  $SOSE->Echo("Incorrect ReCaptcha.");
}
