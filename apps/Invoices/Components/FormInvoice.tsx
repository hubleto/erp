import React, { Component } from 'react'
import FormExtended, {FormExtendedProps, FormExtendedState} from "@hubleto/react-ui/ext/FormExtended";
import { InputFactory } from "@hubleto/react-ui/core/InputFactory";
import request from '@hubleto/react-ui/core/Request';
import TableItems from './TableItems';
import TablePayments from './TablePayments';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";
import TextareaWithHtmlPreview from "@hubleto/react-ui/core/Inputs/TextareaWithHtmlPreview";

interface FormInvoiceProps extends FormExtendedProps {
}

interface FormInvoiceState extends FormExtendedState {
  linkPreparedItem: boolean;
  sendInvoiceEmailType?: any,
  sendInvoicePreparedData?: any,
  sendInvoiceResult?: any,
  htmlPreview?: any,
}

export default class FormInvoice extends FormExtended<FormInvoiceProps, FormInvoiceState> {
  static defaultProps = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
    renderPreviewUi: true,
  }

  props: FormInvoiceProps;
  state: FormInvoiceState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\FormInvoice';

  idOrderInputs: Array<any>;

  constructor(props: FormInvoiceProps) {
    super(props);
  }

  getTabsLeft() {
    let tabs = [];
    if (this.props.id > 0) {
      tabs.push({ uid: 'default', title: <b>{this.translate('Invoice', 'Hubleto\\App\\Community\\Invoices\\Loader', 'Components\\FormInvoice')}</b> });
      tabs.push({ uid: 'payments', title: this.translate('Payments', 'Hubleto\\App\\Community\\Invoices\\Loader', 'Components\\FormInvoice') });
      tabs.push({ uid: 'email', title: this.translate('Email', 'Hubleto\\App\\Community\\Invoices\\Loader', 'Components\\FormInvoice') });
    }
    return [
      ...tabs,
      ...super.getTabsLeft(),
    ];
  }

  getStateFromProps(props: FormInvoiceProps) {
    return {
      ...super.getStateFromProps(props),
      linkPreparedItem: false,
      sendInvoiceEmailType: 'send-invoice',
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['ITEMS'],
    }
  }

  getRecordFormUrl(): string {
    return 'invoices/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  onTabChange() {
    super.onTabChange();

    switch (this.state.activeTabUid) {
      case 'email':
        this.updateEmailPreview();
      break;
    }
  }

  updateEmailPreview() {
    this.setState({sendInvoicePreparedData: null, sendInvoiceResult: null});
    request.post(
      'invoices/api/send-invoice-in-email',
      {
        idInvoice: this.state.record.id,
        emailType: this.state.sendInvoiceEmailType,
        prepare: true
      },
      {},
      (result: any) => {
        this.setState({sendInvoicePreparedData: result})
      }
    );
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    let title = (this.state.record.inbound_outbound == 1 ? this.translate('Inbound') : this.translate('Outbound'));

    switch (R.type) {
      case 1: case '1': title += ' ' + this.translate('Proforma Invoice'); break;
      case 2: case '2': title += ' ' + this.translate('Advance Invoice'); break;
      case 3: case '3': title += ' ' + this.translate('Invoice'); break;
      case 4: case '4': title += ' ' + this.translate('Credit Note'); break;
      case 5: case '5': title += ' ' + this.translate('Debit Note'); break;
    }
    return <>
      <small>{title}</small>
      <h2>{R.number ? R.number : '---'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        const currencySymbol = R && R.CURRENCY ? R.CURRENCY.symbol : '';

        if (this.state.id <= 0) {
          return <>
            {this.inputWrapper('inbound_outbound', { uiStyle: 'buttons' })}
            {this.inputWrapper('type', { uiStyle: 'buttons' })}
            {R.inbound_outbound == 1
              ? this.inputWrapper('id_supplier')
              : this.inputWrapper('id_customer')
            }
            {this.inputWrapper('number', {cssClass: 'text-4xl'})}
          </>
        } else {
          return <>
            <div className='flex flex-col md:flex-row gap-2'>
              {this.input('inbound_outbound', { cssClass: 'w-auto', uiStyle: 'buttons' })}
              {this.input('type', { cssClass: 'w-auto', uiStyle: 'buttons' })}
              <div className='grow'>
                {R.inbound_outbound == 1 ?
                  this.inputWrapper('id_supplier', {wrapperCssClass: 'flex gap-2'})
                : this.inputWrapper('id_customer', {wrapperCssClass: 'flex gap-2'})}
              </div>
            </div>
            <div className='flex flex-col md:flex-row gap-2 mt-2'>
              <div className="flex flex-5 gap-2 mt-2">
                <div className='flex-1 min-w-80'>
                  {this.state.id == -1 ? null : <>
                    <div className='p-2 grow'>
                      {this.inputWrapper('number', {wrapperCssClass: 'block', cssClass: 'text-xl'})}
                    </div>
                    {this.inputWrapper('id_profile', {wrapperCssClass: 'flex gap-2', uiStyle: 'buttons'})}                  
                    {this.inputWrapper('id_payment_method', {wrapperCssClass: 'flex gap-2', uiStyle: 'buttons'})}
                    {this.inputWrapper('id_currency', {wrapperCssClass: 'flex gap-2'})}
                    {this.inputWrapper('vs')}
                    {this.inputWrapper('cs')}
                    {this.inputWrapper('ss')}
                    {this.inputWrapper('notes')}
                  </>}
                </div>
              </div>
              <div className='gap-2 flex-1'>
                <div className='p-2 grow text-nowrap bg-slate-50 text-slate-800'>
                  <div className='text-sm'>
                    <b>{globalThis.hubleto.numberFormat(R.total_excl_vat, 2, ',', ' ')} {currencySymbol}</b>
                    <span className='ml-2'>{this.translate('excl. VAT')}</span>
                  </div>
                  <div className='mt-2'>
                    <span className='text-2xl badge badge-yellow'>
                      {globalThis.hubleto.numberFormat(R.total_incl_vat, 2, ',', ' ')} {currencySymbol}
                    </span>
                    <span className='text-sm ml-2'>{this.translate('incl. VAT')}</span>
                  </div>
                  <div className='text-sm mt-2'>
                    <b>{globalThis.hubleto.numberFormat(R.total_payments, 2, ',', ' ')} {currencySymbol}</b>
                    <span className='ml-2'>{this.translate('paid')}</span>
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
                <div className={'border-t border-t-4 border-t-violet-400 grow ' + (R.date_sent ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
                  {this.inputWrapper('date_sent', {wrapperCssClass: 'block'})}
                </div>
                <div className={'border-t border-t-4 border-t-green-600 grow ' + (R.date_payment ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
                  {this.inputWrapper('date_payment', {wrapperCssClass: 'block'})}
                </div>
                <div>
                  {this.inputWrapper('number_external', {wrapperCssClass: 'flex gap-2'})}
                  {this.inputWrapper('id_issued_by', {wrapperCssClass: 'flex gap-2'})}
                </div>
              </div>
            </div>
            {this.state.id <= 0 ? null : <div className='card mt-2'>
              <div className='card-header'>{this.translate('Items')}</div>
              <div className='card-body'>
                <div className='flex flex-col gap-2'>
                  {this.input('description_before', {cssClass: 'bg-blue-50 text-blue-500'})}
                  <table className='table-default dense not-striped'>
                    <thead>
                      <tr>
                        <td rowSpan={3} className='align-top'>#</td>
                        <th colSpan={3} style={{width: '50%'}}>{this.translate('Order')}</th>
                        <th colSpan={4}>{this.translate('Order item')}</th>
                        <td rowSpan={3}>&nbsp;</td>
                      </tr>
                      <tr>
                        <th colSpan={4}>{this.translate('Item')}</th>
                        <th>{this.translate('Unit price')}</th>
                        <th>{this.translate('Amount')}</th>
                      </tr>
                    </thead>
                    <tbody>
                      {R && R.ITEMS ? R.ITEMS.map((item, key) => {
                        const rowBgClass = (key % 2 == 0 ? 'bg-white' : 'bg-gray-50');

                        return <>
                          <tr key={key + '1'} className={item._toBeDeleted_ ? 'border border-red-400' : ''}>
                            <td rowSpan={3} className={'align-top ' + rowBgClass}>
                              <div className='badge'>{key + 1}</div>
                            </td>
                            <td colSpan={3} style={{width: '50%'}} className={rowBgClass}>
                              {InputFactory({
                                value: item.id_order,
                                cssClass: 'bg-white text-xs',
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
                            <td colSpan={4} className={rowBgClass}>
                              {item.id_order > 0 ?
                                InputFactory({
                                  value: item.id_order_item,
                                  cssClass: 'bg-white text-xs',
                                  description: {
                                    type: 'lookup',
                                    model: 'Hubleto/App/Community/Orders/Models/Item',
                                  },
                                  customEndpointParams: { idOrder: item.id_order },
                                  onChange: (input) => {

                                    request.post('orders/api/get-item',
                                      {idItem: input.state.value},
                                      {},
                                      (data: any) => {
                                        const P = data.item;
                                        R.ITEMS[key].id_order_item = input.state.value;
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
                                })
                              : null}
                            </td>
                            <td rowSpan={3} className={rowBgClass}>
                              <div className='flex gap-2'>
                                <button
                                  className='btn btn-warning'
                                  onClick={() => {
                                    request.post(
                                      'invoices/api/unlink-prepared-item',
                                      {
                                        idInvoice: R.id,
                                        idItem: item.id
                                      },
                                      {},
                                      (result: any) => {
                                        this.loadRecord();
                                      }
                                    );
                                  }}
                                >
                                  <span className='icon'><i className='fas fa-link-slash'></i></span>
                                </button>
                                <button
                                  className='btn btn-danger'
                                  onClick={() => {
                                    R.ITEMS[key]._toBeDeleted_ = true;
                                    this.updateRecord(R);
                                  }}
                                >
                                  <span className='icon'><i className='fas fa-trash'></i></span>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <tr key={key + '2'} className={item._toBeDeleted_ ? 'bg bg-red-50' : ''}>
                            <td className={rowBgClass} colSpan={4}>
                              {InputFactory({
                                value: item.item,
                                cssClass: 'bg-white text-blue-500',
                                description: { type: 'string' },
                                onChange: (e) => {
                                  R.ITEMS[key].item = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass + ' pr-4'}>
                              {InputFactory({
                                value: item.unit_price,
                                cssClass: 'bg-white text-blue-500',
                                description: { type: 'number', unit: currencySymbol + '/unit' },
                                onChange: (e) => {
                                  R.ITEMS[key].unit_price = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                            <td className={rowBgClass + ' pr-4'}>
                              {InputFactory({
                                value: item.amount,
                                cssClass: 'bg-white text-blue-500',
                                description: { type: 'number', unit: 'units' },
                                onChange: (e) => {
                                  R.ITEMS[key].amount = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </td>
                          </tr>
                          <tr key={key + '3'} className={item._toBeDeleted_ ? 'bg bg-red-50' : ''}>
                            <td className={rowBgClass}><div className='flex gap-2 items-center'>
                              {this.translate('Discount')}: {InputFactory({
                                value: item.discount,
                                cssClass: 'bg-white',
                                description: { type: 'number', unit: '%' },
                                onChange: (e) => {
                                  R.ITEMS[key].discount = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </div></td>
                            <td className={rowBgClass}><div className='flex gap-2 items-center'>
                              {this.translate('VAT')}: {InputFactory({
                                value: item.vat,
                                cssClass: 'bg-white',
                                description: { type: 'number', unit: '%' },
                                onChange: (e) => {
                                  R.ITEMS[key].vat = e.state.value;
                                  this.updateRecord(R);
                                }
                              })}
                            </div></td>
                            <td className={rowBgClass + ' text-right'} colSpan={4}>
                              <div className={'badge ' + (item.price_excl_vat < 0 ? 'badge-red' : 'badge-green')}>
                                {globalThis.hubleto.numberFormat(item.price_excl_vat, 2, ',', ' ')} {currencySymbol} {this.translate('excl. VAT')}
                              </div>
                              <div className={'badge ' + (item.price_excl_vat < 0 ? 'badge-red' : 'badge-green')}>
                                {globalThis.hubleto.numberFormat(item.price_incl_vat, 2, ',', ' ')} {currencySymbol} {this.translate('incl. VAT')}
                              </div>
                            </td>
                          </tr>
                        </>;
                      }) : null}
                    </tbody>
                  </table>
                  <div className='flex gap-2'>
                    <button
                      className='btn btn-add mt-2'
                      onClick={() => {
                        if (!R.ITEMS) R.ITEMS = [];
                        R.ITEMS.push({
                          id_invoice: this.state.id,
                          id_customer: R.id_customer,
                        });
                        this.updateRecord(R);
                      }}
                    >
                      <span className='icon'><i className='fas fa-plus'></i></span>
                      <span className='text'>{this.translate('Add new item')}</span>
                    </button>
                    <button
                      className='btn btn-add-outline mt-2'
                      onClick={() => {
                        this.setState({linkPreparedItem: true})
                      }}
                    >
                      <span className='icon'><i className='fas fa-link'></i></span>
                      <span className='text'>{this.translate('Link prepared item')}</span>
                    </button>
                  </div>
                  {this.input('description_after', {cssClass: 'bg-blue-50 text-blue-500'})}
                </div>
              </div>
            </div>}
            {this.state.linkPreparedItem ? <>
              <ModalSimple
                uid={this.props.uid + '_modal_table_link_not_invoiced_items'}
                isOpen={true}
                type='centered'
                showHeader={true}
                title={<>
                  <h2>{this.translate('Link prepared item')}</h2>
                </>}
                onClose={(modal: ModalSimple) => {
                  this.setState({linkPreparedItem: false});
                }}
              >
                <TableItems
                  uid={this.props.uid + "_table_link_not_invoiced_items"}
                  tag={"link_not_invoiced_items"}
                  parentForm={this}
                  idInvoice={0}
                  filters={{fStatus: 1}}
                  readonly={true}
                  onRowClick={(table: any, row: any) => {
                    request.post(
                      'invoices/api/link-prepared-item',
                      {
                        idInvoice: R.id,
                        idItem: row.id
                      },
                      {},
                      (result: any) => {
                        this.setState({linkPreparedItem: false}, () => {
                          this.loadRecord();
                        });
                      }
                    );
                  }}
                />
              </ModalSimple>
            </> : null}
          </>;
        }
      break;

      case 'payments':
        return <TablePayments
          uid={this.props.uid + "_table_invoice_payments"}
          tag={'table_invoice_payments'}
          parentForm={this}
          idInvoice={R.id}
        />;
      break;

      case 'email':
        if (!R.pdf) return <div className='alert alert-danger'>{this.translate('PDF version of the invoice was not generated yet. Cannot send.')}</div>;
        if (this.state.sendInvoiceResult) return <div className='alert alert-success'>{this.translate('Email was sent')}</div>;
        return <>
          <div className='btn-group'>
            <button
              className={'btn ' + (this.state.sendInvoiceEmailType == 'send-invoice' ? 'btn-primary': 'btn-transparent')}
              onClick={() => {
                this.setState({sendInvoiceEmailType: 'send-invoice'}, () => {
                  this.updateEmailPreview();
                });
              }}
            >
              <span className='text'>{this.translate('Send invoice')}</span>
            </button>
            <button
              className={'btn ' + (this.state.sendInvoiceEmailType == 'notify-due-invoice' ? 'btn-primary': 'btn-transparent')}
              onClick={() => {
                this.setState({sendInvoiceEmailType: 'notify-due-invoice'}, () => {
                  this.updateEmailPreview();
                });
              }}
            >
              <span className='text'>{this.translate('Send notification on due invoice')}</span>
            </button>
          </div>
          <div className='mt-2'>
            {!this.state.sendInvoicePreparedData
              ? <div className='alert alert-warning'>{this.translate('Preparing email...')}</div>
              : <>
                <table className='table-default dense'><tbody>
                  <tr>
                    <td>{this.translate('Subject')}:</td>
                    <td>
                      <input
                        className='w-full bg-white'
                        value={this.state.sendInvoicePreparedData.subject ?? ''}
                        onChange={(e: any) => {
                          this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, subject: e.currentTarget.value}});
                        }}
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>{this.translate('From')}:</td>
                    <td>{this.state.sendInvoicePreparedData.senderAccount?.name ?? <div className='text-red-800'>{this.translate('Not configured')}</div>}</td>
                  </tr>
                  <tr>
                    <td>{this.translate('To')}:</td>
                    <td className={this.state.sendInvoicePreparedData.to == '' ? 'bg-red-100' : ''}>
                      <input
                        className='w-full bg-white'
                        value={this.state.sendInvoicePreparedData.to ?? ''}
                        onChange={(e: any) => {
                          this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, to: e.currentTarget.value}});
                        }}
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>{this.translate('CC')}:</td>
                    <td>
                      <input
                        className='w-full bg-white'
                        value={this.state.sendInvoicePreparedData.cc ?? ''}
                        onChange={(e: any) => {
                          this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, cc: e.currentTarget.value}});
                        }}
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>{this.translate('BCC')}:</td>
                    <td>
                      <input
                        className='w-full bg-white'
                        value={this.state.sendInvoicePreparedData.bcc ?? ''}
                        onChange={(e: any) => {
                          this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, bcc: e.currentTarget.value}});
                        }}
                      />
                    </td>
                  </tr>
                  <tr>
                    <td>{this.translate('Email')}:</td>
                    <td>
                      <TextareaWithHtmlPreview
                        value={this.state.sendInvoicePreparedData.bodyHtml ?? ''}
                        onChange={(input: any) => {
                          this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, bodyHtml: input.state.value}});
                        }}
                      ></TextareaWithHtmlPreview>
                    </td>
                  </tr>
                  <tr>
                    <td>{this.translate('Attachments')}:</td>
                    <td>
                      {this.state.sendInvoicePreparedData.attachments ? this.state.sendInvoicePreparedData.attachments.map((att, index) => {
                        return <a
                          className='badge badge-info'
                          href={globalThis.hubleto.config.uploadUrl + "/" + att.file}
                          target="_blank"
                        >{att.name}</a>
                      }) : null}
                    </td>
                  </tr>
                </tbody></table>
                <button className='btn btn-add-outline mt-2'
                  onClick={() => {
                    request.post(
                      'invoices/api/send-invoice-in-email',
                      {
                        idInvoice: this.state.record.id,
                        idSenderAccount: this.state.sendInvoicePreparedData.senderAccount.id,
                        subject: this.state.sendInvoicePreparedData.subject,
                        bodyHtml: this.state.sendInvoicePreparedData.bodyHtml,
                        to: this.state.sendInvoicePreparedData.to,
                        cc: this.state.sendInvoicePreparedData.cc,
                        bcc: this.state.sendInvoicePreparedData.bcc,
                        ATTACHMENTS: this.state.sendInvoicePreparedData.ATTACHMENTS,
                      },
                      {},
                      (result: any) => {
                        this.setState({sendInvoiceResult: result})
                      }
                    );
                  }}
                >
                  <span className='icon'><i className='fas fa-paper-plane'></i></span>
                  <span className='text'>{this.translate('Send email')}</span>
                </button>
              </>
            }
          </div>
        </>;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}
