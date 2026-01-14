import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormMember from './FormMember';

interface TableMembersProps extends TableExtendedProps {
  idDiscussion?: number,
}

interface TableMembersState extends TableExtendedState {
}

export default class TableMembers extends TableExtended<TableMembersProps, TableMembersState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Discussions/Models/Member',
  }

  props: TableMembersProps;
  state: TableMembersState;

  translationContext: string = 'Hubleto\\App\\Community\\Discussions\\Loader';
  translationContextInner: string = 'Components\\TableMembers';

  constructor(props: TableMembersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableMembersProps) {
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
    return <FormMember {...formProps}/>;
  }
}