.. include:: /Includes.rst.txt


.. _installation:

============
Installation
============

Before you begin
================

Before you install the extension make sure you have no other editors installed like `rte_ckeditor`.
It ships by default with TYPO3 and has to be removed by composer command if you are using composer:

.. code-block:: bash

   composer remove typo3/cms-rte-ckeditor

If you're using the legacy installation (without composer) simply go to the extension manager and
disable the `RTE CKEditor` extension.

Install with composer
=====================

This extension is simply installed with one command over the comfortable composer command line:

.. code-block:: bash

   composer require atkins/t3-trix

Install without composer (Legacy installations)
===============================================

If you're using legacy systems without composer you can install it with the Extension Manager
searching for the key :guilabel:`trix`.