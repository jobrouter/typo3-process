.. include:: _includes.txt

.. _dashboard-widgets:

=================
Dashboard Widgets
=================

Target group: **Editors, Integrators, Administrators**

.. contents:: Table of Contents
   :depth: 1
   :local:

With the `Dashboard <https://docs.typo3.org/c/typo3/cms-dashboard/master/en-us/>`_
system extension installed, some widgets can be used to display process
statistics. You can find them in the :guilabel:`Add widget` wizard on the
:guilabel:`JobRouter` tab:

.. figure:: _images/dashboard-add-widget.png
   :alt: Add JobRouter widgets

   Add JobRouter widgets

The widgets are based on the transfer table. All entries are considered -
successfully started, pending and erroneous entries. Prerequisite for meaningful
data is the availability of types in this transfer table - either configured in
:ref:`form definitions <form-finisher>` or :ref:`coded in PHP
<developer-start-instance>`.

.. note::

   The available widgets depend on the access rights of user.


.. _dashboard-widget-instance-starts:

Instance Starts
===============

The instance starts can be visually displayed with a Dashboard widget:

.. figure:: _images/dashboard-widget-instance-starts.png
   :alt: Instance Starts widget

   Instance Starts widget

By default, the last 14 days (included the current day) are shown. This can be
:ref:`adjusted <configuration-dashboard>` to your needs.


.. _dashboard-widget-instance-start-types:

Instance Start Types
====================

The instance starts for different types can be visually displayed with this
widget:

.. figure:: _images/dashboard-widget-instance-start-types.png
   :alt: Instance Start Types widget

   Instance Start Types widget

By default, the last 14 days (included the current day) are shown. This can be
:ref:`adjusted <configuration-dashboard>` to your needs.

Instance Start Status
=====================

The status of the instance starts can be shown with this widget:

.. figure:: _images/dashboard-widget-instance-start-status.png
   :alt: Instance Start Status widget

   Instance Start Status widget
