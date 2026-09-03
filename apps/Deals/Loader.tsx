import React from 'react';
import App from '@hubleto/react-ui/core/App'
import TableDeals from "./Components/FC/TableDeals"
import DealCalendarActivityForm from "./Components/FC/DealCalendarActivityForm"
import request from "@hubleto/react-ui/core/Request";
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';
import { FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';

class DealsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('DealsTableDeals', TableDeals);
    globalThis.hubleto.registerReactComponent('DealCalendarActivityForm', DealCalendarActivityForm);

    // miscellaneous
    globalThis.hubleto.getApp('Hubleto/App/Community/Leads').addCustomFormTab({
      uid: 'deals',
      title: globalThis.hubleto.translate('Deals', 'Hubleto\\App\\Community\\Deals\\Loader', 'manifest'),
      onRender: (form: any) => {
        return <TableDeals
          tag={"table_lead_deal"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_lead_deal"}
          junctionTitle='Deal'
          junctionModel='Hubleto/App/Community/Deals/Models/DealLead'
          junctionSourceColumn='id_lead'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_deal'
        />;
      },
    });

    FormCustomizer.addFormHeaderExtraButton(
      'FormLead',
      (form: FormMeta) => { return form.id <= 0 ? false : {
        title: 'Create deal',
        icon: 'fas fa-diagram-project',
        onClick: (form: FormMeta) => {
          request.get(
            'deals/api/create-from-lead',
            {idLead: form.id},
            (data: any) => {
                if (data.status == "success") {
                globalThis.window.open(globalThis.hubleto.config.projectUrl + `/deals/${data.idDeal}`)
                }
            }
          );
        }
      }}
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Deals', new DealsApp());
