import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormPhaseProps extends HubletoFormProps { }
interface FormPhaseState extends HubletoFormState { }

export default class FormPhase<P, S> extends HubletoForm<FormPhaseProps, FormPhaseState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/Team',
  }

  props: FormPhaseProps;
  state: FormPhaseState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormPhase';

  constructor(props: FormPhaseProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Phase</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
