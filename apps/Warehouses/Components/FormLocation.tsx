import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormLocationProps extends FormExtendedProps { }
interface FormLocationState extends FormExtendedState { }

export default class FormLocation<P, S> extends FormExtended<FormLocationProps, FormLocationState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Team',
  }

  props: FormLocationProps;
  state: FormLocationState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormLocation';

  constructor(props: FormLocationProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Location')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    // This is an example code to render content of the form.
    // You should develop your own render content.
    return <>
      <div className='w-full flex gap-2'>
        <div className="flex-1 border-r border-gray-100">
          {this.inputWrapper('id_warehouse')}
          {this.inputWrapper('code')}
          {this.inputWrapper('id_type')}
          {this.inputWrapper('operational_status')}
          {this.inputWrapper('id_operational_manager')}
          {this.divider(this.translate('Capacity and stock status'))}
          <div className="flex gap-2">
            <div className="w-full">{this.inputWrapper('capacity')}</div>
            <div className="w-full">{this.inputWrapper('capacity_unit')}</div>
          </div>
          {this.inputWrapper('current_stock_status')}
        </div>
        <div className="flex-1">
          {this.divider(this.translate('Placement'))}
          {this.inputWrapper('placement')}
          {this.divider(this.translate('More information'))}
          {this.inputWrapper('description')}
          {this.inputWrapper('photo_1')}
          {this.inputWrapper('photo_2')}
          {this.inputWrapper('photo_3')}
        </div>
      </div>
    </>;
  }
}
