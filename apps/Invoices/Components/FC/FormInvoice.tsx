import React, { useState, useEffect } from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps, FormTabs } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import request from '@hubleto/react-ui/core/Request';
import TableItems from './TableItems';
import TablePayments from './TablePayments';
import TextareaWithHtmlPreview from "@hubleto/react-ui/components/fc/Inputs/TextareaWithHtmlPreview";
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Modal from '@hubleto/react-ui/components/fc/Modal';
import { ModalMeta } from '@hubleto/react-ui/components/fc/ModalInterfaces';
import VarcharInput from '@hubleto/react-ui/components/fc/Inputs/Varchar';
import IntInput from '@hubleto/react-ui/components/fc/Inputs/Int';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import FileInput from '@hubleto/react-ui/components/fc/Inputs/File';
import ModalHeader from '@hubleto/react-ui/components/fc/ModalComponents/Header';
import LoaderBar from '@hubleto/react-ui/components/fc/LoaderBar';

export interface FormInvoiceProps extends FormProps {}

const componentName = 'FormInvoice'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormInvoiceProps) => {
  const form = React.useContext(FormMetaContext);

  const [linkPreparedItem, setLinkPreparedItem] = useState(false);

  const idCustomer: number = useRecordField('id_customer', 0);
  const inboundOutbound: number = useRecordField('inbound_outbound', 0);
  const totalExclVat: number = useRecordField('total_excl_vat', 0);
  const totalInclVat: number = useRecordField('total_incl_vat', 0);
  const totalPayments: number = useRecordField('total_payments', 0);

  const dateDelivery: string = useRecordField('date_delivery', '');
  const dateIssue: string = useRecordField('date_issue', '');
  const dateDue: string = useRecordField('date_due', '');
  const dateSent: string = useRecordField('date_sent', '');
  const datePayment: string = useRecordField('date_payment', '');

  const ITEMS: any = useRecordField('ITEMS', []);
  const CURRENCY: any = useRecordField('CURRENCY', {});
  const currencySymbol = CURRENCY.symbol ?? '';

  if (form.id <= 0) {
    return <>
      {inboundOutbound == 1
        ? <Input field='id_supplier' />
        : <Input field='id_customer' />
      }
      <Input field='number' customInputProps={{cssClass: 'text-4xl'}} />
    </>
  } else {
    return <>
      <div className='flex flex-col md:flex-row gap-2'>
        <div className='grow'>
          {inboundOutbound == 1 ?
            <Input field='id_supplier' customInputProps={{wrapperCssClass: 'flex gap-2'}} />
          : <Input field='id_customer' customInputProps={{wrapperCssClass: 'flex gap-2'}} />}
        </div>
      </div>
      <div className='flex flex-col md:flex-row gap-2'>
        <div className="flex flex-5 gap-2">
          <div className='flex-1 min-w-80'>
            {form.id == -1 ? null : <div className='flex-dyn'>
              <div className='grow'>
                <Input field='number' customInputProps={{wrapperCssClass: 'block', cssClass: 'text-xl'}} />
                <Input field='vs' />
                <Input field='cs' />
                <Input field='ss' />
              </div>
              <div className='grow'>
                <Input field='notes'  customInputProps={{cssClass: 'border border-orange-200'}} />
              </div>
            </div>}


            {form.id <= 0 ? null : <div className='card mt-2'>
              <div className='card-header'>{T.translate('Items')}</div>
              <div className='card-body'>
                <div className='flex flex-col gap-2'>
                  <Input field='description_before' rendeOnlyInputField customInputProps={{cssClass: 'bg-blue-50 text-blue-500'}} />
                  {ITEMS.map((item, key) => {
                    const rowBgClass = (key % 2 == 0 ? 'bg-white' : 'bg-gray-50');

                    return <div key={key} className={'card border ' + (item._toBeDeleted_ ? 'border-red-400' : 'border-blue-400')}>
                        <div className={'card-header ' + rowBgClass}>
                          <div className='badge text-xl'>{key + 1}</div>
                          <VarcharInput
                            value={item.item}
                            cssClass='bg-white text-blue-500'
                            onChange={(input: any, value: any) => {
                              let newItems = ITEMS;
                              newItems[key].item = value;
                              form.changeRecord({ITEMS: newItems});
                            }}
                          ></VarcharInput>
                          <div className={'text-nowrap badge ' + (item.price_excl_vat < 0 ? 'badge-red' : 'badge-green')}>
                            {globalThis.hubleto.numberFormat(item.price_excl_vat, 2, ',', ' ')} {currencySymbol} {T.translate('excl. VAT')}
                          </div>
                          <div className={'text-nowrap badge ' + (item.price_excl_vat < 0 ? 'badge-red' : 'badge-green')}>
                            {globalThis.hubleto.numberFormat(item.price_incl_vat, 2, ',', ' ')} {currencySymbol} {T.translate('incl. VAT')}
                          </div>
                          <button
                            className='btn btn-warning'
                            onClick={() => {
                              request.post(
                                'invoices/api/unlink-prepared-item',
                                {
                                  idInvoice: form.id,
                                  idItem: item.id
                                },
                                {},
                                (result: any) => {
                                  form.reload();
                                }
                              );
                            }}
                          >
                            <span className='icon'><i className='fas fa-link-slash'></i></span>
                          </button>
                          <button
                            className='btn btn-danger'
                            onClick={() => {
                              let newItems = ITEMS;
                              newItems[key]._toBeDeleted_ = true;
                              form.changeRecord({ITEMS: newItems});
                            }}
                          >
                            <span className='icon'><i className='fas fa-trash'></i></span>
                          </button>
                        </div>
                        <div className='card-body flex flex-col gap-2'>
                          <div className='flex gap-2 items-center text-nowrap'>
                            {T.translate('Unit price')}:
                            <IntInput
                              value={item.unit_price}
                              cssClass='bg-white text-blue-500 w-auto'
                              description={{unit: currencySymbol + '/unit'}}
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].unit_price = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></IntInput>
                          </div>
                          <div className='flex gap-2 items-center'>
                            {T.translate('Amount')}:
                            <IntInput
                              value={item.amount}
                              cssClass='bg-white text-blue-500 w-auto'
                              description={{unit: 'units'}}
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].amount = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></IntInput>
                          </div>
                          <div className='flex gap-2 items-center'>
                            {T.translate('Order')}:
                            <LookupInput
                              value={item.id_order}
                              cssClass='bg-white w-auto'
                              description={{
                                model: 'Hubleto/App/Community/Orders/Models/Order'
                              }}
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].id_order = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></LookupInput>
                            {item.id_order > 0 ?
                              <LookupInput
                                value={item.id_order_item}
                                cssClass='bg-white w-auto'
                                description={{
                                  model: 'Hubleto/App/Community/Orders/Models/Item'
                                }}
                                customEndpointParams={{idOrder: item.id_order}}
                                onChange={(input: any, value: any) => {
                                  request.post('orders/api/get-item',
                                    {idItem: input.value},
                                    {},
                                    (data: any) => {
                                      const P = data.item;
                                      let newItems = ITEMS;
                                      newItems[key].id_order_item = value;
                                      newItems[key].item = P?.title ?? '';
                                      newItems[key].unit_price = P?.sales_price ?? 0;
                                      newItems[key].amount = P?.amount ?? 0;
                                      newItems[key].price_excl_vat = P?.price_excl_vat ?? 0;
                                      newItems[key].price_incl_vat = P?.price_incl_vat ?? 0;
                                      newItems[key].vat = P?.vat ?? 0;
                                      newItems[key].discount = P?.discount ?? 0;
                                      form.changeRecord({ITEMS: newItems});
                                    }
                                  )
                                }}
                              ></LookupInput>
                            : null}
                          </div>
                          <div className='flex gap-2 items-center'>
                            {T.translate('Discount')}:
                            <IntInput
                              value={item.discount}
                              cssClass='bg-white w-auto'
                              description={{unit: '%'}}
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].discount = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></IntInput>
                          </div>
                          <div className='flex gap-2 items-center'>
                            {T.translate('VAT')}:
                            <IntInput
                              value={item.vat}
                              cssClass='bg-white w-auto'
                              description={{unit: '%'}}
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].vat = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></IntInput>
                          </div>
                          <div className='flex gap-2 items-center'>
                            {T.translate('Attachments')}:
                            <FileInput
                              value={item.attachment_1}
                              cssClass='bg-white w-auto'
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].attachment_1 = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></FileInput>
                            <FileInput
                              value={item.attachment_2}
                              cssClass='bg-white w-auto'
                              onChange={(input: any, value: any) => {
                                let newItems = ITEMS;
                                newItems[key].attachment_2 = value;
                                form.changeRecord({ITEMS: newItems});
                              }}
                            ></FileInput>
                          </div>
                        </div>
                    </div>;
                  })}
                  <div className='flex gap-2'>
                    <button
                      className='btn btn-add mt-2'
                      onClick={() => {
                        form.changeRecord({ITEMS: [...ITEMS, {
                          id_invoice: form.id,
                          id_customer: idCustomer,
                        }]});
                      }}
                    >
                      <span className='icon'><i className='fas fa-plus'></i></span>
                      <span className='text'>{T.translate('Add new item')}</span>
                    </button>
                    <button
                      className='btn btn-add-outline mt-2'
                      onClick={() => {
                        setLinkPreparedItem(true);
                      }}
                    >
                      <span className='icon'><i className='fas fa-link'></i></span>
                      <span className='text'>{T.translate('Link prepared item')}</span>
                    </button>
                  </div>
                  <Input field='description_after' renderOnlyInputField customInputProps={{cssClass: 'bg-blue-50 text-blue-500'}} />
                </div>
              </div>
            </div>}



          </div>
        </div>
        <div className='gap-2 flex-1'>
          <div className='p-2 grow text-nowrap bg-slate-50 text-slate-800'>
            <div className='text-sm'>
              <b>{globalThis.hubleto.numberFormat(totalExclVat, 2, ',', ' ')} {currencySymbol}</b>
              <span className='ml-2'>{T.translate('excl. VAT')}</span>
            </div>
            <div className='mt-2'>
              <span className='text-2xl badge badge-yellow'>
                {globalThis.hubleto.numberFormat(totalInclVat, 2, ',', ' ')} {currencySymbol}
              </span>
              <span className='text-sm ml-2'>{T.translate('incl. VAT')}</span>
            </div>
            <div className='text-sm mt-2'>
              <b>{globalThis.hubleto.numberFormat(totalPayments, 2, ',', ' ')} {currencySymbol}</b>
              <span className='ml-2'>{T.translate('paid')}</span>
            </div>
          </div>
          <div className={'border-t border-t-4 border-t-blue-400 grow ' + (dateDelivery ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
            <Input field='date_delivery' customInputProps={{wrapperCssClass: 'block'}} />
          </div>
          <div className={'border-t border-t-4 border-t-orange-300 grow ' + (dateIssue ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
            <Input field='date_issue' customInputProps={{wrapperCssClass: 'block'}} />
          </div>
          <div className={'border-t border-t-4 border-t-green-400 grow ' + (dateDue ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
            <Input field='date_due' customInputProps={{wrapperCssClass: 'block'}} />
          </div>
          <div className={'border-t border-t-4 border-t-violet-400 grow ' + (dateSent ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
            <Input field='date_sent' customInputProps={{wrapperCssClass: 'block'}} />
          </div>
          <div className={'border-t border-t-4 border-t-green-600 grow ' + (datePayment ? '' : 'bg-gradient-to-b from-red-50 to-white')}>
            <Input field='date_payment' customInputProps={{wrapperCssClass: 'block'}} />
          </div>
          <div>
            <Input field='number_external' customInputProps={{wrapperCssClass: 'flex gap-2'}} />
            <Input field='id_issued_by' customInputProps={{wrapperCssClass: 'flex gap-2'}} />
          </div>
        </div>
      </div>
      {linkPreparedItem ? <>
        <Modal
          uid={props.uid + '_modal_link_prepared_item'}
          isOpen={true}
          type='centered'
          showHeader={true}
          title={<>
            <h2>{T.translate('Link prepared item')}</h2>
          </>}
          onClose={(modal: ModalMeta) => {
            setLinkPreparedItem(false);
          }}
        >
          <ModalHeader></ModalHeader>
          <TableItems
            uid={props.uid + "_table_link_not_invoiced_items"}
            tag={"link_not_invoiced_items"}
            parentForm={form}
            idInvoice={0}
            filters={{fStatus: 1}}
            readonly={true}
            onRowClick={(table: any, row: any) => {
              request.post(
                'invoices/api/link-prepared-item',
                {
                  idInvoice: form.id,
                  idItem: row.id
                },
                {},
                (result: any) => {
                  setLinkPreparedItem(false);
                  form.reload();
                }
              );
            }}
          />
        </Modal>
      </> : null}
    </>;
  }
}

/** TabPayments */
const TabPayments = (props: FormInvoiceProps) => {
  const form = React.useContext(FormMetaContext);
  return <TablePayments
    uid={props.uid + "_table_invoice_payments"}
    tag={'table_invoice_payments'}
    parentForm={form}
    idInvoice={form.id}
  />;
}

/** TabEmail */
const TabEmail = (props: FormInvoiceProps) => {
  const form = React.useContext(FormMetaContext);

  const [sendInvoiceEmailType, setSendInvoiceEmailType] = useState('send-invoice');
  const [sendInvoicePreparedData, setSendInvoicePreparedData] = useState(null);
  const [sendInvoiceResult, setSendInvoiceResult] = useState(null);
  const [htmlPreview, setHtmlPreview] = useState('');

  const pdf: string = useRecordField('pdf', '');

  if (!pdf) return <div className='alert alert-danger'>{T.translate('PDF version of the invoice was not generated yet. Cannot send.')}</div>;
  if (sendInvoiceResult) return <div className='alert alert-success'>{T.translate('Email was sent')}</div>;

  const updateEmailPreview = () => {
    console.log('updateEmailPreview', sendInvoiceEmailType);
    setSendInvoicePreparedData(null);
    setSendInvoiceResult(null);

    request.post(
      'invoices/api/send-invoice-in-email',
      {
        idInvoice: form.id,
        emailType: sendInvoiceEmailType,
        prepare: true
      },
      {},
      (result: any) => {
        setSendInvoicePreparedData(result);
      }
    );
  }

  useEffect(() => { updateEmailPreview(); }, [sendInvoiceEmailType]);

  return <>
    <div className='btn-group'>
      <button
        className={'btn ' + (sendInvoiceEmailType == 'send-invoice' ? 'btn-primary': 'btn-transparent')}
        onClick={() => {
          setSendInvoiceEmailType('send-invoice');
        }}
      >
        <span className='text'>{T.translate('Send invoice')}</span>
      </button>
      <button
        className={'btn ' + (sendInvoiceEmailType == 'notify-due-invoice' ? 'btn-primary': 'btn-transparent')}
        onClick={() => {
          setSendInvoiceEmailType('notify-due-invoice');
        }}
      >
        <span className='text'>{T.translate('Send notification on due invoice')}</span>
      </button>
    </div>
    <div className='mt-2'>
      {!sendInvoicePreparedData
        ? <div className='alert alert-warning'>
          {T.translate('Preparing email...')}
          <LoaderBar></LoaderBar>
        </div>
        : <>
          <table className='table-default dense'><tbody>
            <tr>
              <td>{T.translate('Subject')}:</td>
              <td>
                <input
                  className='w-full bg-white'
                  value={sendInvoicePreparedData.subject ?? ''}
                  onChange={(e: any) => {
                    setSendInvoicePreparedData({
                      ...sendInvoicePreparedData,
                      subject: e.currentTarget.value
                    });
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>{T.translate('From')}:</td>
              <td>{sendInvoicePreparedData.senderAccount?.name ?? <div className='text-red-800'>{T.translate('Not configured')}</div>}</td>
            </tr>
            <tr>
              <td>{T.translate('To')}:</td>
              <td className={sendInvoicePreparedData.to == '' ? 'bg-red-100' : ''}>
                <input
                  className='w-full bg-white'
                  value={sendInvoicePreparedData.to ?? ''}
                  onChange={(e: any) => {
                    setSendInvoicePreparedData({
                      ...sendInvoicePreparedData,
                      to: e.currentTarget.value
                    });
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>{T.translate('CC')}:</td>
              <td>
                <input
                  className='w-full bg-white'
                  value={sendInvoicePreparedData.cc ?? ''}
                  onChange={(e: any) => {
                    setSendInvoicePreparedData({
                      ...sendInvoicePreparedData,
                      cc: e.currentTarget.value
                    });
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>{T.translate('BCC')}:</td>
              <td>
                <input
                  className='w-full bg-white'
                  value={sendInvoicePreparedData.bcc ?? ''}
                  onChange={(e: any) => {
                    setSendInvoicePreparedData({
                      ...sendInvoicePreparedData,
                      bcc: e.currentTarget.value
                    });
                  }}
                />
              </td>
            </tr>
            <tr>
              <td>{T.translate('Email')}:</td>
              <td>
                <TextareaWithHtmlPreview
                  value={sendInvoicePreparedData.bodyHtml ?? ''}
                  onChange={(input: any) => {
                    setSendInvoicePreparedData({
                      ...sendInvoicePreparedData,
                      subject: input.value
                    });
                  }}
                ></TextareaWithHtmlPreview>
              </td>
            </tr>
            <tr>
              <td>{T.translate('Attachments')}:</td>
              <td>
                {sendInvoicePreparedData.attachments ? sendInvoicePreparedData.attachments.map((att, index) => {
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
                  idInvoice: form.id,
                  idSenderAccount: sendInvoicePreparedData.senderAccount.id,
                  subject: sendInvoicePreparedData.subject,
                  bodyHtml: sendInvoicePreparedData.bodyHtml,
                  to: sendInvoicePreparedData.to,
                  cc: sendInvoicePreparedData.cc,
                  bcc: sendInvoicePreparedData.bcc,
                  ATTACHMENTS: sendInvoicePreparedData.ATTACHMENTS,
                },
                {},
                (result: any) => {
                  setSendInvoiceResult(result);
                }
              );
            }}
          >
            <span className='icon'><i className='fas fa-paper-plane'></i></span>
            <span className='text'>{T.translate('Send email')}</span>
          </button>
        </>
      }
    </div>
  </>;
}

/** FormInvoice */
const FormInvoice = (props: FormInvoiceProps) => {

  let tabs: FormTabs = {
    default: { title: <b>{T.translate('Invoice')}</b>, content: () => <TabDefault {...props} /> }
  }

  if (props.id > 0) {
    tabs.payments = { title: T.translate('Payments'), content: () => <TabPayments {...props} /> };
    tabs.email = { title: T.translate('Email'), content: () => <TabEmail {...props} /> };
  }

  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Invoice'}
    urlSlug='invoices'
    endpointParams={{saveRelations: ['ITEMS']}}
    // onAfterFormInitialized={(form: any) => {}}
    renderTitle={(): React.JSX.Element => {
        const type = useRecordField('type', 0);
        const number = useRecordField('number', '');
        const inboundOutbound = useRecordField('inbound_outbound', 0);
        let title = (inboundOutbound == 1 ? T.translate('Inbound') : T.translate('Outbound'));

        switch (type) {
          case 1: case '1': title += ' ' + T.translate('Proforma Invoice'); break;
          case 2: case '2': title += ' ' + T.translate('Advance Invoice'); break;
          case 3: case '3': title += ' ' + T.translate('Invoice'); break;
          case 4: case '4': title += ' ' + T.translate('Credit Note'); break;
          case 5: case '5': title += ' ' + T.translate('Debit Note'); break;
        }

        return <div>
          <h2>{number}</h2>
          <small>{title}</small>
        </div>;
      }
    }
    renderTopInputs={() => {
      return <div className='modal-top-inputs'>
        <Input field='inbound_outbound' renderOnlyInputField customInputProps={{ cssClass: 'w-auto', uiStyle: 'buttons' }} />
        <Input field='type' renderOnlyInputField customInputProps={{ cssClass: 'w-auto', uiStyle: 'buttons' }} />
        <Input field='id_profile' renderOnlyInputField customInputProps={{wrapperCssClass: 'flex gap-2', uiStyle: 'buttons'}} />
        <Input field='id_payment_method' renderOnlyInputField customInputProps={{wrapperCssClass: 'flex gap-2', uiStyle: 'buttons'}} />
        <Input field='id_currency' renderOnlyInputField customInputProps={{wrapperCssClass: 'flex gap-2'}} />
      </div>;
    }}
    // title={{field: 'some-field-of-the-record', sub: T.translate(componentName)}}
    tabs={tabs}
    {...props}
  ></Form>;
}

export default FormInvoice;
