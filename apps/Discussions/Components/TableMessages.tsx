import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormMessage from './FormMessage';

interface TableMessagesProps extends HubletoTableProps {
  idDiscussion?: number,
}

interface TableMessagesState extends HubletoTableState {
}

export default class TableMessages extends HubletoTable<TableMessagesProps, TableMessagesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Discussions/Models/Message',
  }

  props: TableMessagesProps;
  state: TableMessagesState;

  translationContext: string = 'HubletoApp\\Community\\Discussions::Components\\TableMessages';

  constructor(props: TableMessagesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableMessagesProps) {
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
      idDiscussion: this.props.idDiscussion,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idDiscussion = this.props.idDiscussion;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_discussion: this.props.idDiscussion };
    return <FormMessage {...formProps}/>;
  }
}