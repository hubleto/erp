import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import TableLocations from './TableLocations';

interface FormWarehouseProps extends FormExtendedProps { }
interface FormWarehouseState extends FormExtendedState { }

export default class FormWarehouse<P, S> extends FormExtended<FormWarehouseProps, FormWarehouseState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Warehouses/Models/Warehouse',
  }

  props: FormWarehouseProps;
  state: FormWarehouseState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormWarehouse';

  constructor(props: FormWarehouseProps) {
    super(props);
  }

  getStateFromProps(props: FormWarehouseProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Warehouse')}</b> },
        { uid: 'locations', title: this.translate('Locations'), showCountFor: 'LOCATIONS' },
        ...this.getCustomTabs()
      ]
    }
  }

  getRecordFormUrl(): string {
    return 'warehouses/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Warehouse')}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='w-full flex gap-2'>
          <div className="flex-1 border-r border-gray-100">
            {this.inputWrapper('name')}
            {this.inputWrapper('id_type')}
            {this.inputWrapper('operational_status')}
            {this.inputWrapper('id_operation_manager')}
            {this.divider(this.translate('Contact'))}
            {this.inputWrapper('contact_person')}
            {this.inputWrapper('contact_email')}
            {this.inputWrapper('contact_phone')}
            {this.divider(this.translate('Capacity and stock status'))}
            <div className="flex gap-2">
              <div className="w-full">{this.inputWrapper('capacity')}</div>
              <div className="w-full">{this.inputWrapper('capacity_unit')}</div>
            </div>
            {this.inputWrapper('current_stock_status')}
          </div>
          <div className="flex-1">
            {this.divider(this.translate('Address'))}
            {this.inputWrapper('address')}
            {this.inputWrapper('address_plus_code')}
            {this.inputWrapper('lng')}
            {this.inputWrapper('lat')}
            {this.divider(this.translate('More information'))}
            {this.inputWrapper('description')}
            {this.inputWrapper('photo_1')}
            {this.inputWrapper('photo_2')}
            {this.inputWrapper('photo_3')}
          </div>
        </div>;
      break;
      case 'locations':
        return <>
          {this.state.id < 0 ?
            <div className="badge badge-info">First create warehouse, then you will be prompted to add its locations.</div>
          :
            <TableLocations
              uid={this.props.uid + '_table_locations'}
              parentForm={this}
              idWarehouse={this.state.id}
              customEndpointParams={ { idWarehouse: this.state.id } }
            ></TableLocations>
          }
        </>;
      break;
    };
  }
}
