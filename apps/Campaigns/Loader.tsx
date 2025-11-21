import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableCampaigns from "./Components/TableCampaigns"
import TableRecipients from "./Components/TableRecipients"
import TableRecipientStatuses from "./Components/TableRecipientStatuses"

class CampaignsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('CampaignsTableCampaigns', TableCampaigns);
    globalThis.main.registerReactComponent('CampaignsTableRecipients', TableRecipients);
    globalThis.main.registerReactComponent('CampaignsTableRecipientStatuses', TableRecipientStatuses);
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Campaigns', new CampaignsApp());
