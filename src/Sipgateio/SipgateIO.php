<?php
/**
 * sipgate.io XML generator class
 *
 * Licensed under the MIT License: http://opensource.org/licenses/MIT
 *
 * Generates XML using PHP's DOMDocument class with an easy-to-use interface
 */

namespace SipgateIO;

class SipgateIO {

	/**
	 * @var DOMDocument
	 */
	private $dom;

	/**
	 * @var DOMNode
	 */
	private $response;

	/**
	 * Class constructor
	 *
	 * @param DOMDocument $dom for dependency injection purposes
	 * @param string $charset to use an alternate charset
	 */
	public function __construct(DOMDocument $dom = null, $charset = null) {
		if(isset($dom)) {
			$this->dom = $dom;
		} else {
			$this->dom = new DOMDocument("1.0",($charset ? $charset : "UTF-8"));
		}

		$this->response = $this->dom->createElement('Response');
		$this->dom->appendChild($this->response);
	}

	/**
	 * Play a sound file from the given URL
	 *
	 * @param string $url the URL of the sound file
	 */
	public function play($url) {
		$play = $this->dom->createElement('Play');
		$url = $this->dom->createElement('Url',$url);
		$play->appendChild($url);
		$this->response->appendChild($play);
	}

	/**
	 * Say something (not yet implemented, but chances are good this will happen... :-))
	 *
	 * @param string $text the text that should be said
	 */
	public function say($text) {
		$say = $this->dom->createElement('Say',$text);
		$this->response->appendChild($say);
	}

	/**
	 * Reject a call
	 *
	 * @param string $reason the reason. Currently only "busy" and "rejected" are supported
	 */
	public function reject($reason = null) {
		$reject = $this->dom->createElement('Reject');

		if(isset($reason)) {
			$rejectReason = $this->dom->createAttribute('reason');
			$rejectReason->value = $reason;
			$reject->appendChild($rejectReason);
		}

		$this->response->appendChild($reject);
	}

	/**
	 * Call a number (e.g. to forward the incoming call)
	 *
	 * @param string $number the number to be dialled in E164 (http://de.wikipedia.org/wiki/E.164) format, e.g. "4915799912345"
	 * @param string $callerId the caller Id in E164 format
	 */
	public function dial($number = null, $callerId = null, $anonymous = null) {
		$dial = $this->dom->createElement('Dial');

		if(isset($callerId)) {
			$dialCallerId = $this->dom->createAttribute('callerId');
			$dialCallerId->value = $callerId;
			$dial->appendChild($dialCallerId);
		}
		else if(isset($anonymous)) {
			$dialCallerId = $this->dom->createAttribute('anonymous');
			$dialCallerId->value = $anonymous;
			$dial->appendChild($dialCallerId);
		}

		if(isset($number)) {
			$number = $this->dom->createElement('Number',$number);
			$dial->appendChild($number);
		}

		$this->response->appendChild($dial);
	}

	/**
	 * Send call to voicemail
	 *
	 * @param string $callerId the caller Id in E164 format
	 */
	public function voicemail($callerId = null) {
		$dial = $this->dom->createElement('Dial');
		$voicemail = $this->dom->createElement('Voicemail');
		$dial->appendChild($voicemail);
		$this->response->appendChild($dial);
	}

	/**
	 * Use a custom tag with a given custom value (For future use or regression testing)
	 *
	 * @param string $tagName the custom tag's name
	 * @param string $tagValue the value to be put between the custom tags
	 */
	public function customTag($tagName, $tagValue = null) {
		$customTag = $this->dom->createElement($tagName,$tagValue);
		$this->response->appendChild($customTag);
	}

	/**
	 * Hang up the call
	 */
	public function hangup() {
		$hangup = $this->dom->createElement('Hangup');
		$this->response->appendChild($hangup);
	}

	/**
	 * Set the caller ID using the Dial action
	 *
	 * @param string $callerId the caller ID
	 * @param boolean $anonymous toggle anonymous if set to true
	 */
	public function setCallerId($callerId = null) {
		$dial = $this->dom->createElement('Dial');

		$dialCallerId = $this->dom->createAttribute('callerId');
		$dialCallerId->value = $callerId;
		$dial->appendChild($dialCallerId);

		$this->response->appendChild($dial);
	}

	/**
	 * Set an anonymous caller ID using the Dial action
	 */
	public function setAnonymousCallerId() {
		$dial = $this->dom->createElement('Dial');

		$dialCallerId = $this->dom->createAttribute('anonymous');
		$dialCallerId->value = $anonymous;
		$dial->appendChild($dialCallerId);

		$this->response->appendChild($dial);
	}

	/**
	 * Get the response XML as string
	 *
	 * @return string the response XML
	 */
	public function getResponseXML() {
		return $this->dom->saveXML();
	}

}
