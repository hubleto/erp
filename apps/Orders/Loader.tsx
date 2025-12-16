import React from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import request from "@hubleto/react-ui/core/Request";
import TableOrders from "./Components/TableOrders";
import OrdersFormActivity from "./Components/OrdersFormActivity"
import TablePayments from "./Components/TablePayments"

class OrdersApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('OrdersTableOrders', TableOrders);
    globalThis.main.registerReactComponent('OrdersFormActivity', OrdersFormActivity);
    globalThis.main.registerReactComponent('OrdersTablePayments', TablePayments);

    // miscellaneous
    globalThis.main.getApp('Hubleto/App/Community/Deals').addCustomFormTab({
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

    globalThis.main.getApp('Hubleto/App/Community/Deals').addFormHeaderButton(
      'Create order',
      (form: any) => {
        request.get(
          'orders/api/create-from-deal',
          {idDeal: form.state.record.id},
          (data: any) => {
            if (data.status == "success") {
              globalThis.window.open(globalThis.main.config.projectUrl + '/orders/' + data.idOrder);
            }
          }
        );
      }
    )
  }
}

// register app
globalThis.main.registerApp('Hubleto/App/Community/Orders', new OrdersApp());
