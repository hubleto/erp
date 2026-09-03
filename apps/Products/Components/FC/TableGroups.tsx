import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormGroup from './FormGroup';

const TableGroups = (props: TableProps) => {
  return <Table
    componentName={'TableGroups'}
    parentApp={'Hubleto/App/Community/Products'}
    model='Hubleto/App/Community/Products/Models/Group'
    formUrlSlug='products/groups'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormGroup {...table.getDefaultFormProps()} />}
    {...props}
  ></Table>
}

export default TableGroups;
