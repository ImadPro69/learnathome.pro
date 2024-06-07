<?php

if ( Advanced_Ads_Geo_Version_Check::is_geo_installed() ) {
	Advanced_Ads_Geo_Version_Check::show_deprecated_geo_notice();

	if ( Advanced_Ads_Geo_Version_Check::is_geo_active() ) {
		return;
	}
}

new Advanced_Ads_Geo_Admin();
