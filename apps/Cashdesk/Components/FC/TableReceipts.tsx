import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormReceipt from './FormReceipt';

const componentName = 'TableReceipts'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Cashdesk';

const TableReceipts = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Receipt'}
    formUrlSlug='cashdesk/receipts'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormReceipt {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableReceipts;
