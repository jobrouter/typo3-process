.. include:: _includes.rst.txt

.. _upgrade:

=======
Upgrade
=======

Target group: **Developers**


From version 1.x to version 2
=============================

Version 2 of this extension introduced some breaking changes, notably:

*  The repository classes are no longer based on Extbase. They are now using the
   connection object or the query builder provided by TYPO3 and Doctrine DBAL.

*  The Extbase model classes are gone. Instead there are now immutable entity
   classes for process, processtablefield, step and transfer under the namespace
   :php:`Brotkrueml\JobRouterProcess\Domain\Entity`. There are also no getters
   available anymore, instead use the public properties (which are readonly).
