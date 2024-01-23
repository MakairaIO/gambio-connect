<?php
/* --------------------------------------------------------------
   GoogleTrackingHeader.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioConnectHeaderHeader extends GambioConnectHeaderHeader_parent
{
    /**
     * Prepare data
     */
    public function prepare_data()
    {
        $this->initAutoSuggest();
        
        parent::prepare_data();
    }
    
    
    protected function initAutoSuggest()
    {
        $configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Makaira/GambioConnect');
        $activeSearch = $configurationStorage->get('active_search');
        
        if ($activeSearch === '1') {
            
            $this->content_array['active_search'] = true;
            $jsPublicPath  = DIR_WS_CATALOG . 'ui/assets/autosuggestion.js';
                
            $this->content_array['autosuggestion_js_file']      = $jsPublicPath;
        } else {
            $this->content_array['active_search'] = false;
        }
    }
}