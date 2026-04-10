import React, { Component, createRef, useRef, ChangeEvent } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import TablePosts from './TablePosts';

export interface FormIssueProps extends FormExtendedProps {
}

export interface FormIssueState extends FormExtendedState {
}

export default class FormIssue<P, S> extends FormExtended<FormIssueProps,FormIssueState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-money-check-dollar',
    model: 'Hubleto/App/Community/Issues/Models/Issue',
    renderWorkflowUi: true,
    renderOwnerManagerUi: true,
  };

  props: FormIssueProps;
  state: FormIssueState;

  translationContext: string = 'Hubleto\\App\\Community\\Issues\\Loader';
  translationContextInner: string = 'Components\\FormIssue';

  parentApp: string = 'Hubleto/App/Community/Issues';

  constructor(props: FormIssueProps) {
    super(props);

    this.state = {
      ...this.getStateFromProps(props),
    };
  }

  getTabsLeft() {
    return [
      { uid: 'default', title: <b>{this.translate('Issue')}</b> },
      { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
      ...super.getTabsLeft(),
    ];
  }

  getStateFromProps(props: FormIssueProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  getRecordFormUrl(): string {
    return 'issues/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Issue')}</small>
      <h2>{this.state.record.title ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('from')}
          {this.inputWrapper('title')}
          {this.inputWrapper('description')}
          {this.inputWrapper('notes')}
          {this.inputWrapper('id_customer')}
          {this.inputWrapper('id_mail')}
          <TablePosts uid={"issue_table_posts"} idIssue={R.id}></TablePosts>
        </>;
      break;

      case 'tasks':
        return <TableTasks
          tag={"table_issue_task"}
          parentForm={this}
          uid={this.props.uid + "_table_issue_task"}
          junctionTitle='Issue'
          junctionModel='Hubleto/App/Community/Issues/Models/IssueTask'
          junctionSourceColumn='id_issue'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_task'
        />;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }

}