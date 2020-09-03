<?php

/**
 * Contao Open Source CMS
 *
 * simple extension to automatically save a file field as an UUID instead of the path
 * 
 * @copyright inspiredminds 2015
 * @package   store_uuid
 * @link      http://www.inspiredminds.at
 * @author    Fritz Michael Gschwantner <fmg@inspiredminds.at>
 * @license   GPL-2.0
 */


class StoreUUID extends \Controller
{
    /**
     * Searches through the DCA and converts a file path to an UUID
     * if the DCA field is a single fileTree input
     */
    public function storeFormData( $arrSet, \Form $objForm )
    {
        // get table
        $table = $objForm->targetTable;

        // load DCA
        $this->loadDataContainer( $table );

        // check if DCA exists for target table
        if( !is_array( $GLOBALS['TL_DCA'][ $table ] ) )
            return $arrSet;

        // go through each field
        foreach( $arrSet as $field => $value )
        {
            // check if field exists in DCA
            if( !isset( $GLOBALS['TL_DCA'][ $table ]['fields'][ $field ] ) )
                continue;

            // get DCA for field
            $dcaField = $GLOBALS['TL_DCA'][ $table ]['fields'][ $field ];

            // check for single fileTree inputType
            if( $dcaField['inputType'] != 'fileTree' )
                continue;

            // check if value is array
            if( is_array( $value ) )
            {
                // DCA field must be enabled for multiple selection
                if( !$dcaField['eval']['multiple'] )
                    continue;

                // prepare UUID array
                $uuids = array();

                // go through each file
                foreach( $value as $file )
                {
                    // check if file exists
                    if( file_exists( TL_ROOT .'/'. $file ) )
                    {
                        // get the file object to retrieve UUID
                        $uuids[] = \Dbafs::addResource( $file )->uuid;
                    }
                }

                // set the UUID array
                $arrSet[ $field ] = serialize( $uuids );
            }
            // check if file does indeed exist
            elseif( file_exists( TL_ROOT .'/'. $value ) )
            {
                // get the file object to retrieve UUID
                $objFile = \Dbafs::addResource( $value );

                // set the UUID
                $arrSet[ $field ] = $dcaField['eval']['multiple'] ? serialize( array( $objFile->uuid ) ) : $objFile->uuid;
            }
        }

        // return result
        return $arrSet;
    }

    public function updatePersonalData(\Contao\FrontendUser $user)
    {
        if (!isset($_SESSION['FILES']) || !is_array($_SESSION['FILES'])) {
            return;
        }

        $update = [];
        foreach ($_SESSION['FILES'] as $field => $uploadedFile) {
            if (isset($uploadedFile['storeUuid']) || !$uploadedFile['uploaded'] || !$uploadedFile['uuid']) {
                continue;
            }

            $fieldConfig = $GLOBALS['TL_DCA']['tl_member']['fields'][$field];

            // Only support upload fields as other widgets might handle uploads itself
            if ($fieldConfig['inputType'] !== 'upload') {
                continue;
            }

            $value = \Contao\StringUtil::uuidToBin($uploadedFile['uuid']);

            // Contao upload widget only support single uploads.
            // Save compatible data for multiple fields at least.
            if (! empty($fieldConfig['eval']['multiple'])) {
                $value = [$value];

                if (! empty($fieldConfig['eval']['orderField'])) {
                    $update[$fieldConfig['eval']['orderField']] = $value;
                }
            }

            $update[$field] = $value;

            // Module personal data does not reset FILES session so there may be outdated entries.
            // We cannot delete entry as other extensions might depend on it.
            // Mark file as processed. A new update would override the entry.
            $_SESSION['FILES'][$field]['storeUuid'] = true;
        }

        if ($update === []) {
            return;
        }

        \Contao\Database::getInstance()
            ->prepare('UPDATE tl_member %s WHERE id=?')
            ->set($update)
            ->execute($user->id);
    }
}
