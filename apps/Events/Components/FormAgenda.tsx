import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormAgendaProps extends FormExtendedProps { }
interface FormAgendaState extends FormExtendedState { }

export default class FormAgenda<P, S> extends FormExtended<FormAgendaProps, FormAgendaState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormAgendaProps;
  state: FormAgendaState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormAgenda';

  constructor(props: FormAgendaProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Agenda')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
