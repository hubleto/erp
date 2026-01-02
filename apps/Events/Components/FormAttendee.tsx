import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormAttendeeProps extends HubletoFormProps { }
interface FormAttendeeState extends HubletoFormState { }

export default class FormAttendee<P, S> extends HubletoForm<FormAttendeeProps, FormAttendeeState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
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
