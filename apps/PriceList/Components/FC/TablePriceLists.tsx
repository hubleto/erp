import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormPriceList from './FormPriceList';

const TablePriceLists = (props: TableProps) => {
  return <Table
    componentName='TablePriceLists'
    parentApp='Hubleto/App/Community/PriceLists'
    model='Hubleto/App/Community/PriceLists/Models/PriceList'
    baseUrlSlug='price-lists'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormPriceList {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TablePriceLists;
