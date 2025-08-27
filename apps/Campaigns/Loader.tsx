import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableCampaigns from "./Components/TableCampaigns"

class CampaignsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('CampaignsTableCampaigns', TableCampaigns);
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Campaigns', new CampaignsApp());
