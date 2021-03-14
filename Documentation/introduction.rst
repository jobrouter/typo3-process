.. include:: _includes.rst.txt

.. _introduction:

============
Introduction
============

`JobRouter® <https://www.jobrouter.com/>`_ is a scalable digitisation platform
which links processes, data and documents. The TYPO3 extension `TYPO3 JobRouter
Process <https://github.com/brotkrueml/typo3-jobrouter-process>`_ connects
JobRouter® processes with TYPO3.


What does it do?
================

TYPO3 JobRouter Process is a TYPO3 extension for

* Managing process links and step links to JobRouter® installations in a
  :ref:`backend module <module>`
* Providing a :ref:`form finisher <form-finisher>` to start a process instance

This extension uses the :doc:`JobRouter Client <jobrouter-client:introduction>`
library and has the :doc:`TYPO3 JobRouter Connector <typo3-jobrouter-connector:introduction>`
extension as a requirement to define connections to JobRouter® installations.


Current limitations
===================

- Only process table fields can be used in the form finisher. Subtable
  fields are not handled by now.
- Only text and integer types are available for process table fields in the
  process link configuration.


.. _release-management:

Release management
==================

This extension uses `semantic versioning <https://semver.org/>`_ which
basically means for you, that

* Bugfix updates (e.g. 1.0.0 => 1.0.1) just includes small bug fixes or security
  relevant stuff without breaking changes.
* Minor updates (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks
  without breaking changes.
* Major updates (e.g. 1.0.0 => 2.0.0) includes breaking changes which can be
  refactorings, features or bug fixes.
