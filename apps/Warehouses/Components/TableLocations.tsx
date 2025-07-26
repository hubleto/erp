import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormLocation from './FormLocation';

interface TableLocationsProps extends HubletoTableProps {
  idWarehouse?: number,
}

interface TableLocationsState extends HubletoTableState {
}

export default class TableLocations extends HubletoTable<TableLocationsProps, TableLocationsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Warehouses/Models/Location',
  }

  props: TableLocationsProps;
  state: TableLocationsState;

  translationContext: string = 'HubletoApp\\Community\\Warehouses::Components\\FormLocations';

  constructor(props: TableLocationsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLocationsProps) {
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
      idWarehouse: this.props.idWarehouse,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    // formProps.customEndpointParams.idWarehouse = this.props.idWarehouse;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_warehouse: this.props.idWarehouse };
    console.log(formProps.description);
    return <FormLocation {...formProps}/>;
  }
}