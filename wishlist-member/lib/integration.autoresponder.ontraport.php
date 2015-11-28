<?php

/*
 * Ontraport (Office Auto Pilot) Autoresponder Integration Functions
 * Original Author : Ronaldo Reymundo
 * Version: $Id: integration.autoresponder.ontraport.php 2389 2014-10-22 15:59:18Z mike $
 */

//$__classname__ = 'WLM_AUTORESPONDER_ONTRAPORT';
//$__optionname__ = 'ontraport';
//$__methodname__ = 'ontraport_subscribe';

/* 
 *  ==== PROBLEMS WITH ONTRAPORT AUTORESPONDER AS OF THIS WRITING ====
 * 
 * 1. Ontraport's contact API is very limited that deleting a contact isn't possible right now.
 *    They require that you use the contact ID to delete the contact via API, problem is that when
 *    adding a contact, they don't add the contact ID in the results they return via curl. 
 * 
 * 2. They don't have an API call to fetch Groups so specifying which Group  a member will be added
 *    per membeship level isn't possible. I also don't see how you can add a contact to a Group via API.
 * 
 * 3. API Calls using calls usually is slow that most of my tests results to Maximum execution time issue.
 */

if (!class_exists('WLM_AUTORESPONDER_ONTRAPORT')) {

	class WLM_AUTORESPONDER_ONTRAPORT {

		public function ontraport_subscribe($that, $ar, $wpm_id, $email, $unsub = false) {
			$options = $that->GetOption('Autoresponders');

			$appid = $options['ontraport']['app_id'];
			$key = $options['ontraport']['api_key'];

				if (!$unsub) {
					
					if($options['ontraport']['addenabled'][$wpm_id] == 'yes') {

						// Construct contact data in XML format
						$data = <<<STRING
						<contact>
						<Group_Tag name="Contact Information">
						<field name="First Name">{$that->OrigPost['firstname']}</field>
						<field name="Last Name">{$that->OrigPost['lastname']}</field>
						<field name="E-Mail">{$that->OrigPost['email']}</field>
						</Group_Tag>
						<Group_Tag name="Sequences and Tags">
						<field name="Contact Tags">Test</field>
						<field name="Sequences">*/*3*/*8*/*</field>
						</Group_Tag>
						</contact>
STRING;

						$data = urlencode(urlencode($data));

						//Set your request type and construct the POST request
						$reqType= "add";
						$postargs = "appid=".$appid."&key=".$key."&return_id=1&reqType=".$reqType. "&data=" . $data;
						$request = "https://api.moon-ray.com/cdata.php";

						//Start the curl session and send the data
						$session = curl_init($request);
						curl_setopt ($session, CURLOPT_POST, true);
						curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
						curl_setopt($session, CURLOPT_HEADER, false);
						curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

						// Dirty fix for ssl issue with curl
						curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

						//Store the response from the API for confirmation or to process data
						$response = curl_exec($session);

						//Close the session
						curl_close($session);
					}
					
				} else {
					// If unsub Do nothing, Read comments about the problems above.. ^
				}

		}
	}
}