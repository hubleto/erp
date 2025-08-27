import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormEventAttendee from './FormEventAttendee';

interface TableEventAttendeesProps extends HubletoTableProps {
  idEvent?: number,
}

interface TableEventAttendeesState extends HubletoTableState {
}

export default class TableEventAttendees extends HubletoTable<TableEventAttendeesProps, TableEventAttendeesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Events/Models/EventAttendee',
  }

  props: TableEventAttendeesProps;
  state: TableEventAttendeesState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\TableEventAttendees';

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