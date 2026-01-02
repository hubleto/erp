import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormInventoryProps extends HubletoFormProps { }
interface FormInventoryState extends HubletoFormState { }

export default class FormInventory<P, S> extends HubletoForm<FormInventoryProps, FormInventoryState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Team',
  }

  props: FormInventoryProps;
  state: FormInventoryState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormInventory';

  constructor(props: FormInventoryProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Inventory')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
