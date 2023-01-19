.. include:: /Includes.rst.txt

.. _config-examples:

======================
Configuration Examples
======================

How Do I Use a Different Preset?
================================

By default only one preset is defined with the name "default". However, you can
change it the same way like rte_ckeditor like this using **Page TSconfig**:

.. code-block:: typoscript

   RTE.default.preset = my_preset

You can also specify different presets for different tables and columns like so:

.. code-block:: typoscript

   RTE.config.tt_content.bodytext.preset = my_preset

If you want to set presets only for certain content types you can do so:

.. code-block:: typoscript

   RTE.config.tt_content.bodytext.types.text.preset = minimal

For more examples, see :ref:`t3tsconfig:pageTsRte` in "TSconfig Reference".


How Do I Create My Own Preset?
==============================

In your provider extension:

In :file:`ext_localconf.php`, replace `my_extension` with your extension key, replace `my_preset` and `MyPreset.yaml`
with the name of your preset.

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['my_preset'] = 'EXT:my_extension/Configuration/RTE/MyPreset.yaml';

In :file:`Configuration/RTE/MyPreset.yaml`, create your configuration, for example::

   # This is the complete configuration
   editor:
     config:
       # Specify a custom path
       contentsCss: 'EXT:trix/Resources/Public/Css/contents.css'
       autofocus: true
       heading1:
         tagName: h3

How do I specify custom CSS for the editor?
===========================================

You can give the user a hint of what the styled text will look in the frontend
by adding a custom CSS file to your provider extension and link that file within your preset (see above).

After you created your own preset file (YAML) and linked it within your TSconfig you create a CSS
file within your provider extension, for example at:

:file:`Resources/Public/Css/rte.css`

When creating the CSS file make sure all CSS instructions have the `trix-editor` selector preceeding,
for example:

.. code-block:: code

    trix-editor div {
        margin-bottom: 10px;
    }

Then don't forget to specify the path to the CSS file within your preset,
change my_extension to your extension name::

   # This is the complete configuration
   editor:
     config:
       # Your custom path
       contentsCss: 'EXT:my_extension/Resources/Public/Css/contents.css'
       autofocus: true
       heading1:
         tagName: h3

How do I customize the heading HTML-tag?
========================================

By default only one heading can be styled with Trix-Editor. That heading
is by default set to be a h3-Tag. You can change this to any tag you want by changing
it in a custom preset file (see above) and changed the property "tagName" under "heading1".