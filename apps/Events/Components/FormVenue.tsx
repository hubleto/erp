import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormVenueProps extends HubletoFormProps { }
interface FormVenueState extends HubletoFormState { }

export default class FormVenue<P, S> extends HubletoForm<FormVenueProps, FormVenueState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormVenueProps;
  state: FormVenueState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormVenue';

  constructor(props: FormVenueProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Venue</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
