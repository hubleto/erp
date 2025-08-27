import React, { Component } from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableLeads from "./Components/TableLeads"
import LeadFormActivity from "./Components/LeadFormActivity"
import TableLevels from './Components/TableLevels'

class LeadsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('LeadsTableLeads', TableLeads);
    globalThis.main.registerReactComponent('LeadsFormActivity', LeadFormActivity);
    globalThis.main.registerReactComponent('LeadsTableLevels', TableLevels);

    // miscellaneous
    globalThis.main.getApp('HubletoApp/Community/Campaigns').addFormTab({
      uid: 'leads',
      title: <span className='italic'>Leads</span>,
      onRender: (form: any) => {
        return <TableLeads
          tag={"table_campaign_lead"}
          parentForm={form}
          // //@ts-ignore
          // description={{ui: {showHeader:false}}}
          // descriptionSource='both'
          uid={form.props.uid + "_table_campaign_lead"}
          junctionTitle='Campaign'
          junctionModel='HubletoApp/Community/Leads/Models/LeadCampaign'
          junctionSourceColumn='id_campaign'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_lead'
        />;
      },
    });
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Leads', new LeadsApp());
