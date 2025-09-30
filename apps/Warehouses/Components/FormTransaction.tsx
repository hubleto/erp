import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import TableTransactionItems from './TableTransactionItems';

interface FormTransactionProps extends HubletoFormProps {
  direction?: number,
}
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

  renderTitle(): JSX.Element {
    return <>
      <small>{this.props.direction == 1 ? 'Inbound transaction' : 'Outbound transaction'}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='w-full'>
            {this.inputWrapper('uid')}
            {this.inputWrapper('direction')}
            {this.inputWrapper('type')}
            {this.inputWrapper('id_order')}
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
          <div className='w-full'>
            <TableTransactionItems
              uid={this.props.uid + '_table_transaction_items'}
              parentForm={this}
              idTransaction={this.state.id}
            ></TableTransactionItems>
          </div>
        </div>;
      break;
    }
  }
}
