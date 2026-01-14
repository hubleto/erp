import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormVenueProps extends FormExtendedProps { }
interface FormVenueState extends FormExtendedState { }

export default class FormVenue<P, S> extends FormExtended<FormVenueProps, FormVenueState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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
      <small>{this.translate('Venue')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
