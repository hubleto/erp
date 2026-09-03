import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormAuditLog from './FormAuditLog';

const componentName = 'TableAuditLogs'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/AuditLogs';

const TableAuditLogs = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/AuditLog'}
    formUrlSlug='audit-logs'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormAuditLog {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableAuditLogs;
