.. _configuration:

=============
Configuration
=============

Target group: **Administrators**

.. contents:: Table of Contents
   :depth: 2
   :local:

.. _configuration-extension:

Extension configuration
=======================

To configure the extension, go to :guilabel:`System > Settings > Extension
Configuration` and click on the :guilabel:`Configure extensions` button. Open
the :guilabel:`jobrouter_process` configuration:

.. figure:: /Images/extension-configuration.png
   :alt: Options in the extension configuration

   Options in the extension configuration

Encryption
----------

Encrypt transfer data
~~~~~~~~~~~~~~~~~~~~~

If activated, the fields `processtable` and `summary` are encrypted for enhanced
security in the transfer table when using the :ref:`Preparer <developer-preparer>`
class or the :ref:`JobRouterStartInstance <form-finisher-start-instances>` form
finisher.

.. tip::
   You should consider activating the encryption of the transfer data as this
   ensures confidentiality and integrity!

.. important::
   If you lose or change the encryption key (generated with the :ref:`JobRouter
   Connector extension <ext_jobrouter_connector:introduction>`), data cannot
   be decrypted by the :ref:`start process command <command-start>` anymore!
