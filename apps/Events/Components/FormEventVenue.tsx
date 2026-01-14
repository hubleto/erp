import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormEventVenueProps extends FormExtendedProps { }
interface FormEventVenueState extends FormExtendedState { }

export default class FormEventVenue<P, S> extends FormExtended<FormEventVenueProps, FormEventVenueState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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
      <small>{this.translate('Event venue')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
