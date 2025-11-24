import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormRecipientStatus, { FormRecipientStatusProps } from './FormRecipientStatus';

interface TableRecipientStatusesProps extends HubletoTableProps {
  idCampaign: number
}
interface TableRecipientStatusesState extends HubletoTableState {}

export default class TableRecipientStatuses extends HubletoTable<TableRecipientStatusesProps, TableRecipientStatusesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Campaigns/Models/RecipientStatus',
  }

  props: TableRecipientStatusesProps;
  state: TableRecipientStatusesState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\TableRecipientStatuses';

  constructor(props: TableRecipientStatusesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableRecipientStatusesProps) {
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

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCampaign: this.props.idCampaign
    }
  }

  getCsvImportEndpointParams(): any {
    return {
      model: this.props.model,
      defaultCsvImportValues: {
        id_campaign: this.props.idCampaign,
      }
    }
  }

  rowClassName(rowData: any): string {
    return rowData.is_opted_out ? 'bg-red-300' : (rowData.is_invalid ? 'bg-gray-300' : super.rowClassName(rowData));
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/campaigns/recipients/statuses/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormRecipientStatusProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_campaign: this.props.idCampaign };
    return <FormRecipientStatus {...formProps}/>;
  }
}