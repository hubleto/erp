import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormActivityTypeProps extends HubletoFormProps { }
interface FormActivityTypeState extends HubletoFormState { }

export default class FormActivityType<P, S> extends HubletoForm<FormActivityTypeProps, FormActivityTypeState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Worksheets/Models/Team',
  }

  props: FormActivityTypeProps;
  state: FormActivityTypeState;

  translationContext: string = 'Hubleto\\App\\Community\\Worksheets\\Loader';
  translationContextInner: string = 'Components\\FormActivityType';

  constructor(props: FormActivityTypeProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>ActivityType</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
