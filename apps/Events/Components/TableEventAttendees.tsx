import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormEventAttendee from './FormEventAttendee';

interface TableEventAttendeesProps extends TableExtendedProps {
  idEvent?: number,
}

interface TableEventAttendeesState extends TableExtendedState {
}

export default class TableEventAttendees extends TableExtended<TableEventAttendeesProps, TableEventAttendeesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Events/Models/EventAttendee',
  }

  props: TableEventAttendeesProps;
  state: TableEventAttendeesState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\TableEventAttendees';

  constructor(props: TableEventAttendeesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEventAttendeesProps) {
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
    return <FormEventAttendee {...formProps}/>;
  }
}