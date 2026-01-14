import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormActivityProps extends FormExtendedProps { }
interface FormActivityState extends FormExtendedState { }

export default class FormActivity<P, S> extends FormExtended<FormActivityProps, FormActivityState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Worksheets/Models/Activity',
  }

  props: FormActivityProps;
  state: FormActivityState;

  translationContext: string = 'Hubleto\\App\\Community\\Worksheets\\Loader';
  translationContextInner: string = 'Components\\FormActivity';

  constructor(props: FormActivityProps) {
    super(props);
  }

  // getStateFromProps(props: FormActivityProps) {
  //   return {
  //     ...super.getStateFromProps(props),
  //     tabs: [
  //       { uid: 'default', title: 'Activity' },
  //     ]
  //   }
  // }

  getRecordFormUrl(): string {
    return 'worksheets/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small></small>
      <h2>{this.translate('Activity')}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className="w-full flex gap-2 md:flex-row">
          <div className='w-full'>
            {this.inputWrapper('id_task')}
            {this.inputWrapper('id_type')}
            {this.inputWrapper('date_worked')}
            {this.inputWrapper('description')}
            {this.inputWrapper('worked_hours')}
          </div>
          <div className='w-full'>
            {this.inputWrapper('id_worker')}
            {this.inputWrapper('is_approved')}
            {this.inputWrapper('is_chargeable')}
            {this.inputWrapper('datetime_created')}
          </div>
        </div>;
      break;
    }
  }

}
