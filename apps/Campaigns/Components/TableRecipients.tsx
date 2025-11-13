import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormRecipient, { FormRecipientProps } from './FormRecipient';

interface TableRecipientsProps extends HubletoTableProps {
  idCampaign: number
}
interface TableRecipientsState extends HubletoTableState {}

export default class TableRecipients extends HubletoTable<TableRecipientsProps, TableRecipientsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Campaigns/Models/Recipient',
    orderBy: {field: 'is_opted_out', direction: 'desc'},
  }

  props: TableRecipientsProps;
  state: TableRecipientsState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader';
  translationContextInner: string = 'Components\\TableRecipients';

  constructor(props: TableRecipientsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableRecipientsProps) {
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
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/campaigns/recipients/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormRecipientProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_campaign: this.props.idCampaign };
    return <FormRecipient {...formProps}/>;
  }
}