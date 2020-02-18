.. include:: _includes.txt

.. _form-finisher:

=============
Form Finisher
=============

Target group: **Integrators**, **Developers**


A form finisher `JobRouterStartInstance` is available to start a JobRouter
process instance. After submitting a form, the form values are stored in a
transfer table. A command, hopefully executed regularly, takes these transfer
records and start a process instance. This is due the fact, that a JobRouter
installation can temporarily not available due to maintenance or network
problems. Also the submitting of a form should be as fast as possible.

.. note::

   The finisher can only be used in the form definition directly, not in the
   :guilabel:`Form` module.

So, let's start with an example. The form finisher is defined in the YAML
configuration of the specific form:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterStartInstance
         options:
            handle: 'start_website_contact'
            summary: 'Demo Contact'
            processtable:
               name:
                  mapOnFormField: name
               company:
                  mapOnFormField: company
               email_address:
                  mapOnFormField: email
               phone_number:
                  mapOnFormField: phone
               message:
                  mapOnFormField: message
               form_identifier:
                  staticValue: 'www.example.com/demo'

As you can see, you can define some options. These are:

- `handle` (required): The step handle defined in the
  :ref:`Processes module <module-create-step-link>`.

- `summary`: The summary of the instance.

- `initiator`: The initiator of the instance.

- `username`: The username the instance should be assigned to.

- `jobfunction`: The Job Function the instance should be assigned to.

- `priority`: The priority of the instance (number between 1 and 3).

- `pool`: The pool of the instance (positive number).

- `processtable`: You can map the form fields to the process table fields. As
  you can see in the example above, you define first the process table field
  (e.g `email_address`) and the map it with the key `mapOnFormField` to the
  form field. Alternatively, you can also set a static value.

.. note::

   Only process table fields can be used that are configured in the
   :ref:`process link <module-create-process-link>`.
