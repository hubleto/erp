import React, { Component } from 'react'
import HubletoForm, {HubletoFormProps, HubletoFormState} from "@hubleto/react-ui/ext/HubletoForm";
// import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import HtmlFrame from "@hubleto/react-ui/core/HtmlFrame";
import { InputFactory } from "@hubleto/react-ui/core/InputFactory";
import request from '@hubleto/react-ui/core/Request';
import TableItems from './TableItems';
import TablePayments from './TablePayments';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";

interface FormInvoiceProps extends HubletoFormProps {
}

interface FormInvoiceState extends HubletoFormState {
  linkPreparedItem: boolean;
  sendInvoicePreparedData?: any,
  sendInvoiceResult?: any,
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

  refPreview: any;

  constructor(props: FormInvoiceProps) {
    super(props);

    this.refPreview = React.createRef();
  }

  getStateFromProps(props: FormInvoiceProps) {
    let tabs = [];
    
    if (this.props.id > 0) {
      tabs.push({ uid: 'default', title: <b>{this.translate('Invoice')}</b> });
      tabs.push({ uid: 'preview', title: this.translate('Preview, download, print') });
      // tabs.push({ uid: 'documents', title: this.translate('Documents') });
      tabs.push({ uid: 'payments', title: this.translate('Payments') });
      tabs.push({ uid: 'send', title: this.translate('Send') });
    }
    tabs = [...tabs, ...this.getCustomTabs()];

    return {
      ...super.getStateFromProps(props),
      tabs: tabs,
      linkPreparedItem: false,
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['ITEMS'],
    }
  }

  // getHeaderButtons()
  // {
  //   let buttons = super.getHeaderButtons();
  //   if (this.state.id > 0) {
  //     buttons.push(
  //       {
  //         title: 'Print to PDF',
  //         onClick: () => {
  //           request.post(
  //             'invoices/api/generate-pdf',
  //             {idInvoice: this.state.record.id},
  //             {},
  //             (result: any) => {
  //               if (result.idDocument) {
  //                 window.open(globalThis.hubleto.config.projectUrl + '/documents/' + result.idDocument);
  //               }
  //             }
  //           );
  //         }
  //       }
  //     );
  //   }
  //   return buttons;
  // }

