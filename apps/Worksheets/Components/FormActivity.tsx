import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

interface FormActivityProps extends HubletoFormProps { }
interface FormActivityState extends HubletoFormState { }

export default class FormActivity<P, S> extends HubletoForm<FormActivityProps, FormActivityState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Worksheets/Models/Team',
    tabs: {
      'default': { title: 'Task' },
    }
  }

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'HubletoApp\\Community\\Worksheets::Components\\FormActivity';

  constructor(props: FormActivityProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Activity</h2>
      <small></small>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
          <div className="flex gap-2 flex-col md:flex-row">
            <div className="w-full">{this.inputWrapper('id_task')}</div>
            <div className="w-full">{this.inputWrapper('id_worker')}</div>
          </div>
          {this.inputWrapper('description')}
          <div className="flex gap-2 flex-col md:flex-row">
            <div className="w-full">
              {this.inputWrapper('date_worked')}
              {this.inputWrapper('duration')}
            </div>
            <div className="w-full">
              {this.inputWrapper('id_type')}
              {this.inputWrapper('is_approved')}
              {this.inputWrapper('datetime_created')}
            </div>
          </div>
        </>;
      break;
    }
  }

}
