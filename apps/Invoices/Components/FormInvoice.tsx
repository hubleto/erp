import React, { Component } from 'react'
import HubletoForm, {HubletoFormProps, HubletoFormState} from "@hubleto/react-ui/ext/HubletoForm";
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import { InputFactory } from "@hubleto/react-ui/core/InputFactory";
import request from '@hubleto/react-ui/core/Request';
import TablePayments from './TablePayments';

interface FormInvoiceProps extends HubletoFormProps {
}

interface FormInvoiceState extends HubletoFormState {
}

export default class FormInvoice extends HubletoForm<FormInvoiceProps, FormInvoiceState> {
  static defaultProps = {
    ...HubletoForm.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
    renderWorkflowUi: true,
  }

  props: FormInvoiceProps;
  state: FormInvoiceState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\FormInvoice';

  idOrderInputs: Array<any>;

  constructor(props: FormInvoiceProps) {
    super(props);
  }

  getStateFromProps(props: FormInvoiceProps) {
    let tabs = [];
    
    if (this.props.id > 0) {
      tabs.push({ uid: 'default', title: <b>{this.translate('Invoice')}</b> });
      tabs.push({ uid: 'documents', title: this.translate('Documents') });
      tabs.push({ uid: 'payments', title: this.translate('Payments') });
    }
    tabs = [...tabs, ...this.getCustomTabs()];

    return {
      ...super.getStateFromProps(props),
      tabs: tabs,
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['ITEMS'],
    }
  }

  getHeaderButtons()
  {
    let buttons = super.getHeaderButtons();
    if (this.state.id > 0) {
      buttons.push(
        {
          title: 'Print to PDF',
          onClick: () => {
            request.post(
              'invoices/api/generate-pdf',
              {idInvoice: this.state.record.id},
              {},
              (result: any) => {
                if (result.idDocument) {
                  window.open(globalThis.main.config.projectUrl + '/documents/' + result.idDocument);
                }
              }
            );
          }
        }
      );
    }
    return buttons;
  }

