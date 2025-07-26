import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormDiscussion from './FormDiscussion';

interface TableDiscussionsProps extends HubletoTableProps {
  externalModel?: string,
  externalId?: number,
}

interface TableDiscussionsState extends HubletoTableState {
}

export default class TableDiscussions extends HubletoTable<TableDiscussionsProps, TableDiscussionsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Discussions/Models/Discussion',
  }

  props: TableDiscussionsProps;
  state: TableDiscussionsState;

  translationContext: string = 'HubletoApp\\Community\\Discussions::Components\\TableDiscussions';

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
      externalModel: this.props.externalModel,
      externalId: this.props.externalId,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.externalModel = this.props.externalModel;
    formProps.customEndpointParams.externalId = this.props.externalId;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = {
      external_model: this.props.externalModel,
      external_id: this.props.externalId
    };
    return <FormDiscussion {...formProps}/>;
  }
}