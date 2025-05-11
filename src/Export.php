<?php

namespace Pohoda;

use Pohoda\Export\Address;

class Export
{
	const NS_FTR = 'http://www.stormware.cz/schema/version_2/filter.xsd';
	const NS_TYPE = 'http://www.stormware.cz/schema/version_2/type.xsd';

	public $ico = '';

	private $invoices = [];
	private $address = [];

	private $lastId = 0;
	private $exportFolder;

	public function __construct($ico)
	{
		$this->ico = $ico;
	}

	/**
	 * Set absolute path to folder for xml export
	 * @param string $path
	 * @return $this
	 */
	public function setExportFolder($path)
	{
		$path = rtrim($path, DIRECTORY_SEPARATOR);
		if (is_writable($path) === false)
			throw new \InvalidArgumentException("$path is not writable");

		$this->exportFolder = $path;
		return $this;
	}

	public function setInvoice($invoice)
	{
		trigger_error('DEPRECATED: use addInvoice() instead');
		$this->invoices[] = $invoice;
	}

	public function addInvoice(Invoice $invoice)
	{
		$this->invoices[] = $invoice;
	}

	public function addAddress(Address $address)
	{
		$this->address[] = $address;
	}


	public function exportToFile($exportId, $application, $fileName, $errorsNo, $note = '')
	{

		$xml = $this->export($exportId, $application, $note);
		$incomplete = '';
		if ($errorsNo > 0) {
			$incomplete = '_incomplete';
		}
		$xml->asXML((is_null($this->exportFolder) ? dirname(__FILE__) : $this->exportFolder) . '/' . $fileName . '_lastId-' . $this->lastId . $incomplete . '.xml');
	}

	public function exportAsXml($exportId, $application, $note = '')
	{
		header("Content-Type:text/xml; charset=utf-8");
		$xml = $this->export($exportId, $application, $note);
		echo $xml->asXML();
	}

	public function exportAsString($exportId, $application, $note = '')
	{
		$xml = $this->export($exportId, $application, $note);
		echo $xml->asXML();
	}

	public function getIco() {
		if(!is_null($this->ico) && $this->ico !== '') {
			return $this->ico;
		} elseif(count($this->invoices)) {
			//ico from invoice
			return $this->invoices[0]->getProviderIdentity()['ico'];
		} else {
			throw new \Exception('ICO must be defined');
		}
	}

	public function export($exportId, $application, $note = '')
	{
		$xmlText = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
		<dat:dataPack id=\"" . $exportId . "\" ico=\"" . $this->getIco() . "\" 
		application=\"" . $application . "\" version = \"2.0\" note=\"" . $note . "\" 
		xmlns:dat=\"http://www.stormware.cz/schema/version_2/data.xsd\" 
		xmlns:typ=\"". self::NS_TYPE ."\"
		xmlns:ftr=\"" .  self::NS_FTR . "\"
		xmlns:inv=\"". Invoice::NS . "\"
		xmlns:adb=\"" . Address::NS . "\">
		</dat:dataPack>";

		$xml = simplexml_load_string($xmlText, SimpleXMLElementExtended::class );

		$i = $a = 0;

		/** @var Address $item */
		foreach ($this->address as $item) {
			$a++;
			$dataItem = $xml->addChild("dat:dataPackItem");
			$dataItem->addAttribute('version', "2.0");
			$dataItem->addAttribute('id', $item->getId());

			$item->export($dataItem);
		}


		/** @var Invoice $item */
		foreach ($this->invoices as $item) {
			$i++;
			$dataItem = $xml->addChild("dat:dataPackItem");
			$dataItem->addAttribute('version', "2.0");
			$dataItem->addAttribute('id', $item->getId());

			$item->export($dataItem);

			if ($item->getId() > $this->lastId) {
				$this->lastId = $item->getId();
			}
		}

		return $xml;
	}
}
