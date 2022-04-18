<?php

declare(strict_types=1);

namespace Baraja\XmlToPhp;


final class Helper
{
	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . static::class . ' is static and cannot be instantiated.');
	}


	/**
	 * @param \DOMElement|\DOMNode $node
	 * @return array<int|string, mixed>|string
	 */
	public static function domNodeToArray($node): array|string
	{
		$output = [];
		switch ($node->nodeType) {
			case 4: // XML_CDATA_SECTION_NODE
			case 3: // XML_TEXT_NODE
				return trim($node->textContent);
			case 1: // XML_ELEMENT_NODE
				for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
					$child = $node->childNodes->item($i);
					assert($child !== null);
					$v = self::domNodeToArray($child);
					if (isset($child->tagName)) {
						$t = $child->tagName;
						if (!isset($output[$t])) {
							$output[$t] = [];
						}
						/** @phpstan-ignore-next-line */
						if (is_array($v) && empty($v)) {
							$v = '';
						}
						$output[$t][] = $v;
					/** @phpstan-ignore-next-line */
					} elseif ($v || $v === '0') {
						$output = is_string($v) ? $v : implode(',', $v);
					}
				}
				if ($node->attributes !== null && $node->attributes->length > 0 && !is_array($output)) { // has attributes but isn't an array
					$output = ['@content' => $output]; // change output into an array.
				}
				if (is_array($output)) {
					if ($node->attributes !== null && $node->attributes->length > 0) {
						$a = [];
						foreach ($node->attributes as $attrName => $attrNode) {
							/** @var \DOMAttr $attrNode */
							$a[$attrName] = $attrNode->value;
						}
						$output['@attributes'] = $a;
					}
					foreach ($output as $t => $v) {
						if ($t !== '@attributes' && is_array($v) && count($v) === 1) {
							$output[$t] = $v[0];
						}
					}
				}
				break;
		}

		/** @phpstan-ignore-next-line */
		return $output;
	}
}
