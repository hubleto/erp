import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormCashRegister, { FormCashRegisterProps } from './FormCashRegister';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import { FormProps } from '@hubleto/react-ui/core/Form';
import request from '@hubleto/react-ui/core/Request';

interface TableCashRegistersProps extends HubletoTableProps {
}

interface TableCashRegistersState extends HubletoTableState {
  tableContactsDescription?: any,
  tableLeadsDescription?: any,
  tableDealsDescription?: any,
  tableDocumentsDescription?: any,
}

export default class TableCashRegisters extends HubletoTable<TableCashRegistersProps, TableCashRegistersState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Cashdesk/Models/CashRegister',
  }

  props: TableCashRegistersProps;
  state: TableCashRegistersState;

  translationContext: string = 'Hubleto\\App\\Community\\Cashdesk\\Loader';
  translationContextInner: string = 'Components\\TableCashRegisters';

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide'
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/cashdesk/cash-registers/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps: FormCashRegisterProps = this.getFormProps() as FormCashRegisterProps;
    return <FormCashRegister {...formProps}/>;
  }
  
  renderTable(): JSX.Element {
    return <div className='grid grid-cols-4 gap-2'>
      {this.state.data.data.map((row, index) => {
        return <a
          href={globalThis.main.config.projectUrl + '/cashdesk/cash-registers/' + row.id}
          className='btn btn-square btn-transparent btn-large'
        >
          <div className='card-header text-center flex-col'>
            <div><i className='fas fa-cash-register text-4xl mb-4'></i></div>
            <div>{row.identifier}</div>
          </div>
          <div className='card-body'>
            {row.description}
          </div>
        </a>;
      })}
    </div>;
  }
}