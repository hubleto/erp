import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormTypeProps extends HubletoFormProps { }
interface FormTypeState extends HubletoFormState { }

export default class FormType<P, S> extends HubletoForm<FormTypeProps, FormTypeState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormTypeProps;
  state: FormTypeState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormType';

  constructor(props: FormTypeProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Type</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
