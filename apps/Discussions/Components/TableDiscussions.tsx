import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormDiscussion from './FormDiscussion';

interface TableDiscussionsProps extends TableExtendedProps { }

interface TableDiscussionsState extends TableExtendedState {
}

export default class TableDiscussions extends TableExtended<TableDiscussionsProps, TableDiscussionsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Discussions/Models/Discussion',
  }

  props: TableDiscussionsProps;
  state: TableDiscussionsState;

  translationContext: string = 'Hubleto\\App\\Community\\Discussions\\Loader';
  translationContextInner: string = 'Components\\TableDiscussions';

  constructor(props: TableDiscussionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDiscussionsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormDiscussion {...formProps}/>;
  }
}