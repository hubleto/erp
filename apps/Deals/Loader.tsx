import React from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableDeals from "./Components/TableDeals"
import DealFormActivity from "./Components/DealFormActivity"
import request from "@hubleto/react-ui/core/Request";

class DealsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('DealsTableDeals', TableDeals);
    globalThis.main.registerReactComponent('DealsFormActivity', DealFormActivity);

    // miscellaneous
    globalThis.main.getApp('HubletoApp/Community/Leads').addFormTab({
      uid: 'deals',
      title: <span className='italic'>Deals</span>,
      onRender: (form: any) => {
        return <TableDeals
          tag={"table_lead_deal"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_lead_deal"}
          junctionTitle='Deal'
          junctionModel='HubletoApp/Community/Deals/Models/DealLead'
          junctionSourceColumn='id_lead'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_deal'
        />;
      },
    });

    globalThis.main.getApp('HubletoApp/Community/Leads').addFormHeaderButton(
      'Create deal',
      (form: any) => {
        request.get(
          'deals/api/create-from-lead',
          {idLead: form.state.record.id},
          (data: any) => {
              if (data.status == "success") {
              globalThis.window.open(globalThis.main.config.projectUrl + `/deals/${data.idDeal}`)
              }
          }
        );
      }
    )
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Deals', new DealsApp());
