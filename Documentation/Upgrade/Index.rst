.. include:: /Includes.rst.txt

.. _upgrade:

=======
Upgrade
=======

Target group: **Developers**


From version 2.0 to 3.0
=======================

The namespace of the JobRouter TYPO3 Process classes have changed from

.. code-block:: text

   \Brotkrueml\JobRouterProcess

to

.. code-block:: text

   \JobRouter\Addon\Typo3Process

The easiest way to update your code to the new namespace is to use
search/replace in your project.


From version 1.x to version 2
=============================

Version 2 of this extension introduced some breaking changes, notably:

*  The repository classes are no longer based on Extbase. They are now using the
   connection object or the query builder provided by TYPO3 and Doctrine DBAL.

*  The Extbase model classes are gone. Instead there are now immutable entity
   classes for process, processtablefield, step and transfer under the namespace
   :php:`JobRouter\AddOn\Typo3Process\Domain\Entity`. There are also no getters
   available anymore, instead use the public properties (which are readonly).
