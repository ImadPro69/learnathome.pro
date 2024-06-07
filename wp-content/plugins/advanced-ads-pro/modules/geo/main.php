<?php

if ( Advanced_Ads_Geo_Version_Check::is_geo_active() ) {
	return;
}

new Advanced_Ads_Geo();
Advanced_Ads_Geo_Plugin::get_instance();
