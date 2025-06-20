import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCampaign, { FormCampaignProps } from './FormCampaign';

interface TableCampaignsProps extends TableProps {}
interface TableCampaignsState extends TableState {}

export default class TableCampaigns extends Table<TableCampaignsProps, TableCampaignsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/Campaign',
  }

  props: TableCampaignsProps;
  state: TableCampaignsState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\TableCampaigns';

  constructor(props: TableCampaignsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCampaignsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormCampaignProps;
    return <FormCampaign {...formProps}/>;
  }
}