import App from '@hubleto/react-ui/core/App'
import request from "@hubleto/react-ui/core/Request";
import TableProfiles from "./Components/TableProfiles"
import TableInvoices from "./Components/TableInvoices"
import TableItems from "./Components/TableItems"
import TablePayments from "./Components/TablePayments"

class InvoicesApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('InvoicesTableProfiles', TableProfiles);
    globalThis.hubleto.registerReactComponent('InvoicesTableInvoices', TableInvoices);
    globalThis.hubleto.registerReactComponent('InvoicesTableItems', TableItems);
    globalThis.hubleto.registerReactComponent('InvoicesTablePayments', TablePayments);

    globalThis.hubleto.getApp('Hubleto/App/Community/Orders').addFormHeaderButton(
      'Create invoice',
      (form: any) => {
        request.get(
          'invoices/api/create-invoice-from-order',
          {idOrder: form.state.record.id},
          (data: any) => {
            if (data.status == "success") {
              globalThis.window.open(globalThis.hubleto.config.projectUrl + '/invoices/' + data.idInvoice);
            }
          }
        );
      }
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Invoices', new InvoicesApp());
