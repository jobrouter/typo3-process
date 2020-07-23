.. include:: _includes.txt

.. _form-finisher:

=============
Form finisher
=============

Target group: **Integrators**, **Developers**

.. contents:: Table of Contents
   :depth: 3
   :local:

Start instances
===============

A form finisher `JobRouterStartInstance` is available to start a JobRouter®
process instance. After submitting a form, the form values are stored in a
transfer table. A command, hopefully executed regularly, takes these transfer
records and start a process instance. This is due the fact, that a JobRouter®
installation can temporarily not available due to maintenance or network
problems. Also the submitting of a form should be as fast as possible.

.. note::

   The finisher can only be used in the yaml form definition, not in the
   :guilabel:`Form` GUI module.


Start one instance
------------------

So, let's start with an example. The form finisher is defined in the YAML
configuration of the specific form:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterStartInstance
         options:
            handle: 'start_website_contact'
            type: 'demo'
            summary: 'Demo Contact'
            processtable:
               name: '{preName} {lastName}'
               company: '{company}'
               email_address: '{email}'
               phone_number: '{phone}'
               message: '{message}'
               form_identifier: 'www.example.com/demo'

As you can see, you can define some options. These are:

- `handle` (required): The step handle defined in the
  :ref:`Processes module <module-create-step-link>`.

- `type`: The type can be used for statistical evaluation.

- `summary`: The summary of the instance.

- `initiator`: The initiator of the instance.

- `username`: The username the instance should be assigned to.

- `jobfunction`: The Job Function the instance should be assigned to.

- `priority`: The priority of the instance (number between 1 and 3).

- `pool`: The pool of the instance (positive number).

- `processtable`: You can map the form fields to the process table fields. As
  you can see in the example above, you define the process table field as the
  key (e.g `email_address`) and then map it with the to the form field
  identifier which is enclosed in curly brackets (e.g. `{email}`).
  You can also set a static value, combine a static value with a form field
  or map multiple form fields to a process table field.

.. note::
   Only process table fields that are configured in the :ref:`process link
   <module-create-process-link>` are possible. If a process table field
   is used that is not defined, an exception is thrown.

   Inputs that are longer than the defined process table field length are
   shortened to the maximum length.

   If the value of a form field is an array, like from a multi checkbox, the
   array is converted to a csv string and stored in the given process table
   field. The value can be reconverted to an array, e.g. in a JobRouter® rule,
   with the PHP function :php:`str_getcsv()`.


Start multiple instances
------------------------

It is also possible to start multiple instances – even on different JobRouter®
installations. Just use the array notation in :yaml:`options`:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterStartInstance
         options:
            -
               handle: 'start_website_contact'
               summary: 'Demo Contact'
               processtable:
                  name: '{preName} {lastName}'
                  company: '{company}'
                  email_address: '{email}'
                  phone_number: '{phone}'
                  message: '{message}'
                  form_identifier: 'www.example.com/demo'
            -
               handle: 'collect_anonymous_messages'
               summary: 'Demo Contact'
               processtable:
                  ANON_MESSAGE: '{message}'
                  FROM_URL: 'https://www.example.com/demo'


.. _form-finisher-variables:

Variables
---------

You can use variables in the common parameters, such as :yaml:`summary` or
:yaml:`initiator`, and in the process table fields.

Variables look like: :yaml:`{__variableName}` or
:yaml:`{__variableName.subKey}` − in curly brackets with a double underscore
at the beginning.

Example:

.. code-block:: yaml

   finishers:
      -
         identifier: JobRouterStartInstance
         options:
            handle: 'start_website_contact'
            summary: '{__LLL:EXT:your_ext/Resources/Private/Language/forms.xlf:demo.summary} ({__language.navigationTitle})'
            initiator: '{__transferIdentifier}'
            processtable:
               name: '{preName} {lastName}'
               company: '{company}'
               email_address: '{email}'
               phone_number: '{phone}'
               message: '{message}'
               from_website: '{__language.base}'

