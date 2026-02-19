import React from 'react';
import App from '@hubleto/react-ui/core/App'
import request from "@hubleto/react-ui/core/Request";
import TableOrders from "./Components/TableOrders";
import OrdersFormActivity from "./Components/OrdersFormActivity"
import TableItems from "./Components/TableItems"
import TableQuotes from './Components/TableQuotes';

class OrdersApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('OrdersTableOrders', TableOrders);
    globalThis.hubleto.registerReactComponent('OrdersFormActivity', OrdersFormActivity);
    globalThis.hubleto.registerReactComponent('OrdersTableItems', TableItems);
    globalThis.hubleto.registerReactComponent('OrdersTableQuotes', TableQuotes);

    // miscellaneous
    globalThis.hubleto.getApp('Hubleto/App/Community/Deals').addCustomFormTab({
      uid: 'orders',
      title: 'Orders',
      onRender: (form: any) => {
        return <TableOrders
          tag={"table_order_deal"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_order_deal"}
          junctionTitle='Deal'
          junctionModel='Hubleto/App/Community/Orders/Models/OrderDeal'
          junctionSourceColumn='id_deal'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_order'
        />;
      },
    });

    globalThis.hubleto.getApp('Hubleto/App/Community/Deals').addFormHeaderButton(
      'Create order',
      (form: any) => {
        request.get(
          'orders/api/create-from-deal',
          {idDeal: form.state.record.id},
          (data: any) => {
            if (data.status == "success") {
              globalThis.window.open(globalThis.hubleto.config.projectUrl + '/orders/' + data.idOrder);
            }
          }
        );
      }
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Orders', new OrdersApp());
