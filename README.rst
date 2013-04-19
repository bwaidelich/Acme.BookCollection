*************************
Book Collection Neos Site
*************************

This contains the TYPO3 Flow package "Acme.BookCollection" that can be installed as "Site Package" in Neos and
demonstrates following features:

* Custom Content Elements
* Creating simple Backend Modules
* Custom Page Nodes (each book is a page node in the TYPO3CR)

**Note:** This is a package we created in the `Neos workshop <http://inspiringflow.de/workshops/workshop2>`_ at "Inspiring Flow".
It is based on Roberts Flow Package `RobertLemke.Example.Bookshop <https://github.com/robertlemke/RobertLemke.Example.Bookshop>`_ 

============
Installation
============

1. Clone this package to the ``Packages/Sites`` of your Neos distribution:

::

	git clone git://github.com/bwaidelich/Acme.BookCollection.git Acme.BookCollection

2. Import the site contents (optional):

::

	./flow site:import --package-key Acme.BookCollection


=====
Usage
=====

Just head to the new "Books" module which should appear as new entry in the "Management" module and try creating some
book nodes by entering valid ISBN codes.
Here are some example codes for you to test this:

* 1416982752
* 1174673230
* 1594203083
* 0756639824

The new created books will appear as sub pages of "books".

**Note:** "Book" pages will be created in **your workspace**. That allows you to adjust the data before publishing the page to the live workspace.
