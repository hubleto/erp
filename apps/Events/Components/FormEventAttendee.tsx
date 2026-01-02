import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormEventAttendeeProps extends HubletoFormProps { }
interface FormEventAttendeeState extends HubletoFormState { }

export default class FormEventAttendee<P, S> extends HubletoForm<FormEventAttendeeProps, FormEventAttendeeState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormEventAttendeeProps;
  state: FormEventAttendeeState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormEventAttendee';

  constructor(props: FormEventAttendeeProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Event attendee')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
