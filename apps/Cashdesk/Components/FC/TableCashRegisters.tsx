import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormCashRegister from './FormCashRegister';

const componentName = 'TableCashRegisters'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Cashdesk';

const TableCashRegisters = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CashRegister'}
    formUrlSlug='cashdesk/cash-registers'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormCashRegister {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableCashRegisters;
