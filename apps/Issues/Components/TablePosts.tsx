import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormPost, { FormPostProps } from './FormPost';

interface TablePostsProps extends TableExtendedProps {
  idIssue?: number,
}
interface TablePostsState extends TableExtendedState {}

export default class TablePosts extends TableExtended<TablePostsProps, TablePostsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Issues/Models/Post',
  }

  props: TablePostsProps;
  state: TablePostsState;

  translationContext: string = 'Hubleto\\App\\Community\\Issues\\Loader';
  translationContextInner: string = 'Components\\TablePosts';

  constructor(props: TablePostsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TablePostsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idIssue: this.props.idIssue,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  setRecordFormUrl(id: number) {
    globalThis.window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/issues/posts/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps: FormPostProps = this.getFormProps();
    formProps.customEndpointParams.idKey = this.props.idIssue
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_issue: this.props.idIssue };
    return <FormPost {...formProps}/>;
  }
}