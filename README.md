Translator Plug-In for Oempro
==============================

Oempro can be translated to any language by editing the central language file. However, you will need a text editor to edit the big language file.

Translator plug-in makes translation easier. It includes built-in Google Translator support and with a single mouse click, you can use Google Translator to translate phrases.

REQUIREMENTS
------------
Translator plug-in requires Oempro v4.1.16 or higher.

INSTALLATION
------------
Installation is easy and takes only a few minutes. After downloading the Translator plug-in, follow these steps to complete the installation:

1. Unzip the downloaded Translator plug-in package to your desktop
1. Go into the Translator plug-in directory
1. Upload the octtranslator directory inside "upload" directory into your Oempro's /plugins/ directory on the server
1. Login to your Oempro administration area
1. Click "Settings" on the top right menu
1. Click "Plug-Ins" on the left menu
1. Click "Enable" button next to Translator plug-in on the list
1. That's all. Now, Translator plug-in is enabled and ready to use.

UPGRADE
-------
In order to upgrade Translator plug-in, simply get the latest commit from GitHub repository. Then upload contents "upload" directory to your Oempro/plugins/ directory.

Then go to your Oempro administration area, click "Settings" on the top right menu and then click "Plug-Ins" on the left menu. Disable and enable your Translator plug-in and upgrade will be completed.

Once Translator plug-in is enabled, a new side-bar link will be inserted to the settings menu. After logging into your administration area, click "Settings" menu item on the top right menu.

Then click "Translator" menu item on the left.

Translator plug-in's main view will ask you to select previously created translation project or start a new one. Just select one of the languages from the list and then click "Proceed" button.

Once you click "Proceed" button, Translator plug-in will prepare the environment required for translating the default English language pack to the selected one. Once it's ready, you will be redirected to the translation screen.

You have three options for translating phrases:

1. Manual translation: By clicking on the text area and translating
1. Translating a specific phrase: By clicking on the text area and then clicking "Google Translate" link below the text area
1. Translating all phrases: Clicking "Click here to translate all phrases on this page" option

Once you are done on a specific page, before proceeding to next or previous page, you need to click "Save Changes" button at the end of the page. Otherwise, your changes will be lost.

In order to export the new translation file, just click "Tools" tab on and click "Export" button.

Translator plug-in will prepare the new language folder for you and ask you to download it as a ZIP file.

Unzip the uploaded ZIP file and then upload the translation folder to the following location:

	/oempro/templates/weefive/languages/

Then go to Admin Area > Settings > Preferences section and click the language drop list. You should see the new translation package in the list.

> Notice #1: This small plug-in is built to make your translation process easier on a well designed user interface. For some situations, you may still need to edit the language file manually on a text editor.

Future Oempro updates may contain some changes in the default English language pack. Those changes should be applied to your translations manually. You may refer to version change log to see changed/added language file entries.