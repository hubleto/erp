import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormCustomer, { FormCustomerProps } from './FormCustomer';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import { FormProps } from '@hubleto/react-ui/core/Form';
import request from '@hubleto/react-ui/core/Request';

interface TableCustomersProps extends HubletoTableProps {
}

interface TableCustomersState extends HubletoTableState {
  tableContactsDescription?: any,
  tableLeadsDescription?: any,
  tableDealsDescription?: any,
  tableDocumentsDescription?: any,
}

export default class TableCustomers extends HubletoTable<TableCustomersProps, TableCustomersState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Customers/Models/Customer',
  }

  props: TableCustomersProps;
  state: TableCustomersState;

  translationContext: string = 'Hubleto\\App\\Community\\Customers\\Loader';
  translationContextInner: string = 'Components\\TableCustomers';

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide'
    }
  }

  getCsvImportEndpointParams(): any {
    return {
      model: this.props.model
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/customers/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "virt_tags") {
      return <>
        {data.TAGS.map((tag, key) => {
          return <div style={{backgroundColor: tag.TAG.color}} className='badge' key={'tag-' + data.id + '-' + key}>{tag.TAG.name}</div>;
        })}
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  onAfterLoadTableDescription(description: any): any {
    request.get(
      'api/table/describe',
      {
        model: 'Hubleto/App/Community/Contacts/Models/Contact',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableContactsDescription: description} as TableCustomersState);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'Hubleto/App/Community/Leads/Models/Lead',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableLeadsDescription: description} as TableCustomersState);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'Hubleto/App/Community/Deals/Models/Deal',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableDealsDescription: description} as TableCustomersState);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'Hubleto/App/Community/Customers/Models/CustomerDocument',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableDocumentsDescription: description} as TableCustomersState);
      }
    );

    return description;
  }

  renderForm(): JSX.Element {
    let formProps: FormCustomerProps = this.getFormProps() as FormCustomerProps;
    formProps.uid = 'form_customer';
    formProps.tableContactsDescription = this.state.tableContactsDescription;
    formProps.tableLeadsDescription = this.state.tableLeadsDescription;
    formProps.tableDealsDescription = this.state.tableDealsDescription;
    return <FormCustomer {...formProps}/>;
  }
}