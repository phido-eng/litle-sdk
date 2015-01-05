<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

// =begin
// Copyright (c) 2011 Litle & Co.

// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.
// =end
// class and methods to parse a XML document into an object
class XMLParser{
	
	public static function domParser($xml)
	{
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		return $doc;
	}
	
	public static function getNode($dom, $elementName)
	{
		$elements = $dom->getElementsByTagName($elementName);
		$retVal = "";
		foreach ($elements as $element) {
			$retVal = $element->nodeValue;
		}
		return $retVal;
	}

	public static function getAttribute($dom, $elementName, $attributeName)
	{
		$attributes = $dom->getElementsByTagName($elementName)->item(0);
		
		if (!is_null($attributes)) {
			return $attributes->getAttribute($attributeName);
		}
		
		return false;
	}
	
	public static function getDomDocumentAsString($dom)
	{
		return $dom->saveXML($dom);
	}

	/**
	 * Convert an XML DOM document to an array
	 * 
	 * @param  DOMElement|DOMDocument $root
	 * @return array
	 */
	public static function xmlDOMDocumentToArray($root) {
		$result = array();

        if ($root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ($attrs as $attr) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }

        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['_value'] = $child->nodeValue;
                    return count($result) == 1 ? $result['_value'] : $result;
                }
            }
            $groups = array();
            foreach ($children as $child) {
                if (!isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = self::xmlDOMDocumentToArray($child);
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = array($result[$child->nodeName]);
                        $groups[$child->nodeName] = 1;
                    }
                    $result[$child->nodeName][] = self::xmlDOMDocumentToArray($child);
                }
            }
        }

        return $result;
	}
}
?>
