export type ToggleableSettingsData = {
  disableXmlRpc: boolean;
  forceHttps: boolean;
  maintenanceMode: boolean;
  forceWww: boolean;
};

export type NonToggleableSettingsData = {
  bypassCode: string;
  currentWpVersion: string;
  newestWpVersion: string;
  phpVersion: string;
};

export type SettingsData = NonToggleableSettingsData & ToggleableSettingsData;
