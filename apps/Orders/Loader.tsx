import React from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import request from "@hubleto/react-ui/core/Request";
import TableOrders from "./Components/TableOrders";

class OrdersApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('OrdersTableOrders', TableOrders);

    // miscellaneous
    globalThis.main.getApp('HubletoApp/Community/Deals').addFormTab({
      uid: 'orders',
      title: <span className='italic'>Orders</span>,
      onRender: (form: any) => {
        return <TableOrders
          tag={"table_order_deal"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_order_deal"}
          junctionTitle='Deal'
          junctionModel='HubletoApp/Community/Orders/Models/OrderDeal'
          junctionSourceColumn='id_deal'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_order'
        />;
      },
    });

    globalThis.main.getApp('HubletoApp/Community/Deals').addFormHeaderButton(
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
globalThis.main.registerApp('HubletoApp/Community/Orders', new OrdersApp());
