<?php

declare(strict_types=1);

namespace Baraja\XmlToPhp;


final class Convertor
{

	/**
	 * Convert xml string to php array - useful to get a serializable value.
	 *
	 * @return mixed[]
	 */
	public static function covertToArray(string $xml): array
	{
		assert(\class_exists('\DOMDocument'));
		$doc = new \DOMDocument();
		$doc->loadXML($xml);
		$root = $doc->documentElement;
		$output = (array) Helper::domNodeToArray($root);
		$output['@root'] = $root->tagName;

		return $output ?? [];
	}
}
