import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormCategory from './FormCategory';

const TableCategories = (props: TableProps) => {
  return <Table
    componentName='TableCategories'
    parentApp='Hubleto/App/Community/Products'
    model='Hubleto/App/Community/Products/Models/Category'
    formUrlSlug='products/categories'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormCategory {...table.getDefaultFormProps()} />}
    {...props}
  ></Table>
}

export default TableCategories;
