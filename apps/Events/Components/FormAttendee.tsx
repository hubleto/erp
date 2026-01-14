import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormAttendeeProps extends FormExtendedProps { }
interface FormAttendeeState extends FormExtendedState { }

export default class FormAttendee<P, S> extends FormExtended<FormAttendeeProps, FormAttendeeState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormAttendeeProps;
  state: FormAttendeeState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormAttendee';

  constructor(props: FormAttendeeProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Attendee')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
