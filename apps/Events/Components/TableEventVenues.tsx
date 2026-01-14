import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormEventVenue from './FormEventVenue';

interface TableEventVenuesProps extends TableExtendedProps {
  idEvent?: number,
}

interface TableEventVenuesState extends TableExtendedState {
}

export default class TableEventVenues extends TableExtended<TableEventVenuesProps, TableEventVenuesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Events/Models/EventVenue',
  }

  props: TableEventVenuesProps;
  state: TableEventVenuesState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\TableEventVenues';

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