import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import TableTransactionItems from './TableTransactionItems';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

interface FormTransactionProps extends HubletoFormProps { }
interface FormTransactionState extends HubletoFormState { }

export default class FormTransaction<P, S> extends HubletoForm<FormTransactionProps, FormTransactionState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Transaction',
  }

  props: FormTransactionProps;
  state: FormTransactionState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormTransaction';

  constructor(props: FormTransactionProps) {
    super(props);
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
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='w-full flex-1'>
            {this.inputWrapper('uid')}
            {this.inputWrapper('type')}
            {this.inputWrapper('id_supplier')}
            {this.inputWrapper('supplier_invoice_number')}
            {this.inputWrapper('supplier_order_number')}
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
            <div className='flex gap-2 items-center bg-blue-50 p-2'>
              <div className='grow'>{this.inputWrapper('id_location_old')}</div>
              <div><i className='fas fa-arrow-right'></i></div>
              <div className='grow'>{this.inputWrapper('id_location_new')}</div>
            </div>
            <table className='table-default dense mt-2'>
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Qty.</th>
                  <th>Purchase price</th>
                </tr>
              </thead>
              <tbody>
                {R.ITEMS ? R.ITEMS.map((item, index) => {
                  return <>
                    <tr>
                      <td>
                        <Lookup
                          model='Hubleto/App/Community/Products/Models/Product'
                          urlAdd={'products/add'}
                          value={item.id_product}
                          cssClass='font-bold'
                          onChange={(input: any, value: any) => { this.updateItem(index, {id_product: value}); }}
                        ></Lookup>
                      </td>
                      <td>
                        <Int
                          value={item.quantity}
                          description={{decimals: 4}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {quantity: value}); }}
                        ></Int>
                      </td>
                      <td>
                        <Int
                          value={item.purchase_price}
                          description={{decimals: 4, unit: '€'}}
                          onChange={(input: any, value: any) => { this.updateItem(index, {purchase_price: value}); }}
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
          </div>
        </div>;
      break;
    }
  }
}
