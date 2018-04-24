Youtube Plugin
==============

Description
===========
This plugin allows inserting Youtube videos using embed code or just the video
URL.

Installation
============
1. Download the plugin from http://ckeditor.com/addon/youtube
2. Place the plugin in the root libraries folder (/libraries).
3. Enable CKEditor Youtube module in the Drupal admin.

Each filter format will now have a config tab for this plugin.

Usage
=====
Go to the Text formats and editors settings (/admin/config/content/formats) and
add the Youtube Button to any CKEditor-enabled text format you want.

If you are using the "Limit allowed HTML tags and correct faulty HTML" filter
make sure that Allowed HTML tags include:

```
<div> <iframe width height src frameborder allowfullscreen> <object> <param>
<a> <img>
```

If the filter is enabled, these tags should be added automatically the first
time you add the YouTube button to the toolbar.

To change the video once added, place the cursor right after the video and press
the DELETE key. The video is considered just a character and it will be deleted
as any other piece of text. Then you can embed the new video as usual.

Dependencies
============
This module requires:

* CKEditor (core)

Uninstallation
==============
1. Uninstall the module from 'Administer >> Modules'.

MAINTAINERS
===========
Mauricio Dinarte - https://www.drupal.org/u/dinarcon

Credits
=======

Initial development and maintenance by Agaric.
