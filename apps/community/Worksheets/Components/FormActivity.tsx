import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/src/core/Components/HubletoForm';

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
         <div className='w-full flex gap-2'>
           <div className="flex-1 border-r border-gray-100">
             {this.inputWrapper('id_worker')}
             {this.inputWrapper('id_task')}
             {this.inputWrapper('description')}
             {this.inputWrapper('duration')}
             {this.inputWrapper('id_type')}
             {this.inputWrapper('is_approved')}
             {this.inputWrapper('datetime_created')}
           </div>
           <div className="flex-1">
           </div>
         </div>
        </>;
      break;
    }
  }

}
