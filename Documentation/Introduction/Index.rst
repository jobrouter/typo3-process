.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

`JobRouter®`_ is a scalable digitalisation platform which links processes, data
and documents. The TYPO3 extension `TYPO3 JobRouter Process`_ connects
JobRouter® processes with TYPO3.


What does it do?
================

TYPO3 JobRouter Process is a TYPO3 extension for

* Managing process links and step links to JobRouter® installations in a
  :ref:`backend module <module>`
* Providing a :ref:`form finisher <form-finisher>` to start a process instance

This extension uses the `JobRouter REST Client`_ library and has the
:doc:`TYPO3 JobRouter Connector <ext_jobrouter_connector:introduction>`
extension as a requirement to define connections to JobRouter® installations.

.. note::
   If you find a bug or want to propose a feature, please use the
   `issue tracker on GitHub`_.


Current limitations
===================

*  Only process table fields can be used in the form finisher. Subtable
   fields are not handled by now.
*  Only text, integer and attachment types are available for process table
   fields in the process link configuration.


.. _release-management:

Release management
==================

This extension uses `semantic versioning`_ which basically means for you, that

*  Bugfix updates (e.g. 1.0.0 => 1.0.1) just includes small bug fixes or security
   relevant stuff without breaking changes.
*  Minor updates (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks
   without breaking changes.
*  Major updates (e.g. 1.0.0 => 2.0.0) includes breaking changes which can be
   refactorings, features or bug fixes.

The changes between the different versions can be found in the
:ref:`changelog <changelog>`.


.. _issue tracker on GitHub: https://github.com/jobrouter/typo3-process/issues
.. _JobRouter®: https://www.jobrouter.com/
.. _JobRouter REST Client: https://github.com/jobrouter/php-rest-client
.. _semantic versioning: https://semver.org/
.. _TYPO3 JobRouter Process: https://github.com/jobrouter/typo3-process