  getRecordFormUrl(): string {
    return 'invoices/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    const r = this.state.record;
    return <>
      <small>{this.translate('Invoice')}</small>
      <h2>{r.number ? r.number : '---'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        const currencySymbol = R && R.CURRENCY ? R.CURRENCY.symbol : '';
        return <div className='flex gap-2'>
          <div className='gap-2 w-56'>
            <div className='p-2 grow'>
              {this.inputWrapper('number', {wrapperCssClass: 'block', cssClass: 'text-4xl'})}
            </div>
            <div className='p-2 grow text-nowrap bg-slate-100 text-slate-800'>
              <div>
                <div className='text-4xl'>
                  <b>{globalThis.main.numberFormat(R.total_excl_vat, 2, ',', ' ')} {currencySymbol}</b>
                </div>
                <div className='text-sm'>excl. VAT</div>
              </div>
              <div className='mt-2'>
                <div className='text-4xl'>
                  {globalThis.main.numberFormat(R.total_incl_vat, 2, ',', ' ')} {currencySymbol}
                </div>
                <div className='text-sm'>incl. VAT</div>
              </div>
            </div>
            <div className={'border-t border-t-4 border-t-blue-400 grow ' + (R.date_delivery ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
              {this.inputWrapper('date_delivery', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-orange-300 grow ' + (R.date_issue ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
              {this.inputWrapper('date_issue', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-green-400 grow ' + (R.date_due ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
              {this.inputWrapper('date_due', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-green-600 grow ' + (R.date_payment ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
              {this.inputWrapper('date_payment', {wrapperCssClass: 'block'})}
            </div>
          </div>
          <div className="flex flex-col flex-5 gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('type', {uiStyle: 'buttons'})}
              {this.inputWrapper('id_profile', {uiStyle: 'buttons'})}
              {this.inputWrapper('id_customer')}
              {this.state.id == -1 ? null : <>
                {this.inputWrapper('id_currency')}
                <div className='flex gap-2'>
                  <div className='grow'>
                    {this.inputWrapper('vs')}
                  </div>
                  <div className='grow'>
                    {this.inputWrapper('cs')}
                  </div>
                  <div className='grow'>
                    {this.inputWrapper('ss')}
                  </div>
                </div>
                {this.inputWrapper('notes')}
                {this.inputWrapper('id_issued_by')}
              </>}
            </div>
            {this.state.id <= 0 ? null :
              <div className='card flex-2'>
                <div className='card-header'>Items</div>
                <div className='card-body'>
                  <table className='table-default dense not-striped'>
                    <thead>
                      <tr>
                        <th colSpan={1} style={{width: '50%'}}>Order</th>
                        <th colSpan={6}>Product</th>
                      </tr>
                      <tr>
                        <th>Item</th>
                        <th>Unit price</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>VAT</th>
                        <th>Price excl. VAT</th>
                        <th>Price incl. VAT</th>
                      </tr>
                    </thead>
                    <tbody>
                      {R && R.ITEMS ? R.ITEMS.map((item, key) => {
                        const rowBgClass = (key % 2 == 0 ? 'bg-white' : 'bg-gray-50');

                        return <>
                          <tr key={key + '1'} className={item._toBeDeleted_ ? 'border border-red-400' : ''}>
                            <td colSpan={1} style={{width: '50%'}} className={rowBgClass}>
                              {InputFactory({
                                value: item.id_order,
                                cssClass: 'bg-white min-w-64',
                                description: {
                                  type: 'lookup',
                                  model: 'Hubleto/App/Community/Orders/Models/Order'
                                },
                                onInit: (input) => {
                                  this.idOrderInputs[key] = input;
                                },
                                onChange: (input, value) => {
                                  R.ITEMS[key].id_order = input.state.value;
                                  R.ITEMS[key].item = input.refInput.current.getValue()[0]?._LOOKUP;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td colSpan={6} className={rowBgClass}>
                              {InputFactory({
                                value: item.id_order_product,
                                cssClass: 'bg-white min-w-64',
                                description: {
                                  type: 'lookup',
                                  model: 'Hubleto/App/Community/Orders/Models/OrderProduct',
                                },
                                customEndpointParams: { idOrder: item.id_order },
                                onChange: (input) => {

                                  request.post('orders/api/get-product',
                                    {idOrderProduct: input.state.value},
                                    {},
                                    (data: any) => {
                                      const P = data.orderProduct;
                                      R.ITEMS[key].id_order_product = input.state.value;
                                      R.ITEMS[key].item = P?.title ?? '';
                                      R.ITEMS[key].unit_price = P?.sales_price ?? 0;
                                      R.ITEMS[key].amount = P?.amount ?? 0;
                                      R.ITEMS[key].price_excl_vat = P?.price_excl_vat ?? 0;
                                      R.ITEMS[key].price_incl_vat = P?.price_incl_vat ?? 0;
                                      R.ITEMS[key].vat = P?.vat ?? 0;
                                      R.ITEMS[key].discount = P?.discount ?? 0;
                                      this.updateRecord(R);
                                    }
                                  )
                                }
                              })}
                            </td>
                            <td rowSpan={2} className={rowBgClass}>
                              <button
                                className='btn btn-danger'
                                onClick={() => {
                                  R.ITEMS[key]._toBeDeleted_ = true;
                                  this.updateRecord(R);
                                }}
                              >
                                <span className='icon'><i className='fas fa-trash'></i></span>
                              </button>
                            </td>
                          </tr>
                          <tr key={key + '2'} className={item._toBeDeleted_ ? 'bg bg-red-50' : ''}>
                            <td className={rowBgClass}>
                              {InputFactory({
                                value: item.item,
                                cssClass: 'bg-white',
                                description: { type: 'string' },
                                onChange: (e) => {
                                  R.ITEMS[key].item = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass}>
                              {InputFactory({
                                value: item.unit_price,
                                cssClass: 'bg-white',
                                description: { type: 'number' },
                                onChange: (e) => {
                                  R.ITEMS[key].unit_price = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass}>
                              {InputFactory({
                                value: item.amount,
                                cssClass: 'bg-white',
                                description: { type: 'number' },
                                onChange: (e) => {
                                  R.ITEMS[key].amount = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass}>
                              {InputFactory({
                                value: item.discount,
                                cssClass: 'bg-white',
                                description: { type: 'number', unit: '%' },
                                onChange: (e) => {
                                  R.ITEMS[key].discount = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass}>
                              {InputFactory({
                                value: item.vat,
                                cssClass: 'bg-white',
                                description: { type: 'number', unit: '%' },
                                onChange: (e) => {
                                  R.ITEMS[key].vat = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass}>
                              {globalThis.main.numberFormat(item.price_excl_vat, 2, ',', ' ')} {currencySymbol}
                            </td>
                            <td className={rowBgClass}>
                              {globalThis.main.numberFormat(item.price_incl_vat, 2, ',', ' ')} {currencySymbol}
                            </td>
                          </tr>
                        </>;
                      }) : null}
                    </tbody>
                  </table>
                  <button
                    className='btn btn-add mt-2'
                    onClick={() => {
                      if (!R.ITEMS) R.ITEMS = [];
                      R.ITEMS.push({id_invoice: this.state.id});
                      this.updateRecord(R);
                    }}
                  >
                    <span className='icon'><i className='fas fa-plus'></i></span>
                    <span className='text'>Add item</span>
                  </button>
                </div>
              </div>
            }
          </div>
        </div>;
      break;

      case 'documents':
        return <>
          <TableDocuments
            uid={this.props.uid + "_table_invoice_documents"}
            tag={'table_invoice_documents'}
            parentForm={this}
            junctionModel='Hubleto\App\Community\Invoices\Models\InvoiceDocument'
            junctionSourceColumn='id_invoice'
            junctionDestinationColumn='id_document'
            junctionSourceRecordId={R.id}
          />
        </>
      break;

      case 'payments':
        return <TablePayments
          uid={this.props.uid + "_table_invoice_payments"}
          tag={'table_invoice_payments'}
          parentForm={this}
          idInvoice={R.id}
        />;
      break;

    }
  }
}
