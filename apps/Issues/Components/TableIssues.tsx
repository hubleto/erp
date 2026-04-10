import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormIssue, { FormIssueProps } from './FormIssue';

interface TableIssuesProps extends TableExtendedProps {}
interface TableIssuesState extends TableExtendedState {}

export default class TableIssues extends TableExtended<TableIssuesProps, TableIssuesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Issues/Models/Issue',
  }

  props: TableIssuesProps;
  state: TableIssuesState;

  translationContext: string = 'Hubleto\\App\\Community\\Issues\\Loader';
  translationContextInner: string = 'Components\\TableIssues';

  constructor(props: TableIssuesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableIssuesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/issues/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps: FormIssueProps = this.getFormProps();
    return <FormIssue {...formProps}/>;
  }
}