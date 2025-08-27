import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormMember from './FormMember';

interface TableMembersProps extends HubletoTableProps {
  idDiscussion?: number,
}

interface TableMembersState extends HubletoTableState {
}

export default class TableMembers extends HubletoTable<TableMembersProps, TableMembersState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Discussions/Models/Member',
  }

  props: TableMembersProps;
  state: TableMembersState;

  translationContext: string = 'HubletoApp\\Community\\Discussions::Components\\TableMembers';

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