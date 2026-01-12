import React, { Component } from 'react'
import { setUrlParam, getUrlParam, deleteUrlParam } from '@hubleto/react-ui/core/Helper';
import { TableDescription } from '@hubleto/react-ui/core/Table';
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormInvoice from './FormInvoice';
import moment from "moment";

interface TableInvoicesProps extends HubletoTableProps {
  idCustomer?: any,
  number?: any,
  vs?: any,
  dateIssueFrom?: any,
  dateIssueTo?: any,
  dateDeliveryFrom?: any,
  dateDeliveryTo?: any,
  dateDueFrom?: any,
  dateDueTo?: any,
  datePaymentFrom?: any,
  datePaymentTo?: any,
}

interface TableInvoicesState extends HubletoTableState {
  idCustomer: any,
  number: any,
  vs: any,
  dateIssueFrom: any,
  dateIssueTo: any,
  dateDeliveryFrom: any,
  dateDeliveryTo: any,
  dateDueFrom: any,
  dateDueTo: any,
  datePaymentFrom: any,
  datePaymentTo: any,
}

export default class TableInvoices extends HubletoTable<TableInvoicesProps, TableInvoicesState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    itemsPerPage: 25,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Invoices/Models/Invoice',
    // description: {
    //   ui: { addButtonText: this.translate('Create invoice') }
    // },
  }

  props: TableInvoicesProps;
  state: TableInvoicesState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\TableInvoices';

  constructor(props: TableInvoicesProps) {
    super(props);
    this.state = this.getStateFromProps(props) as TableInvoicesState;
  }

  getStateFromProps(props: TableInvoicesProps) {
    return {
      ...super.getStateFromProps(props),
      idCustomer: props.idCustomer ?? (getUrlParam('id-customer') ?? 0),
      number: props.number ?? (getUrlParam('number') ?? ''),
      vs: props.vs ?? (getUrlParam('vs') ?? ''),
      dateIssueFrom: props.dateIssueFrom ?? (getUrlParam('date-issue-from') ?? ''),
      dateIssueTo: props.dateIssueTo ?? (getUrlParam('date-issue-to') ?? ''),
      dateDeliveryFrom: props.dateDeliveryFrom ?? (getUrlParam('date-delivery-from') ?? ''),
      dateDeliveryTo: props.dateDeliveryTo ?? (getUrlParam('date-delivery-to') ?? ''),
      dateDueFrom: props.dateDueFrom ?? (getUrlParam('date-due-from') ?? ''),
      dateDueTo: props.dateDueTo ?? (getUrlParam('date-due-to') ?? ''),
      datePaymentFrom: props.datePaymentFrom ?? (getUrlParam('date-payment-from') ?? ''),
      datePaymentTo: props.datePaymentTo ?? (getUrlParam('date-payment-to') ?? ''),
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
      idCustomer: this.state.idCustomer,
      number: this.state.number,
      vs: this.state.vs,
      dateIssueFrom: this.state.dateIssueFrom,
      dateIssueTo: this.state.dateIssueTo,
      dateDeliveryFrom: this.state.dateDeliveryFrom,
      dateDeliveryTo: this.state.dateDeliveryTo,
      dateDueFrom: this.state.dateDueFrom,
      dateDueTo: this.state.dateDueTo,
      datePaymentFrom: this.state.datePaymentFrom,
      datePaymentTo: this.state.datePaymentTo,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/invoices/' + (id > 0 ? id : 'add'));
  }

  cellClassName(columnName: string, column: any, rowData: any) {
    const cellClassName = super.cellClassName(columnName, column, rowData);

    if (columnName == 'date_due') {
      const now = moment();
      const daysDue = moment(now).diff(moment(rowData['date_due']), 'days');
      console.log(rowData['date_due'], daysDue);
      if (daysDue >= 0) return cellClassName + ' text-red-800';
      else if (daysDue > -7) return cellClassName + ' text-yellow-800';
      else return cellClassName;
    } else {
      return cellClassName;
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "date_sent" && !data['date_sent']) {
      return <div className='badge badge-danger'>{this.translate('Not sent')}</div>;
    } else if (columnName == "date_payment" && !data['date_payment']) {
      return <div className='badge badge-danger'>{this.translate('Not paid')}</div>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderFooter(): JSX.Element {
    let totalExclVat = 0;
    let totalVat = 0;
    let totalInclVat = 0;

    for (let i in this.state.data?.data) {
      const row = this.state.data?.data[i];
      totalExclVat += parseFloat(row['total_excl_vat']);
      totalVat += parseFloat(row['total_incl_vat']) - parseFloat(row['total_excl_vat']);
      totalInclVat += parseFloat(row['total_incl_vat']);
    }

    return <>
      <div className="font-bold">
        {this.translate('Total excl. VAT')}: {totalExclVat.toFixed(2)} €<br/>
        {this.translate('Total VAT')}: {totalVat.toFixed(2)} €<br/>
        {this.translate('Total incl. VAT')}: {totalInclVat.toFixed(2)} €
      </div>
    </>
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.description = {
      defaultValues: {
        id_klient: this.state.idCustomer,
        id_profil: 1,
        id_vystavil: globalThis.hubleto.idUser,
      },
      ui: { headerClassName: 'bg-indigo-50', },
    };
    return <FormInvoice {...formProps}/>;
  }
}