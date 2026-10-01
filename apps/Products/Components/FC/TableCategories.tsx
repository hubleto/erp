import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormCategory from './FormCategory';

const TableCategories = (props: TableProps) => {
  return <Table
    componentName='TableCategories'
    parentApp='Hubleto/App/Community/Products'
    model='Hubleto/App/Community/Products/Models/Category'
    baseUrlSlug='products/categories'
    // the tree has to be asked for in the first request - the server's description arrives with the data
    description={{ui: {dataView: 'tree'}}}
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormCategory {...table.getDefaultFormProps()} />}
    {...props}
  ></Table>
}

export default TableCategories;
