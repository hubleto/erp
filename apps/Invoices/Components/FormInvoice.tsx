import React, { Component } from 'react'
import HubletoForm, {HubletoFormProps, HubletoFormState} from "@hubleto/react-ui/ext/HubletoForm";
import TableInvoiceItems from './TableInvoiceItems';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import { InputFactory } from "@hubleto/react-ui/core/InputFactory";
import WorkflowSelector from '../../Workflow/Components/WorkflowSelector';
import request from '@hubleto/react-ui/core/Request';

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

  constructor(props: FormInvoiceProps) {
    super(props);
  }

  getStateFromProps(props: FormInvoiceProps) {
    let tabs = [];
    
    if (this.props.id > 0) {
      tabs.push({ uid: 'default', title: <b>{this.translate('Invoice')}</b> });
      tabs.push({ uid: 'documents', title: this.translate('Documents'), showCountFor: 'DOCUMENTS' });
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

  // renderTopMenu(): JSX.Element {
  //   return <>
  //     {super.renderTopMenu()}
  //     {this.state.id <= 0 ? null : <>
  //       <div className='flex-2 pl-4'><WorkflowSelector parentForm={this}></WorkflowSelector></div>
  //       {this.inputWrapper('id_profile', {wrapperCssClass: 'flex gap-2'})}
  //     </>}
  //   </>
  // }

  renderTitle(): JSX.Element {
    const r = this.state.record;
    return <>
      <small>{this.translate('Invoice')}</small>
      <h2>{r.number ? r.number : '---'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    let R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='flex w-full bg-gradient-to-b from-slate-100 to-white'>
            <div className='border-t border-t-4 border-t-slate-600 p-2 grow text-nowrap bg-slate-100 text-slate-800'>
              <div>
                <div className='text-sm'>
                  <b>{globalThis.main.numberFormat(R.total_excl_vat, 2, ',', ' ')} {R.CURRENCY.symbol}</b>
                  &nbsp;excl. VAT
                </div>
              </div>
              <div className='mt-2'>
                <div className='text-2xl'>
                  {globalThis.main.numberFormat(R.total_incl_vat, 2, ',', ' ')} {R.CURRENCY.symbol}
                </div>
                <div className='text-sm'>incl. VAT</div>
              </div>
            </div>
            <div className='border-t border-t-4 border-t-blue-600 p-2 grow'>
              {this.inputWrapper('number', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-blue-400 p-2 grow ' + (R.date_delivery ? '' : 'bg-gradient-to-b from-red-50 to-white border-b border-b-red-800')}>
              {this.inputWrapper('date_delivery', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-orange-300 p-2 grow ' + (R.date_issue ? '' : 'bg-gradient-to-b from-red-50 to-white border-b border-b-red-800')}>
              {this.inputWrapper('date_issue', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-green-400 p-2 grow ' + (R.date_due ? '' : 'bg-gradient-to-b from-red-50 to-white border-b border-b-red-800')}>
              {this.inputWrapper('date_due', {wrapperCssClass: 'block'})}
            </div>
            <div className={'border-t border-t-4 border-t-green-600 p-2 grow ' + (R.date_payment ? '' : 'bg-gradient-to-b from-red-50 to-white border-b border-b-red-800')}>
              {this.inputWrapper('date_payment', {wrapperCssClass: 'block'})}
            </div>
          </div>
          <div className="flex gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('type')}
              {this.inputWrapper('id_template')}
              {this.inputWrapper('id_currency')}
              {this.state.id == -1 ? null : <>
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
            <div className='card flex-2'>
              <div className='card-header'>Items</div>
              <div className='card-body'>
                <table className='table-default dense not-striped'>
                  <thead>
                    <tr>
                      <th>Item</th>
                      <th>Unit price</th>
                      <th>Amount</th>
                      <th>Price excl. VAT</th>
                      <th>Price incl. VAT</th>
                      <th>VAT</th>
                      <th>Discount</th>
                    </tr>
                  </thead>
                  <tbody>
                    {R.ITEMS.map((item, key) => {
                      console.log(item);
                      return <tr key={key} className={item._toBeDeleted_ ? 'border border-red-400' : ''}>
                        <td>
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
                        <td>
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
                        <td>
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
                        <td>
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
                        <td>
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
                        <td>
                          {globalThis.main.numberFormat(item.price_excl_vat, 2, ',', ' ')} {R.CURRENCY.symbol}
                        </td>
                        <td>
                          {globalThis.main.numberFormat(item.price_incl_vat, 2, ',', ' ')} {R.CURRENCY.symbol}
                        </td>
                        <td>
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
                      </tr>;
                    })}
                  </tbody>
                </table>
                <button
                  className='btn btn-add mt-2'
                  onClick={() => {
                    R.ITEMS.push({id_invoice: this.state.id});
                    this.updateRecord(R);
                  }}
                >
                  <span className='icon'><i className='fas fa-plus'></i></span>
                  <span className='text'>Add item</span>
                </button>
              </div>
            </div>
          </div>
        </>;
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

    }
  }
}
