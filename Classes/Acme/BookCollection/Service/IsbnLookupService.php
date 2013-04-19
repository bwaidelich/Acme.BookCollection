<?php
namespace Acme\BookCollection\Service;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Client\CurlEngine;

/**
 * An ISBN Lookup Service
 *
 * @Flow\Scope("singleton")
 */
class IsbnLookupService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\Browser
	 */
	protected $browser;

	/**
	 * @param string $isbn
	 * @return array
	 */
	public function getBookInfo($isbn) {
		$this->browser->setRequestEngine(new CurlEngine());
		$response = $this->browser->request(sprintf('http://isbndb.com/api/books.xml?access_key=CCFEHU64&index1=isbn&results=texts&value1=%s', $isbn));
		$xml = simplexml_load_string($response->getContent());

		$bookData = $xml->xpath('//BookData');
		if (count($bookData) < 1) {
			return array();
		}
		return array (
			'title' => (string)$bookData[0]->Title,
			'publisher' => (string)$bookData[0]->PublisherText,
			'authors' => (string)$bookData[0]->AuthorsText,
			'description' => (string)$bookData[0]->Summary
		);
	}

}
?>