  getRecordFormUrl(): string {
    return 'invoices/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  onTabChange() {
    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'preview':
        this.updatePreview(this.state.record.id_template);
      break;
      case 'send':
        this.setState({sendInvoicePreparedData: null, sendInvoiceResult: null});
        request.post(
          'invoices/api/send-invoice-in-email',
          {
            idInvoice: this.state.record.id,
            prepare: true
          },
          {},
          (result: any) => {
            this.setState({sendInvoicePreparedData: result})
          }
        );
      break;
    }
  }

  updatePreview(idTemplate: number) {
    request.post(
      'invoices/api/get-preview-html',
      {
        idInvoice: this.state.record.id,
        idTemplate: idTemplate,
      },
      {},
      (result: any) => {
        this.refPreview.current.setState({content: result.html});
      }
    );
  }

  showPreviewVars() {
    request.post(
      'invoices/api/get-preview-vars',
      {
        idInvoice: this.state.record.id,
        idTemplate: this.state.record.id_template,
      },
      {},
      (vars: any) => {
        this.refPreview.current.setState({content: '<pre>' + JSON.stringify(vars.vars, null, 2) + '</pre>'});
      }
    );
  }

  renderTitle(): JSX.Element {
    const r = this.state.record;
    return <>
      <small>{this.state.record.inbound_outbound == 1 ? 'Inbound Invoice' : 'Outbound Invoice'}</small>
      <h2>{r.number ? r.number : '---'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        const currencySymbol = R && R.CURRENCY ? R.CURRENCY.symbol : '';
        return <>
          <div className='flex gap-2'>
            {this.input('inbound_outbound', { cssClass: 'w-auto', uiStyle: 'buttons' })}
            {this.input('type', { cssClass: 'w-auto', uiStyle: 'buttons' })}
            <div className='grow'>
              {R.inbound_outbound == 1 ?
                this.inputWrapper('id_supplier', {wrapperCssClass: 'flex gap-2'})
              : this.inputWrapper('id_customer', {wrapperCssClass: 'flex gap-2'})}
            </div>
          </div>
          <div className='flex gap-2 mt-2'>
            <div className='gap-2 w-56'>
              <div className='p-2 grow'>
                {this.inputWrapper('number', {wrapperCssClass: 'block', cssClass: 'text-4xl'})}
              </div>
              <div className='p-2 grow text-nowrap bg-slate-100 text-slate-800'>
                <div>
                  <div className='text-4xl'>
                    <b>{globalThis.hubleto.numberFormat(R.total_excl_vat, 2, ',', ' ')} {currencySymbol}</b>
                  </div>
                  <div className='text-sm'>excl. VAT</div>
                </div>
                <div className='mt-2'>
                  <div className='text-4xl'>
                    {globalThis.hubleto.numberFormat(R.total_incl_vat, 2, ',', ' ')} {currencySymbol}
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
              <div className={'border-t border-t-4 border-t-violet-400 grow ' + (R.date_sent ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
                {this.inputWrapper('date_sent', {wrapperCssClass: 'block'})}
              </div>
              <div className={'border-t border-t-4 border-t-green-600 grow ' + (R.date_payment ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
                {this.inputWrapper('date_payment', {wrapperCssClass: 'block'})}
              </div>
            </div>
            <div className="flex flex-5 gap-2 mt-2">
              <div className='flex-1 min-w-80'>
                {this.state.id == -1 ? null : <>
                  {this.inputWrapper('id_profile', {wrapperCssClass: 'flex gap-2', uiStyle: 'buttons'})}                  
                  {this.inputWrapper('id_payment_method', {wrapperCssClass: 'flex gap-2', uiStyle: 'buttons'})}
                  {this.inputWrapper('id_currency', {wrapperCssClass: 'flex gap-2'})}
                  {this.inputWrapper('vs')}
                  {this.inputWrapper('cs')}
                  {this.inputWrapper('ss')}
                  {this.inputWrapper('notes')}
                  {this.inputWrapper('number_external', {wrapperCssClass: 'flex gap-2'})}
                  {this.inputWrapper('id_issued_by', {wrapperCssClass: 'flex gap-2'})}
                </>}
              </div>
              {this.state.id <= 0 ? null : <div>
                {this.inputWrapper('description')}
                <div className='card flex-2'>
                  <div className='card-header'>{this.translate('Items')}</div>
                  <div className='card-body'>
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
                              <td colSpan={4} className={rowBgClass}>
                                {InputFactory({
                                  value: item.id_order_item,
                                  cssClass: 'bg-white min-w-64',
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
                                })}
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
                                  cssClass: 'bg-white',
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
                                  cssClass: 'bg-white',
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
                                  cssClass: 'bg-white',
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
                                Discount: {InputFactory({
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
                                VAT: {InputFactory({
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
                                <div>
                                  {globalThis.hubleto.numberFormat(item.price_excl_vat, 2, ',', ' ')} {currencySymbol} excl. VAT
                                </div>
                                <div>
                                  {globalThis.hubleto.numberFormat(item.price_incl_vat, 2, ',', ' ')} {currencySymbol} incl. VAT
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
                  </div>
                </div>
              </div>}
            </div>
          </div>
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
      break;

      case 'preview':
        return <div className='flex gap-2 h-full'>
          <div className='flex-1 w-72 flex flex-col gap-2'>
            <div className='grow'>
              {this.inputWrapper('id_template', {
                uiStyle: 'buttons-vertical',
                onChange: (input: any) => {
                  this.updatePreview(input.state.value);
                }
              })}
            </div>
          </div>
          <div className='flex-3 flex flex-col'>
            <div className='flex gap-2 align-center justify-end'>
              <div>
                {this.input('pdf', {readonly: true})}
              </div>
              <div className='flex gap-2'>
                <button
                  className='btn btn-transparent mb-4'
                  onClick={() => {
                    request.post(
                      'invoices/api/generate-pdf',
                      {idInvoice: this.state.record.id},
                      {},
                      (result: any) => {
                        // if (result.idDocument) {
                        //   window.open(globalThis.hubleto.config.projectUrl + '/documents/' + result.idDocument);
                        // }
                        // this.reload();
                        if (result && result.pdfFile) {
                          this.updateRecord({pdf: result.pdfFile});
                        }
                      }
                    );
                  }}
                >
                  <span className='icon'><i className='fas fa-download'></i></span>
                  <span className='text'>{this.translate('Export to PDF')}</span>
                </button>
                <button
                  className='btn btn-transparent mb-4'
                  onClick={() => {
                    const iframe = window.frames[this.props.uid + '_invoice_preview'];
                    const documentTitle = document.title;
                    document.title = 'Invoice ' + R.number + ' ' + R.CUSTOMER.name;
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();

                    document.title = documentTitle;
                  }}
                >
                  <span className='icon'><i className='fas fa-print'></i></span>
                  <span className='text'>{this.translate('Print')}</span>
                </button>
              </div>
            </div>
            <div className='w-full h-full card mt-2'>
              <div className="card-body">
                <HtmlFrame
                  ref={this.refPreview}
                  className='w-full h-full'
                  iframeId={this.props.uid + '_invoice_preview'}
                />
              </div>
              <div className='card-footer'>
                <a
                  href='#'
                  onClick={() => {
                    this.showPreviewVars();
                  }}
                >{this.translate('Show variables which can be used in template')}</a>
              </div>
            </div>
          </div>
        </div>;
      break;

      // case 'documents':
      //   return <>
      //     <TableDocuments
      //       uid={this.props.uid + "_table_invoice_documents"}
      //       tag={'table_invoice_documents'}
      //       parentForm={this}
      //       junctionModel='Hubleto\App\Community\Invoices\Models\InvoiceDocument'
      //       junctionSourceColumn='id_invoice'
      //       junctionDestinationColumn='id_document'
      //       junctionSourceRecordId={R.id}
      //     />
      //   </>
      // break;

      case 'payments':
        return <TablePayments
          uid={this.props.uid + "_table_invoice_payments"}
          tag={'table_invoice_payments'}
          parentForm={this}
          idInvoice={R.id}
        />;
      break;

      case 'send':
        if (!this.state.sendInvoicePreparedData) return <div className='alert'>Preparing email...</div>;
        if (this.state.sendInvoiceResult) return <div className='alert alert-success'>{JSON.stringify(this.state.sendInvoiceResult)}</div>;
        return <>

          <table className='table-default dense'><tbody>
            <tr>
              <td>Subject:</td>
              <td>
                <input
                  className='w-full'
                  value={this.state.sendInvoicePreparedData.subject ?? ''}
                  onChange={(e: any) => {
                    this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, subject: e.currentTarget.value}});
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>From:</td>
              <td>{this.state.sendInvoicePreparedData.senderAccount.name}</td>
            </tr>
            <tr>
              <td>To:</td>
              <td>
                <input
                  className='w-full'
                  value={this.state.sendInvoicePreparedData.to ?? ''}
                  onChange={(e: any) => {
                    this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, to: e.currentTarget.value}});
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>CC:</td>
              <td>
                <input
                  className='w-full'
                  value={this.state.sendInvoicePreparedData.cc ?? ''}
                  onChange={(e: any) => {
                    this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, cc: e.currentTarget.value}});
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>BCC:</td>
              <td>
                <input
                  className='w-full'
                  value={this.state.sendInvoicePreparedData.bcc ?? ''}
                  onChange={(e: any) => {
                    this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, bcc: e.currentTarget.value}});
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>Email:</td>
              <td>
                <textarea
                  className='w-full'
                  onChange={(e: any) => {
                    this.setState({sendInvoicePreparedData: {...this.state.sendInvoicePreparedData, bodyHtml: e.currentTarget.value}});
                  }}
                >{this.state.sendInvoicePreparedData.bodyHtml ?? ''}</textarea>
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
                },
                {},
                (result: any) => {
                  this.setState({sendInvoiceResult: result})
                }
              );
            }}
          >
            <span className='text'>Send invoice</span>
          </button>
        </>;
      break;

    }
  }
}
