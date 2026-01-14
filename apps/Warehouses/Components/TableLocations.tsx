import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormLocation from './FormLocation';

interface TableLocationsProps extends TableExtendedProps {
  idWarehouse?: number,
}

interface TableLocationsState extends TableExtendedState {
}

export default class TableLocations extends TableExtended<TableLocationsProps, TableLocationsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Warehouses/Models/Location',
  }

  props: TableLocationsProps;
  state: TableLocationsState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\FormLocations';

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