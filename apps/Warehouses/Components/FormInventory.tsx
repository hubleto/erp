import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormInventoryProps extends FormExtendedProps { }
interface FormInventoryState extends FormExtendedState { }

export default class FormInventory<P, S> extends FormExtended<FormInventoryProps, FormInventoryState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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
