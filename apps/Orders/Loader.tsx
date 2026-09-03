import React from 'react';
import App from '@hubleto/react-ui/core/App'
import request from "@hubleto/react-ui/core/Request";
import TableOrders from "./Components/FC/TableOrders";
import TableItems from "./Components/FC/TableItems"
import TableQuotes from './Components/FC/TableQuotes';
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';
import { FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';

class OrdersApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('OrdersTableOrders', TableOrders);
    globalThis.hubleto.registerReactComponent('OrdersTableItems', TableItems);
    globalThis.hubleto.registerReactComponent('OrdersTableQuotes', TableQuotes);

    FormCustomizer.addFormHeaderExtraButton(
      'FormDeal',
      (form: FormMeta) => { return form.id <= 0 ? false : {
        title: 'Create order',
        icon: 'fas fa-money-check-dollar',
        onClick: (form: FormMeta) => {
          request.get(
            'orders/api/create-from-deal',
            {idDeal: form.id},
            (data: any) => {
              if (data.status == "success") {
                globalThis.window.open(globalThis.hubleto.config.projectUrl + '/orders/' + data.idOrder);
              }
            }
          );
        }
      }}
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Orders', new OrdersApp());
