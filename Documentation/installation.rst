.. include:: _includes.rst.txt

.. _installation:

============
Installation
============

Target group: **Administrators**

.. contents::
   :depth: 1
   :local:


.. _installation-requirements:

Requirements
============

The extension is available for TYPO3 v10 LTS and TYPO3 v11 LTS.


.. _version-matrix:

Version matrix
==============

================= ========== ===========
JobRouter Process PHP        TYPO3
================= ========== ===========
1.0               7.2 - 7.4  10.4
----------------- ---------- -----------
1.1               7.3 - 8.1  10.4 / 11.5
================= ========== ===========


.. _installation-composer:

Installation via composer
=========================

The recommended way to install this extension is by using Composer. In your
Composer-based TYPO3 project root, just type::

   composer req brotkrueml/typo3-jobrouter-process

The extension offers some configuration which is explained in the
:ref:`Configuration <Configuration>` chapter.


.. _installation-extension-manager:

Installation in Extension Manager
=================================

You can also install the extension from the `TYPO3 Extension Repository (TER)`_.
See :ref:`t3start:extensions_legacy_management` for a manual how to
install an extension.

.. _TYPO3 Extension Repository (TER): https://extensions.typo3.org/extension/jobrouter_process/
