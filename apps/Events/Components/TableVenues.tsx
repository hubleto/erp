import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormVenue from './FormVenue';

interface TableVenuesProps extends HubletoTableProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TableVenuesState extends HubletoTableState {
}

export default class TableVenues extends HubletoTable<TableVenuesProps, TableVenuesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Events/Models/Venue',
  }

  props: TableVenuesProps;
  state: TableVenuesState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\TableVenues';

  constructor(props: TableVenuesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableVenuesProps) {
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
      // Uncomment and modify these lines if you want to create URL-based filtering for your model
      // idCustomer: this.props.idCustomer,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    // formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    // if (!formProps.description) formProps.description = {};
    // formProps.description.defaultValues = { idDashboard: this.props.idDashboard };
    return <FormVenue {...formProps}/>;
  }
}