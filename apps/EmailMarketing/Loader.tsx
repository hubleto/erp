import App from '@hubleto/react-ui/core/App'
import TableCampaigns from "./Components/TableCampaigns"
import TableCampaignsSchedules from "./Components/TableCampaignsSchedules"
import TableCampaignsSchedulesRecipients from "./Components/TableCampaignsSchedulesRecipients"
import TableEmails from "./Components/TableEmails"
import TableEmailClicks from "./Components/TableEmailClicks"
import TableRecipients from "./Components/TableRecipients"
import TableRecipientStatuses from "./Components/TableRecipientStatuses"

class EmailMarketingApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('EmailMarketingTableCampaigns', TableCampaigns);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableCampaignsSchedules', TableCampaignsSchedules);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableCampaignsSchedulesRecipients', TableCampaignsSchedulesRecipients);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableEmails', TableEmails);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableEmailClicks', TableEmailClicks);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableRecipients', TableRecipients);
    globalThis.hubleto.registerReactComponent('EmailMarketingTableRecipientStatuses', TableRecipientStatuses);
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/EmailMarketing', new EmailMarketingApp());
