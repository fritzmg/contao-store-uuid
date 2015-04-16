Contao Store UUID
===================

Mini extension which integrates a storeFormData hook to automatically convert file paths to UUIDs. The hook checks if there is a DCA available for the target table and then checks if the field is a single fileTree input and converts the file path to an UUID before saving it to the database.
