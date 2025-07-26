import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormEvent from './FormEvent';

interface TableEventsProps extends HubletoTableProps {
  // Uncomment and modify these lines if you want to create URL-based filtering for your model
  // idCustomer?: number,
}

interface TableEventsState extends HubletoTableState {
}

export default class TableEvents extends HubletoTable<TableEventsProps, TableEventsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Events/Models/Event',
  }

  props: TableEventsProps;
  state: TableEventsState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\TableEvents';

  constructor(props: TableEventsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEventsProps) {
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
    return <FormEvent {...formProps}/>;
  }
}