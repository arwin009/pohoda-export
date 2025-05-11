<?php

namespace Pohoda;


/**
 * Class SimpleXMLElementExtended
 *
 * A simple extended version of SimpleXMLElement that adds CDATA whenever necessary
 *
 * @author  Ronald Edelschaap <rlwedelschaap@gmail.com> <first author>
 * @updated 01-05-2016
 * @license http://www.gnu.org/licenses/gpl-2.0 GPL v2.0
 * @version 1.1
 */
class SimpleXMLElementExtended extends \SimpleXMLElement
{

	/**
	 * @inheritdoc \SimpleXMLElement::addChild
	 *
	 * @return \SimpleXMLElement|SimpleXMLElementExtended
	 */
	public function addChild($name, $value = NULL, $namespace = NULL)
	{
		$_value = self::filterUnicodeCharacters((string) $value);

		if ($_value !== ''
			&& (strpos($_value, '<') !== FALSE || strpos($_value, '>') !== FALSE || strpos($_value,	'&') !== FALSE || strpos($_value, '"') !== FALSE)
		) {
			$child = $this->addChild($name, NULL, $namespace);
			$child->addCData($value);

			return $child;
		}

		return parent::addChild($name, $value, $namespace);
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private static function filterUnicodeCharacters($string)
	{
		return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
	}

	/**
	 * Add CDATA text in a node
	 *
	 * @param string $value The value to add
	 */
	private function addCData($value)
	{
		$node = dom_import_simplexml($this);
		$owner = $node->ownerDocument;
		$node->appendChild($owner->createCDATASection($value));
	}

}
