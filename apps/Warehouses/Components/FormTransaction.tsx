import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormTransactionProps extends HubletoFormProps { }
interface FormTransactionState extends HubletoFormState { }

export default class FormTransaction<P, S> extends HubletoForm<FormTransactionProps, FormTransactionState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Team',
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
      <small>Transaction</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
