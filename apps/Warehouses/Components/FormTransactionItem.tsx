import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import TableTransactionItems from './TableTransactionItems';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

interface FormTransactionItemProps extends HubletoFormProps {
  direction?: number,
}
interface FormTransactionItemState extends HubletoFormState { }

export default class FormTransactionItem<P, S> extends HubletoForm<FormTransactionItemProps, FormTransactionItemState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Transaction',
  }

  props: FormTransactionItemProps;
  state: FormTransactionItemState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormTransactionItem';

  constructor(props: FormTransactionItemProps) {
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
      <small>{this.translate('Transaction item')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
