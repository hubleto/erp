import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import request from '@hubleto/react-ui/core/Request';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

interface FormTransactionProps extends FormExtendedProps { }
interface FormTransactionState extends FormExtendedState {
  productInfo: any,
}

export default class FormTransaction<P, S> extends FormExtended<FormTransactionProps, FormTransactionState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Transaction',
  }

  static TYPE_INBOUND = 1;
  static TYPE_OUTBOUND = 2;
  static TYPE_INTERNAL = 3;
  static TYPE_ADJUSTMENT = 4;

  props: FormTransactionProps;
  state: FormTransactionState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormTransaction';

  constructor(props: FormTransactionProps) {
    super(props);
  }

  getStateFromProps(props: FormTransactionProps) {
    return {
      ...super.getStateFromProps(props),
      productInfo: null,
    };
  }

  getEndpointParams(): object {
    return {
      ...super.getEndpointParams() as any,
      saveRelations: ['ITEMS'],
    };
  }

  getRecordFormUrl(): string {
    return 'warehouses/transactions/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  getTabsLeft() {
    return [
      { uid: 'default', title: <b>{this.translate('Transaction')}</b> },
      ...super.getTabsLeft(),
    ];
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Transaction')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

  updateItem(index: number, newItem: any) {
    let newRecord = this.state.record;
    if (!newRecord.ITEMS[index]) newRecord.ITEMS[index] = {};
    newRecord.ITEMS[index].id_transaction = { _useMasterRecordId_: true };
    newRecord.ITEMS[index] = {...newRecord.ITEMS[index], ...newItem};

    this.updateRecord(newRecord);

    if (newItem.id_product && newItem.id_product > 0) {
      let idProduct = newItem.id_product;
      request.post(
        'warehouses/api/get-product-info',
        { idProduct: idProduct },
        {},
        (data: any) => {
          let productInfo: any = null;

          if (data.status && data.status == 'success') {
            productInfo = data.productInfo;
          } else {
            productInfo = {error: data.message};
          }

          let newProductInfo = this.state.productInfo ?? {};
          newProductInfo[idProduct] = productInfo;
          console.log('newProductInfo', newProductInfo);

          this.setState({productInfo: newProductInfo });
        },
        (error: any) => {
        }
      )
    }
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='w-full flex-1'>
            {this.inputWrapper('uid')}
            {this.inputWrapper('type', {readonly: this.state.updatingRecord, uiStyle: 'buttons-vertical'})}
            {this.inputWrapper('id_order')}
            {/* {this.inputWrapper('id_supplier')}
            {this.inputWrapper('supplier_invoice_number')}
            {this.inputWrapper('supplier_order_number')} */}
            {this.inputWrapper('batch_number')}
            {this.inputWrapper('serial_number')}
            {this.inputWrapper('document_1')}
            {this.inputWrapper('document_2')}
            {this.inputWrapper('document_3')}
            {this.inputWrapper('notes')}
            {this.inputWrapper('created_on')}
            {this.inputWrapper('id_created_by')}
          </div>
          <div className='w-full flex-3'>
            <table className='table-default dense mt-2'>
              <thead>
                <tr>
                  {R.type == FormTransaction.TYPE_INBOUND ? null : <th>{this.translate('From')}</th>}
                  {R.type == FormTransaction.TYPE_OUTBOUND ? null : <th>{this.translate('To')}</th>}
                  <th>{this.translate('Product')}</th>
                  <th>{this.translate('Qty.')}</th>
                  {R.type == FormTransaction.TYPE_INBOUND ? <th>{this.translate('Purchase price')}</th>
                  : R.type == FormTransaction.TYPE_OUTBOUND ? <th>{this.translate('Sales price')}</th>
                  : null}
                </tr>
              </thead>
              <tbody>
                {R.ITEMS ? R.ITEMS.map((item, index) => {
                  let productInfo = this.state.productInfo ? (this.state.productInfo[item.id_product] ?? null) : null;
                  return <tr>
                    {R.type == FormTransaction.TYPE_INBOUND ? null :
                      <td className='bg-blue-50 p-2'>
                        {this.input('id_location_old', {readonly: this.state.updatingRecord})}
                      </td>
                    }
                    {R.type == FormTransaction.TYPE_OUTBOUND ? null :
                      <td className='bg-green-50 p-2'>
                        {this.input('id_location_new', {readonly: this.state.updatingRecord})}
                      </td>
                    }
                    <td>
                      <Lookup
                        model='Hubleto/App/Community/Products/Models/Product'
                        urlAdd={'products/add'}
                        value={item.id_product}
                        cssClass='font-bold'
                        onChange={(input: any, value: any) => { this.updateItem(index, {id_product: value}); }}
                        readonly={this.state.updatingRecord}
                      ></Lookup>
                      {productInfo && productInfo.INVENTORY ? productInfo.INVENTORY.map((item, key) => {
                        console.log(item);
                        return <div key={key}>
                          {globalThis.hubleto.numberFormat(item.quantity, 2)}
                          &nbsp;{productInfo.PRODUCT?.unit ?? 'x'}
                          &nbsp;@
                          &nbsp;<a href={globalThis.hubleto.config.projectUrl + '/warehouses/locations/' + item.LOCATION?.id} target="_blank">
                            {item.LOCATION?.code} @ {item.LOCATION?.WAREHOUSE?.name}
                          </a>
                        </div>
                      }) : null}
                    </td>
                    <td>
                      <Int
                        value={item.quantity}
                        description={{decimals: 4}}
                        onChange={(input: any, value: any) => { this.updateItem(index, {quantity: value}); }}
                        readonly={this.state.updatingRecord}
                      ></Int>
                    </td>
                    {R.type == FormTransaction.TYPE_INBOUND || R.type == FormTransaction.TYPE_OUTBOUND ? 
                      <td>
                        <Int
                          value={item.unit_price}
                          description={{decimals: 4, unit: '€'}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {unit_price: value}); }}
                          readonly={this.state.updatingRecord}
                        ></Int>
                      </td>
                    : null}
                  </tr>
                }) : null}
              </tbody>
            </table>
            {this.state.creatingRecord ?
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
                <span className='text'>{this.translate('Add product')}</span>
              </button>
            : null}
          </div>
        </div>;
      break;
    }
  }
}
