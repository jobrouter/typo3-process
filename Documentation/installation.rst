.. include:: _includes.rst.txt

.. _installation:

============
Installation
============

Target group: **Administrators**

.. note::

   The extension is available for TYPO3 v10 LTS.


.. _installation-requirements:

Requirements
============

The extension has no PHP requirements in addition to TYPO3 and the TYPO3
JobRouter Connector extension.


.. _installation-composer:

Installation via composer
=========================

#. Add a dependency ``brotkrueml/typo3-jobrouter-process`` to your project's
   :file:`composer.json` file to install the current stable version::

      composer req brotkrueml/typo3-jobrouter-process

#. Activate the extension in the Extension Manager.


.. _installation-extension-manager:

Installation in Extension Manager
=================================

The extension needs to be installed as any other extension of TYPO3 CMS in
the Extension Manager:

#. Switch to the module :guilabel:`Admin Tools` > :guilabel:`Extensions`.

#. Get the extension

   #. **Get it from the Extension Manager:** Select the
      :guilabel:`Get Extensions` entry in the upper menu bar, search for the
      extension key ``jobrouter_process`` and import the extension from the
      repository.

   #. **Get it from typo3.org:** You can always get the current version from
      `https://extensions.typo3.org/extension/jobrouter_process/
      <https://extensions.typo3.org/extension/jobrouter_process/>`_ by
      downloading the ``zip`` file. Upload the file afterwards in the Extension
      Manager.
