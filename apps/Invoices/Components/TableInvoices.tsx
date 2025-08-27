import React, { Component } from 'react'
import { setUrlParam, getUrlParam, deleteUrlParam } from '@hubleto/react-ui/core/Helper';
import { TableDescription } from '@hubleto/react-ui/core/Table';
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormInvoice from './FormInvoice';
import InputLookup from '@hubleto/react-ui/core/Inputs/Lookup';
import InputVarchar from '@hubleto/react-ui/core/Inputs/Varchar';
import InputDateTime from '@hubleto/react-ui/core/Inputs/DateTime';

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
    model: 'HubletoApp/Community/Invoices/Models/Invoice',
    // description: {
    //   ui: { addButtonText: this.translate('Create invoice') }
    // },
  }

  props: TableInvoicesProps;
  state: TableInvoicesState;

  translationContext: string = 'HubletoApp\\Community\\Invoices\\Loader::Components\\TableInvoices';

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
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/invoices/' + id);
  }

  onAfterLoadTableDescription(description: TableDescription): TableDescription {
    delete description.columns.ks;
    delete description.columns.ss;
    delete description.columns.id_profil;

    if (this.props.idCustomer > 0) delete description.columns['id_klient'];

    description.columns['TOTAL_EXCLUDING_VAT'] = {
      type: 'float',
      title: this.translate('Total excl. VAT'),
      unit: '€',
    };

    description.columns['TOTAL_VAT'] = {
      type: 'float',
      title: this.translate('VAT'),
      unit: '€',
    };

    description.columns['TOTAL_INCLUDING_VAT'] = {
      type: 'float',
      title: this.translate('Total incl. VAT'),
      unit: '€',
    };

    return description;
  }

  cellClassName(columnName: string, column: any, rowData: any) {
    let cellClassName = super.cellClassName(columnName, column, rowData);

    if (rowData['date_payment'] == '0000-00-00') cellClassName += ' bg-red-50';

    return cellClassName;
  }

  _datetimeFilter(stateParamName: string, urlParamName: string): JSX.Element {
    return <>
      <InputDateTime
        uid={this.props.uid + '_filter_' + urlParamName}
        type='date'
        value={this.state[stateParamName] ?? ''}
        onChange={(value: any) => {
          this.setState({[stateParamName]: value} as TableInvoicesState, () => { this.loadData(); });
          if (value == null) deleteUrlParam(urlParamName);
          else setUrlParam(urlParamName, value);
        }}
      ></InputDateTime>
    </>;
  }

  renderFilter(): JSX.Element {
    return <>
      <div className="card">
        <div className="card-body flex gap-1">
          <div><i className="fas fa-filter text-2xl text-gray-200 mr-4"></i></div>
          <div className={"p-1 flex-1" + (this.state.idCustomer > 0 || this.state.number != '' || this.state.vs != '' ? " bg-yellow-100" : "")}>
            {this.props.idCustomer ? null : <div>
              <b className="text-sm">{this.translate('Customer')}</b><br/>
              <InputLookup
                uid={this.props.uid + '_filter_customer'}
                model='HubletoApp/Community/Customers/Models/Customer'
                value={this.state.idCustomer}
                onChange={(value: any) => {
                  this.setState({idCustomer: value} as TableInvoicesState, () => { this.loadData(); });
                  if (value == 0) deleteUrlParam('id-customer');
                  else setUrlParam('id-customer', value);
                }}
              ></InputLookup>
            </div>}
            <div>
              <b className="text-sm">{this.translate('Number')}</b><br/>
              <InputVarchar
                uid={this.props.uid + '_filter_number'}
                value={this.state.number}
                onChange={(value: any) => {
                  this.setState({number: value} as TableInvoicesState, () => { this.loadData(); });
                  if (value == '') deleteUrlParam('number');
                  else setUrlParam('number', value);
                }}
              ></InputVarchar>
            </div>
            <div>
              <b className="text-sm">{this.translate('Variable symbol')}</b><br/>
              <InputVarchar
                uid={this.props.uid + '_filter_vs'}
                value={this.state.vs}
                onChange={(value: any) => {
                  this.setState({vs: value} as TableInvoicesState, () => { this.loadData(); });
                  if (value == '') deleteUrlParam('vs');
                  else setUrlParam('vs', value);
                }}
              ></InputVarchar>
            </div>
          </div>
          <div className={"p-1 flex-1" + (this.state.dateIssueFrom != '' || this.state.dateIssueTo != '' || this.state.dateDeliveryFrom != '' || this.state.dateDeliveryTo != '' || this.state.dateDueFrom != '' || this.state.dateDueTo != ''? " bg-yellow-100" : "")}>
            <b className="text-sm">{this.translate('Issue')}</b><br/>
            <div className="flex">
              {this._datetimeFilter('dateIssueFrom', 'date-issue-from')}
              {this._datetimeFilter('dateIssueTo', 'date-issue-to')}
            </div>
            <b className="text-sm">{this.translate('Delivery')}</b><br/>
            <div className="flex">
              {this._datetimeFilter('dateDeliveryFrom', 'date-delivery-from')}
              {this._datetimeFilter('dateDeliveryTo', 'date-delivery-to')}
            </div>
            <b className="text-sm">{this.translate('Due')}</b><br/>
            <div className="flex">
              {this._datetimeFilter('dateDueFrom', 'date-due-from')}
              {this._datetimeFilter('dateDueTo', 'date-due-to')}
            </div>
          </div>
          <div className={"p-1 flex-1" + (this.state.datePaymentFrom != '' || this.state.datePaymentTo != '' ? " bg-yellow-100" : "")}>
            <b className="text-sm">{this.translate('Payment')}</b><br/>
            <div className="flex">
              {this._datetimeFilter('datePaymentFrom', 'date-payment-from')}
              {this._datetimeFilter('datePaymentTo', 'date-payment-to')}
            </div>
          </div>
        </div>
      </div>
    </>;
  }

  renderFooter(): JSX.Element {
    let totalExclVat = 0;
    let totalVat = 0;
    let totalInclVat = 0;

    for (let i in this.state.data?.data) {
      const row = this.state.data?.data[i];
      totalExclVat += parseFloat(row['TOTAL_EXCLUDING_VAT']);
      totalVat += parseFloat(row['TOTAL_VAT']);
      totalInclVat += parseFloat(row['TOTAL_INCLUDING_VAT']);
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
        id_vystavil: globalThis.main.idUser,
      },
      ui: { headerClassName: 'bg-indigo-50', },
    };
    return <FormInvoice {...formProps}/>;
  }
}