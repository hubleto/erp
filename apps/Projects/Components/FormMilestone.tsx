import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableMilestoneReports from './TableMilestoneReports';

interface FormMilestoneProps extends FormExtendedProps { }
interface FormMilestoneState extends FormExtendedState { }

export default class FormMilestone<P, S> extends FormExtended<FormMilestoneProps, FormMilestoneState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/Milestone',
  }

  props: FormMilestoneProps;
  state: FormMilestoneState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormMilestone';

  constructor(props: FormMilestoneProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Milestone')}</small>
      <h2>{this.state.record.title ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='grow'>
            {this.inputWrapper('id_project')}
            {this.inputWrapper('title')}
            {this.inputWrapper('date_due')}
            {this.inputWrapper('expected_output')}
            {this.inputWrapper('description')}
            {this.inputWrapper('color')}
          </div>
          <div className='grow card'>
            <div className='card-header'>Reports</div>
            <div className='card-body'>
              <TableMilestoneReports
                tag={"table_project_milestone_report"}
                parentForm={this}
                uid={this.props.uid + "_table_project_milestone_report"}
                idMilestone={R.id}
              />
            </div>
          </div>
        </div>;
      break;
    }
  }

}
