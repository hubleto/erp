import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormEventVenueProps extends HubletoFormProps { }
interface FormEventVenueState extends HubletoFormState { }

export default class FormEventVenue<P, S> extends HubletoForm<FormEventVenueProps, FormEventVenueState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormEventVenueProps;
  state: FormEventVenueState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormEventVenue';

  constructor(props: FormEventVenueProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Event venue</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
