<?php

/**
 * Contao Open Source CMS
 *
 * simple extension to automatically save a file field as an UUID instead of the path
 * 
 * @copyright inspiredminds 2015
 * @package   sharebuttons
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
            if( $dcaField['inputType'] != 'fileTree' || $dcaField['eval']['multiple'] )
                continue;

            // convert file path to UUID
            if( file_exists( TL_ROOT .'/'. $value ) )
            {
                $objFile = \Dbafs::addResource( $value );
                $arrSet[ $field ] = $objFile->uuid;
            }
        }

        // return result
        return $arrSet;
    }
}
