<?php
namespace Acme\BookCollection\Controller\Module;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Fluid\View\AbstractTemplateView;
use TYPO3\Neos\Controller\Module\StandardController;
use TYPO3\Neos\Domain\Service\ContentContext;

/**
 * Backend Module that allows users to create book pages by entering a ISBN number.
 *
 * Note: This extends \TYPO3\Neos\Controller\Module\StandardController in order to get breadcrumb navigation and proper initialization out of the box
 */
class BookController extends StandardController {

	/**
	 * @Flow\Inject
	 * @var \Acme\BookCollection\Service\IsbnLookupService
	 */
	protected $isbnLookupService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 */
	protected $nodeRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * @var \TYPO3\Neos\Domain\Service\ContentContext
	 */
	protected $contentContext;


	/**
	 * @return void
	 */
	public function initializeAction() {
		/** @var $user \TYPO3\Neos\Domain\Model\User */
		$user = $this->securityContext->getPartyByType('TYPO3\Neos\Domain\Model\User');
		$workspaceName = $user->getPreferences()->get('context.workspace');

		// initialize content context with the workspace of the currently logged in Neos user
		$this->contentContext = new ContentContext($workspaceName);
		$this->nodeRepository->setContext($this->contentContext);

		// call the parents initializeAction method!
		parent::initializeAction();
	}

	/**
	 * Shows a list of books
	 *
	 * @return void
	 */
	public function indexAction() {
		$booksRootNodePath = $this->moduleConfiguration['booksRootNodePath'];
		$bookNodes = $this->nodeRepository->findByParentAndNodeType($booksRootNodePath, 'Acme.BookCollection:Book', $this->contentContext->getWorkspace());
		$this->view->assign('bookNodes', $bookNodes);
	}

	/**
	 * Adds the book specified by an ISBN
	 *
	 * @param array $newBook An array containing an isbn property
	 * @return void
	 */
	public function createIsbnAction(array $newBook) {
		$bookInfo = $this->isbnLookupService->getBookInfo($newBook['isbn']);
		if ($bookInfo === array()) {
			$this->addFlashMessage('No book found with ISBN %s.', 'Invalid ISBN', Message::SEVERITY_ERROR, array($newBook['isbn']));
			$this->redirect('index');
		}

		$booksRootNodePath = $this->moduleConfiguration['booksRootNodePath'];
		$parentNode = $this->nodeRepository->findOneByPath($booksRootNodePath, $this->contentContext->getWorkspace());

		$bookNodeName = strtolower(str_replace(' ', '-', $bookInfo['title']));
		$bookNodeType = $this->nodeTypeManager->getNodeType('Acme.BookCollection:Book');
		$bookNode = $parentNode->createNode($bookNodeName, $bookNodeType);

		$bookNode->setProperty('title', $bookInfo['title']);
		$bookNode->setProperty('isbn', $newBook['isbn']);
		$bookNode->setProperty('publisher', $bookInfo['publisher']);
		$bookNode->setProperty('authors', $bookInfo['authors']);

		$bookDescriptionNodeType = $this->nodeTypeManager->getNodeType('TYPO3.Neos.NodeTypes:Text');
		$productDescriptionNode = $bookNode->getNode('description')->createNode('description-text', $bookDescriptionNodeType);
		$productDescriptionNode->setProperty('text', $bookInfo['description']);

		$this->addFlashMessage('Created book page at "%s"', 'Book page created!', Message::SEVERITY_OK, array($bookNode->getPath()));
		$this->redirect('index');
	}

}

?>