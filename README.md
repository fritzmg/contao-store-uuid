[![](https://img.shields.io/packagist/v/fritzmg/contao-store-uuid.svg)](https://packagist.org/packages/fritzmg/contao-store-uuid)
[![](https://img.shields.io/packagist/dt/fritzmg/contao-store-uuid.svg)](https://packagist.org/packages/fritzmg/contao-store-uuid)

Contao Store UUID
===================

Mini extension which integrates a storeFormData and a updatePersonalData hook to automatically convert uploaded file paths to UUIDs. 

storeFormData
-------------

The extension checks if there is a DCA available for the target table and then checks if the field is a single fileTree input and converts the file path to an UUID before saving it to the database.


updatePersonalData
------------------

The extension checks if there is a DCA available for an uploaded file and then checks if the field is an upload field (fileTree backend widget is mapped to upload field by the personal data module) and then stores the uuid if the file is stored in the file system.

For DCA fields marked as multiple a single file is saved and the correspondig order field is also updated (if defined).
