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
      <h2>Record #{this.state.record.id ?? '0'}</h2>
      <small>Worksheet</small>
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
             {this.inputWrapper('activity_description')}
             {this.inputWrapper('duration')}
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
