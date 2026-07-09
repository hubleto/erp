import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormMilestoneTaskProps extends FormExtendedProps { }
interface FormMilestoneTaskState extends FormExtendedState { }

export default class FormMilestoneTask<P, S> extends FormExtended<FormMilestoneTaskProps, FormMilestoneTaskState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/MilestoneTask',
  }

  props: FormMilestoneTaskProps;
  state: FormMilestoneTaskState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormMilestoneTask';

  constructor(props: FormMilestoneTaskProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Milestone task')}</small>
      <h2>{this.state.record.id_task ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_milestone')}
          {this.inputWrapper('id_task')}
        </>;
      break;
    }
  }

}
