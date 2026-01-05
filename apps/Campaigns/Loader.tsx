import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableCampaigns from "./Components/TableCampaigns"
import TableRecipients from "./Components/TableRecipients"
import TableRecipientStatuses from "./Components/TableRecipientStatuses"
import CampaignFormActivity from "./Components/CampaignFormActivity"

class CampaignsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('CampaignsTableCampaigns', TableCampaigns);
    globalThis.hubleto.registerReactComponent('CampaignsTableRecipients', TableRecipients);
    globalThis.hubleto.registerReactComponent('CampaignsTableRecipientStatuses', TableRecipientStatuses);
    globalThis.hubleto.registerReactComponent('CampaignFormActivity', CampaignFormActivity);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Campaigns', new CampaignsApp());
