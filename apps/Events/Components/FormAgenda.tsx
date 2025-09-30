import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormAgendaProps extends HubletoFormProps { }
interface FormAgendaState extends HubletoFormState { }

export default class FormAgenda<P, S> extends HubletoForm<FormAgendaProps, FormAgendaState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
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
      <small>Agenda</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
