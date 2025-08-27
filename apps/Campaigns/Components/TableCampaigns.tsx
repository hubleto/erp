import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormCampaign, { FormCampaignProps } from './FormCampaign';

interface TableCampaignsProps extends HubletoTableProps {}
interface TableCampaignsState extends HubletoTableState {}

export default class TableCampaigns extends HubletoTable<TableCampaignsProps, TableCampaignsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
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

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/campaigns/' + id);
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormCampaignProps;
    return <FormCampaign {...formProps}/>;
  }
}