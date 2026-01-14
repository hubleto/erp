import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormMessage from './FormMessage';

interface TableMessagesProps extends TableExtendedProps {
  idDiscussion?: number,
}

interface TableMessagesState extends TableExtendedState {
}

export default class TableMessages extends TableExtended<TableMessagesProps, TableMessagesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Discussions/Models/Message',
  }

  props: TableMessagesProps;
  state: TableMessagesState;

  translationContext: string = 'Hubleto\\App\\Community\\Discussions\\Loader';
  translationContextInner: string = 'Components\\TableMessages';

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