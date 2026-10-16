import React from 'react';
import App from '@hubleto/react-ui/core/App'
import request from "@hubleto/react-ui/core/Request";
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';
import { type FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';
import TablePriceLists from "./Components/FC/TablePriceLists"
import TableItems from "./Components/FC/TableItems"
import TableAssignments from "./Components/FC/TableAssignments"
import TableRules from "./Components/FC/TableRules"
import TableHistories from "./Components/FC/TableHistories"
import TableResolutions from "./Components/FC/TableResolutions"
import TablePriceListHasDocuments from "./Components/FC/TablePriceListHasDocuments"
import PriceSimulator from "./Components/FC/PriceSimulator"
import PriceMatrix from "./Components/FC/PriceMatrix"
import EffectivePriceLists from "./Components/FC/EffectivePriceLists"
import Translator from '@hubleto/react-ui/core/Translator';
const T = new Translator('Hubleto/App/Community/PriceLists/Loader', 'manifest');

/** Parent document models of the "Apply price list" button, by the form of the other app. */
const documentForms: { [formName: string]: string } = {
  FormOrder: 'Hubleto/App/Community/Orders/Models/Order',
  FormDeal: 'Hubleto/App/Community/Deals/Models/Deal',
  FormInvoice: 'Hubleto/App/Community/Invoices/Models/Invoice',
  FormReceipt: 'Hubleto/App/Community/Cashdesk/Models/Receipt',
};

class PriceListApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('PriceListTablePriceLists', TablePriceLists);
    globalThis.hubleto.registerReactComponent('PriceListTableItems', TableItems);
    globalThis.hubleto.registerReactComponent('PriceListTableAssignments', TableAssignments);
    globalThis.hubleto.registerReactComponent('PriceListTableRules', TableRules);
    globalThis.hubleto.registerReactComponent('PriceListTableHistories', TableHistories);
    globalThis.hubleto.registerReactComponent('PriceListTableResolutions', TableResolutions);
    globalThis.hubleto.registerReactComponent('PriceListTablePriceListHasDocuments', TablePriceListHasDocuments);
    globalThis.hubleto.registerReactComponent('PriceListPriceSimulator', PriceSimulator);
    globalThis.hubleto.registerReactComponent('PriceListPriceMatrix', PriceMatrix);

    // tab "Price lists" in the product form
    FormCustomizer.addTab(
      'FormProduct',
      'price-lists',
      (form: FormMeta) => { return form.id <= 0 ? false : {
        title: T.translate('Price lists'),
        content: (form: FormMeta) => {
          return <TableItems
            tag={"table_price_list_item_product"}
            parentForm={form}
            uid={form.uid + "_table_price_list_item_product"}
            idProduct={form.id}
          />;
        }
      }}
    );

    // tab "Price lists" in the customer form
    FormCustomizer.addTab(
      'FormCustomer',
      'price-lists',
      (form: FormMeta) => { return form.id <= 0 ? false : {
        title: T.translate('Price lists'),
        content: (form: FormMeta) => {
          return <>
            <EffectivePriceLists idCustomer={form.id} />
            <TableAssignments
              tag={"table_price_list_assignment_customer"}
              parentForm={form}
              uid={form.uid + "_table_price_list_assignment_customer"}
              idCustomer={form.id}
            />
          </>;
        }
      }}
    );

    // tabs with assignments in forms of shops, projects and deals
    const assignmentTabs: Array<{ formName: string, prop: string }> = [
      { formName: 'FormShop', prop: 'idShop' },
      { formName: 'FormProject', prop: 'idProject' },
      { formName: 'FormDeal', prop: 'idDeal' },
    ];
    assignmentTabs.map((assignmentTab) => {
      FormCustomizer.addTab(
        assignmentTab.formName,
        'price-lists',
        (form: FormMeta) => { return form.id <= 0 ? false : {
          title: T.translate('Price lists'),
          content: (form: FormMeta) => {
            const idParams: any = {};
            idParams[assignmentTab.prop] = form.id;
            return <TableAssignments
              tag={"table_price_list_assignment_" + assignmentTab.formName}
              parentForm={form}
              uid={form.uid + "_table_price_list_assignment_" + assignmentTab.formName}
              {...idParams}
            />;
          }
        }}
      );
    });

    // documents: tab with resolutions of their items and the button "Apply price list"
    Object.keys(documentForms).map((formName: string) => {
      FormCustomizer.addTab(
        formName,
        'price-list-resolutions',
        (form: FormMeta) => { return form.id <= 0 ? false : {
          title: T.translate('Price list'),
          content: (form: FormMeta) => {
            return <TableResolutions
              tag={"table_price_list_resolution_" + formName}
              parentForm={form}
              uid={form.uid + "_table_price_list_resolution_" + formName}
              parentModel={documentForms[formName].replace(/\//g, '\\')}
              idParentRecord={form.id}
            />;
          }
        }}
      );

      FormCustomizer.addFormHeaderExtraButton(
        formName,
        (form: FormMeta) => { return form.id <= 0 ? false : {
          title: T.translate('Apply price list'),
          icon: 'fas fa-tags',
          onClick: (form: FormMeta) => {
            request.get(
              'price-lists/api/apply-to-document',
              {model: documentForms[formName], idRecord: form.id},
              (data: any) => {
                if (data.status == "success") form.reload();
                else alert(data.message);
              }
            );
          }
        }}
      );
    });
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/PriceLists', new PriceListApp());
