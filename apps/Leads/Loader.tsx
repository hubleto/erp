import React, { Component } from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableLeads from "./Components/TableLeads"
import LeadFormActivity from "./Components/LeadFormActivity"
import TableLevels from './Components/TableLevels'

class LeadsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('LeadsTableLeads', TableLeads);
    globalThis.hubleto.registerReactComponent('LeadsTableLevels', TableLevels);
    globalThis.hubleto.registerReactComponent('LeadFormActivity', LeadFormActivity);

    // miscellaneous
    globalThis.hubleto.getApp('Hubleto/App/Community/Campaigns').addCustomFormTab({
      uid: 'leads',
      title: 'Leads',
      onRender: (form: any) => {
        return <TableLeads
          tag={"table_campaign_lead"}
          parentForm={form}
          uid={form.props.uid + "_table_campaign_lead"}
          junctionTitle='Campaign'
          junctionModel='Hubleto/App/Community/Leads/Models/LeadCampaign'
          junctionSourceColumn='id_campaign'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_lead'
        />;
      },
    });
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Leads', new LeadsApp());