.. hint::

   You can build own variable resolvers. Have a look in the section
   :ref:`developer-variable-resolvers`.


Transfer identifier
~~~~~~~~~~~~~~~~~~~

The transfer identifier is generated from the form identifier and a unique key
to identify associated transfers – as you can start multiple instances from one
form. The identifier is stored in a column in the transfer table together with
the form data. This is useful if you want to check the data from the instance
against the original data from the form.

The identifier looks like this: `form_demo-20_63fca23b1accb` where `form`
indicates the identifier is generated by a from finisher, `demo-20` is the form
identifier with the content element uid of the form plugin and `63fca23b1accb`
is the unique key.

Use the :yaml:`{__transferIdentifier}` variable to add the transfer identifier.


Localisation labels
~~~~~~~~~~~~~~~~~~~

Values can be localised with the help of the localisation labels known from
various parts in TYPO3. The variable starts with :yaml:`{__LLL:` and holds
the path to the translation file and the according key, e.g.
:yaml:`{__LLL:EXT:your_ext/Resources/Private/Language/locallang.xlf:your.label}`.

If the label is not found and therefore cannot be translated the value is
untouched.

You can use multiple localisation labels in one value.

Language information
~~~~~~~~~~~~~~~~~~~~

The language information defined in the Site Configuration can be used, namely:

{__language.base}
   The base URL for the language, e.g. `https://example.org/en/`.

{__language.direction}
   The language direction: `ltr` (left to right) or `rtl` (right to left).

{__language.flagIdentifier}
   The defined TYPO3 flag identifier used in TYPO3's backend, e.g. `flags-gb`.

{__language.hreflang}
   Language tag for the language defined by RFC 1766 / 3066 for `lang`
   `hreflang` attributes, e.g. `en-gb`.

{__language.languageId}
   The language ID defined in the TYPO3 installation. It is the uid in the
   `sys_language` table.

{__language.locale}
   The used locale, e.g. `en_GB.UTF-8`.

{__language.navigationTitle}
   The navigation title defined in the site configuration, used as label
   within language menus, e.g. `English`.

{__language.title}
   The title defined in the site configuration, e.g. `English`.

{__language.twoLetterIsoCode}
   The `ISO-639-1 <https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes>`_
   language ISO code, e.g. `en`.

{__language.typo3Language}
   `default` for English, otherwise one of TYPO3's internal language keys.

Multiple language variables can be used in one value.


JobRouter language information
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Sometimes it is necessary to send not the language code of the page in your form
but instead the language string JobRouter® uses (e.g. ``german`` instead of
``de``). This can be useful for sending localised emails from the process in the
relevant language.

Use the :yaml:`{__jobRouterLanguage}` variable for that. The following languages
are supported by JobRouter® by now:

+----------------+---------------------+
| ISO 639-1 code | JobRouter® language |
+================+=====================+
| ar             | arabic              |
+----------------+---------------------+
| cs             | czech               |
+----------------+---------------------+
| da             | danish              |
+----------------+---------------------+
| de             | german              |
+----------------+---------------------+
| en             | english             |
+----------------+---------------------+
| es             | spanish             |
+----------------+---------------------+
| fi             | finnish             |
+----------------+---------------------+
| fr             | french              |
+----------------+---------------------+
| hu             | hungarian           |
+----------------+---------------------+
| it             | italian             |
+----------------+---------------------+
| ja             | japanese            |
+----------------+---------------------+
| nl             | dutch               |
+----------------+---------------------+
| pl             | polish              |
+----------------+---------------------+
| ro             | romanian            |
+----------------+---------------------+
| ru             | russian             |
+----------------+---------------------+
| sk             | slovak              |
+----------------+---------------------+
| sl             | slovenian           |
+----------------+---------------------+
| tr             | turkish             |
+----------------+---------------------+
| zh             | chinese             |
+----------------+---------------------+

If the language is not available, an empty string is returned by the variable
resolver.
