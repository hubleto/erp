import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormEventVenue from './FormEventVenue';

interface TableEventVenuesProps extends HubletoTableProps {
  idEvent?: number,
}

interface TableEventVenuesState extends HubletoTableState {
}

export default class TableEventVenues extends HubletoTable<TableEventVenuesProps, TableEventVenuesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Events/Models/EventVenue',
  }

  props: TableEventVenuesProps;
  state: TableEventVenuesState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\TableEventVenues';

  constructor(props: TableEventVenuesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEventVenuesProps) {
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
      idEvent: this.props.idEvent,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idEvent = this.props.idEvent;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_event: this.props.idEvent };
    return <FormEventVenue {...formProps}/>;
  }
}