import React, { Component, ChangeEvent } from "react";
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import Barcode from 'react-barcode';

export interface FormReceiptProps extends FormExtendedProps { }
export interface FormReceiptState extends FormExtendedState { }

export default class FormReceipt<P, S> extends FormExtended<FormReceiptProps, FormReceiptState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: "Hubleto/App/Community/Cashdesk/Models/Receipt"
  };

  props: FormReceiptProps;
  state: FormReceiptState;

  refLogActivityInput: any;

  translationContext: string = 'Hubleto\\App\\Community\\Cashdesk\\Loader';
  translationContextInner: string = 'Components\\FormReceipt';

  constructor(props: FormReceiptProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
    }
  }

  getStateFromProps(props: FormReceiptProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Receipt')}</b> },
      ],
    };
  }

  getRecordFormUrl(): string {
    return 'cashdesk/receipts/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['ITEMS'],
    }
  }

  updateItem(index: number, newItem: any) {
    let newRecord = this.state.record;
    if (!newRecord.ITEMS[index]) newRecord.ITEMS[index] = {};
    newRecord.ITEMS[index].id_receipt = { _useMasterRecordId_: true };
    newRecord.ITEMS[index] = {...newRecord.ITEMS[index], ...newItem};
    this.updateRecord(newRecord);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Receipt')}</small>
      <h2>{this.state.record.number ? this.state.record.number : ''}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='w-full flex-3'>
            {this.inputWrapper('id_company')}
            {this.inputWrapper('number')}
            {this.inputWrapper('id_cash_register')}
            {this.inputWrapper('created')}
            {this.inputWrapper('sent_to_cash_register')}
            <table className='table-default dense mt-2'>
              <thead>
                <tr>
                  <th>{this.translate('Product')}</th>
                  <th>Qty.</th>
                  <th>Unit price incl. VAT (€)</th>
                  <th>VAT (%)</th>
                  <th>Total price incl. VAT (€)</th>
                </tr>
              </thead>
              <tbody>
                {R.ITEMS ? R.ITEMS.map((item, index) => {
                  return <>
                    <tr>
                      <td>
                        <Lookup
                          model='Hubleto/App/Community/Products/Models/Product'
                          value={item.id_product}
                          cssClass='font-bold'
                          onChange={(input: any, value: any) => { this.updateItem(index, {id_product: value}); }}
                        ></Lookup>
                      </td>
                      <td>
                        <Int
                          value={item.quantity}
                          description={{decimals: 4, unit: 'x'}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {quantity: value}); }}
                        ></Int>
                      </td>
                      <td>
                        <Int
                          value={item.vat_percent}
                          description={{decimals: 4, unit: '%'}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {vat_percent: value}); }}
                        ></Int>
                      </td>
                      <td>
                        <Int
                          value={item.unit_price_excl_vat}
                          description={{decimals: 4, unit: '€'}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {unit_price_incl_vat: value}); }}
                        ></Int>
                      </td>
                      <td>
                        <Int
                          value={item.total_price_incl_vat}
                          description={{decimals: 4, unit: '€'}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {total_price_incl_vat: value}); }}
                        ></Int>
                      </td>
                    </tr>
                  </>
                }) : null}
              </tbody>
            </table>
            <button
              className='btn btn-add mt-2'
              onClick={() => {
                let newR = R
                if (!newR.ITEMS) newR.ITEMS = [];
                newR.ITEMS.push({});
                this.updateRecord(newR);
              }}
            >
              <span className='icon'><i className='fas fa-plus'></i></span>
              <span className='text'>{this.translate('Add item')}</span>
            </button>

            <div className='mt-8 text-center'>
              <button className='btn btn-add-outline btn-extra-large'>
                <span className='icon'><i className='fas fa-arrow-right'></i></span>
                <span className='text'>{this.translate('Send to cash register')}</span>
              </button>
            </div>
          </div>
          <div className='w-full flex-1'>
            <div className='m-4 shadow p-2 rounded-lg border border-gray-200'>
              <div className='border-dashed border-b p-2 text-center'>
                <b>{R.COMPANY.name}</b><br/>
                TAX ID: {R.COMPANY.tax_id}<br/>
                VAT ID: {R.COMPANY.vat_id}<br/>
                {R.COMPANY.street_1}<br/>
                {R.COMPANY.street_2}<br/>
                {R.COMPANY.zip} {R.COMPANY.city}<br/>
                {R.COMPANY.country}
              </div>
              <div className='text-center font-bold border-dashed border-b p-2'>
                RECEIPT #{R.number}
              </div>
              <div className='border-dashed border-b p-2'>
                {R.ITEMS ? R.ITEMS.map((item, index) => {
                  return <div className='w-full my-2'>
                    <div className='text-blue-400'>{item.PRODUCT.ean ?? '-'}</div>
                    <div className='font-bold'>{item.PRODUCT.name}</div>
                    <div className='flex gap justify-between w-full'>
                     <div>{globalThis.hubleto.numberFormat(item.quantity, 2, ",", " ")}x</div>
                     <div>{globalThis.hubleto.numberFormat(item.unit_price_incl_vat, 2, ",", " ")} €</div>
                     <div>{globalThis.hubleto.numberFormat(item.vat_percent, 2, ",", " ")} %</div>
                     <div>{globalThis.hubleto.numberFormat(item.total_price_incl_vat, 2, ",", " ")} €</div>
                    </div>
                  </div>;
                }) : null}
              </div>
              <div className='p-2 mt-2 bg-gray-600 text-white text-3xl'>
                TOTAL: {globalThis.hubleto.numberFormat(R.total_price_incl_vat, 2, ",", " ")} €
              </div>
              <div className='mt-4 p-2 text-center'>
                {R.created}<br/>
                Cash register identifier: {R.CASH_REGISTER.identifier}<br/>
                <br/>
                <div className='m-auto'>
                  <Barcode value={R.CASH_REGISTER.identifier} height={30} lineColor='#444444' margin={0} className='w-full'/>
                </div>
              </div>
            </div>
          </div>
        </div>
      break;

      default:
        super.renderTab(tabUid);
      break;
    }
  }

}
