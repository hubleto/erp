import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormWarehouse from './FormWarehouse';

interface TableWarehousesProps extends HubletoTableProps {
  // idCustomer?: number,
}

interface TableWarehousesState extends HubletoTableState {
  // idCustomer: number,
}

export default class FormWarehouses extends HubletoTable<TableWarehousesProps, TableWarehousesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Warehouses/Models/Warehouse',
  }

  props: TableWarehousesProps;
  state: TableWarehousesState;

  translationContext: string = 'HubletoApp\\Community\\Warehouses::Components\\FormWarehouses';

  constructor(props: TableWarehousesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableWarehousesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      // idCustomer: this.props.idCustomer,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    // formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    // if (!formProps.description) formProps.description = {};
    // formProps.description.defaultValues = { idDashboard: this.state.recordId };
    return <FormWarehouse {...formProps}/>;
  }
}