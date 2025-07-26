import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormCampaign, { FormCampaignProps } from './FormCampaign';

interface TableCampaignsProps extends TableProps {}
interface TableCampaignsState extends TableState {}

export default class TableCampaigns extends Table<TableCampaignsProps, TableCampaignsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Campaigns/Models/Campaign',
  }

  props: TableCampaignsProps;
  state: TableCampaignsState;

  translationContext: string = 'HubletoApp\\Community\\Campaigns\\Loader::Components\\TableCampaigns';

  constructor(props: TableCampaignsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCampaignsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide',
    };
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormCampaignProps;
    return <FormCampaign {...formProps}/>;
  }
}