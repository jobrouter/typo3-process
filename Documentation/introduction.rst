.. include:: _includes.txt

.. _introduction:

============
Introduction
============

`JobRouter <https://www.jobrouter.com/>`_ is a scalable digitisation platform
which links processes, data and documents. The TYPO3 extension `TYPO3 JobRouter
Process <https://github.com/brotkrueml/typo3-jobrouter-process>`_ connects
JobRouter processes with TYPO3.

.. admonition:: Work In Progress

   Currently, the TYPO3 JobRouter Process extension is in a development phase.
   As it can be used already, the API is still subject to change.


What does it do?
================

TYPO3 JobRouter Process is a TYPO3 extension for

* Managing process and instance links to JobRouter installations with a
  :ref:`module <module>`
* Providing a :ref:`form finisher <form-finisher>` to start a process instance

This extension uses the :doc:`JobRouter Client <client:introduction>`
library and has the :doc:`TYPO3 JobRouter Connector <connector:introduction>`
extension as a requirement to define connections to JobRouter installations.


Current limitations
===================

Only process table fields can be used. Subtable fields are not handled by this
extension by now. This is planned in future releases.
